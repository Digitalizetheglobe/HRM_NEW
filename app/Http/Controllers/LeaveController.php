<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Leave as LocalLeave;
use App\Models\LeaveType;
use App\Mail\LeaveActionSend;
use App\Models\Utility;
use App\Models\User;
use App\Notifications\LeaveCreatedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Imports\EmployeesImport;
use App\Exports\LeaveExport;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\GoogleCalendar\Event as GoogleEvent;
use App\Models\EmployeeLeaveBalance;
use Twilio\Rest\Client;


class LeaveController extends Controller
{
    public function index(Request $request)
    {

        if (\Auth::user()->can('Manage Leave')) {
            $leaveBalances = [];
            
            if (\Auth::user()->type == 'employee') {
                $user     = \Auth::user();
                $employee = Employee::where('user_id', '=', $user->id)->first();
                $leaves = LocalLeave::where('employee_id', '=', $employee->id)->orderBy('id', 'desc')->get();
                
                if ($employee) {
                    $now = now();
                    $el = LeaveType::where('title', 'Earned Leave')->where('created_by', '=', \Auth::user()->creatorId())->first();
                    $cl = LeaveType::where('title', 'Casual Leave')->where('created_by', '=', \Auth::user()->creatorId())->first();
                    
                    if ($el) {
                        $balance = EmployeeLeaveBalance::getOrCreateBalance($employee, $el, $now);
                        
                        $isProbation = false;
                        if ($employee->company_doj) {
                            $doj = \Carbon\Carbon::parse($employee->company_doj);
                            $monthsSinceJoining = ($now->year - $doj->year) * 12 + ($now->month - $doj->month);
                            if ($monthsSinceJoining < 3) {
                                $isProbation = true;
                            }
                        }
                        
                        if ($isProbation) {
                            $probationLeavesTaken = LocalLeave::where('employee_id', $employee->id)
                                ->whereIn('status', ['Pending', 'Approved'])
                                ->sum('total_leave_days');
                            $allocated = 2.0;
                            $used = (float)$probationLeavesTaken;
                            $carryForward = 0.0;
                            $available = max(0.0, 2.0 - $probationLeavesTaken);
                        } else {
                            $allocated = (float)$balance->allocated_days;
                            $used = (float)$balance->used_days;
                            $carryForward = (float)$balance->carry_forward_days;
                            $available = max(0.0, ($allocated + $carryForward) - $used);
                        }
                    } else {
                        $allocated = 0.0;
                        $used = 0.0;
                        $carryForward = 0.0;
                        $available = 0.0;
                    }
                    
                    $casualUsedThisMonth = 0.0;
                    if ($cl) {
                        $casualUsedThisMonth = LocalLeave::where('employee_id', $employee->id)
                            ->where('leave_type_id', $cl->id)
                            ->where('status', 'Approved')
                            ->whereYear('start_date', $now->year)
                            ->whereMonth('start_date', $now->month)
                            ->sum('total_leave_days');
                    }
                    
                    $leaveBalances = [
                        [
                            'title' => __('Your Leave'),
                            'value' => $allocated + $carryForward,
                            'subtext' => __('Allocated: ') . number_format($allocated, 2) . ($carryForward > 0 ? ' | CF: ' . number_format($carryForward, 2) : ''),
                            'type' => 'total'
                        ],
                        [
                            'title' => __('Used Leaves'),
                            'value' => $used,
                            'subtext' => __('Total leaves taken this year'),
                            'type' => 'used'
                        ],
                        [
                            'title' => __('Remaining Leaves'),
                            'value' => $available,
                            'subtext' => __('Leaves available for use'),
                            'type' => 'remaining'
                        ],
                        [
                            'title' => __('Casual Leaves'),
                            'value' => $casualUsedThisMonth,
                            'subtext' => __('Used this month (Unlimited)'),
                            'type' => 'casual'
                        ]
                    ];
                }
            } else {
                // Filter leaves based on user type
                $query = LocalLeave::query();

                if (\Auth::user()->type == 'company') {
                    $query->where('created_by', '=', \Auth::user()->creatorId());
                } elseif (strtolower(\Auth::user()->type) == 'director') {
                    $query->where('created_by', '=', \Auth::user()->creatorId());
                } elseif (strtolower(\Auth::user()->type) == 'hr') {
                    $query->where('forwarded_to_director_id', \Auth::id())
                          ->where('company_approved', true);
                } else {
                    $query->where('created_by', '=', \Auth::user()->creatorId());
                }

                // Apply Month and Year filters
                if ($request->filled('month')) {
                    $month = $request->month;
                    $year = $request->filled('year') ? $request->year : date('Y');
                    
                    $startDate = $year . '-' . $month . '-01';
                    $endDate = date('Y-m-t', strtotime($startDate));
                    
                    $query->where(function ($q) use ($startDate, $endDate) {
                        $q->whereBetween('start_date', [$startDate, $endDate])
                          ->orWhereBetween('end_date', [$startDate, $endDate])
                          ->orWhere(function ($q2) use ($startDate, $endDate) {
                              $q2->where('start_date', '<=', $startDate)
                                 ->where('end_date', '>=', $endDate);
                          });
                    });
                } elseif ($request->filled('year')) {
                    $query->where(function($q) use ($request) {
                        $q->whereYear('start_date', $request->year)
                          ->orWhereYear('end_date', $request->year);
                    });
                }

                $leaves = $query->orderBy('id', 'desc')
                               ->with(['employees', 'leaveType'])
                               ->get();
            }

            return view('leave.index', compact('leaves', 'leaveBalances'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

        // public function employeeJson(Request $request)
        // {
        //     $employees = Employee::where('branch_id', $request->branch)->get()->mapWithKeys(function ($employee) {
        //         return [$employee->id => $employee->full_name];
        //     })->toArray();

        //     return response()->json($employees);
        // }

   public function create()
{
    if (\Auth::user()->can('Create Leave')) {
        $employeeId = null;

        if (Auth::user()->type == 'employee') {
            $employees = Employee::where('user_id', '=', \Auth::user()->id)->first();
            if ($employees) {
                $employeeId = $employees->id;
            }
        } else {
            $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->mapWithKeys(function ($employee) {
                return [$employee->id => $employee->full_name];
            });
            // Default to first employee if exists
            if ($employees->count() > 0) {
                $employeeId = $employees->keys()->first();
            }
        }

        // Get all leave types dynamically
        $leavetypes = LeaveType::where('created_by', '=', \Auth::user()->creatorId())->get();
        
        return view('leave.create', compact('employees', 'leavetypes', 'employeeId'));
    } else {
        return response()->json(['error' => __('Permission denied.')], 401);
    }
}  

    public function store(Request $request)
    {
        if (\Auth::user()->can('Create Leave')) {
            $rules = [
                'employee_id' => 'required',
                'leave_type_id' => 'required|exists:leave_types,id',
                'leave_duration_type' => 'required|in:full_day,half_day,flexible_days,mixed',
                'leave_reason' => 'required',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ];

            $validator = \Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }
            $actual_leave_type = LeaveType::find($request->leave_type_id);
            $isUnlimited = (bool)$actual_leave_type->unlimited;
            $calc_duration_type = $request->leave_duration_type;

            // Calculate total leave days
            // If form provides an override (from the new simplified UI), use it directly
            if ($request->filled('total_leave_days_override') && is_numeric($request->total_leave_days_override)) {
                $total_leave_days = (float)$request->total_leave_days_override;
                // Ensure it's a valid 0.5 increment
                if (fmod($total_leave_days, 0.5) != 0 || $total_leave_days < 0.5) {
                    return redirect()->back()->with('error', __('Leave days must be in 0.5-day increments and at least 0.5.'));
                }
                // Validate against working days in range
                $max_working_days = $this->calculateWorkingDays($request->employee_id, $request->start_date, $request->end_date);
                if ($total_leave_days > $max_working_days) {
                    return redirect()->back()->with('error', __('The requested leave days (' . $total_leave_days . ') cannot exceed the total working days (' . $max_working_days . ') in the selected range.'));
                }
            } elseif ($calc_duration_type === 'half_day') {
                $total_leave_days = 0.5;
            } else {
                $total_leave_days = $this->calculateWorkingDays($request->employee_id, $request->start_date, $request->end_date);
            }

            if (!$isUnlimited) {
                $employee = Employee::find($request->employee_id);
                $now = \Carbon\Carbon::parse($request->start_date);
                $balance = EmployeeLeaveBalance::getOrCreateBalance($employee, $actual_leave_type, $now);

                if ($balance) {
                    $sharedTypeIds = LeaveType::whereIn('title', ['Earned Leave', 'Sick Leave'])->pluck('id')->toArray();
                    $pendingDaysQuery = LocalLeave::where('employee_id', $request->employee_id)
                        ->where('status', 'Pending')
                        ->whereMonth('start_date', $now->month)
                        ->whereYear('start_date', $now->year);
                        
                    if (in_array($actual_leave_type->id, $sharedTypeIds)) {
                        $pendingDays = $pendingDaysQuery->whereIn('leave_type_id', $sharedTypeIds)->sum('total_leave_days');
                    } else {
                        $pendingDays = $pendingDaysQuery->where('leave_type_id', $actual_leave_type->id)->sum('total_leave_days');
                    }

                    $isProbation = false;
                    if ($employee->company_doj) {
                        $doj = \Carbon\Carbon::parse($employee->company_doj);
                        $monthsSinceJoining = ($now->year - $doj->year) * 12 + ($now->month - $doj->month);
                        if ($monthsSinceJoining < 3) {
                            $isProbation = true;
                        }
                    }

                    if ($isProbation) {
                        if (strtolower(trim($actual_leave_type->title)) !== 'earned leave') {
                            return redirect()->back()->with('error', __('Only Earned Leave is available during probation.'));
                        }
                        $probationLeavesTaken = LocalLeave::where('employee_id', $request->employee_id)
                            ->whereIn('status', ['Pending', 'Approved'])
                            ->sum('total_leave_days');
                        $availableDays = max(0, 2.0 - $probationLeavesTaken);
                    } else {
                        $availableDays = ($balance->allocated_days + $balance->carry_forward_days) - ($balance->used_days + $pendingDays);
                    }
                    
                    // Validate requested days against available balance
                    if ($total_leave_days > $availableDays) {
                        return redirect()->back()->with('error', __('You only have '.number_format($availableDays, 2).' days available. Cannot request '.$total_leave_days.' day(s).'));
                    }
                }
            }
            $leave = new LocalLeave();
            $leave->employee_id = $request->employee_id;
            $leave->leave_type_id = $actual_leave_type->id;
            $leave->leave_duration_type = in_array($calc_duration_type, ['flexible_days', 'mixed']) ? 'full_day' : $calc_duration_type;
            
            if ($calc_duration_type === 'half_day' && $request->half_day_session) {
                $leave->half_day_session = $request->half_day_session;
            } else {
                $leave->half_day_session = null;
            }

            $leave->applied_on = date('Y-m-d');
            $leave->start_date = $request->start_date;
            $leave->end_date = $request->end_date;
            $leave->total_leave_days = $total_leave_days;
            $leave->leave_reason = $request->leave_reason;
            $leave->remark = $request->remark ?? null;
            $leave->status = 'Pending';
            $leave->created_by = \Auth::user()->creatorId();
            
            if ($calc_duration_type === 'half_day' && $request->has('half_day_session')) {
                $sessionText = $request->half_day_session === 'first_half' ? 'First Half' : 'Second Half';
                $leave->remark = ($leave->remark ? $leave->remark . ' | ' : '') . 'Half Day Session: ' . $sessionText;
            }
            
            $leave->save();

            // Send SMS notification to specified numbers
            $employee = Employee::find($leave->employee_id);
            $leaveTypeName = $actual_leave_type ? $actual_leave_type->title : 'Leave';
            $this->sendLeaveCreationSMS($employee, $leaveTypeName, $leave);

            // Send notification to company users and director users
            $notificationData = [
                'leave_id' => $leave->id,
                'message' => 'New leave application: ' . ($employee ? $employee->full_name : 'Employee') . ' applied for ' . $leaveTypeName,
                'employee_name' => $employee ? $employee->full_name : 'Employee',
                'leave_type' => $leaveTypeName,
                'url' => route('leave.index'),
            ];

            // Get all company users and director users
            $companyUsers = User::where('type', 'company')
                ->where('created_by', \Auth::user()->creatorId())
                ->get();
            
            $directorUsers = User::where(function($query) {
                    $query->where('type', 'director')
                          ->orWhere('type', 'Director');
                })
                ->where('created_by', \Auth::user()->creatorId())
                ->get();

            // Send notifications
            $adminDeviceTokens = [];
            foreach ($companyUsers as $user) {
                $user->notify(new LeaveCreatedNotification($notificationData));
                if ($user->device_token) $adminDeviceTokens[] = $user->device_token;
            }
            
            foreach ($directorUsers as $user) {
                $user->notify(new LeaveCreatedNotification($notificationData));
                if ($user->device_token) $adminDeviceTokens[] = $user->device_token;
            }

            if (!empty($adminDeviceTokens)) {
                \App\Helpers\OneSignalHelper::sendPushNotification(
                    $adminDeviceTokens,
                    'New Leave Request',
                    $notificationData['message'],
                    ['url' => route('leave.index')]
                );
            }

            // Send email to company
            $setings = Utility::settings();
            if (isset($setings['leave_status']) && $setings['leave_status'] == 1) {
                $companyUser = User::find($employee->created_by);
                if ($companyUser) {
                    $company_email = !empty($setings['company_email']) ? $setings['company_email'] : $companyUser->email;
                    $uArr = [
                        'leave_email' => $employee->email,
                        'leave_status_name' => $employee->full_name,
                        'leave_status' => 'Applied',
                        'leave_reason' => $leave->leave_reason,
                        'leave_start_date' => $leave->start_date,
                        'leave_end_date' => $leave->end_date,
                        'total_leave_days' => $leave->total_leave_days,
                    ];
                    Utility::sendEmailTemplate('leave_status', [$company_email], $uArr);
                }
            }

            // Google calendar sync
            if ($request->get('synchronize_type') == 'google_calender') {
                $type = 'leave';
                $request1 = new GoogleEvent();
                $request1->title = !empty(\Auth::user()->getLeaveType($leave->leave_type_id)) ? 
                    \Auth::user()->getLeaveType($leave->leave_type_id)->title : '';
                $request1->start_date = $request->start_date;
                $request1->end_date = $request->end_date;
                Utility::addCalendarData($request1, $type);
            }

            return redirect()->route('leave.index')->with('success', __('Leave successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(LocalLeave $leave)
    {
        if (\Auth::user()->can('Manage Leave')) {
            if ($leave->created_by == \Auth::user()->creatorId()) {
                $employee  = Employee::find($leave->employee_id);
                $leavetype = LeaveType::find($leave->leave_type_id);
                
                return view('leave.show', compact('employee', 'leavetype', 'leave'));
            } else {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function edit(LocalLeave $leave)
    {
        if (\Auth::user()->can('Edit Leave')) {
            if ($leave->created_by == \Auth::user()->creatorId()) {

                if (Auth::user()->type == 'employee') {
                    $employees = Employee::where('employee_id', '=', \Auth::user()->creatorId())->first();
                } else {
                    $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->mapWithKeys(function ($employee) {
                        return [$employee->id => $employee->full_name];
                    });
                }

                // Get all leave types dynamically
                $leavetypes = LeaveType::where('created_by', '=', \Auth::user()->creatorId())->get();

                return view('leave.edit', compact('leave', 'employees', 'leavetypes'));
            } else {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, $leave)
    {
        $leave = LocalLeave::find($leave);
        if (\Auth::user()->can('Edit Leave')) {
            if ($leave->created_by == Auth::user()->creatorId()) {
                $rules = [
                    'employee_id' => 'required',
                    'leave_type_id' => 'required|exists:leave_types,id',
                    'start_date' => 'required|date',
                    'end_date' => 'required|date|after_or_equal:start_date',
                    'leave_reason' => 'required',
                ];
                if ($request->has('leave_duration_type')) {
                    $rules['leave_duration_type'] = 'required|in:full_day,half_day,flexible_days';
                }
                if ($request->leave_duration_type === 'flexible_days') {
                    $rules['flexible_leave_days'] = 'required|numeric|min:0.5';
                }

                $validator = \Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                $leave_type = LeaveType::find($request->leave_type_id);
                if (\Auth::user()->type == 'employee') {
                    $employee = Employee::where('user_id', '=', \Auth::user()->id)->first();
                } else {
                    $employee = Employee::find($request->employee_id);
                }

                $isUnlimited = (bool)$leave_type->unlimited;
                
                $calc_duration_type = $request->input('leave_duration_type', $leave->leave_duration_type);
                
                // Calculate total leave days excluding Week Off days for all leave types
                if ($calc_duration_type === 'half_day') {
                    $total_leave_days = 0.5;
                } elseif ($calc_duration_type === 'flexible_days') {
                    $total_leave_days = (float)$request->input('flexible_leave_days', $leave->total_leave_days);
                } else {
                    $total_leave_days = $this->calculateWorkingDays($employee->id, $request->start_date, $request->end_date);
                }

                if ($calc_duration_type === 'flexible_days') {
                    $max_working_days = $this->calculateWorkingDays($employee->id, $request->start_date, $request->end_date);
                    if ($total_leave_days > $max_working_days) {
                        return redirect()->back()->with('error', __('The requested leave days (' . $total_leave_days . ') cannot exceed the total working days (' . $max_working_days . ') in the selected date range.'));
                    }
                    if (fmod($total_leave_days, 0.5) != 0) {
                        return redirect()->back()->with('error', __('Leave days must be in 0.5-day increments.'));
                    }
                }
                
                if (!$isUnlimited) {
                    $now = \Carbon\Carbon::parse($request->start_date);
                    $balance = EmployeeLeaveBalance::getOrCreateBalance($employee, $leave_type, $now);

                    if ($balance) {
                    $sharedTypeIds = LeaveType::whereIn('title', ['Earned Leave', 'Sick Leave'])->pluck('id')->toArray();
                    $pendingDaysQuery = LocalLeave::where('employee_id', $employee->id)
                        ->where('status', 'Pending')
                        ->whereNotIn('id', [$leave->id])
                        ->whereMonth('start_date', $now->month)
                        ->whereYear('start_date', $now->year);
                        
                    if (in_array($leave_type->id, $sharedTypeIds)) {
                        $pendingDays = $pendingDaysQuery->whereIn('leave_type_id', $sharedTypeIds)->sum('total_leave_days');
                    } else {
                        $pendingDays = $pendingDaysQuery->where('leave_type_id', $leave_type->id)->sum('total_leave_days');
                    }
                    
                    $alreadyUsedDays = 0;
                    $isCurrentShared = in_array($leave->leave_type_id, $sharedTypeIds);
                    $isTargetShared = in_array($leave_type->id, $sharedTypeIds);
                    if ($leave->status == 'Approved' && ($leave->leave_type_id == $leave_type->id || ($isCurrentShared && $isTargetShared))) {
                        $alreadyUsedDays = (float)$leave->total_leave_days;
                    }

                        $isProbation = false;
                        if ($employee->company_doj) {
                            $doj = \Carbon\Carbon::parse($employee->company_doj);
                            $monthsSinceJoining = ($now->year - $doj->year) * 12 + ($now->month - $doj->month);
                            if ($monthsSinceJoining < 3) {
                                $isProbation = true;
                            }
                        }

                        if ($isProbation) {
                            if (strtolower(trim($leave_type->title)) !== 'earned leave') {
                                return redirect()->back()->with('error', __('Only Earned Leave is available during probation.'));
                            }
                            $probationLeavesTaken = LocalLeave::where('employee_id', $employee->id)
                                ->whereIn('status', ['Pending', 'Approved'])
                                ->whereNotIn('id', [$leave->id])
                                ->sum('total_leave_days');
                            $availableDays = max(0, 2.0 - $probationLeavesTaken);
                        } else {
                            $availableDays = ($balance->allocated_days + $balance->carry_forward_days) - ($balance->used_days - $alreadyUsedDays + $pendingDays);
                        }
                        
                        if ($total_leave_days > $availableDays) {
                            return redirect()->back()->with('error', __('You only have '.number_format($availableDays, 2).' days available for '.$leave_type->title.' this month.'));
                        }
                    }
                }

                // If the leave status is Approved, we must adjust the balance
                if ($leave->status == 'Approved') {
                    $this->restoreLeaveBalance($leave->employee_id, $leave->getOriginal('leave_type_id'), $leave->getOriginal('total_leave_days'));
                    if (!$isUnlimited) {
                        $now = \Carbon\Carbon::parse($request->start_date);
                        $newBalance = EmployeeLeaveBalance::getOrCreateBalance($employee, $leave_type, $now);
                        $newBalance->used_days += $total_leave_days;
                        $newBalance->save();
                    }
                }

                $leave->employee_id      = $employee->id;
                $leave->leave_type_id    = $request->leave_type_id;
                if ($request->has('leave_duration_type')) {
                    $leave->leave_duration_type = ($calc_duration_type === 'flexible_days') ? 'full_day' : $calc_duration_type;
                }
                $leave->start_date       = $request->start_date;
                $leave->end_date         = $request->end_date;
                $leave->total_leave_days = $total_leave_days;
                $leave->leave_reason     = $request->leave_reason;
                $leave->remark           = $request->remark ?? null;

                $leave->save();

                return redirect()->route('leave.index')->with('success', __('Leave successfully updated.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(LocalLeave $leave)
    {
        if (\Auth::user()->can('Delete Leave')) {
            if ($leave->created_by == \Auth::user()->creatorId()) {
                // If leave was approved, restore the balance before deleting
                if ($leave->status == 'Approved') {
                    $total_leave_days = $leave->total_leave_days;
                    // For regular leaves, restore monthly balance
                    $this->restoreLeaveBalance($leave->employee_id, $leave->leave_type_id, $total_leave_days, $leave->start_date);
                }
                
                $leave->delete();

                return redirect()->route('leave.index')->with('success', __('Leave successfully deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    
    /**
     * Calculate working days excluding Week Off days
     * @param int $employeeId
     * @param string $startDate
     * @param string $endDate
     * @return int Number of working days (excluding Week Off)
     */
    private function calculateWorkingDays($employeeId, $startDate, $endDate)
    {
        $employee = Employee::find($employeeId);
        if (!$employee) {
            return 0;
        }
        
        $weekOff = strtolower(trim((string) ('Sunday' ?? '')));
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $end->add(new \DateInterval('P1D')); // Include end date
        
        $workingDays = 0;
        $currentDate = clone $start;
        
        while ($currentDate < $end) {
            $dayName = strtolower($currentDate->format('l')); // monday, tuesday, etc.
            
            // Check if this is a Week Off day
            $isWeekOff = false;
            if ($weekOff !== '') {
                $isWeekOff = ($dayName === $weekOff);
            } else {
                // Default: Saturday and Sunday are Week Off
                $isWeekOff = in_array($dayName, ['saturday', 'sunday'], true);
            }
            
            // Count only working days (exclude Week Off)
            if (!$isWeekOff) {
                $workingDays++;
            }
            
            $currentDate->modify('+1 day');
        }
        
        return $workingDays;
    }
    



    public function export()
    {
        $name = 'leave_' . date('Y-m-d i:h:s');
        $data = Excel::download(new LeaveExport(), $name . '.xlsx');

        return $data;
    }

    public function action($id)
    {
        $leave     = LocalLeave::find($id);
        $employee  = Employee::find($leave->employee_id);
        $leavetype = LeaveType::find($leave->leave_type_id);



        return view('leave.action', compact('employee', 'leavetype', 'leave'));
    }

    public function changeaction(Request $request)
    {
        $leave = LocalLeave::find($request->leave_id);
        $previousStatus = $leave->status;
        $newStatus = $request->status;
        $total_leave_days = $leave->total_leave_days;
        
        $leaveType = LeaveType::find($leave->leave_type_id);
        $isUnlimited = $leaveType && $leaveType->unlimited == 1;
        $isCompanyUser = Auth::user()->type == 'company';
        $isDirectorUser = strtolower(Auth::user()->type) == 'director';
        $isHrUser = strtolower(Auth::user()->type) == 'hr';
        $employee = Employee::find($leave->employee_id);
        
        // Handle forwarding for ALL leave types (Company User)
        if ($isCompanyUser && $newStatus == 'Approved') {
            $directorId = $request->input('director_id');
            if (!empty($directorId)) {
                $leave->company_approved = true;
                $leave->forwarded_to_director_id = $directorId;
                $leave->forwarded_by_company_id = Auth::id();
                $leave->forwarded_at = now();
                $leave->status = 'Pending';
                $leave->save();
                return redirect()->route('leave.index')->with('success', __('Leave approved and forwarded successfully.'));
            }
        }
        
        // Handle director approval
        if ($isDirectorUser) {
            if (!$leave->forwarded_to_director_id || $leave->forwarded_to_director_id != Auth::id() || !$leave->company_approved) {
                return redirect()->route('leave.index')->with('error', __('You can only approve leaves that are forwarded to you by company users.'));
            }
            if ($newStatus == 'Approved') {
                $leave->director_approved = true;
                $leave->status = 'Approved';
            } else {
                $leave->status = 'Rejected';
            }
            $leave->save();
            
            if ($newStatus == 'Approved' && !$isUnlimited) {
                $now = \Carbon\Carbon::parse($leave->start_date);
                $balance = EmployeeLeaveBalance::getOrCreateBalance($employee, $leaveType, $now);
                if ($balance) {
                    $balance->used_days += $total_leave_days;
                    $balance->save();
                }
            }
        }
        
        // Handle HR approval
        if ($isHrUser && $leave->forwarded_to_director_id == Auth::id() && $leave->company_approved) {
            if ($newStatus == 'Approved') {
                $leave->director_approved = true;
                $leave->status = 'Approved';
            } else {
                $leave->status = 'Rejected';
            }
            $leave->save();
            
            if ($newStatus == 'Approved' && !$isUnlimited) {
                $now = \Carbon\Carbon::parse($leave->start_date);
                $balance = EmployeeLeaveBalance::getOrCreateBalance($employee, $leaveType, $now);
                if ($balance) {
                    $balance->used_days += $total_leave_days;
                    $balance->save();
                }
            }
        }
        
        // Handle Company User directly approving (if not forwarded)
        if ($isCompanyUser && $previousStatus == 'Pending') {
            if ($newStatus == 'Approved') {
                $leave->company_approved = true;
            }
        }
        
        // Restore/Deduct balances
        if ($previousStatus == 'Approved' && $newStatus == 'Rejected') {
            $this->restoreLeaveBalance($leave->employee_id, $leave->leave_type_id, $total_leave_days, $leave->start_date);
        }
        
        if ($newStatus == 'Approved' && $previousStatus != 'Approved') {
            if ($leave->forwarded_to_director_id && $leave->director_approved) {
                // Already deducted
            } else {
                if (!$isUnlimited) {
                    $now = \Carbon\Carbon::parse($leave->start_date);
                    $balance = EmployeeLeaveBalance::getOrCreateBalance($employee, $leaveType, $now);
                    if ($balance) {
                        $balance->used_days += $total_leave_days;
                        $balance->save();
                    }
                }
            }
        }
        
        $leave->status = $newStatus;
        $leave->save();

        // Send notifications and templates
        $setting = Utility::settings(\Auth::user()->creatorId());
        if (isset($setting['twilio_leave_approve_notification']) && $setting['twilio_leave_approve_notification'] == 1) {
            $uArr = ['leave_status' => $leave->status];
            Utility::send_twilio_msg($employee->phone, 'leave_approve_reject', $uArr);
        }

        $setings = Utility::settings();
        
        // Push notification for employee
        $employeeUser = User::find($employee->user_id);
        if ($employeeUser && $employeeUser->device_token) {
            $title = $request->status == 'Approved' ? 'Leave Approved' : 'Leave Rejected';
            $message = 'Your leave request from ' . $leave->start_date . ' to ' . $leave->end_date . ' has been ' . strtolower($request->status) . '.';
            \App\Helpers\OneSignalHelper::sendPushNotification(
                $employeeUser->device_token,
                $title,
                $message,
                ['url' => route('leave.index')]
            );
        }

        if ($setings['leave_status'] == 1) {
            $uArr = [
                'leave_email' => $employee->email,
                'leave_status_name' => $employee->full_name,
                'leave_status' => $request->status,
                'leave_reason' => $leave->leave_reason,
                'leave_start_date' => $leave->start_date,
                'leave_end_date' => $leave->end_date,
                'total_leave_days' => $leave->total_leave_days,
            ];
            $resp = Utility::sendEmailTemplate('leave_status', [$employee->email], $uArr);
            return redirect()->route('leave.index')->with('success', __('Leave status successfully updated.') . ((!empty($resp) && $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
        }

        return redirect()->route('leave.index')->with('success', __('Leave status successfully updated.'));
    }

    public function jsoncount(Request $request)
    {
        $employeeId = $request->employee_id;
        $employee = Employee::find($employeeId);
        if (!$employee) {
            return response()->json([]);
        }
        
        $now = now();
        $leaveTypes = LeaveType::where('created_by', '=', \Auth::user()->creatorId())->get();
        $data = [];
        
        foreach ($leaveTypes as $lt) {
            $isProbation = false;
            if ($employee->company_doj) {
                $doj = \Carbon\Carbon::parse($employee->company_doj);
                $monthsSinceJoining = ($now->year - $doj->year) * 12 + ($now->month - $doj->month);
                if ($monthsSinceJoining < 3) {
                    $isProbation = true;
                }
            }

            if ($isProbation && strtolower(trim($lt->title)) !== 'earned leave') {
                continue;
            }

            $balance = EmployeeLeaveBalance::getOrCreateBalance($employee, $lt, $now);
            
            if ($lt->unlimited) {
                $available = 'Unlimited';
            } else {
                $isProbation = false;
                if ($employee->company_doj) {
                    $doj = \Carbon\Carbon::parse($employee->company_doj);
                    $monthsSinceJoining = ($now->year - $doj->year) * 12 + ($now->month - $doj->month);
                    if ($monthsSinceJoining < 3) {
                        $isProbation = true;
                    }
                }

                if ($isProbation) {
                    $probationLeavesTaken = LocalLeave::where('employee_id', $employeeId)
                        ->whereIn('status', ['Pending', 'Approved'])
                        ->sum('total_leave_days');
                    $available = max(0.0, 2.0 - $probationLeavesTaken);
                } else {
                    $available = ($balance->allocated_days + $balance->carry_forward_days) - $balance->used_days;
                    $available = max(0.0, $available);
                    
                    $pendingDays = LocalLeave::where('employee_id', $employeeId)
                        ->where('leave_type_id', $lt->id)
                        ->where('status', 'Pending')
                        ->whereMonth('start_date', $now->month)
                        ->whereYear('start_date', $now->year)
                        ->sum('total_leave_days');
                    
                    $available = max(0.0, (float)$available - (float)$pendingDays);
                }
            }
            
            $data[] = [
                'id' => $lt->id,
                'title' => $lt->title,
                'unlimited' => $lt->unlimited,
                'available' => $available,
            ];
        }
        
        return response()->json($data);
    }

    public function calender(Request $request)
    {
        $created_by = \Auth::user()->creatorId();
        $Meetings = LocalLeave::where('created_by', $created_by)->get();

        $today_date = date('m');
        $current_month_event = LocalLeave::select('id', 'start_date', 'employee_id', 'created_at')->whereRaw('MONTH(start_date)=' . $today_date)->get();

        $arrMeeting = [];

        foreach ($Meetings as $meeting) {
            $arr['id']        = $meeting['id'];
            $arr['employee_id']     = $meeting['employee_id'];
            // $arr['leave_type_id']     = date('Y-m-d', strtotime($meeting['start_date']));
        }

        $leaves = LocalLeave::where('created_by', '=', \Auth::user()->creatorId())->get();
        if (\Auth::user()->type == 'employee') {
            $user     = \Auth::user();
            $employee = Employee::where('user_id', '=', $user->id)->first();
            $leaves   = LocalLeave::where('employee_id', '=', $employee->id)->get();
        } else {
            $leaves = LocalLeave::where('created_by', '=', \Auth::user()->creatorId())->get();
        }

        return view('leave.calender', compact('leaves'));
    }

    public function get_leave_data(Request $request)
    {
        $arrayJson = [];
        if ($request->get('calender_type') == 'google_calender') {
            $type = 'leave';
            $arrayJson =  Utility::getCalendarData($type);
        } else {
            $data = LocalLeave::where('created_by', \Auth::user()->creatorId())->get();

            foreach ($data as $val) {
                $end_date = date_create($val->end_date);
                date_add($end_date, date_interval_create_from_date_string("1 days"));
                $arrayJson[] = [
                    "id" => $val->id,
                    "title" => !empty(\Auth::user()->getLeaveType($val->leave_type_id)) ? \Auth::user()->getLeaveType($val->leave_type_id)->title : '',
                    "start" => $val->start_date,
                    "end" => date_format($end_date, "Y-m-d H:i:s"),
                    "className" => $val->color,
                    "textColor" => '#FFF',
                    "allDay" => true,
                    "url" => route('leave.action', $val['id']),
                ];
            }
        }

        return $arrayJson;
    }


    
    public function getMonthlyBalance($employeeId)
    {
        $employee = Employee::find($employeeId);
        if (!$employee) {
            return response()->json(['status' => 'error', 'message' => 'Employee not found']);
        }
        $now = now();
        $leaveTypes = LeaveType::where('created_by', '=', \Auth::user()->creatorId())->get();
        $totalAvailable = 0;
        $balances = [];
        
        foreach ($leaveTypes as $lt) {
            $balance = EmployeeLeaveBalance::getOrCreateBalance($employee, $lt, $now);
            
            if ($lt->unlimited) {
                $available = 'Unlimited';
            } else {
                $isProbation = false;
                if ($employee->company_doj) {
                    $doj = \Carbon\Carbon::parse($employee->company_doj);
                    $monthsSinceJoining = ($now->year - $doj->year) * 12 + ($now->month - $doj->month);
                    if ($monthsSinceJoining < 3) {
                        $isProbation = true;
                    }
                }

                if ($isProbation) {
                    $probationLeavesTaken = LocalLeave::where('employee_id', $employeeId)
                        ->whereIn('status', ['Pending', 'Approved'])
                        ->sum('total_leave_days');
                    $available = max(0.0, 2.0 - $probationLeavesTaken);
                } else {
                    $available = ($balance->allocated_days + $balance->carry_forward_days) - $balance->used_days;
                    $available = max(0.0, $available);
                    
                    $pendingDays = LocalLeave::where('employee_id', $employeeId)
                        ->where('leave_type_id', $lt->id)
                        ->where('status', 'Pending')
                        ->whereMonth('start_date', $now->month)
                        ->whereYear('start_date', $now->year)
                        ->sum('total_leave_days');
                    $available = max(0.0, (float)$available - (float)$pendingDays);
                }
                $totalAvailable += $available;
            }
            
            $balances[$lt->title] = $available;
        }
        
        return response()->json([
            'balances' => $balances,
            'total_available' => $totalAvailable,
            'status' => 'success'
        ]);
    }
    
    public function checkHalfDayAvailability(Request $request)
    {
        $employeeId = $request->employee_id;
        $date = $request->date;
        
        // Check if there's already a half day leave for this date
        $existingLeave = LocalLeave::where('employee_id', $employeeId)
            ->where('leave_duration_type', 'half_day')
            ->where('start_date', $date)
            ->whereIn('status', ['Pending', 'Approved'])
            ->first();
        
        return response()->json([
            'exists' => $existingLeave ? true : false,
            'status' => 'success'
        ]);
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->can('Delete Employee')) {
            abort(403, 'Permission Denied');
        }
        
        $request->validate([
            'unit_ids' => 'required|string' // We'll receive a JSON string
        ]);
        
        // Decode the JSON string to array
        $unitIds = json_decode($request->unit_ids, true);
        
        if (!is_array($unitIds) || empty($unitIds)) {
            return redirect()->route('units.index')
                ->with('error', 'No units selected for deletion.');
        }
        
        $deleteCount = Unit::whereIn('id', $unitIds)->delete();
        
        return redirect()->route('units.index')
            ->with('success', "Successfully deleted $deleteCount units.");
    }

    /**
     * Send WhatsApp notification when a leave is created
     * Uses Twilio Content Template for WhatsApp
     */
    private function sendLeaveCreationSMS($employee, $leaveTypeName, $leave)
    {
        \Log::info('sendLeaveCreationSMS function called (WhatsApp)', [
            'employee_id' => $employee ? $employee->id : null,
            'leave_id' => $leave->id ?? null
        ]);
        
        try {
            // Get Twilio settings
            $settings = Utility::settings(\Auth::user()->creatorId());
            
            // Twilio credentials from settings
            $account_sid = isset($settings['twilio_sid']) ? $settings['twilio_sid'] : env('TWILIO_SID');
            $auth_token = isset($settings['twilio_token']) ? $settings['twilio_token'] : env('TWILIO_AUTH_TOKEN');
            
            if (!$account_sid || !$auth_token) {
                \Log::error('Twilio credentials not configured');
                return;
            }
            
            // Initialize Twilio client
            $client = new Client($account_sid, $auth_token);
            \Log::info('Twilio client initialized for WhatsApp');
            
            // Use TwilioService for WhatsApp template
            \Log::info('Using TwilioService for WhatsApp template');
            $twilioService = new \App\Services\TwilioService();
            
            // Prepare message variables for template
            $employeeName = $employee ? $employee->full_name : 'Employee';
            $startDate = date('d-m-Y', strtotime($leave->start_date));
            $endDate = date('d-m-Y', strtotime($leave->end_date));
            
            // Get Leave Duration
            $leaveDuration = 'Full Day'; // Default
            if ($leave->leave_duration_type == 'half_day') {
                if ($leave->half_day_session == 'first_half') {
                    $leaveDuration = 'Half Day (First Half)';
                } elseif ($leave->half_day_session == 'second_half') {
                    $leaveDuration = 'Half Day (Second Half)';
                } else {
                    $leaveDuration = 'Half Day';
                }
            }
            
            // Prepare content variables for template, matching the {{1}}, {{2}}, etc.
            $contentVariables = [
                "1" => $employeeName,
                "2" => $leaveTypeName,
                "3" => $leaveDuration,
                "4" => $startDate,
                "5" => $endDate
            ];
            
            // Send to multiple numbers
            $successCount = 0;
            $failCount = 0;
            
            foreach ($toNumbers as $toNumber) {
                $result = $twilioService->sendWhatsAppTemplate($toNumber, $contentVariables);
                
                if ($result) {
                    $successCount++;
                    \Log::info('Leave notification sent via TwilioService (template) to ' . $toNumber . ' - SID: ' . $result->sid);
                } else {
                    $failCount++;
                    \Log::error('Failed to send leave notification via TwilioService (template) to ' . $toNumber);
                }
            }
            
            \Log::info('WhatsApp template sending completed', [
                'success_count' => $successCount,
                'fail_count' => $failCount,
                'total_recipients' => count($toNumbers)
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in sendLeaveCreationSMS: ' . $e->getMessage());
            \Log::error('Error details: ' . $e->getTraceAsString());
        }
    }

    public function leaveDetails()
    {
        if (\Auth::user()->type != 'company') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $employees = Employee::where('created_by', \Auth::user()->creatorId())->notTerminated()->get();
        $leaveTypes = LeaveType::where('created_by', \Auth::user()->creatorId())->get();
        $employeeLeaveDetails = [];
        $now = now();

        foreach ($employees as $employee) {
            $balances = [];
            foreach ($leaveTypes as $lt) {
                $balances[$lt->id] = $this->getEmployeeCurrentLeaveBalance($employee, $lt, $now);
            }

            $employeeLeaveDetails[] = [
                'employee' => $employee,
                'balances' => $balances,
            ];
        }

        return view('leave.leave-details', compact('employeeLeaveDetails', 'leaveTypes'));
    }

    private function getEmployeeCurrentLeaveBalance($employee, $leaveType, $now)
    {
        if ($leaveType->unlimited) {
            return 'Unlimited';
        }

        $balance = EmployeeLeaveBalance::getOrCreateBalance($employee, $leaveType, $now);
        return $balance ? max(0.0, $balance->getAvailableDaysAttribute()) : 0.0;
    }

    private function restoreLeaveBalance($employeeId, $leaveTypeId, $totalDays, $date = null)
    {
        $now = $date ? \Carbon\Carbon::parse($date) : now();
        $lt = LeaveType::find($leaveTypeId);
        if ($lt && strtolower($lt->title) == 'sick leave') {
            $earnedLeaveType = LeaveType::where('title', 'Earned Leave')->first();
            if ($earnedLeaveType) {
                $leaveTypeId = $earnedLeaveType->id;
                $lt = $earnedLeaveType;
            }
        }
        
        $employee = Employee::find($employeeId);
        if ($employee && $lt) {
            $balance = EmployeeLeaveBalance::getOrCreateBalance($employee, $lt, $now);
            if ($balance) {
                $balance->used_days = max(0.0, $balance->used_days - $totalDays);
                $balance->save();
                EmployeeLeaveBalance::updateCarryForward($employee, $lt, $now);
            }
        }
    }

    public function reason($id)
    {
        $leave = LocalLeave::find($id);
        
        return view('leave.reason', compact('leave'));
    }


}
