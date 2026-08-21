<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRegularisation;
use App\Models\AttendanceEmployee;
use App\Models\Employee;
use App\Models\User;
use App\Models\Utility;
use App\Notifications\AttendanceRegularisationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceRegularisationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (\Auth::user()->type == 'employee') {
            $emp = !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0;
            $regularisations = AttendanceRegularisation::where('employee_id', $emp)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Company user - show all regularisations
            $regularisations = AttendanceRegularisation::where('created_by', \Auth::user()->creatorId())
                ->with('employee')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('attendance.regularisation.index', compact('regularisations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (\Auth::user()->type != 'employee' && \Auth::user()->type != 'company') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        return view('attendance.regularisation.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (\Auth::user()->type != 'employee' && \Auth::user()->type != 'company') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $validator = Validator::make($request->all(), [
            'missed_attendance_date' => 'required|date',
            'punch_in_time' => 'required',
            'punch_out_time' => 'required',
            'reason' => 'required|in:Missed Punch,Technical Error,Others',
            'remark' => 'required|string|max:500',
        ]);

        if (\Auth::user()->type == 'company') {
            $validator->addRules(['employee_id' => 'required|exists:employees,id']);
        }

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        // For company users, use employee_id from request; for employees, use their own employee record
        if (\Auth::user()->type == 'company') {
            $emp = $request->employee_id;
            
            // Verify the employee belongs to the same company
            $employee = Employee::where('id', $emp)
                ->where('created_by', \Auth::user()->creatorId())
                ->first();
            
            if (!$employee) {
                return redirect()->back()->with('error', __('Employee not found or permission denied.'));
            }
        } else {
            $emp = !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0;
            
            if ($emp == 0) {
                return redirect()->back()->with('error', __('Employee record not found.'));
            }
        }

        // Check if punch out time is after punch in time
        if (strtotime($request->punch_out_time) <= strtotime($request->punch_in_time)) {
            return redirect()->back()->with('error', __('Punch Out Time must be after Punch In Time.'));
        }

        $regularisation = new AttendanceRegularisation();
        $regularisation->employee_id = $emp;
        $regularisation->missed_attendance_date = $request->missed_attendance_date;
        $regularisation->punch_in_time = $request->punch_in_time . ':00';
        $regularisation->punch_out_time = $request->punch_out_time . ':00';
        $regularisation->reason = $request->reason;
        $regularisation->remark = $request->remark;
        $regularisation->status = 'Pending';
        $regularisation->created_by = \Auth::user()->creatorId();
        $regularisation->save();

        // Send notification to company users, HR, and Director users
        $employee = Employee::find($emp);
        $creatorId = \Auth::user()->creatorId();
        
        // Get company users - use creatorId() same as LeaveController does
        $companyUsers = User::where('type', 'company')
            ->where('created_by', $creatorId)
            ->get();
        
        // Get HR and Director users
        $hrDirectorUsers = User::where(function($query) {
                $query->where('type', 'hr')
                      ->orWhere('type', 'HR')
                      ->orWhere('type', 'director')
                      ->orWhere('type', 'Director');
            })
            ->where('created_by', $creatorId)
            ->get();

        $notificationData = [
            'regularisation_id' => $regularisation->id,
            'message' => 'New attendance regularisation request: ' . ($employee ? $employee->name : 'Employee') . ' submitted a request for ' . \Auth::user()->dateFormat($request->missed_attendance_date),
            'employee_name' => $employee ? $employee->name : 'Employee',
            'date' => $request->missed_attendance_date,
            'url' => route('attendance-regularisation.index'),
        ];

        // Log for debugging
        \Log::info('Sending attendance regularisation notifications', [
            'regularisation_id' => $regularisation->id,
            'company_users_count' => $companyUsers->count(),
            'hr_director_users_count' => $hrDirectorUsers->count(),
            'creator_id' => $creatorId,
            'employee_user_id' => \Auth::id(),
            'company_user_ids' => $companyUsers->pluck('id')->toArray()
        ]);

        // Send notifications to company users
        $adminDeviceTokens = [];
        foreach ($companyUsers as $user) {
            try {
                $user->notify(new AttendanceRegularisationNotification($notificationData));
                if ($user->device_token) $adminDeviceTokens[] = $user->device_token;
                \Log::info('Notification sent to company user', ['user_id' => $user->id, 'user_email' => $user->email]);
            } catch (\Exception $e) {
                \Log::error('Failed to send regularisation notification to company user', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Send notifications to HR and Director users
        foreach ($hrDirectorUsers as $user) {
            try {
                $user->notify(new AttendanceRegularisationNotification($notificationData));
                if ($user->device_token) $adminDeviceTokens[] = $user->device_token;
                \Log::info('Notification sent to HR/Director user', ['user_id' => $user->id, 'user_email' => $user->email]);
            } catch (\Exception $e) {
                \Log::error('Failed to send regularisation notification to HR/Director user', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        if (!empty($adminDeviceTokens)) {
            \App\Helpers\OneSignalHelper::sendPushNotification(
                $adminDeviceTokens,
                'Attendance Regularisation Request',
                $notificationData['message'],
                ['url' => route('attendance-regularisation.index')]
            );
        }

        return redirect()->route('attendance-regularisation.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $regularisation = AttendanceRegularisation::with('employee')->find($id);

        if (!$regularisation) {
            return redirect()->back()->with('error', __('Regularisation request not found.'));
        }

        // Check permissions
        if (\Auth::user()->type == 'employee') {
            $emp = !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0;
            if ($regularisation->employee_id != $emp) {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            if ($regularisation->created_by != \Auth::user()->creatorId()) {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }

        return view('attendance.regularisation.show', compact('regularisation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $regularisation = AttendanceRegularisation::find($id);

        if (!$regularisation) {
            return redirect()->back()->with('error', __('Regularisation request not found.'));
        }

        // Check permissions - only employee can edit, and only if status is Pending
        if (\Auth::user()->type == 'employee') {
            $emp = !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0;
            if ($regularisation->employee_id != $emp) {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
            if ($regularisation->status != 'Pending') {
                return redirect()->back()->with('error', __('You can only edit pending requests.'));
            }
        } else {
            // Company users can also edit
            if ($regularisation->created_by != \Auth::user()->creatorId()) {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }

        return view('attendance.regularisation.edit', compact('regularisation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $regularisation = AttendanceRegularisation::find($id);

        if (!$regularisation) {
            return redirect()->back()->with('error', __('Regularisation request not found.'));
        }

        // Check permissions
        if (\Auth::user()->type == 'employee') {
            $emp = !empty(\Auth::user()->employee) ? \Auth::user()->employee->id : 0;
            if ($regularisation->employee_id != $emp) {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
            if ($regularisation->status != 'Pending') {
                return redirect()->back()->with('error', __('You can only edit pending requests.'));
            }
        } else {
            if ($regularisation->created_by != \Auth::user()->creatorId()) {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }

        $validator = Validator::make($request->all(), [
            'missed_attendance_date' => 'required|date',
            'punch_in_time' => 'required',
            'punch_out_time' => 'required',
            'reason' => 'required|in:Missed Punch,Technical Error,Others',
            'remark' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        // Check if punch out time is after punch in time
        if (strtotime($request->punch_out_time) <= strtotime($request->punch_in_time)) {
            return redirect()->back()->with('error', __('Punch Out Time must be after Punch In Time.'));
        }

        $regularisation->missed_attendance_date = $request->missed_attendance_date;
        $regularisation->punch_in_time = $request->punch_in_time . ':00';
        $regularisation->punch_out_time = $request->punch_out_time . ':00';
        $regularisation->reason = $request->reason;
        $regularisation->remark = $request->remark;
        $regularisation->save();

        return redirect()->route('attendance-regularisation.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $regularisation = AttendanceRegularisation::find($id);

        if (!$regularisation) {
            return redirect()->back()->with('error', __('Regularisation request not found.'));
        }

        // Only company users can delete
        if (\Auth::user()->type == 'employee') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($regularisation->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $regularisation->delete();

        return redirect()->route('attendance-regularisation.index');
    }

    /**
     * Approve the attendance regularisation request
     */
    public function approve($id)
    {
        if (\Auth::user()->type == 'employee') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $regularisation = AttendanceRegularisation::with('employee')->find($id);

        if (!$regularisation) {
            return redirect()->back()->with('error', __('Regularisation request not found.'));
        }

        if ($regularisation->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($regularisation->status != 'Pending') {
            return redirect()->back()->with('error', __('This request has already been processed.'));
        }

        // Update attendance record
        $startTime = Utility::getValByName('company_start_time');
        $endTime = Utility::getValByName('company_end_time');

        $clockIn = $regularisation->punch_in_time;
        $clockOut = $regularisation->punch_out_time;

        // Calculate late time
        $totalLateSeconds = strtotime($clockIn) - strtotime($startTime);
        $hours = floor($totalLateSeconds / 3600);
        $mins = floor($totalLateSeconds / 60 % 60);
        $secs = floor($totalLateSeconds % 60);
        $late = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

        // Calculate early leaving
        $totalEarlyLeavingSeconds = strtotime($endTime) - strtotime($clockOut);
        $hours = floor($totalEarlyLeavingSeconds / 3600);
        $mins = floor($totalEarlyLeavingSeconds / 60 % 60);
        $secs = floor($totalEarlyLeavingSeconds % 60);
        $earlyLeaving = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

        // Calculate overtime
        if (strtotime($clockOut) > strtotime($endTime)) {
            $totalOvertimeSeconds = strtotime($clockOut) - strtotime($endTime);
            $hours = floor($totalOvertimeSeconds / 3600);
            $mins = floor($totalOvertimeSeconds / 60 % 60);
            $secs = floor($totalOvertimeSeconds % 60);
            $overtime = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
        } else {
            $overtime = '00:00:00';
        }

        // Calculate total worked hours
        $workedSeconds = strtotime($clockOut) - strtotime($clockIn);
        $workedHours = $workedSeconds / 3600;

        // Determine status
        if ($workedHours >= AttendanceEmployee::REQUIRED_WORKING_HOURS) {
            $status = AttendanceEmployee::STATUS_PRESENT;
        } else {
            $status = AttendanceEmployee::STATUS_HALF_DAY;
        }

        // Check if attendance record exists
        $attendance = AttendanceEmployee::where('employee_id', $regularisation->employee_id)
            ->where('date', $regularisation->missed_attendance_date)
            ->first();

        if ($attendance) {
            // Update existing attendance
            $attendance->clock_in = $clockIn;
            $attendance->clock_out = $clockOut;
            $attendance->status = $status;
            $attendance->late = $late;
            $attendance->early_leaving = ($earlyLeaving > 0) ? $earlyLeaving : '00:00:00';
            $attendance->overtime = $overtime;
            $attendance->save();
        } else {
            // Create new attendance record
            $attendance = new AttendanceEmployee();
            $attendance->employee_id = $regularisation->employee_id;
            $attendance->date = $regularisation->missed_attendance_date;
            $attendance->clock_in = $clockIn;
            $attendance->clock_out = $clockOut;
            $attendance->status = $status;
            $attendance->late = $late;
            $attendance->early_leaving = ($earlyLeaving > 0) ? $earlyLeaving : '00:00:00';
            $attendance->overtime = $overtime;
            $attendance->total_rest = '00:00:00';
            $attendance->created_by = \Auth::user()->creatorId();
            $attendance->save();
        }

        // Update regularisation status
        $regularisation->status = 'Approved';
        $regularisation->approved_by = \Auth::user()->id;
        $regularisation->approved_at = now();
        $regularisation->save();

        return redirect()->route('attendance-regularisation.index');
    }

    /**
     * Reject the attendance regularisation request
     */
    public function reject($id)
    {
        if (\Auth::user()->type == 'employee') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $regularisation = AttendanceRegularisation::find($id);

        if (!$regularisation) {
            return redirect()->back()->with('error', __('Regularisation request not found.'));
        }

        if ($regularisation->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($regularisation->status != 'Pending') {
            return redirect()->back()->with('error', __('This request has already been processed.'));
        }

        $regularisation->status = 'Rejected';
        $regularisation->approved_by = \Auth::user()->id;
        $regularisation->approved_at = now();
        $regularisation->save();

        return redirect()->route('attendance-regularisation.index');
    }

    /**
     * Show action modal for attendance regularisation
     */
    public function action($id)
    {
        if (\Auth::user()->type == 'employee') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $regularisation = AttendanceRegularisation::with('employee')->find($id);

        if (!$regularisation) {
            return redirect()->back()->with('error', __('Regularisation request not found.'));
        }

        if ($regularisation->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        return view('attendance.regularisation.action', compact('regularisation'));
    }

    /**
     * Handle action (approve/reject) for attendance regularisation
     */
    public function changeaction(Request $request)
    {
        if (\Auth::user()->type == 'employee') {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $regularisation = AttendanceRegularisation::with('employee')->find($request->regularisation_id);

        if (!$regularisation) {
            return redirect()->back()->with('error', __('Regularisation request not found.'));
        }

        if ($regularisation->created_by != \Auth::user()->creatorId()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        if ($regularisation->status != 'Pending') {
            return redirect()->back()->with('error', __('This request has already been processed.'));
        }

        $status = $request->status;

        if ($status == 'Approved') {
            // Update attendance record
            $startTime = Utility::getValByName('company_start_time');
            $endTime = Utility::getValByName('company_end_time');

            $clockIn = $regularisation->punch_in_time;
            $clockOut = $regularisation->punch_out_time;

            // Calculate late time
            $totalLateSeconds = strtotime($clockIn) - strtotime($startTime);
            $hours = floor($totalLateSeconds / 3600);
            $mins = floor($totalLateSeconds / 60 % 60);
            $secs = floor($totalLateSeconds % 60);
            $late = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

            // Calculate early leaving
            $totalEarlyLeavingSeconds = strtotime($endTime) - strtotime($clockOut);
            $hours = floor($totalEarlyLeavingSeconds / 3600);
            $mins = floor($totalEarlyLeavingSeconds / 60 % 60);
            $secs = floor($totalEarlyLeavingSeconds % 60);
            $earlyLeaving = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);

            // Calculate overtime
            if (strtotime($clockOut) > strtotime($endTime)) {
                $totalOvertimeSeconds = strtotime($clockOut) - strtotime($endTime);
                $hours = floor($totalOvertimeSeconds / 3600);
                $mins = floor($totalOvertimeSeconds / 60 % 60);
                $secs = floor($totalOvertimeSeconds % 60);
                $overtime = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
            } else {
                $overtime = '00:00:00';
            }

            // Calculate total worked hours
            $workedSeconds = strtotime($clockOut) - strtotime($clockIn);
            $workedHours = $workedSeconds / 3600;

            // Determine status
            if ($workedHours >= AttendanceEmployee::REQUIRED_WORKING_HOURS) {
                $attendanceStatus = AttendanceEmployee::STATUS_PRESENT;
            } else {
                $attendanceStatus = AttendanceEmployee::STATUS_HALF_DAY;
            }

            // Check if attendance record exists
            $attendance = AttendanceEmployee::where('employee_id', $regularisation->employee_id)
                ->where('date', $regularisation->missed_attendance_date)
                ->first();

            if ($attendance) {
                // Update existing attendance
                $attendance->clock_in = $clockIn;
                $attendance->clock_out = $clockOut;
                $attendance->status = $attendanceStatus;
                $attendance->late = $late;
                $attendance->early_leaving = ($earlyLeaving > 0) ? $earlyLeaving : '00:00:00';
                $attendance->overtime = $overtime;
                $attendance->save();
            } else {
                // Create new attendance record
                $attendance = new AttendanceEmployee();
                $attendance->employee_id = $regularisation->employee_id;
                $attendance->date = $regularisation->missed_attendance_date;
                $attendance->clock_in = $clockIn;
                $attendance->clock_out = $clockOut;
                $attendance->status = $attendanceStatus;
                $attendance->late = $late;
                $attendance->early_leaving = ($earlyLeaving > 0) ? $earlyLeaving : '00:00:00';
                $attendance->overtime = $overtime;
                $attendance->total_rest = '00:00:00';
                $attendance->created_by = \Auth::user()->creatorId();
                $attendance->save();
            }
        }

        // Update regularisation status
        $regularisation->status = $status;
        $regularisation->approved_by = \Auth::user()->id;
        $regularisation->approved_at = now();
        $regularisation->save();

        return redirect()->route('attendance-regularisation.index');
    }

    /**
     * Get employees for company user dropdown (searchable)
     */
    public function getEmployees(Request $request)
    {
        try {
            if (\Auth::user()->type != 'company') {
                return response()->json(['error' => __('Permission denied.')], 403);
            }

            $search = $request->get('search', '');
            $search = trim($search);
            
            // Get all active employees (exclude terminated and resigned)
            $query = Employee::where('created_by', \Auth::user()->creatorId());
            
            // Exclude terminated employees
            $query->whereNotIn('id', function($q) {
                $q->select('employee_id')
                  ->from('terminations')
                  ->whereDate('termination_date', '<=', now());
            });
            
            // Exclude resigned employees
            $query->whereNotIn('id', function($q) {
                $q->select('employee_id')
                  ->from('resignations')
                  ->whereDate('resignation_date', '<=', now());
            });
            
            // Apply search filter if provided
            if (!empty($search)) {
                $searchLower = strtolower($search);
                $query->where(function($q) use ($searchLower) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . $searchLower . '%'])
                      ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . $searchLower . '%'])
                      ->orWhereRaw('LOWER(CONCAT(name, " ", last_name)) LIKE ?', ['%' . $searchLower . '%'])
                      ->orWhereRaw('LOWER(employee_id) LIKE ?', ['%' . $searchLower . '%'])
                      ->orWhereRaw('LOWER(email) LIKE ?', ['%' . $searchLower . '%']);
                });
            }
            
            $employees = $query->select('id', 'name', 'last_name', 'employee_id', 'email')
                ->orderBy('name', 'asc')
                ->limit(100)
                ->get()
                ->map(function($employee) {
                    $fullName = trim(($employee->name ?? '') . ' ' . ($employee->last_name ?? ''));
                    $displayName = $fullName ?: ($employee->name ?? 'N/A');
                    return [
                        'id' => $employee->id,
                        'text' => $displayName . ' (' . ($employee->employee_id ?? 'N/A') . ')',
                        'name' => $displayName,
                        'employee_id' => $employee->employee_id,
                    ];
                });

            return response()->json($employees);

        } catch (\Exception $e) {
            \Log::error('AttendanceRegularisationController getEmployees error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => __('Something went wrong.')], 500);
        }
    }
}
