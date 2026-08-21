<?php

namespace App\Http\Controllers;

    use App\Imports\AttendanceImport;
    use App\Models\AttendanceEmployee;
    use App\Models\Branch;
    use App\Models\Department;
    use App\Models\Employee;
    use App\Models\IpRestrict;
    use App\Models\Termination;
    use App\Models\User;
    use App\Models\Utility;
    use Carbon\Carbon;
    use DateTime;
    use Illuminate\Http\Request;
    use App\Models\LeaveType;
    use App\Models\EmployeeLeaveBalance;
    use App\Models\Leave as LocalLeave;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Facades\DB;

    class AttendanceEmployeeController extends Controller
    {

        public function index(Request $request)
        {
            $this->resolvePastSinglePunches();
            if (\Auth::user()->can('Manage Attendance')) {
                $branch = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                $branch->prepend('All', '');

                $department = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                $department->prepend('All', '');

                // Get employees for filter dropdown - exclude Director and Hr users
                $employees = Employee::where('created_by', \Auth::user()->creatorId())
                    ->whereHas('user', function($query) {
                        $query->where('type', 'employee');
                    })
                    ->with('user')
                    ->get()
                    ->mapWithKeys(function ($employee) {
                        return [$employee->id => $employee->full_name];
                    });
                $employees->prepend('All', '');

                if (\Auth::user()->type == 'employee') {
                    $emp = !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0;

                    $attendanceEmployee = AttendanceEmployee::where('employee_id', $emp)
                                    ->orderBy('date', 'desc')
                                    ->orderBy('clock_in', 'desc');

                    if ($request->type == 'monthly' && !empty($request->month)) {
                        $month = date('m', strtotime($request->month));
                        $year  = date('Y', strtotime($request->month));


                        $start_date = date($year . '-' . $month . '-01');
                        $end_date = date('Y-m-t', strtotime('01-' . $month . '-' . $year));

                        // old date
                        // $end_date   = date($year . '-' . $month . '-t');

                        $attendanceEmployee->whereBetween(
                            'date',
                            [
                                $start_date,
                                $end_date,
                            ]
                        );
                    } elseif ($request->type == 'daily' && !empty($request->date)) {
                        $attendanceEmployee->where('date', $request->date);
                    } else {
                        $month      = date('m');
                        $year       = date('Y');
                        $start_date = date($year . '-' . $month . '-01');
                        $end_date = date('Y-m-t', strtotime('01-' . $month . '-' . $year));

                        // old date
                        // $end_date   = date($year . '-' . $month . '-t');

                        $attendanceEmployee->whereBetween(
                            'date',
                            [
                                $start_date,
                                $end_date,
                            ]
                        );
                    }

                    $attendanceEmployee = $attendanceEmployee->get();
                } else {
                    $employee = Employee::select('id')->where('created_by', \Auth::user()->creatorId())
                        ->whereHas('user', function($query) {
                            $query->where('type', 'employee');
                        });
                    if (!empty($request->branch)) {
                        $employee->where('branch_id', $request->branch);
                    }

                    if (!empty($request->department)) {
                        $employee->where('department_id', $request->department);
                    }

                    if (!empty($request->employee)) {
                        $employee->where('id', $request->employee);
                    }

                    $employee = $employee->get()->pluck('id');

                    $attendanceEmployee = AttendanceEmployee::whereIn('employee_id', $employee)
                                    ->orderBy('date', 'desc')
                                    ->orderBy('clock_in', 'desc');
                    
                    if ($request->type == 'monthly' && !empty($request->month)) {

                        $month = date('m', strtotime($request->month));
                        $year  = date('Y', strtotime($request->month));
                        $start_date = date($year . '-' . $month . '-01');
                        $end_date = date('Y-m-t', strtotime('01-' . $month . '-' . $year));

                        // old date
                        // $end_date   = date($year . '-' . $month . '-t');

                        $attendanceEmployee->whereBetween(
                            'date',
                            [
                                $start_date,
                                $end_date,
                            ]
                        );
                    } elseif ($request->type == 'daily' && !empty($request->date)) {
                        $attendanceEmployee->where('date', $request->date);
                    } else {

                        $month      = date('m');
                        $year       = date('Y');
                        $start_date = date($year . '-' . $month . '-01');
                        $end_date = date('Y-m-t', strtotime('01-' . $month . '-' . $year));
                        // old date
                        // $end_date   = date($year . '-' . $month . '-t');

                        $attendanceEmployee->whereBetween(
                            'date',
                            [
                                $start_date,
                                $end_date,
                            ]
                        );
                    }

                $attendanceEmployee = $attendanceEmployee->get();

                // Calculate late marks and early leaving dynamically for existing data
                $attendanceEmployee->transform(function ($attendance) {
                    // Removed dynamic clock_out override so actual times show on calendar

                    $attendance->late = $this->calculateLateMark($attendance->clock_in, $attendance->date, $attendance->employee_id);
                    $attendance->early_leaving = $this->calculateEarlyLeaving($attendance->clock_out, $attendance->date, $attendance->employee_id);
                    return $attendance;
                });
            }

            return view('attendance.index', compact('attendanceEmployee', 'branch', 'department', 'employees'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }

        public function create()
        {
            if (\Auth::user()->can('Create Attendance')) {
                $employees = User::where('created_by', '=', Auth::user()->creatorId())->where('type', '=', "employee")->get()->pluck('name', 'id');

                // Get employees with single punch in (clock_in exists but clock_out is null or empty)
                $singlePunchEmployees = AttendanceEmployee::where('created_by', '=', Auth::user()->creatorId())
                    ->whereNotNull('clock_in')
                    ->where(function($query) {
                        $query->whereNull('clock_out')
                            ->orWhere('clock_out', '=', '')
                            ->orWhere('clock_out', '=', '00:00:00');
                    })
                    ->pluck('employee_id')
                    ->unique()
                    ->toArray();

                return view('attendance.create', compact('employees', 'singlePunchEmployees'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }

        
        public function store(Request $request)
        {
            if (\Auth::user()->can('Create Attendance')) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'employee_id' => 'required',
                        'date' => 'required',
                        'clock_in' => 'required',
                        'clock_out' => 'required',
                    ]
                );

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }

                // Check for existing attendance
                $attendance = AttendanceEmployee::where('employee_id', '=', $request->employee_id)
                    ->where('date', '=', $request->date)
                    ->where('clock_out', '=', '00:00:00')
                    ->get()
                    ->toArray();

                if ($attendance) {
                    return redirect()->route('attendanceemployee.index')->with('error', __('Employee Attendance Already Created.'));
                }

                $date = date("Y-m-d");

                // Calculate total worked hours
                $workedSeconds = strtotime($request->clock_out) - strtotime($request->clock_in);
                $workedHours = $workedSeconds / 3600;
                
                $statusData = $this->determineStatus($request->date, $workedHours, $request->employee_id, $request->clock_in . ':00');

                $employeeAttendance = new AttendanceEmployee();
                $employeeAttendance->employee_id   = $request->employee_id;
                $employeeAttendance->date          = $request->date;
                $employeeAttendance->status        = $statusData['status'];
                $employeeAttendance->status_reason = $statusData['reason'];
                $employeeAttendance->clock_in      = $request->clock_in . ':00';
                $employeeAttendance->clock_out     = $request->clock_out . ':00';
                $employeeAttendance->late          = '00:00:00';
                $employeeAttendance->early_leaving = '00:00:00';
                $employeeAttendance->early_arrival = $this->calculateEarlyArrival($request->clock_in . ':00', $request->date, $request->employee_id);
                $employeeAttendance->overtime      = '00:00:00';
                $employeeAttendance->total_rest    = '00:00:00';
                $employeeAttendance->created_by    = \Auth::user()->creatorId();
                $employeeAttendance->save();

                return redirect()->route('attendanceemployee.index')->with('success', __('Employee attendance successfully created.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        
        public function show(Request $request)
        {
            // return redirect()->back();
            return redirect()->route('attendanceemployee.index');
        }

        public function edit($id)
        {
            if (\Auth::user()->can('Edit Attendance')) {
                $attendanceEmployee = AttendanceEmployee::where('id', $id)->first();
                $employees          = Employee::where('created_by', '=', \Auth::user()->creatorId())
                    ->whereHas('user', function($query) {
                        $query->where('type', 'employee');
                    })
                    ->get()->mapWithKeys(function ($employee) {
                        return [$employee->id => $employee->full_name];
                    });

                return view('attendance.edit', compact('attendanceEmployee', 'employees'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }

        public function update(Request $request, $id)
        {
            if (\Auth::user()->type == 'company' || \Auth::user()->type == 'hr') {
                $check = AttendanceEmployee::where('id', '=', $id)
                                        ->where('employee_id', '=', $request->employee_id)
                                        ->where('date', $request->date)
                                        ->first();

                if (!$check) {
                    return redirect()->route('attendanceemployee.index')->with('error', __('Attendance record not found.'));
                }

                $startTime = Utility::getValByName('company_start_time');
                $endTime   = Utility::getValByName('company_end_time');

                $clockIn = $request->clock_in;
                $clockOut = $request->clock_out;

                // Calculate total worked hours
                $workedSeconds = strtotime($clockOut) - strtotime($clockIn);
                $workedHours = $workedSeconds / 3600;
                
                // Determine status
                $statusData = $this->determineStatus($check->date ?? date('Y-m-d'), $workedHours, $request->employee_id, $request->clock_in . ':00');
                
                $late = '00:00:00';
                $earlyLeaving = '00:00:00';

                $attendanceEmployee                = AttendanceEmployee::find($check->id);
                $attendanceEmployee->clock_out     = $request->clock_out . ':00';
                $attendanceEmployee->status        = $statusData['status'];
                $attendanceEmployee->status_reason = $statusData['reason'];
                $earlyArrival = $this->calculateEarlyArrival($clockIn, $check->date ?? date('Y-m-d'), $request->employee_id);
                $overtime = '00:00:00';

                if ($check->date == date('Y-m-d')) {
                    $check->update([
                        'late' => $late,
                        'early_leaving' => ($earlyLeaving > 0) ? $earlyLeaving : '00:00:00',
                        'early_arrival' => $earlyArrival,
                        'overtime' => $overtime,
                        'clock_in' => $clockIn,
                        'clock_out' => $clockOut,
                        'status' => $statusData['status'],
                        'status_reason' => $statusData['reason']
                    ]);

                    return redirect()->route('attendanceemployee.index')->with('success', __('Employee attendance successfully updated.'));
                } else {
                    return redirect()->route('attendanceemployee.index')->with('error', __('You can only update current day attendance.'));
                }
            }

            $employeeId      = !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0;
            $todayAttendance = AttendanceEmployee::where('employee_id', '=', $employeeId)->where('date', date('Y-m-d'))->first();

            $startTime = Utility::getValByName('company_start_time');
            $endTime   = Utility::getValByName('company_end_time');
            if (Auth::user()->type == 'employee') {

                $date = date("Y-m-d");
                $time = date("H:i:s");

                $workedSeconds = strtotime($time) - strtotime($todayAttendance->clock_in);
                $workedHours = $workedSeconds / 3600;
                
                $statusData = $this->determineStatus($todayAttendance->date ?? date('Y-m-d'), $workedHours, $employeeId, $todayAttendance->clock_in ?? null);

                $attendanceEmployee['status']        = $statusData['status'];
                $attendanceEmployee['status_reason'] = $statusData['reason'];
                $attendanceEmployee['clock_out']     = $time;
                $attendanceEmployee['late']          = '00:00:00';
                $attendanceEmployee['early_leaving'] = '00:00:00';
                $attendanceEmployee['overtime']      = '00:00:00';

                if (!empty($request->date)) {
                    $attendanceEmployee['date']       =  $request->date;
                }
                AttendanceEmployee::where('id', $id)->update($attendanceEmployee);

                return redirect()->route('dashboard')->with('success', __('Employee successfully clock Out.'));
            } else {
                $date = date("Y-m-d");
                $clockout_time = date("H:i:s");
                $attendanceEmployee                = AttendanceEmployee::find($id);
                
                $workedSeconds = strtotime($clockout_time) - strtotime($attendanceEmployee->clock_in);
                $workedHours = $workedSeconds / 3600;
                
                $statusData = $this->determineStatus($attendanceEmployee->date ?? date('Y-m-d'), $workedHours, $attendanceEmployee->employee_id, $attendanceEmployee->clock_in);

                $attendanceEmployee->status        = $statusData['status'];
                $attendanceEmployee->status_reason = $statusData['reason'];
                $attendanceEmployee->clock_out     = $clockout_time;
                $attendanceEmployee->late          = '00:00:00';
                $attendanceEmployee->early_leaving = '00:00:00';
                $attendanceEmployee->early_arrival = $attendanceEmployee->early_arrival ?? '00:00:00';
                $attendanceEmployee->overtime      = '00:00:00';
                $attendanceEmployee->total_rest    = '00:00:00';

                $attendanceEmployee->save();

                return redirect()->back()->with('success', __('Employee attendance successfully updated.'));
            }
        }

        public function destroy($id)
        {
            if (\Auth::user()->can('Delete Attendance')) {
                $attendance = AttendanceEmployee::where('id', $id)->first();

                $attendance->delete();

                return redirect()->route('attendanceemployee.index')->with('success', __('Attendance successfully deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }

        
        public function bulkAttendance(Request $request)
        {
            if (\Auth::user()->can('Create Attendance')) {

                $branch = Branch::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                $branch->prepend('Select Branch', '');

                $department = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                $department->prepend('Select Department', '');

                $employees = [];
                if (!empty($request->branch) && !empty($request->department)) {
                    $employees = Employee::where('created_by', \Auth::user()->creatorId())
                        ->whereHas('user', function($query) {
                            $query->where('type', 'employee');
                        })
                        ->where('branch_id', $request->branch)
                        ->where('department_id', $request->department)
                        ->whereNotIn('id', function ($query) {
                            $query->select('employee_id')->from('terminations');
                        })
                        ->get();
                }

                return view('attendance.bulk', compact('employees', 'branch', 'department'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }

        public function bulkAttendanceData(Request $request)
        {
            if (\Auth::user()->can('Create Attendance')) {
                if (!empty($request->branch) && !empty($request->department)) {
                    $startTime = Utility::getValByName('company_start_time');
                    $endTime   = Utility::getValByName('company_end_time');
                    $date      = $request->date;

                    $employees = $request->employee_id;
                    $atte      = [];
                    foreach ($employees as $employee) {
                        $present = 'present-' . $employee;
                        $in      = 'in-' . $employee;
                        $out     = 'out-' . $employee;
                        $atte[]  = $present;
                        if ($request->$present == 'on') {

                            $in  = date("H:i:s", strtotime($request->$in));
                            $out = date("H:i:s", strtotime($request->$out));

                            // Calculate total worked hours
                            $workedSeconds = strtotime($out) - strtotime($in);
                            $workedHours = $workedSeconds / 3600;
                            
                            $statusData = $this->determineStatus($request->date, $workedHours, $employee, $in);

                            $attendance = AttendanceEmployee::where('employee_id', '=', $employee)->where('date', '=', $request->date)->first();

                            if (!empty($attendance)) {
                                $employeeAttendance = $attendance;
                            } else {
                                $employeeAttendance              = new AttendanceEmployee();
                                $employeeAttendance->employee_id = $employee;
                                $employeeAttendance->created_by  = \Auth::user()->creatorId();
                            }

                            $employeeAttendance->date          = $request->date;
                            $employeeAttendance->status        = $statusData['status']; 
                            $employeeAttendance->status_reason = $statusData['reason'];
                            $employeeAttendance->clock_in      = $in;
                            $employeeAttendance->clock_out     = $out;
                            $employeeAttendance->late          = '00:00:00';
                            $employeeAttendance->early_leaving = '00:00:00';
                            $employeeAttendance->early_arrival = $this->calculateEarlyArrival($in, $request->date, $employee);
                            $employeeAttendance->overtime      = '00:00:00';
                            $employeeAttendance->total_rest    = '00:00:00';
                            $employeeAttendance->save();
                        } else {
                            $attendance = AttendanceEmployee::where('employee_id', '=', $employee)->where('date', '=', $request->date)->first();

                            if (!empty($attendance)) {
                                $employeeAttendance = $attendance;
                            } else {
                                $employeeAttendance              = new AttendanceEmployee();
                                $employeeAttendance->employee_id = $employee;
                                $employeeAttendance->created_by  = \Auth::user()->creatorId();
                            }

                            $employeeAttendance->status        = AttendanceEmployee::STATUS_ABSENT;
                            $employeeAttendance->date          = $request->date;
                            $employeeAttendance->clock_in      = '00:00:00';
                            $employeeAttendance->clock_out     = '00:00:00';
                            $employeeAttendance->late          = '00:00:00';
                            $employeeAttendance->early_leaving = '00:00:00';
                            $employeeAttendance->early_arrival = '00:00:00';
                            $employeeAttendance->overtime      = '00:00:00';
                            $employeeAttendance->total_rest    = '00:00:00';
                            $employeeAttendance->save();
                        }
                    }

                    return redirect()->back()->with('success', __('Employee attendance successfully created.'));
                } else {
                    return redirect()->back()->with('error', __('Branch & department field required.'));
                }
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }

        public function importFile()
        {
            return view('attendance.import');
        }

        public function import(Request $request)
        {
            $rules = [
                'file' => 'required|mimes:csv,txt,xlsx',
            ];
            $validator = \Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->getMessageBag()->first());
            }

            try {
                $data = (new AttendanceImport())->toArray(request()->file('file'))[0];
                
                // Determine if this is our "Report" format
                $isReportFormat = false;
                foreach ($data as $row) {
                    if (isset($row[0]) && (strpos($row[0], 'Employee Code:') !== false || strpos($row[0], 'Empployee Code:') !== false)) {
                        $isReportFormat = true;
                        break;
                    }
                }

                if ($isReportFormat) {
                    return $this->processReportImport($data);
                } else {
                    // Fallback to flat format logic (standard import)
                    return $this->processFlatImport($data);
                }

            } catch (\Exception $e) {
                \Log::error('Attendance Import Error: ' . $e->getMessage());
                return redirect()->back()->with('error', __('Error processing file: ') . $e->getMessage());
            }
        }

        /**
         * Process the multi-row Report format (Exported format)
         */
        protected function processReportImport($data)
        {
            $dates = [];
            $currentEmployee = null;
            $processedCount = 0;

            $startTime = Utility::getValByName('company_start_time');
            $endTime   = Utility::getValByName('company_end_time');

            for ($i = 0; $i < count($data); $i++) {
                $row = $data[$i];
                if (empty($row)) continue;

                $firstCell = trim((string)($row[0] ?? ''));

                // 1. Identify Employee Code row
                if (strpos($firstCell, 'Employee Code:') !== false || strpos($firstCell, 'Empployee Code:') !== false) {
                    $empCode = trim(str_replace(['Employee Code:', 'Empployee Code:'], '', $firstCell));
                    $currentEmployee = Employee::where('employee_id', $empCode)->where('created_by', \Auth::user()->creatorId())->first();
                    continue;
                }

                // 2. Identify Dates row (labeled "Days")
                if ($firstCell === 'Days') {
                    $dates = [];
                    for ($j = 1; $j < count($row); $j++) {
                        $val = trim((string)($row[$j] ?? ''));
                        if (empty($val)) break; 
                        $dates[$j] = $val;
                    }
                    continue;
                }

                // 3. Process Status, InTime, OutTime rows
                if ($currentEmployee && $firstCell === 'Status') {
                    $statusRow = $row;
                    $inTimeRow = $data[$i + 1] ?? [];
                    $outTimeRow = $data[$i + 2] ?? [];
                    
                    if (trim((string)($inTimeRow[0] ?? '')) !== 'InTime' || trim((string)($outTimeRow[0] ?? '')) !== 'OutTime') {
                        continue;
                    }

                    // Extract period from first row
                    $periodStr = trim((string)($data[0][0] ?? ''));
                    preg_match('/([A-Za-z]+ \d+ \d{4}) To ([A-Za-z]+ \d+ \d{4})/', $periodStr, $matches);
                    $baseDate = !empty($matches[1]) ? \Carbon\Carbon::parse($matches[1]) : \Carbon\Carbon::now();

                    foreach ($dates as $colIdx => $dayLabel) {
                        $dayNum = (int)filter_var($dayLabel, FILTER_SANITIZE_NUMBER_INT);
                        if (!$dayNum) continue;

                        $dateObj = $baseDate->copy()->day($dayNum);
                        $date = $dateObj->toDateString();
                        
                        $newStatus = trim((string)($statusRow[$colIdx] ?? ''));
                        $newIn = trim((string)($inTimeRow[$colIdx] ?? ''));
                        $newOut = trim((string)($outTimeRow[$colIdx] ?? ''));

                        if (empty($newStatus)) continue;

                        $this->applyAttendanceTransition($currentEmployee, $date, $newStatus, $newIn, $newOut, $startTime, $endTime);
                        $processedCount++;
                    }
                    
                    $i += 3; 
                }
            }

            return redirect()->back()->with('success', sprintf(__('Import completed. Processed %d records.'), $processedCount));
        }

        /**
         * Process traditional flat format (Email, Date, In, Out)
         */
        protected function processFlatImport($data)
        {
            $startTime = Utility::getValByName('company_start_time');
            $endTime   = Utility::getValByName('company_end_time');
            $processedCount = 0;

            foreach ($data as $key => $value) {
                if ($key == 0) continue; 

                $employeeData = Employee::where('email', $value[0])->where('created_by', \Auth::user()->creatorId())->first();
                if (!$employeeData) continue;

                $date = $value[1];
                $newIn = $value[2];
                $newOut = $value[3];
                $newStatus = !empty($newIn) ? 'P' : 'A';

                $this->applyAttendanceTransition($employeeData, $date, $newStatus, $newIn, $newOut, $startTime, $endTime);
                $processedCount++;
            }

            return redirect()->back()->with('success', sprintf(__('Import completed. Processed %d records.'), $processedCount));
        }

        /**
         * Core Transition Logic
         */
        protected function applyAttendanceTransition($employee, $date, $newStatus, $newIn, $newOut, $startTime, $endTime)
        {
            $newStatus = strtoupper($newStatus);
            if ($newStatus === 'A') $newStatus = 'A';

            // Get current state from database to detect changes
            $currentAtt = AttendanceEmployee::where('employee_id', $employee->id)->where('date', $date)->first();
            $currentLeaveCode = $this->getLeaveStatus($employee->id, $date);
            
            $currentStatus = '';
            if ($currentAtt) {
                if ($currentAtt->status === 'Present') $currentStatus = 'P';
                elseif ($currentAtt->status === 'Single Punch In') $currentStatus = 'SP';
                elseif ($currentAtt->late !== '00:00:00') $currentStatus = 'LM';
                else $currentStatus = 'P';
            } elseif ($currentLeaveCode) {
                $currentStatus = $currentLeaveCode;
            } else {
                // If neither attendance nor leave exists, check if it's a Weekly Off
                $dayName = \Carbon\Carbon::parse($date)->format('l');
                if (strtolower('Sunday') === strtolower($dayName)) {
                    $currentStatus = 'WO';
                } else {
                    $currentStatus = 'LOP'; // Default for no record
                }
            }

            // ONLY apply transition if the status has changed
            if ($newStatus === $currentStatus) {
                // Special case: If status is P but times have changed, still update attendance
                if ($newStatus === 'P' && $currentAtt) {
                    $this->updateAttendanceTimes($currentAtt, $newIn, $newOut, $date);
                }
                return;
            }

            if (in_array($newStatus, ['P', 'LM', 'SP'])) {
                $this->processPresentTransition($employee, $date, $newIn, $newOut, $startTime, $endTime, $newStatus);
            }
            elseif (in_array($newStatus, ['LOP', 'EL', 'SL', 'CO'])) {
                $this->processLeaveTransition($employee, $date, $newStatus);
            }
            elseif ($newStatus === 'WO') {
                $this->processWOTransition($employee, $date);
            }
        }

        protected function updateAttendanceTimes($attendance, $in, $out, $date, $action = 'update')
        {
            if (empty($in) || $in === '00:00' || $in === '00:00:00') return;
            if (empty($out) || $out === '00:00' || $out === '00:00:00') return;

            if (strlen($in) == 5) $in .= ':00';
            if (strlen($out) == 5) $out .= ':00';

            // Only update if actually different
            if ($attendance->clock_in !== $in || $attendance->clock_out !== $out) {
                $attendance->clock_in = $in;
                $attendance->clock_out = $out;
                
                if ($action === 'create' || $action === 'update') {
                    $attendance->late = $this->calculateLateMark($in, $date, $attendance->employee_id);
                    $attendance->early_leaving = $this->calculateEarlyLeaving($out, $date, $attendance->employee_id);
                    $attendance->early_arrival = $this->calculateEarlyArrival($in, $date, $attendance->employee_id);
                }
                
                $attendance->save();
            }
        }

        protected function processPresentTransition($employee, $date, $in, $out, $startTime, $endTime, $statusCode)
        {
            LocalLeave::where('employee_id', $employee->id)
                ->where('start_date', '<=', $date)
                ->where('end_date', '>=', $date)
                ->delete();

            if (empty($in) || $in === '00:00' || $in === '00:00:00') $in = $startTime;
            if (empty($out) || $out === '00:00' || $out === '00:00:00') $out = $endTime;

            if (strlen($in) == 5) $in .= ':00';
            if (strlen($out) == 5) $out .= ':00';
            
            $status = 'Present';
            if ($statusCode === 'SP') $status = 'Single Punch In';

            $attendance = AttendanceEmployee::updateOrCreate(
                ['employee_id' => $employee->id, 'date' => $date],
                [
                    'status' => $status,
                    'clock_in' => $in,
                    'clock_out' => $out,
                    'total_rest' => '00:00:00',
                    'created_by' => \Auth::user()->creatorId(),
                ]
            );
            
            $this->updateAttendanceTimes($attendance, $in, $out, $date, 'create');
        }

        protected function processLeaveTransition($employee, $date, $code)
        {
            AttendanceEmployee::where('employee_id', $employee->id)->where('date', $date)->delete();

            $leaveTypeId = 6; 
            if ($code === 'EL') $leaveTypeId = 2;
            elseif ($code === 'SL') $leaveTypeId = 1;

            $exists = LocalLeave::where('employee_id', $employee->id)
                ->where('start_date', '<=', $date)
                ->where('end_date', '>=', $date)
                ->exists();

            if (!$exists) {
                LocalLeave::create([
                    'employee_id' => $employee->id,
                    'leave_type_id' => $leaveTypeId,
                    'applied_on' => date('Y-m-d'),
                    'start_date' => $date,
                    'end_date' => $date,
                    'total_leave_days' => 1,
                    'leave_duration_type' => 'full_day',
                    'leave_reason' => __('Imported via Excel'),
                    'status' => 'Approved',
                    'created_by' => \Auth::user()->creatorId(),
                ]);
            }
        }

        protected function processWOTransition($employee, $date)
        {
            AttendanceEmployee::where('employee_id', $employee->id)->where('date', $date)->delete();
            LocalLeave::where('employee_id', $employee->id)
                ->where('start_date', '<=', $date)
                ->where('end_date', '>=', $date)
                ->delete();
        }

        public function attendance(Request $request)
        {
            $isAjax = $request->ajax() || $request->wantsJson();

            // ================= EMPLOYEE =================
            $employeeId = Auth::user()->employee->id;
            $date = date('Y-m-d');
            $time = date('H:i:s');

            $attendance = AttendanceEmployee::where('employee_id', $employeeId)
                ->where('date', $date)
                ->first();

            // ================= PUNCH IN (Session 1) =================
            if (!$attendance) {
                $attendance = new AttendanceEmployee();
                $attendance->employee_id = $employeeId;
                $attendance->date = $date;
                $attendance->clock_in = $time; // Record time immediately
                $attendance->clock_out = '00:00:00';
                $attendance->status = AttendanceEmployee::STATUS_SINGLE_PUNCH;
                $attendance->late = $this->calculateLateMark($time, $date, $employeeId);
                $attendance->early_arrival = '00:00:00';
                $attendance->created_by = Auth::user()->id;

                $attendance->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Punch In successful.'
                ]);
            }

            // ================= PUNCH OUT (Session 1) =================
            if ($attendance->clock_out === '00:00:00') {
                // Check if employee has submitted daily report for this shift's date
                $hasProjectUpdate = \App\Models\ProjectDailyUpdate::where('employee_id', $attendance->employee_id)
                    ->where('work_date', $attendance->date)->exists();
                
                $hasGeneralTask = \App\Models\GeneralDailyTask::where('employee_id', $attendance->employee_id)
                    ->where('work_date', $attendance->date)->exists();

                if (!$hasProjectUpdate && !$hasGeneralTask) {
                    return response()->json([
                        'success' => false,
                        'message' => __('Please add update.') . ' <a href="' . route('projects.my_updates') . '" class="btn btn-sm btn-primary ms-2">' . __('Next') . '</a>'
                    ]);
                }

                $clockIn  = strtotime($attendance->clock_in);
                $clockOut = strtotime($time);
                $workedSeconds = max($clockOut - $clockIn, 0);

                $attendance->clock_out = $time; // Record time immediately
                $attendance->early_leaving = '00:00:00';

                // Calculate overtime
                $employee = \App\Models\Employee::find($attendance->employee_id);
                $shift = $employee->getShiftTimings($date);
                $startTime = \Carbon\Carbon::parse($date . ' ' . $shift['start_time']);
                $endTime = \Carbon\Carbon::parse($date . ' ' . $shift['end_time']);
                if ($endTime->lt($startTime)) {
                    $endTime->addDay();
                }
                $shiftSeconds = $endTime->diffInSeconds($startTime);
                $overtimeStartSeconds = $shiftSeconds + (30 * 60);

                $totalOvertimeSeconds = $workedSeconds - $overtimeStartSeconds;
                if ($totalOvertimeSeconds > 0) {
                    $attendance->overtime = gmdate('H:i:s', $totalOvertimeSeconds);
                } else {
                    $attendance->overtime = '00:00:00';
                }

                $workedHours = $workedSeconds / 3600;
                $statusData = $this->determineStatus($date, $workedHours, $employee->id, $attendance->clock_in);
                
                $attendance->status = $statusData['status'];
                $attendance->status_reason = $statusData['reason'];

                $attendance->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Punch Out successful.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Attendance already completed for today.'
            ], 400);

        }

    


        public function calendar(Request $request)
        {
            $this->resolvePastSinglePunches();
            if (\Auth::user()->can('Manage Attendance')) {
                $employees = [];
                $selectedEmployee = null;
                
                // Get terminated employee IDs
                $terminatedEmployeeIds = Termination::pluck('employee_id')->toArray();
                
                // Exclude terminated employees and non-employee users (Director, Hr) from the list
                $allEmployees = Employee::where('created_by', \Auth::user()->creatorId())
                    ->whereNotIn('id', $terminatedEmployeeIds)
                    ->whereHas('user', function($query) {
                        $query->where('type', 'employee');
                    })
                    ->get();

                // For employee users - automatically select their own record
                if (\Auth::user()->type == 'employee') {
                    $selectedEmployee = Employee::where('user_id', \Auth::user()->id)->first();
                    if ($selectedEmployee) {
                        $employees = [$selectedEmployee];
                    }
                } 
                // For company users - check if employee is selected
                else {
                    if ($request->has('employee_id') && $request->employee_id) {
                        $selectedEmployee = Employee::find($request->employee_id);
                        if ($selectedEmployee) {
                            $employees = [$selectedEmployee];
                        }
                    }
                }

                // Get current month and year
                $currentMonth = request()->input('month', date('m'));
                $currentYear = request()->input('year', date('Y'));

                $currentDate = \Carbon\Carbon::create($currentYear, $currentMonth, 1);
                $previousMonth = $currentDate->copy()->subMonth();
                $nextMonth = $currentDate->copy()->addMonth();

                $attendanceData = [];

                // Only process data if we have a selected employee
                if ($selectedEmployee) {
                    foreach ($employees as $employee) {
                        // Get all attendance records (no month filter)
                        $attendances = DB::table('attendance_employees')
                            ->where('employee_id', $employee->id)
                            ->get()
                            ->map(function ($item) {
                                $date = \Carbon\Carbon::parse($item->date)->format('Y-m-d');
                                return [
                                    'date' => $date,
                                    'status' => $item->status,
                                    'status_reason' => $item->status_reason ?? null,
                                    'clock_in' => $item->clock_in,
                                    'clock_out' => $item->clock_out,
                                    'late' => $item->late ?? '00:00:00',
                                    'early_leaving' => $item->early_leaving ?? '00:00:00'
                                ];
                            });

                        // Get all approved leaves (no month filter)
                        $leaves = LocalLeave::where('employee_id', $employee->id)
                            ->where('status', 'Approved')
                            ->with('leaveType')
                            ->get()
                            ->map(function ($item) {
                                return [
                                    'start_date' => \Carbon\Carbon::parse($item->start_date)->format('Y-m-d'),
                                    'end_date' => \Carbon\Carbon::parse($item->end_date)->format('Y-m-d'),
                                    'leave_reason' => $item->leave_reason,
                                    'leave_type' => $item->leaveType ? $item->leaveType->title : 'Unknown'
                                ];
                            });

                        $weekOffDay = strtolower('Sunday'); // e.g. 'sunday'

                        $employeeData = [];

                        // Mark from attendance records
                        foreach ($attendances as $attendance) {
                            $isSinglePunch = empty($attendance['clock_out']) || 
                                            $attendance['clock_out'] == '00:00:00' || 
                                            $attendance['clock_out'] == null;
                            
                            $type = 'present';
                            if ($isSinglePunch) {
                                $type = 'single_punch';
                            } elseif ($attendance['status'] === AttendanceEmployee::STATUS_HALF_DAY) {
                                $type = 'half_day';
                            }

                            $employeeData[$attendance['date']] = [
                                'type' => $type,
                                'clock_in' => $attendance['clock_in'],
                                'clock_out' => $attendance['clock_out'],
                                'late' => $attendance['late'],
                                'early_leaving' => $attendance['early_leaving'],
                                'status_reason' => $attendance['status_reason']
                            ];
                        }

                        // Mark 'leave' days
                        foreach ($leaves as $leave) {
                            $start = \Carbon\Carbon::parse($leave['start_date']);
                            $end = \Carbon\Carbon::parse($leave['end_date']);

                            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                                $formattedDate = $date->format('Y-m-d');

                                if (!isset($employeeData[$formattedDate])) {
                                    $employeeData[$formattedDate] = [
                                        'type' => 'leave',
                                        'reason' => $leave['leave_reason'],
                                        'leave_type' => $leave['leave_type']
                                    ];
                                }
                            }
                        }

                        // Fill in 'week_off' and 'absent' for all dates in the calendar view
                        // We'll process 3 months to ensure smooth navigation
                        $startRange = $currentDate->copy()->subMonth(); // Show previous month
                        $endRange = $currentDate->copy()->addMonth(); // Show next month

                        // Fetch holidays
                        $holidaysQuery = \App\Models\Holiday::whereBetween('start_date', [$startRange->format('Y-m-d'), $endRange->format('Y-m-d')])
                            ->orWhereBetween('end_date', [$startRange->format('Y-m-d'), $endRange->format('Y-m-d')])
                            ->get();
                        
                        $holidays = [];
                        foreach ($holidaysQuery as $holiday) {
                            $hStart = \Carbon\Carbon::parse($holiday->start_date);
                            $hEnd = \Carbon\Carbon::parse($holiday->end_date);
                            for ($d = $hStart->copy(); $d->lte($hEnd); $d->addDay()) {
                                $holidays[] = $d->format('Y-m-d');
                            }
                        }
                        for ($date = $startRange->copy(); $date->lte($endRange); $date->addDay()) {
                            $dateFormatted = $date->format('Y-m-d');
                            $dayName = strtolower($date->format('l')); // e.g. 'sunday'

                            if (!isset($employeeData[$dateFormatted])) {
                                if (in_array($dateFormatted, $holidays)) {
                                    $employeeData[$dateFormatted] = ['type' => 'Holiday'];
                                } elseif ($weekOffDay && $dayName === $weekOffDay) {
                                    $employeeData[$dateFormatted] = ['type' => 'week_off'];
                                } elseif (!$weekOffDay && $dayName === 'sunday') {
                                    $employeeData[$dateFormatted] = ['type' => 'week_off'];
                                } elseif ($date->lte(\Carbon\Carbon::today())) {
                                    $employeeData[$dateFormatted] = ['type' => 'absent'];
                                }
                                // else: future dates remain unmarked
                            }
                        }

                        // Sort data by date
                        ksort($employeeData);

                        $attendanceData[$employee->id] = [
                            'name' => $employee->full_name,
                            'data' => $employeeData
                        ];
                    }
                }

                return view('attendance.calendar', [
                    'attendanceData' => $attendanceData,
                    'currentMonth' => $currentMonth,
                    'currentYear' => $currentYear,
                    'previousMonth' => $previousMonth->format('m'),
                    'previousYear' => $previousMonth->format('Y'),
                    'nextMonth' => $nextMonth->format('m'),
                    'nextYear' => $nextMonth->format('Y'),
                    'allEmployees' => $allEmployees,
                    'selectedEmployee' => $selectedEmployee
                ]);
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }

        public function export(Request $request)
        {
            $this->resolvePastSinglePunches();
            if (\Auth::user()->can('Manage Attendance')) {
                // Get the same filtered data as the index method
                if (\Auth::user()->type == 'employee') {
                    $emp = !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0;
                    $query = AttendanceEmployee::where('employee_id', $emp);
                    $baseEmployeeIds = collect([$emp])->filter();
                } else {
                    $employee = Employee::select('id')->where('created_by', \Auth::user()->creatorId())
                        ->whereHas('user', function($query) {
                            $query->where('type', 'employee');
                        });
                    
                    if (!empty($request->branch)) {
                        $employee->where('branch_id', $request->branch);
                    }

                    if (!empty($request->department)) {
                        $employee->where('department_id', $request->department);
                    }

                    if (!empty($request->employee)) {
                        $employee->where('id', $request->employee);
                    }

                    $baseEmployeeIds = $employee->get()->pluck('id');
                    $query = AttendanceEmployee::whereIn('employee_id', $baseEmployeeIds);
                }
                
                // Apply date filters
                if ($request->type == 'monthly' && !empty($request->month)) {
                    $month = date('m', strtotime($request->month));
                    $year = date('Y', strtotime($request->month));
                    $start_date = date($year . '-' . $month . '-01');
                    $end_date = date('Y-m-t', strtotime('01-' . $month . '-' . $year));
                } else {
                    $month = date('m');
                    $year = date('Y');
                    $start_date = date($year . '-' . $month . '-01');
                    $end_date = date('Y-m-t', strtotime('01-' . $month . '-' . $year));
                }

                // IMPORTANT: Do not show/fill future dates for the current month.
                // Use company timezone from settings so "today" matches the business timezone.
                $tz = \App\Models\Utility::getValByName('timezone') ?: config('app.timezone');
                $today = \Carbon\Carbon::now($tz)->startOfDay();
                $startCarbon = \Carbon\Carbon::parse($start_date, $tz)->startOfDay();
                $endCarbon = \Carbon\Carbon::parse($end_date, $tz)->startOfDay();
                if ($startCarbon->lte($today) && $endCarbon->gt($today)) {
                    $end_date = $today->format('Y-m-d');
                }
                
                $query->whereBetween('date', [$start_date, $end_date]);

                $attendances = $query->orderBy('date', 'asc')
                                    ->orderBy('clock_in', 'asc')
                                    ->get();

                // Get all dates in the selected period
                $dates = [];
                $current = \Carbon\Carbon::parse($start_date);
                $end = \Carbon\Carbon::parse($end_date);
                
                while ($current <= $end) {
                    $dates[] = $current->format('Y-m-d');
                    $current->addDay();
                }

                $holidaysQuery = \App\Models\Holiday::whereBetween('start_date', [$start_date, $end_date])
                    ->orWhereBetween('end_date', [$start_date, $end_date])
                    ->get();
                $holidays = [];
                foreach ($holidaysQuery as $holiday) {
                    $hStart = \Carbon\Carbon::parse($holiday->start_date);
                    $hEnd = \Carbon\Carbon::parse($holiday->end_date);
                    for ($d = $hStart->copy(); $d->lte($hEnd); $d->addDay()) {
                        $holidays[] = $d->format('Y-m-d');
                    }
                }

                // Get all employees in the export set:
                // - employees that have attendance rows in range
                // - PLUS employees that have approved leave in range (even if no attendance rows)
                $attendanceEmployeeIds = $attendances->pluck('employee_id')->unique()->values();

                $leavesForEmployeeList = LocalLeave::query()
                    ->whereIn('employee_id', $baseEmployeeIds)
                    ->where('status', 'Approved')
                    ->where(function ($q) use ($start_date, $end_date) {
                        $q->whereBetween('start_date', [$start_date, $end_date])
                        ->orWhereBetween('end_date', [$start_date, $end_date])
                        ->orWhere(function ($q2) use ($start_date, $end_date) {
                            $q2->where('start_date', '<=', $start_date)
                                ->where('end_date', '>=', $end_date);
                        });
                    })
                    ->pluck('employee_id')
                    ->unique()
                    ->values();

                $employeeIds = $attendanceEmployeeIds->merge($leavesForEmployeeList)->unique()->values();

                $employees = Employee::whereIn('id', $employeeIds)
                                    ->with('user')
                                    ->get();

                // Group attendance by employee and date
                $attendanceData = [];
                foreach ($attendances as $attendance) {
                    $lateTime = $this->calculateLateMark($attendance->clock_in, $attendance->date, $attendance->employee_id);
                    $earlyLeavingTime = $this->calculateEarlyLeaving($attendance->clock_out, $attendance->date, $attendance->employee_id);
                    
                    $clockOut = $attendance->clock_out;
                    $status = $attendance->status;
                    // Past single punches are now natively resolved in the database
                    $attendanceData[$attendance->employee_id][$attendance->date] = [
                        'status' => $status,
                        'clock_in' => $attendance->clock_in,
                        'clock_out' => $clockOut,
                        'total' => $this->calculateWorkedHours($attendance->clock_in, $clockOut),
                        'late' => $lateTime,
                        'early_leaving' => $earlyLeavingTime,
                        'early_arrival' => $attendance->early_arrival ?? $this->calculateEarlyArrival($attendance->clock_in, $attendance->date, $attendance->employee_id)
                    ];
                }

                // Build leave/comp-off codes by employee+date for export status mapping
                // Codes requested:
                // Present -> P, Absent/Leave Without Pay -> LWP, Single Punch -> SP,
                // Leave full day -> EL, Leave half day -> SL, Casual Leave -> LOP, Comp-Off -> CO, Weekly Off -> WO
                $leaveCodes = [];
                $codePriority = ['LWP' => 3, 'SL' => 2, 'EL' => 1, 'LOP' => 1];

                $leaves = LocalLeave::query()
                    ->with('leaveType')
                    ->whereIn('employee_id', $employeeIds)
                    ->where('status', 'Approved')
                    ->where(function ($q) use ($start_date, $end_date) {
                        $q->whereBetween('start_date', [$start_date, $end_date])
                        ->orWhereBetween('end_date', [$start_date, $end_date])
                        ->orWhere(function ($q2) use ($start_date, $end_date) {
                            $q2->where('start_date', '<=', $start_date)
                                ->where('end_date', '>=', $end_date);
                        });
                    })
                    ->get();

                foreach ($leaves as $leave) {
                    $empId = (int) $leave->employee_id;
                    $typeTitle = strtolower(trim(optional($leave->leaveType)->title ?? ''));
                    $duration = strtolower(trim($leave->leave_duration_type ?? 'full_day'));

                    $code = $this->getLeaveCode($leave);

                    $leaveStart = \Carbon\Carbon::parse($leave->start_date);
                    $leaveEnd = \Carbon\Carbon::parse($leave->end_date);
                    $periodStart = $leaveStart->lt(\Carbon\Carbon::parse($start_date)) ? \Carbon\Carbon::parse($start_date) : $leaveStart;
                    $periodEnd = $leaveEnd->gt(\Carbon\Carbon::parse($end_date)) ? \Carbon\Carbon::parse($end_date) : $leaveEnd;

                    for ($d = $periodStart->copy(); $d->lte($periodEnd); $d->addDay()) {
                        $dateKey = $d->toDateString();
                        $existing = $leaveCodes[$empId][$dateKey] ?? null;
                        if (!$existing) {
                            $leaveCodes[$empId][$dateKey] = $code;
                            continue;
                        }
                        if (($codePriority[$code] ?? 0) > ($codePriority[$existing] ?? 0)) {
                            $leaveCodes[$empId][$dateKey] = $code;
                        }
                    }
                }

                // Final status codes per employee+date for the Excel export
                $statusCodes = [];
                foreach ($employees as $emp) {
                    $empId = (int) $emp->id;
                    $weekOff = strtolower(trim((string) ($emp->week_off_day ?? '')));

                    foreach ($dates as $date) {
                        // Never mark future dates in export (extra safety; dates are already clamped)
                        if (\Carbon\Carbon::parse($date, $tz)->startOfDay()->gt($today)) {
                            $statusCodes[$empId][$date] = '';
                            continue;
                        }

                        $dayName = strtolower(\Carbon\Carbon::parse($date)->format('l')); // monday, tuesday...
                        $isWeekOff = $weekOff !== ''
                            ? ($dayName === strtolower($weekOff))
                            : ($dayName === 'sunday');

                        $att = $attendanceData[$empId][$date] ?? null;
                        $leaveCode = $leaveCodes[$empId][$date] ?? null;

                        $clockIn = $att['clock_in'] ?? null;
                        $clockOut = $att['clock_out'] ?? null;
                        $hasPunch = !empty($clockIn) && $clockIn !== '00:00:00';
                        $isToday = \Carbon\Carbon::parse($date, $tz)->isSameDay($today);

                        // Priority order: Punched (P/LM/SP) > WO > Other leaves (EL, CO, LOP) > Absent
                        
                        // Priority 1: If employee actually punched
                        if ($hasPunch) {
                            $status = $att['status'] ?? null;
                            if ($status === \App\Models\AttendanceEmployee::STATUS_EARLY_CLOCK_OUT) {
                                $statusCodes[$empId][$date] = 'ECO';
                            } elseif ($status === \App\Models\AttendanceEmployee::STATUS_HALF_DAY) {
                                $statusCodes[$empId][$date] = 'HD';
                            } else {
                                if (empty($clockOut) || $clockOut === '00:00:00') {
                                    // Rule: Same day -> Single Punch. Next day onwards -> Half Day
                                    $recordDate = \Carbon\Carbon::parse($date)->startOfDay();
                                    $todayDate = \Carbon\Carbon::today();
                                    
                                    if ($recordDate->lt($todayDate)) {
                                        $statusCodes[$empId][$date] = 'HD';
                                    } else {
                                        $statusCodes[$empId][$date] = ($leaveCode === 'SL') ? 'SL' : 'SP';
                                    }
                                } else {
                                    $statusCodes[$empId][$date] = ($leaveCode === 'SL') ? 'SL' : 'P';
                                }
                            }
                            continue;
                        }

                        // Priority 2: Check for Week Off if no punch
                        if ($isWeekOff) {
                            $statusCodes[$empId][$date] = 'WO';
                            continue;
                        }

                        // Priority 3: Check for leave codes (EL, CO, LOP, SL)
                        if (!empty($leaveCode)) {
                            $statusCodes[$empId][$date] = $leaveCode;
                            continue;
                        }

                        // Priority 4: Holiday
                        if (in_array($date, $holidays)) {
                            $statusCodes[$empId][$date] = 'H';
                            continue;
                        }

                        // For TODAY only: no punch and no leave yet
                        if ($isToday) {
                            $statusCodes[$empId][$date] = '';
                            continue;
                        }

                        // Default absent
                        $statusCodes[$empId][$date] = 'A';
                    }
                }

                // Collect leave details for each employee (EL, SL, CO)
                // This will be used to display leaves in the Excel export
                $leaveDetails = [];
                foreach ($leaves as $leave) {
                    $empId = (int) $leave->employee_id;
                    $typeTitle = strtolower(trim(optional($leave->leaveType)->title ?? ''));
                    $duration = strtolower(trim($leave->leave_duration_type ?? 'full_day'));

                    $code = $this->getLeaveCode($leave);

                    // Only collect paid leaves (exclude LOP/LWP)
                    if ($code !== 'LOP' && $code !== 'LWP') {
                        $leaveStart = \Carbon\Carbon::parse($leave->start_date);
                        $leaveEnd = \Carbon\Carbon::parse($leave->end_date);
                        $periodStart = $leaveStart->lt(\Carbon\Carbon::parse($start_date)) ? \Carbon\Carbon::parse($start_date) : $leaveStart;
                        $periodEnd = $leaveEnd->gt(\Carbon\Carbon::parse($end_date)) ? \Carbon\Carbon::parse($end_date) : $leaveEnd;

                        // Find employee to check week off
                        $employee = $employees->firstWhere('id', $empId);
                        $weekOff = $employee ? strtolower(trim((string) ('Sunday' ?? ''))) : '';

                        for ($d = $periodStart->copy(); $d->lte($periodEnd); $d->addDay()) {
                            $dateKey = $d->toDateString();
                            
                            // Check if this date is a Week Off (WO takes priority, so don't show leave in leaves table)
                            $dayName = strtolower($d->format('l'));
                            $isWeekOff = ($weekOff === $dayName);
                            
                            // Only add if not a Week Off day (WO days are shown in status, not in leaves section)
                            if (!$isWeekOff) {
                                if (!isset($leaveDetails[$empId][$code])) {
                                    $leaveDetails[$empId][$code] = [];
                                }
                                $leaveDetails[$empId][$code][] = $dateKey;
                            }
                        }
                    }
                }

                // Calculate payable days totals for each employee
                $payableDaysTotals = [];
                foreach ($employees as $employee) {
                    $empId = (int) $employee->id;
                    $presentDays = 0;
                    $ecoDays = 0;
                    $hdDays = 0;
                    $lwpDays = 0;
                    $elDays = 0;
                    $slDays = 0;
                    $clDays = 0;
                    $coDays = 0;
                    $woDays = 0;
                    $absentDays = 0;
                    $totalLeaves = 0;
                    
                    if (isset($statusCodes[$empId])) {
                        foreach ($statusCodes[$empId] as $date => $code) {
                            if (empty($code)) {
                                continue;
                            }
                            
                            switch ($code) {
                                case 'P':
                                    $presentDays += 1;
                                    break;
                                case 'H':
                                    $presentDays += 1;
                                    break;
                                case 'SP':
                                    // Single punch tracked separately, not counted in Present
                                    break;
                                case 'ECO':
                                    $ecoDays++;
                                    break;
                                case 'HD':
                                    $hdDays++;
                                    break;
                                case 'A':
                                    $absentDays++;
                                    break;
                                case 'LOP':
                                case 'LWP':
                                    $lwpDays++;
                                    break;
                                case 'WO':
                                    $woDays++;
                                    break;
                                default:
                                    $totalLeaves++;
                                    if ($code === 'EL') {
                                        $elDays++;
                                    } elseif ($code === 'SL') {
                                        $slDays++;
                                    } elseif ($code === 'CL') {
                                        $clDays++;
                                    } elseif ($code === 'CO') {
                                        $coDays++;
                                    }
                                    break;
                            }
                        }
                    }
                    
                    // Total Payable Days = Present + WO + Leaves + (Half Days / 2) + ECO
                    $totalPayableDays = $presentDays + $woDays + $totalLeaves + ($hdDays / 2) + $ecoDays;
                    
                    $payableDaysTotals[$empId] = [
                        'present' => $presentDays,
                        'eco' => $ecoDays,
                        'hd' => $hdDays,
                        'lwp' => $lwpDays,
                        'wo' => $woDays,
                        'el' => $elDays,
                        'sl' => $slDays,
                        'cl' => $clDays,
                        'co' => $coDays,
                        'total_leaves' => $totalLeaves,
                        'total' => $totalPayableDays
                    ];
                }

                // Calculate single employee specific data if needed
                $isSingleEmployee = (count($employees) === 1);
                
                $totalWorkingDays = 0;
                $totalHoursFormatted = '00:00';
                $requiredHoursFormatted = '00:00';
                $extraShortHours = '00:00';
                
                if ($isSingleEmployee) {
                    $employee = $employees->first();
                    $empId = $employee->id;
                    $totalWorkedMinutes = 0;
                    $totalRequiredMinutes = 0;
                    
                    foreach ($dates as $date) {
                        $dayOfWeek = \Carbon\Carbon::parse($date)->dayOfWeek;
                        // Skip Sundays
                        if ($dayOfWeek == \Carbon\Carbon::SUNDAY) continue;
                        
                        $totalWorkingDays++;
                        $requiredMinutes = ($dayOfWeek == \Carbon\Carbon::SATURDAY) ? 270 : 480;
                        $totalRequiredMinutes += $requiredMinutes;
                        
                        $att = $attendanceData[$empId][$date] ?? null;
                        if ($att && !empty($att['clock_in']) && $att['clock_in'] !== '00:00:00' && !empty($att['clock_out']) && $att['clock_out'] !== '00:00:00') {
                            $inParts = explode(':', $att['clock_in']);
                            $outParts = explode(':', $att['clock_out']);
                            
                            $inMins = ((int)$inParts[0] * 60) + (int)$inParts[1];
                            $outMins = ((int)$outParts[0] * 60) + (int)$outParts[1];
                            
                            $diff = $outMins - $inMins;
                            if ($diff < 0) {
                                $diff = (24 * 60) - $inMins + $outMins;
                            }
                            $totalWorkedMinutes += $diff;
                        }
                    }
                    
                    $totalHoursFormatted = sprintf("%02d:%02d", floor($totalWorkedMinutes / 60), $totalWorkedMinutes % 60);
                    $requiredHoursFormatted = sprintf("%02d:%02d", floor($totalRequiredMinutes / 60), $totalRequiredMinutes % 60);
                    
                    $diffMins = $totalWorkedMinutes - $totalRequiredMinutes;
                    if ($diffMins >= 0) {
                        $extraShortHours = '+' . sprintf("%02d:%02d", floor($diffMins / 60), $diffMins % 60);
                    } else {
                        $diffMins = abs($diffMins);
                        $extraShortHours = '-' . sprintf("%02d:%02d", floor($diffMins / 60), $diffMins % 60);
                    }
                }

                // Generate Excel file
                $fileName = 'attendance_' . date('Y-m-d') . '.xlsx';
                
                return \Excel::download(new class($dates, $employees, $attendanceData, $start_date, $end_date, $statusCodes, $payableDaysTotals, $leaveDetails, $isSingleEmployee, $totalWorkingDays, $totalHoursFormatted, $requiredHoursFormatted, $extraShortHours) implements \Maatwebsite\Excel\Concerns\FromView, \Maatwebsite\Excel\Concerns\WithStyles {
                    private $dates;
                    private $employees;
                    private $attendanceData;
                    private $start_date;
                    private $end_date;
                    private $statusCodes;
                    private $payableDaysTotals;
                    private $leaveDetails;
                    private $isSingleEmployee;
                    private $totalWorkingDays;
                    private $totalHoursFormatted;
                    private $requiredHoursFormatted;
                    private $extraShortHours;

                    public function __construct($dates, $employees, $attendanceData, $start_date, $end_date, $statusCodes, $payableDaysTotals, $leaveDetails, $isSingleEmployee, $totalWorkingDays, $totalHoursFormatted, $requiredHoursFormatted, $extraShortHours)
                    {
                        $this->dates = $dates;
                        $this->employees = $employees;
                        $this->attendanceData = $attendanceData;
                        $this->start_date = $start_date;
                        $this->end_date = $end_date;
                        $this->statusCodes = $statusCodes;
                        $this->payableDaysTotals = $payableDaysTotals;
                        $this->leaveDetails = $leaveDetails;
                        $this->isSingleEmployee = $isSingleEmployee;
                        $this->totalWorkingDays = $totalWorkingDays;
                        $this->totalHoursFormatted = $totalHoursFormatted;
                        $this->requiredHoursFormatted = $requiredHoursFormatted;
                        $this->extraShortHours = $extraShortHours;
                    }

                    public function view(): \Illuminate\View\View
                    {
                        $viewName = $this->isSingleEmployee ? 'attendance.export_employee' : 'attendance.export';
                        return view($viewName, [
                            'dates' => $this->dates,
                            'employees' => $this->employees,
                            'attendanceData' => $this->attendanceData,
                            'statusCodes' => $this->statusCodes,
                            'start_date' => $this->start_date,
                            'end_date' => $this->end_date,
                            'payableDaysTotals' => $this->payableDaysTotals,
                            'leaveDetails' => $this->leaveDetails,
                            'totalWorkingDays' => $this->totalWorkingDays,
                            'totalHoursFormatted' => $this->totalHoursFormatted,
                            'requiredHoursFormatted' => $this->requiredHoursFormatted,
                            'extraShortHours' => $this->extraShortHours
                        ]);
                    }

                    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
                    {
                        // Apply borders to all cells
                        // Main table columns (dates + 1 for label column)
                        $mainTableColumns = count($this->dates) + 1;
                        // Summary table is separate, so we need to account for both tables
                        // Estimate: main table + spacing + summary table (9 columns)
                        $lastColumn = $mainTableColumns + 9;
                        $lastRow = (count($this->employees) * 6) + 5;
                        
                        $sheet->getStyle('A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColumn) . $lastRow)
                            ->getBorders()
                            ->getAllBorders()
                            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                        
                        // Center align all cells
                        $sheet->getStyle('A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColumn) . $lastRow)
                            ->getAlignment()
                            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    }
                }, $fileName);
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }

        protected function getLeaveStatus($employeeId, $date)
        {
            $leave = LocalLeave::with('leaveType')
                ->where('employee_id', $employeeId)
                ->where('start_date', '<=', $date)
                ->where('end_date', '>=', $date)
                ->where('status', 'Approved')
                ->first();

            if (!$leave) return null;

            return $this->getLeaveCode($leave);
        }

        private function getLeaveCode($leave)
        {
            if (!$leave || !$leave->leaveType) {
                return 'EL';
            }
            $lt = $leave->leaveType;
            $typeTitle = strtolower(trim($lt->title));
            if ($lt->unlimited) {
                if ($typeTitle === 'casual leave' || str_contains($typeTitle, 'casual')) {
                    return 'LOP';
                }
                return 'LWP';
            }
            if ($typeTitle === 'sick leave' || str_contains($typeTitle, 'sick')) return 'SL';
            if ($typeTitle === 'earned leave' || str_contains($typeTitle, 'earned')) return 'EL';
            
            $words = explode(' ', preg_replace('/[^a-zA-Z0-9\s]/', '', $lt->title));
            $initials = '';
            foreach ($words as $word) {
                if (!empty($word)) {
                    $initials .= strtoupper($word[0]);
                }
            }
            return !empty($initials) ? substr($initials, 0, 3) : 'EL';
        }

        private function calculateWorkedHours($clockIn, $clockOut)
        {
            if ($clockIn == '00:00:00' || $clockOut == '00:00:00') {
                return '00:00';
            }
            
            $start = \Carbon\Carbon::parse($clockIn);
            $end = \Carbon\Carbon::parse($clockOut);
            
            $diff = $start->diff($end);
            
            return sprintf('%02d:%02d', $diff->h, $diff->i);
        }

        /**
         * Calculate worked hours as decimal (for comparison with REQUIRED_WORKING_HOURS)
         */
        private function calculateWorkedHoursDecimal($clockIn, $clockOut)
        {
            if ($clockIn == '00:00:00' || $clockOut == '00:00:00') {
                return 0;
            }
            
            // Parse times - assuming same day
            $start = \Carbon\Carbon::parse($clockIn);
            $end = \Carbon\Carbon::parse($clockOut);
            
            // If end time is earlier than start (crossed midnight), add 24 hours
            if ($end->lt($start)) {
                $end->addDay();
            }
            
            $workedSeconds = $end->diffInSeconds($start);
            $workedHours = $workedSeconds / 3600;
            
            return $workedHours;
        }


        protected function calculateAttendanceStatus($clockIn, $clockOut, $date)
        {
            // If no clock in at all, return Absent
            if (empty($clockIn) || $clockIn == '00:00:00') {
                return 'Absent';
            }
            
            // If clocked in but not out, return Single Punch In
            if (empty($clockOut) || $clockOut == '00:00:00') {
                return 'Single Punch In';
            }
            
            // Calculate total worked time
            $start = \Carbon\Carbon::parse($date . ' ' . $clockIn);
            $end = \Carbon\Carbon::parse($date . ' ' . $clockOut);
            $totalMinutes = $end->diffInMinutes($start);
            
            // Determine status based on worked hours
            if ($totalMinutes >= 480) { // 8 hours = 480 minutes
                return 'Present';
            } elseif ($totalMinutes >= 300) { // 5 hours = 300 minutes
                return 'Early Clock-Out';
            } else {
                return 'Half Day';
            }
        }

        /**
         * Calculate late mark based on office timings with 10 min grace period
         * 
         * @param string $clockIn Time in H:i:s format
         * @param string $date Date in Y-m-d format
         * @param int|null $employeeId Employee ID
         * @return string Late duration in H:i:s format (00:00:00 if not late)
         */
        protected function calculateLateMark($clockIn, $date, $employeeId = null)
        {
            if (!$employeeId) {
                $employeeId = \Auth::user()->employee->id ?? null;
            }
            if (!$employeeId || empty($clockIn) || $clockIn == '00:00:00') return '00:00:00';

            $employee = \App\Models\Employee::find($employeeId);
            if (!$employee) return '00:00:00';

            $shift = $employee->getShiftTimings($date);
            $startTime = \Carbon\Carbon::parse($date . ' ' . $shift['start_time']);
            $clockInTime = \Carbon\Carbon::parse($date . ' ' . $clockIn);

            $graceTime = $startTime->copy()->addMinutes(10);

            if ($clockInTime->gt($graceTime)) {
                $lateSeconds = $clockInTime->diffInSeconds($startTime);
                return gmdate('H:i:s', $lateSeconds);
            }

            return '00:00:00';
        }

        /**
         * Calculate early leaving based on office timings
         * 
         * @param string $clockOut Time in H:i:s format
         * @param string $date Date in Y-m-d format
         * @param int|null $employeeId Employee ID
         * @return string Early leaving duration in H:i:s format (00:00:00 if not early)
         */
        protected function calculateEarlyLeaving($clockOut, $date, $employeeId = null)
        {
            if (!$employeeId) {
                $employeeId = \Auth::user()->employee->id ?? null;
            }
            if (!$employeeId || empty($clockOut) || $clockOut == '00:00:00') return '00:00:00';

            $employee = \App\Models\Employee::find($employeeId);
            if (!$employee) return '00:00:00';

            $shift = $employee->getShiftTimings($date);
            $endTime = \Carbon\Carbon::parse($date . ' ' . $shift['end_time']);
            $clockOutTime = \Carbon\Carbon::parse($date . ' ' . $clockOut);

            $toleranceTime = $endTime->copy()->subMinutes(10);

            if ($clockOutTime->lt($toleranceTime)) {
                $earlySeconds = $endTime->diffInSeconds($clockOutTime);
                return gmdate('H:i:s', $earlySeconds);
            }

            return '00:00:00';
        }

        /**
         * Calculate early arrival based on office timings
         * 
         * @param string $clockIn Time in H:i:s format
         * @param string $date Date in Y-m-d format
         * @param int|null $employeeId Employee ID
         * @return string Early arrival duration in H:i:s format (00:00:00 if not early)
         */
        protected function calculateEarlyArrival($clockIn, $date, $employeeId = null)
        {
            if (!$employeeId) {
                $employeeId = \Auth::user()->employee->id ?? null;
            }
            if (!$employeeId || empty($clockIn) || $clockIn == '00:00:00') return '00:00:00';

            $employee = \App\Models\Employee::find($employeeId);
            if (!$employee) return '00:00:00';

            $shift = $employee->getShiftTimings($date);
            $startTime = \Carbon\Carbon::parse($date . ' ' . $shift['start_time']);
            $clockInTime = \Carbon\Carbon::parse($date . ' ' . $clockIn);

            $toleranceTime = $startTime->copy()->subMinutes(10);

            if ($clockInTime->lt($toleranceTime)) {
                $earlySeconds = $startTime->diffInSeconds($clockInTime);
                return gmdate('H:i:s', $earlySeconds);
            }

            return '00:00:00';
        }

        /**
         * Resolves any past single punches to Half Day by adding the appropriate hours
         */
        protected function resolvePastSinglePunches()
        {
            $pastPunches = AttendanceEmployee::where('date', '<', \Carbon\Carbon::today()->format('Y-m-d'))
                ->where(function($q) {
                    $q->where('status', AttendanceEmployee::STATUS_SINGLE_PUNCH)
                      ->orWhere(function($q2) {
                          $q2->whereNull('clock_out')->orWhere('clock_out', '00:00:00');
                      });
                })
                ->whereNotNull('clock_in')->where('clock_in', '!=', '00:00:00')
                ->get();
                
            foreach ($pastPunches as $attendance) {
                $date = $attendance->date;
                $dayOfWeek = \Carbon\Carbon::parse($date)->dayOfWeek;
                $hoursToAdd = ($dayOfWeek == \Carbon\Carbon::SATURDAY) ? 3 : 5;
                $clockOut = \Carbon\Carbon::parse($date . ' ' . $attendance->clock_in)->addHours($hoursToAdd)->format('H:i:s');
                
                $attendance->clock_out = $clockOut;
                $attendance->status = AttendanceEmployee::STATUS_HALF_DAY;
                $attendance->save();
            }
        }

        /**
         * Determine attendance status based on date and worked hours.
         */
        public function determineStatus($date, $workedHours, $employeeId = null, $clockInTime = null)
        {
            $status = AttendanceEmployee::STATUS_ABSENT;
            $reason = null;

            $isSaturday = \Carbon\Carbon::parse($date)->dayOfWeek === \Carbon\Carbon::SATURDAY;
            $requiredHours = $isSaturday ? AttendanceEmployee::SATURDAY_REQUIRED_WORKING_HOURS : AttendanceEmployee::REQUIRED_WORKING_HOURS;
            $earlyLeavingHours = $isSaturday ? AttendanceEmployee::SATURDAY_EARLY_LEAVING_HOURS : AttendanceEmployee::EARLY_LEAVING_HOURS;

            if ($workedHours >= $requiredHours) {
                $status = AttendanceEmployee::STATUS_PRESENT;
            } elseif ($workedHours >= $earlyLeavingHours) {
                $status = AttendanceEmployee::STATUS_EARLY_CLOCK_OUT;
            } elseif ($workedHours > 0) {
                $status = AttendanceEmployee::STATUS_HALF_DAY;
            }

            if ($employeeId && $clockInTime && $status != AttendanceEmployee::STATUS_ABSENT) {
                $isLate = false;
                $lateDuration = $this->calculateLateMark($clockInTime, $date, $employeeId);
                if ($lateDuration !== '00:00:00') {
                    $isLate = true;
                }

                $employee = \App\Models\Employee::find($employeeId);
                if ($employee) {
                    $shift = $employee->getShiftTimings($date);
                    $startTime = \Carbon\Carbon::parse($date . ' ' . $shift['start_time']);
                    $actualClockIn = \Carbon\Carbon::parse($date . ' ' . $clockInTime);
                    
                    // Rule 1: Punch in time >= 5 hours after shift start = Half Day
                    if ($actualClockIn->diffInMinutes($startTime) >= 300 && $actualClockIn->gt($startTime)) {
                        return ['status' => AttendanceEmployee::STATUS_HALF_DAY, 'reason' => 'Punch-In after 5 Hours'];
                    }
                }

                // Rule 2: 4th Late Mark or more = Half Day
                if ($isLate) {
                    $startOfMonth = \Carbon\Carbon::parse($date)->startOfMonth()->format('Y-m-d');
                    $endOfMonth = \Carbon\Carbon::parse($date)->endOfMonth()->format('Y-m-d');

                    // Count late marks prior to this date in the same month
                    $lateMarksCount = AttendanceEmployee::where('employee_id', $employeeId)
                        ->whereBetween('date', [$startOfMonth, $endOfMonth])
                        ->where('date', '<', $date)
                        ->where('late', '!=', '00:00:00')
                        ->count();
                    
                    // The current punch is also a late mark, so the total will be count + 1
                    $totalLates = $lateMarksCount + 1;

                    if ($totalLates >= 4) {
                        $reasonText = $totalLates . 'th Late Mark';
                        if ($totalLates == 4) $reasonText = '4th Late Mark';
                        elseif ($totalLates == 5) $reasonText = '5th Late Mark';
                        
                        return ['status' => AttendanceEmployee::STATUS_HALF_DAY, 'reason' => $reasonText];
                    }
                }
            }

            return ['status' => $status, 'reason' => $reason];
        }

    public function report(\Illuminate\Http\Request $request)
    {
        if (\Auth::user()->can('Manage Attendance') || \Auth::user()->type == 'employee') {
            if (\Auth::user()->type == 'employee') {
                $emp = Employee::where('user_id', \Auth::user()->id)->first();
                $employees = $emp ? [$emp->id => $emp->name] : [];
            } else {
                $terminatedEmployeeIds = Termination::pluck('employee_id')->toArray();
                $employees = Employee::where('created_by', \Auth::user()->creatorId())
                    ->whereNotIn('id', $terminatedEmployeeIds)
                    ->whereHas('user', function($query) {
                        $query->where('type', 'employee');
                    })
                    ->get()
                    ->pluck('name', 'id');
                $employees->prepend('Select Employee', '');
            }

            return view('attendance.report', compact('employees'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function attendanceOverview(\Illuminate\Http\Request $request)
    {
        try {
            $employeeId = $request->employee_id ?? (\Auth::user()->employee->id ?? 0);
            $filterType = $request->filter_type ?? 'today';
    
            if (!$employeeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found'
                ]);
            }
    
            $data = [];
    
            if ($filterType === 'today' || $filterType === 'date') {
                if ($filterType === 'today') {
                    $date = \Carbon\Carbon::today()->format('Y-m-d');
                } else {
                    $requestDate = $request->input('date');
                    if (empty($requestDate)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Date is required',
                            'debug' => 'No date parameter received'
                        ]);
                    }
                    try {
                        $date = \Carbon\Carbon::parse($requestDate)->format('Y-m-d');
                    } catch (\Exception $e) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Invalid date format: ' . $e->getMessage()
                        ]);
                    }
                }
                
                $startDate = \Carbon\Carbon::parse($date)->startOfDay();
                $endDate = \Carbon\Carbon::parse($date)->endOfDay();
    
                $attendance = \App\Models\AttendanceEmployee::where('employee_id', $employeeId)
                    ->where('date', $date)
                    ->first();
    
                if ($attendance) {
                    $clockIn = null;
                    $clockOut = null;
                    $isLate = false;
                    
                    if ($attendance->clock_in && $attendance->clock_in != '00:00:00') {
                        $clockIn = \Carbon\Carbon::parse($attendance->date . ' ' . $attendance->clock_in)->format('h:i A');
                        
                        $employee = \App\Models\Employee::find($employeeId);
                        if ($employee) {
                            $shift = $employee->getShiftTimings($attendance->date);
                            $expectedStartTime = \Carbon\Carbon::parse($attendance->date . ' ' . $shift['start_time']);
                            $graceTime = $expectedStartTime->copy()->addMinutes(10);
                            $clockInTime = \Carbon\Carbon::parse($attendance->date . ' ' . $attendance->clock_in);
                            $isLate = $clockInTime->gt($graceTime);
                        } else {
                            $companyStartTime = \App\Models\Utility::getValByName('company_start_time');
                            if ($companyStartTime) {
                                $clockInTime = \Carbon\Carbon::parse($attendance->date . ' ' . $attendance->clock_in);
                                $expectedStartTime = \Carbon\Carbon::parse($attendance->date . ' ' . $companyStartTime);
                                if (\Carbon\Carbon::parse($attendance->date)->isSaturday()) {
                                    $expectedStartTime = \Carbon\Carbon::parse($attendance->date . ' 11:00:00');
                                }
                                $isLate = $clockInTime->gt($expectedStartTime);
                            }
                        }
                    }
                    
                    if ((empty($attendance->clock_out) || $attendance->clock_out == '00:00:00') && !empty($attendance->clock_in) && $attendance->clock_in != '00:00:00') {
                        $recordDate = \Carbon\Carbon::parse($attendance->date)->startOfDay();
                        if ($recordDate->lt(\Carbon\Carbon::today())) {
                            $dayOfWeek = $recordDate->format('l');
                            $hoursToAdd = ($dayOfWeek === 'Saturday') ? 3 : 5;
                            $autoClockOut = \Carbon\Carbon::parse($attendance->date . ' ' . $attendance->clock_in)->addHours($hoursToAdd)->format('H:i:s');
                            $attendance->clock_out = $autoClockOut;
                            $attendance->status = \App\Models\AttendanceEmployee::STATUS_HALF_DAY;
                        }
                    }

                    if ($attendance->clock_out && $attendance->clock_out != '00:00:00') {
                        $clockOut = \Carbon\Carbon::parse($attendance->date . ' ' . $attendance->clock_out)->format('h:i A');
                    }
    
                    $hoursCompleted = 0;
                    $recordDetails = [];
                    if ($attendance->clock_in && $attendance->clock_in != '00:00:00') {
                        $clockInTime = \Carbon\Carbon::parse($attendance->date . ' ' . $attendance->clock_in);
                        $clockOutTime = null;
                        $isRunning = false;
                        
                        if ($attendance->clock_out && $attendance->clock_out != '00:00:00') {
                            $clockOutTime = \Carbon\Carbon::parse($attendance->date . ' ' . $attendance->clock_out);
                            if ($clockOutTime->lt($clockInTime)) {
                                $clockOutTime->addDay();
                            }
                        } else {
                            $selectedDate = \Carbon\Carbon::parse($date);
                            if ($selectedDate->isToday()) {
                                $tz = \App\Models\Utility::getValByName('timezone') ?: config('app.timezone');
                                $clockOutTime = \Carbon\Carbon::now($tz);
                                $isRunning = true;
                            }
                        }
                        
                        if ($clockOutTime) {
                            $totalSeconds = $clockInTime->diffInSeconds($clockOutTime);
                            $hoursCompleted = $totalSeconds / 3600;
                            
                            $h = floor($totalSeconds / 3600);
                            $m = floor(($totalSeconds / 60) % 60);
                            $s = $totalSeconds % 60;
                            
                            $duration_formatted = $h . " " . ($h == 1 ? "hour" : "hours") . ", " . $m . " " . ($m == 1 ? "minute" : "minutes") . ", " . $s . " " . ($s == 1 ? "second" : "seconds");
                            $duration_hms = sprintf('%d:%02d:%02d', $h, $m, $s);
                            
                            $recordDetails[] = [
                                'date' => \Carbon\Carbon::parse($attendance->date)->format('d M, Y'),
                                'clock_in' => \Carbon\Carbon::parse($attendance->date . ' ' . $attendance->clock_in)->format('H:i:s'),
                                'clock_out' => $isRunning ? 'Running' : \Carbon\Carbon::parse($attendance->date . ' ' . $attendance->clock_out)->format('H:i:s'),
                                'duration_formatted' => $duration_formatted,
                                'duration_hms' => $duration_hms,
                                'is_running' => $isRunning,
                                'status' => $attendance->status,
                                'status_reason' => $attendance->status_reason
                            ];
                        }
                    }

                    $totalHours = (\Carbon\Carbon::parse($date)->dayOfWeek == \Carbon\Carbon::SATURDAY) ? 4.5 : 8;
                    
                    $requiredSeconds = $totalHours * 3600;
                    $workedSeconds = isset($totalSeconds) ? $totalSeconds : 0;
                    
                    $remaining_seconds = 0;
                    $remaining_formatted = '0 hours, 0 minutes, 0 seconds';
                    $overtime_seconds = 0;
                    $overtime_formatted = '0 hours, 0 minutes, 0 seconds';
                    
                    if ($workedSeconds < $requiredSeconds) {
                        $remaining_seconds = $requiredSeconds - $workedSeconds;
                        $h_rem = floor($remaining_seconds / 3600);
                        $m_rem = floor(($remaining_seconds / 60) % 60);
                        $s_rem = $remaining_seconds % 60;
                        $remaining_formatted = $h_rem . " " . ($h_rem == 1 ? "hour" : "hours") . ", " . $m_rem . " " . ($m_rem == 1 ? "minute" : "minutes") . ", " . $s_rem . " " . ($s_rem == 1 ? "second" : "seconds");
                    } elseif ($workedSeconds > $requiredSeconds) {
                        $overtime_seconds = $workedSeconds - $requiredSeconds;
                        $h_ot = floor($overtime_seconds / 3600);
                        $m_ot = floor(($overtime_seconds / 60) % 60);
                        $s_ot = $overtime_seconds % 60;
                        $overtime_formatted = $h_ot . " " . ($h_ot == 1 ? "hour" : "hours") . ", " . $m_ot . " " . ($m_ot == 1 ? "minute" : "minutes") . ", " . $s_ot . " " . ($s_ot == 1 ? "second" : "seconds");
                    }
    
                    $data = [
                        'clock_in' => $clockIn,
                        'clock_out' => $clockOut,
                        'hours_completed' => round($hoursCompleted, 2),
                        'today_hours' => round($hoursCompleted, 2),
                        'is_late' => $isLate,
                        'date' => $date,
                        'clock_in_raw' => $attendance->clock_in,
                        'total_hours' => $totalHours,
                        'records' => $recordDetails,
                        'total_duration_formatted' => isset($duration_formatted) ? $duration_formatted : '0 seconds',
                        'summation_expression' => isset($duration_hms) ? ($duration_hms . ' = ' . $duration_formatted) : '0 seconds',
                        'remaining_seconds' => $remaining_seconds,
                        'remaining_formatted' => $remaining_formatted,
                        'overtime_seconds' => $overtime_seconds,
                        'overtime_formatted' => $overtime_formatted,
                        'overtime_label' => 'Daily Overtime'
                    ];
                } else {
                    $totalHours = (\Carbon\Carbon::parse($date)->dayOfWeek == \Carbon\Carbon::SATURDAY) ? 4.5 : 8;
                    $requiredSeconds = $totalHours * 3600;
                    $remaining_formatted = $totalHours . " " . ($totalHours == 1 ? "hour" : "hours") . ", 0 minutes, 0 seconds";
                    $data = [
                        'clock_in' => null,
                        'clock_out' => null,
                        'hours_completed' => 0,
                        'total_hours' => $totalHours,
                        'records' => [],
                        'total_duration_formatted' => '0 seconds',
                        'summation_expression' => '0 seconds',
                        'remaining_seconds' => $requiredSeconds,
                        'remaining_formatted' => $remaining_formatted,
                        'overtime_seconds' => 0,
                        'overtime_formatted' => '0 hours, 0 minutes, 0 seconds',
                        'overtime_label' => 'Daily Overtime'
                    ];
                }
            } elseif ($filterType === 'weekly') {
                $referenceDate = $request->input('date');
                try {
                    if (!empty($referenceDate)) {
                        $ref = \Carbon\Carbon::parse($referenceDate);
                    } else {
                        $ref = \Carbon\Carbon::now();
                    }
                } catch (\Exception $e) {
                    $ref = \Carbon\Carbon::now();
                }
    
                $startOfWeek = $ref->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
                $endOfWeek = $ref->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
                
                $startDate = $startOfWeek->copy()->startOfDay();
                $endDate = $endOfWeek->copy()->endOfDay();
    
                $attendances = \App\Models\AttendanceEmployee::where('employee_id', $employeeId)
                    ->whereBetween('date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
                    ->orderBy('date', 'asc')
                    ->get();
    
                $hoursCompleted = 0;
                $daysWorked = 0;
                $recordDetails = [];
                $totalPeriodSeconds = 0;
                $hmsList = [];
    
                foreach ($attendances as $attendance) {
                    if ($attendance->clock_in && $attendance->clock_in != '00:00:00') {
                        $clockInTime = \Carbon\Carbon::parse($attendance->date . ' ' . $attendance->clock_in);
                        $clockOutTime = null;
                        $isRunning = false;
                        
                        if ($attendance->clock_out && $attendance->clock_out != '00:00:00') {
                            $clockOutTime = \Carbon\Carbon::parse($attendance->date . ' ' . $attendance->clock_out);
                            if ($clockOutTime->lt($clockInTime)) {
                                $clockOutTime->addDay();
                            }
                        } else {
                            $attendanceDate = \Carbon\Carbon::parse($attendance->date);
                            if ($attendanceDate->isToday()) {
                                $tz = \App\Models\Utility::getValByName('timezone') ?: config('app.timezone');
                                $clockOutTime = \Carbon\Carbon::now($tz);
                                $isRunning = true;
                            }
                        }
                        
                        if ($clockOutTime) {
                            $totalSeconds = $clockInTime->diffInSeconds($clockOutTime);
                            $totalPeriodSeconds += $totalSeconds;
                            
                            $h = floor($totalSeconds / 3600);
                            $m = floor(($totalSeconds / 60) % 60);
                            $s = $totalSeconds % 60;
                            
                            $duration_formatted = $h . " " . ($h == 1 ? "hour" : "hours") . ", " . $m . " " . ($m == 1 ? "minute" : "minutes") . ", " . $s . " " . ($s == 1 ? "second" : "seconds");
                            $duration_hms = sprintf('%d:%02d:%02d', $h, $m, $s);
                            $hmsList[] = $duration_hms;
                            
                            $recordDetails[] = [
                                'date' => \Carbon\Carbon::parse($attendance->date)->format('d M, Y'),
                                'clock_in' => \Carbon\Carbon::parse($attendance->date . ' ' . $attendance->clock_in)->format('H:i:s'),
                                'clock_out' => $isRunning ? 'Running' : \Carbon\Carbon::parse($attendance->date . ' ' . $attendance->clock_out)->format('H:i:s'),
                                'duration_formatted' => $duration_formatted,
                                'duration_hms' => $duration_hms,
                                'is_running' => $isRunning,
                                'status' => $attendance->status,
                                'status_reason' => $attendance->status_reason
                            ];
                            
                            $daysWorked++;
                        }
                    }
                }
                
                $hoursCompleted = $totalPeriodSeconds / 3600;
                
                $total_hours_sum = floor($totalPeriodSeconds / 3600);
                $total_minutes_sum = floor(($totalPeriodSeconds / 60) % 60);
                $total_seconds_sum = $totalPeriodSeconds % 60;
                
                $total_duration_formatted = $total_hours_sum . " " . ($total_hours_sum == 1 ? "hour" : "hours") . ", " . $total_minutes_sum . " " . ($total_minutes_sum == 1 ? "minute" : "minutes") . ", " . $total_seconds_sum . " " . ($total_seconds_sum == 1 ? "second" : "seconds");
                $summation_expression = count($hmsList) > 0 ? (implode(' + ', $hmsList) . ' = ' . $total_duration_formatted) : '0 seconds';
    
                $totalHours = 0;
                $current = $startOfWeek->copy();
                while ($current <= $endOfWeek) {
                    if ($current->dayOfWeek == \Carbon\Carbon::SATURDAY) {
                        $totalHours += 4.5;
                    } elseif ($current->dayOfWeek !== \Carbon\Carbon::SUNDAY) {
                        $totalHours += 8;
                    }
                    $current->addDay();
                }
    
                $todayAttendance = \App\Models\AttendanceEmployee::where('employee_id', $employeeId)
                    ->where('date', \Carbon\Carbon::today()->format('Y-m-d'))
                    ->first();
                
                $todayHours = 0;
                $todayClockIn = null;
                $todayClockOut = null;
                
                if ($todayAttendance && $todayAttendance->clock_in && $todayAttendance->clock_in != '00:00:00') {
                    $todayClockIn = \Carbon\Carbon::parse($todayAttendance->date . ' ' . $todayAttendance->clock_in)->format('h:i A');
                    if ($todayAttendance->clock_out && $todayAttendance->clock_out != '00:00:00') {
                        $todayClockOut = \Carbon\Carbon::parse($todayAttendance->date . ' ' . $todayAttendance->clock_out)->format('h:i A');
                        $clockInTime = \Carbon\Carbon::parse($todayAttendance->date . ' ' . $todayAttendance->clock_in);
                        $clockOutTime = \Carbon\Carbon::parse($todayAttendance->date . ' ' . $todayAttendance->clock_out);
                        if ($clockOutTime->lt($clockInTime)) {
                            $clockOutTime->addDay();
                        }
                        $todayHours = $clockInTime->diffInMinutes($clockOutTime) / 60;
                    }
                }
                
                $requiredSeconds = $totalHours * 3600;
                $workedSeconds = $totalPeriodSeconds;
                
                $remaining_seconds = 0;
                $remaining_formatted = '0 hours, 0 minutes, 0 seconds';
                $overtime_seconds = 0;
                $overtime_formatted = '0 hours, 0 minutes, 0 seconds';
                
                if ($workedSeconds < $requiredSeconds) {
                    $remaining_seconds = $requiredSeconds - $workedSeconds;
                    $h_rem = floor($remaining_seconds / 3600);
                    $m_rem = floor(($remaining_seconds / 60) % 60);
                    $s_rem = $remaining_seconds % 60;
                    $remaining_formatted = $h_rem . " " . ($h_rem == 1 ? "hour" : "hours") . ", " . $m_rem . " " . ($m_rem == 1 ? "minute" : "minutes") . ", " . $s_rem . " " . ($s_rem == 1 ? "second" : "seconds");
                } elseif ($workedSeconds > $requiredSeconds) {
                    $overtime_seconds = $workedSeconds - $requiredSeconds;
                    $h_ot = floor($overtime_seconds / 3600);
                    $m_ot = floor(($overtime_seconds / 60) % 60);
                    $s_ot = $overtime_seconds % 60;
                    $overtime_formatted = $h_ot . " " . ($h_ot == 1 ? "hour" : "hours") . ", " . $m_ot . " " . ($m_ot == 1 ? "minute" : "minutes") . ", " . $s_ot . " " . ($s_ot == 1 ? "second" : "seconds");
                }

                $data = [
                    'hours_completed' => round($hoursCompleted, 2),
                    'total_hours' => $totalHours,
                    'days_worked' => $daysWorked,
                    'percentage' => $totalHours > 0 ? round(($hoursCompleted / $totalHours) * 100, 1) : 0,
                    'week_start' => $startOfWeek->format('M d, Y'),
                    'week_end' => $endOfWeek->format('M d, Y'),
                    'clock_in' => $todayClockIn,
                    'clock_out' => $todayClockOut,
                    'today_hours' => round($todayHours, 2),
                    'records' => $recordDetails,
                    'total_duration_formatted' => $total_duration_formatted,
                    'summation_expression' => $summation_expression,
                    'remaining_seconds' => $remaining_seconds,
                    'remaining_formatted' => $remaining_formatted,
                    'overtime_seconds' => $overtime_seconds,
                    'overtime_formatted' => $overtime_formatted,
                    'overtime_label' => 'Weekly Overtime'
                ];
            } elseif ($filterType === 'monthly') {
                $requestMonth = $request->input('month');
                if (empty($requestMonth)) {
                    $month = \Carbon\Carbon::now()->startOfMonth();
                } else {
                    try {
                        $month = \Carbon\Carbon::parse($requestMonth . '-01');
                    } catch (\Exception $e) {
                        $month = \Carbon\Carbon::now()->startOfMonth();
                    }
                }
    
                $startOfMonth = $month->copy()->startOfMonth();
                $endOfMonth = $month->copy()->endOfMonth();
                
                $startDate = $startOfMonth->copy()->startOfDay();
                $endDate = $endOfMonth->copy()->endOfDay();
    
                $attendances = \App\Models\AttendanceEmployee::where('employee_id', $employeeId)
                    ->whereBetween('date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                    ->orderBy('date', 'asc')
                    ->get();
    
                $hoursCompleted = 0;
                $daysWorked = 0;
                $recordDetails = [];
                $totalPeriodSeconds = 0;
                $hmsList = [];
    
                foreach ($attendances as $attendance) {
                    if ($attendance->clock_in && $attendance->clock_in != '00:00:00') {
                        $clockInTime = \Carbon\Carbon::parse($attendance->date . ' ' . $attendance->clock_in);
                        $clockOutTime = null;
                        $isRunning = false;
                        
                        if ($attendance->clock_out && $attendance->clock_out != '00:00:00') {
                            $clockOutTime = \Carbon\Carbon::parse($attendance->date . ' ' . $attendance->clock_out);
                            if ($clockOutTime->lt($clockInTime)) {
                                $clockOutTime->addDay();
                            }
                        } else {
                            $attendanceDate = \Carbon\Carbon::parse($attendance->date);
                            if ($attendanceDate->isToday()) {
                                $tz = \App\Models\Utility::getValByName('timezone') ?: config('app.timezone');
                                $clockOutTime = \Carbon\Carbon::now($tz);
                                $isRunning = true;
                            }
                        }
                        
                        if ($clockOutTime) {
                            $totalSeconds = $clockInTime->diffInSeconds($clockOutTime);
                            $totalPeriodSeconds += $totalSeconds;
                            
                            $h = floor($totalSeconds / 3600);
                            $m = floor(($totalSeconds / 60) % 60);
                            $s = $totalSeconds % 60;
                            
                            $duration_formatted = $h . " " . ($h == 1 ? "hour" : "hours") . ", " . $m . " " . ($m == 1 ? "minute" : "minutes") . ", " . $s . " " . ($s == 1 ? "second" : "seconds");
                            $duration_hms = sprintf('%d:%02d:%02d', $h, $m, $s);
                            $hmsList[] = $duration_hms;
                            
                            $recordDetails[] = [
                                'date' => \Carbon\Carbon::parse($attendance->date)->format('d M, Y'),
                                'clock_in' => \Carbon\Carbon::parse($attendance->date . ' ' . $attendance->clock_in)->format('H:i:s'),
                                'clock_out' => $isRunning ? 'Running' : \Carbon\Carbon::parse($attendance->date . ' ' . $attendance->clock_out)->format('H:i:s'),
                                'duration_formatted' => $duration_formatted,
                                'duration_hms' => $duration_hms,
                                'is_running' => $isRunning,
                                'status' => $attendance->status,
                                'status_reason' => $attendance->status_reason
                            ];
                            
                            $daysWorked++;
                        }
                    }
                }
                
                $hoursCompleted = $totalPeriodSeconds / 3600;
                
                $total_hours_sum = floor($totalPeriodSeconds / 3600);
                $total_minutes_sum = floor(($totalPeriodSeconds / 60) % 60);
                $total_seconds_sum = $totalPeriodSeconds % 60;
                
                $total_duration_formatted = $total_hours_sum . " " . ($total_hours_sum == 1 ? "hour" : "hours") . ", " . $total_minutes_sum . " " . ($total_minutes_sum == 1 ? "minute" : "minutes") . ", " . $total_seconds_sum . " " . ($total_seconds_sum == 1 ? "second" : "seconds");
                $summation_expression = count($hmsList) > 0 ? (implode(' + ', $hmsList) . ' = ' . $total_duration_formatted) : '0 seconds';
    
                $totalHours = 0;
                $current = $startOfMonth->copy();
                while ($current <= $endOfMonth) {
                    if ($current->dayOfWeek == \Carbon\Carbon::SATURDAY) {
                        $totalHours += 4.5;
                    } elseif ($current->dayOfWeek !== \Carbon\Carbon::SUNDAY) {
                        $totalHours += 8;
                    }
                    $current->addDay();
                }
    
                $todayAttendance = \App\Models\AttendanceEmployee::where('employee_id', $employeeId)
                    ->where('date', \Carbon\Carbon::today()->format('Y-m-d'))
                    ->first();
                
                $todayHours = 0;
                $todayClockIn = null;
                $todayClockOut = null;
                
                if ($todayAttendance && $todayAttendance->clock_in && $todayAttendance->clock_in != '00:00:00') {
                    $todayClockIn = \Carbon\Carbon::parse($todayAttendance->date . ' ' . $todayAttendance->clock_in)->format('h:i A');
                    if ($todayAttendance->clock_out && $todayAttendance->clock_out != '00:00:00') {
                        $todayClockOut = \Carbon\Carbon::parse($todayAttendance->date . ' ' . $todayAttendance->clock_out)->format('h:i A');
                        $clockInTime = \Carbon\Carbon::parse($todayAttendance->date . ' ' . $todayAttendance->clock_in);
                        $clockOutTime = \Carbon\Carbon::parse($todayAttendance->date . ' ' . $todayAttendance->clock_out);
                        if ($clockOutTime->lt($clockInTime)) {
                            $clockOutTime->addDay();
                        }
                        $todayHours = $clockInTime->diffInMinutes($clockOutTime) / 60;
                    }
                }
                
                $requiredSeconds = $totalHours * 3600;
                $workedSeconds = $totalPeriodSeconds;
                
                $remaining_seconds = 0;
                $remaining_formatted = '0 hours, 0 minutes, 0 seconds';
                $overtime_seconds = 0;
                $overtime_formatted = '0 hours, 0 minutes, 0 seconds';
                
                if ($workedSeconds < $requiredSeconds) {
                    $remaining_seconds = $requiredSeconds - $workedSeconds;
                    $h_rem = floor($remaining_seconds / 3600);
                    $m_rem = floor(($remaining_seconds / 60) % 60);
                    $s_rem = $remaining_seconds % 60;
                    $remaining_formatted = $h_rem . " " . ($h_rem == 1 ? "hour" : "hours") . ", " . $m_rem . " " . ($m_rem == 1 ? "minute" : "minutes") . ", " . $s_rem . " " . ($s_rem == 1 ? "second" : "seconds");
                } elseif ($workedSeconds > $requiredSeconds) {
                    $overtime_seconds = $workedSeconds - $requiredSeconds;
                    $h_ot = floor($overtime_seconds / 3600);
                    $m_ot = floor(($overtime_seconds / 60) % 60);
                    $s_ot = $overtime_seconds % 60;
                    $overtime_formatted = $h_ot . " " . ($h_ot == 1 ? "hour" : "hours") . ", " . $m_ot . " " . ($m_ot == 1 ? "minute" : "minutes") . ", " . $s_ot . " " . ($s_ot == 1 ? "second" : "seconds");
                }

                $data = [
                    'hours_completed' => round($hoursCompleted, 2),
                    'total_hours' => $totalHours,
                    'days_worked' => $daysWorked,
                    'percentage' => $totalHours > 0 ? round(($hoursCompleted / $totalHours) * 100, 1) : 0,
                    'month_name' => $month->format('F Y'),
                    'clock_in' => $todayClockIn,
                    'clock_out' => $todayClockOut,
                    'today_hours' => round($todayHours, 2),
                    'records' => $recordDetails,
                    'total_duration_formatted' => $total_duration_formatted,
                    'summation_expression' => $summation_expression,
                    'remaining_seconds' => $remaining_seconds,
                    'remaining_formatted' => $remaining_formatted,
                    'overtime_seconds' => $overtime_seconds,
                    'overtime_formatted' => $overtime_formatted,
                    'overtime_label' => 'Monthly Overtime'
                ];
            }
    
            // Calculate stats for the selected period ($startDate to $endDate)
            $present_days = 0;
            $half_days = 0;
            $late_marks = 0;
            $absent_days = 0;
            $leave_days = 0;
            $payable_days = 0.0;
            
            // Fetch holidays
            $holidaysQuery = \App\Models\Holiday::where(function($q) use($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                  ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            })->get();
            
            $holidays = [];
            foreach ($holidaysQuery as $holiday) {
                $hStart = \Carbon\Carbon::parse($holiday->start_date);
                $hEnd = \Carbon\Carbon::parse($holiday->end_date);
                for ($d = $hStart->copy(); $d->lte($hEnd); $d->addDay()) {
                    $holidays[] = $d->format('Y-m-d');
                }
            }
            
            // Fetch approved leaves
            $leavesQuery = \App\Models\Leave::where('employee_id', $employeeId)
                ->where('status', 'Approved')
                ->where(function($q) use($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                      ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                })->get();
                
            $leaves = [];
            foreach ($leavesQuery as $leave) {
                $lStart = \Carbon\Carbon::parse($leave->start_date);
                $lEnd = \Carbon\Carbon::parse($leave->end_date);
                for ($d = $lStart->copy(); $d->lte($lEnd); $d->addDay()) {
                    $leaves[] = $d->format('Y-m-d');
                }
            }
            
            // Fetch attendances
            $attendancesQuery = \App\Models\AttendanceEmployee::where('employee_id', $employeeId)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->get()
                ->keyBy('date');
                
            $employee = \App\Models\Employee::find($employeeId);
            $weekOffDay = 'sunday';
            if ($employee && !empty($employee->week_off)) {
                $weekOffDay = strtolower($employee->week_off);
            }
            
            $todayDate = \Carbon\Carbon::today();
            for ($d = $startDate->copy(); $d->lte($endDate); $d->addDay()) {
                $dateFormatted = $d->format('Y-m-d');
                $dayName = strtolower($d->format('l'));
                
                // If it's a future date, don't count it for absent/present/payable
                if ($d->gt($todayDate)) {
                    continue;
                }
                
                if (in_array($dateFormatted, $leaves)) {
                    $leave_days++;
                    $payable_days += 1.0;
                } elseif (in_array($dateFormatted, $holidays)) {
                    $payable_days += 1.0;
                } elseif ($dayName === $weekOffDay) {
                    $payable_days += 1.0;
                } elseif (isset($attendancesQuery[$dateFormatted])) {
                    $att = $attendancesQuery[$dateFormatted];
                    if ($att->status === \App\Models\AttendanceEmployee::STATUS_PRESENT || $att->status === \App\Models\AttendanceEmployee::STATUS_EARLY_CLOCK_OUT) {
                        $present_days++;
                        $payable_days += 1.0;
                    } elseif ($att->status === \App\Models\AttendanceEmployee::STATUS_HALF_DAY || $att->status === \App\Models\AttendanceEmployee::STATUS_SINGLE_PUNCH) {
                        $half_days++;
                        $payable_days += 0.5;
                    } elseif ($att->status === \App\Models\AttendanceEmployee::STATUS_ABSENT) {
                        $absent_days++;
                    }
                    
                    if (!empty($att->late) && $att->late !== '00:00:00') {
                        $late_marks++;
                    }
                } else {
                    $absent_days++;
                }
            }
            
            $data['present_days'] = $present_days;
            $data['half_days'] = $half_days;
            $data['late_marks'] = $late_marks;
            $data['absent_days'] = $absent_days;
            $data['leave_days'] = $leave_days;
            $data['payable_days'] = $payable_days;

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching attendance data',
                'debug' => $e->getMessage() . ' at line ' . $e->getLine()
            ], 500);
        }
    }
}
