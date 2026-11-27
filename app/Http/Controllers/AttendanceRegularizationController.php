<?php

namespace App\Http\Controllers;

use App\Models\AttendanceEmployee;
use App\Models\AttendanceRegularization;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AttendanceRegularizationController extends Controller
{
    private function ensureTableExists(): void
    {
        if (!Schema::hasTable('attendance_regularizations')) {
            Schema::create('attendance_regularizations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id')->index();
                $table->date('date')->index();
                $table->time('clock_in');
                $table->time('clock_out');
                $table->text('reason');
                $table->string('status', 20)->default('Pending')->index();
                $table->unsignedBigInteger('action_by')->nullable();
                $table->timestamp('action_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->unsignedBigInteger('created_by')->index();
                $table->timestamps();
            });
        }
    }

    private function canApprove(): bool
    {
        $user = Auth::user();
        return in_array(strtolower($user->type), ['company', 'hr', 'director', 'super admin'], true)
            || $user->can('Edit Attendance')
            || $user->can('Manage Attendance');
    }

    private function checkDateEligibility(int $employeeId, int $creatorId, string $date): array
    {
        $today = Carbon::today()->toDateString();
        if ($date >= $today) {
            return [
                'eligible' => false,
                'reason'   => __('You can only select past dates (before today) for regularization.'),
            ];
        }

        if (Carbon::parse($date)->isSunday()) {
            return [
                'eligible' => false,
                'reason'   => __('Regularization is not allowed on Sundays.'),
            ];
        }

        $isHoliday = \App\Models\Holiday::where('created_by', $creatorId)
            ->whereDate('date', $date)
            ->exists();
        if ($isHoliday) {
            return [
                'eligible' => false,
                'reason'   => __('Regularization is not allowed on Holidays.'),
            ];
        }

        $isApprovedLeave = \App\Models\Leave::where('employee_id', $employeeId)
            ->where('status', 'Approved')
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->exists();
        if ($isApprovedLeave) {
            return [
                'eligible' => false,
                'reason'   => __('Regularization is not allowed on Approved Leave dates.'),
            ];
        }

        $hasProjectUpdate = \App\Models\ProjectDailyUpdate::where('employee_id', $employeeId)
            ->where('work_date', $date)
            ->exists();
        $hasGeneralTask = \App\Models\GeneralDailyTask::where('employee_id', $employeeId)
            ->where('work_date', $date)
            ->exists();

        if (!$hasProjectUpdate && !$hasGeneralTask) {
            return [
                'eligible' => false,
                'reason'   => __('No work update found for this date. Only past dates with a submitted work update can be regularized.'),
                'needs_work_update' => true,
            ];
        }

        return ['eligible' => true, 'reason' => null];
    }

    public function index(Request $request)
    {
        $this->ensureTableExists();

        $user = Auth::user();
        $isEmployee = $user->type === 'employee';
        $statusFilter = $request->get('status', 'All');

        $query = AttendanceRegularization::with(['employee', 'actionUser'])
            ->where('created_by', $user->creatorId())
            ->orderByRaw("CASE WHEN status = 'Pending' THEN 0 ELSE 1 END")
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc');

        if ($isEmployee) {
            $employeeId = $user->employee->id ?? 0;
            $query->where('employee_id', $employeeId);
        }

        if (in_array($statusFilter, ['Pending', 'Approved', 'Rejected'], true)) {
            $query->where('status', $statusFilter);
        }

        $regularizations = $query->get();

        $countsQuery = AttendanceRegularization::where('created_by', $user->creatorId());
        if ($isEmployee) {
            $countsQuery->where('employee_id', $user->employee->id ?? 0);
        }
        $allForCounts = $countsQuery->get(['status']);
        $counts = [
            'all'      => $allForCounts->count(),
            'pending'  => $allForCounts->where('status', 'Pending')->count(),
            'approved' => $allForCounts->where('status', 'Approved')->count(),
            'rejected' => $allForCounts->where('status', 'Rejected')->count(),
        ];

        $eligibleDates = [];
        if ($isEmployee && !empty($user->employee)) {
            $employeeId = $user->employee->id;
            $today = Carbon::today()->toDateString();

            $projectDates = \App\Models\ProjectDailyUpdate::where('employee_id', $employeeId)
                ->where('work_date', '<', $today)
                ->pluck('work_date')
                ->toArray();
            $generalDates = \App\Models\GeneralDailyTask::where('employee_id', $employeeId)
                ->where('work_date', '<', $today)
                ->pluck('work_date')
                ->toArray();

            $candidateDates = collect(array_merge($projectDates, $generalDates))
                ->unique()
                ->sortDesc()
                ->values();

            foreach ($candidateDates as $cDate) {
                $check = $this->checkDateEligibility($employeeId, $user->creatorId(), $cDate);
                if ($check['eligible']) {
                    $eligibleDates[] = $cDate;
                }
            }
        }

        return view('attendance.regularization', [
            'regularizations' => $regularizations,
            'isEmployee'      => $isEmployee,
            'canApprove'      => !$isEmployee && $this->canApprove(),
            'statusFilter'    => $statusFilter,
            'counts'          => $counts,
            'eligibleDates'   => $eligibleDates,
        ]);
    }

    public function fetchDateAttendance(Request $request)
    {
        $this->ensureTableExists();

        $request->validate([
            'date' => 'required|date',
        ]);

        $user = Auth::user();
        $employee = $user->employee;
        if (!$employee) {
            return response()->json([
                'eligible'  => false,
                'reason'    => __('Employee profile not found.'),
                'exists'    => false,
                'clock_in'  => null,
                'clock_out' => null,
                'status'    => null,
            ]);
        }

        $eligibility = $this->checkDateEligibility($employee->id, $user->creatorId(), $request->date);

        $attendance = AttendanceEmployee::where('employee_id', $employee->id)
            ->where('date', $request->date)
            ->first();

        $clockIn = ($attendance && $attendance->clock_in && $attendance->clock_in !== '00:00:00')
            ? substr($attendance->clock_in, 0, 5)
            : null;
        $clockOut = ($attendance && $attendance->clock_out && $attendance->clock_out !== '00:00:00')
            ? substr($attendance->clock_out, 0, 5)
            : null;

        return response()->json([
            'eligible'          => $eligibility['eligible'],
            'reason'            => $eligibility['reason'],
            'needs_work_update' => $eligibility['needs_work_update'] ?? false,
            'exists'            => (bool) $attendance,
            'clock_in'          => $clockIn,
            'clock_out'         => $clockOut,
            'status'            => $attendance ? $attendance->status : null,
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureTableExists();

        $user = Auth::user();
        $employee = $user->employee;
        if (!$employee) {
            return redirect()->back()->with('error', __('Employee profile not found.'));
        }

        $request->validate([
            'date'        => 'required|date|before:today',
            'clock_in'    => 'required|date_format:H:i',
            'clock_out'   => 'required|date_format:H:i',
            'reason_type' => 'nullable|string|max:100',
            'reason'      => 'required|string|min:3|max:1000',
        ]);

        $eligibility = $this->checkDateEligibility($employee->id, $user->creatorId(), $request->date);
        if (!$eligibility['eligible']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $eligibility['reason']);
        }

        if (strtotime($request->clock_out) <= strtotime($request->clock_in)) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('Punch Out time must be after Punch In time.'));
        }

        $alreadyPending = AttendanceRegularization::where('employee_id', $employee->id)
            ->where('date', $request->date)
            ->where('status', 'Pending')
            ->exists();

        if ($alreadyPending) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('A pending regularization request already exists for this date.'));
        }

        $reasonText = trim($request->reason);
        if (!empty($request->reason_type) && $request->reason_type !== 'Other') {
            $reasonText = '[' . $request->reason_type . '] ' . $reasonText;
        }

        $regularization = AttendanceRegularization::create([
            'employee_id' => $employee->id,
            'date'        => $request->date,
            'clock_in'    => $request->clock_in . ':00',
            'clock_out'   => $request->clock_out . ':00',
            'reason'      => $reasonText,
            'status'      => 'Pending',
            'created_by'  => $user->creatorId(),
        ]);

        // Notify company user(s) via FCM push in background
        $creatorId = $user->creatorId();
        $companyUserIds = User::where('type', 'company')
            ->where(function ($q) use ($creatorId) {
                $q->where('id', $creatorId)->orWhere('created_by', $creatorId);
            })
            ->pluck('id')
            ->unique()
            ->values()
            ->toArray();

        $employeeName = $employee->full_name ?: $user->name;
        $formattedDate = Carbon::parse($regularization->date)->format('d M Y');
        $targetUrl = route('attendance.regularization.index');

        dispatch(function () use ($companyUserIds, $employeeName, $formattedDate, $targetUrl) {
            try {
                if (!empty($companyUserIds)) {
                    app(\App\Services\FcmPushService::class)->sendToUsers(
                        $companyUserIds,
                        'Attendance Regularization Request',
                        "{$employeeName} requested attendance regularization for {$formattedDate}.",
                        [
                            'type' => 'attendance_regularization',
                            'url'  => $targetUrl,
                        ]
                    );
                }
            } catch (\Throwable $e) {
                \Log::warning('Regularization FCM notification failed: ' . $e->getMessage());
            }
        })->afterResponse();

        return redirect()->route('attendance.regularization.index')
            ->with('success', __('Attendance regularization request submitted successfully.'));
    }

    public function approve($id)
    {
        $this->ensureTableExists();

        $user = Auth::user();
        if ($user->type === 'employee' || !$this->canApprove()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $regularization = AttendanceRegularization::where('id', $id)
            ->where('created_by', $user->creatorId())
            ->firstOrFail();

        if ($regularization->status !== 'Pending') {
            return redirect()->back()->with('error', __('This request has already been processed.'));
        }

        app(AttendanceEmployeeController::class)->applyRegularizedAttendance(
            (int) $regularization->employee_id,
            $regularization->date,
            $regularization->clock_in,
            $regularization->clock_out,
            (int) $user->creatorId()
        );

        $regularization->status = 'Approved';
        $regularization->action_by = $user->id;
        $regularization->action_at = now();
        $regularization->save();

        // Notify employee via FCM in background
        $employee = Employee::find($regularization->employee_id);
        $empUserId = $employee ? $employee->user_id : null;
        $formattedDate = Carbon::parse($regularization->date)->format('d M Y');
        $targetUrl = route('attendance.regularization.index');

        if ($empUserId) {
            dispatch(function () use ($empUserId, $formattedDate, $targetUrl) {
                try {
                    app(\App\Services\FcmPushService::class)->sendToUsers(
                        [$empUserId],
                        'Regularization Approved',
                        "Your attendance regularization for {$formattedDate} has been approved.",
                        [
                            'type' => 'attendance_regularization_approved',
                            'url'  => $targetUrl,
                        ]
                    );
                } catch (\Throwable $e) {
                    \Log::warning('Regularization approval FCM failed: ' . $e->getMessage());
                }
            })->afterResponse();
        }

        return redirect()->route('attendance.regularization.index')
            ->with('success', __('Attendance regularization approved and attendance updated.'));
    }

    public function reject(Request $request, $id)
    {
        $this->ensureTableExists();

        $user = Auth::user();
        if ($user->type === 'employee' || !$this->canApprove()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $regularization = AttendanceRegularization::where('id', $id)
            ->where('created_by', $user->creatorId())
            ->firstOrFail();

        if (!in_array($regularization->status, ['Pending', 'Approved'], true)) {
            return redirect()->back()->with('error', __('This request is already rejected.'));
        }

        $wasApproved = $regularization->status === 'Approved';

        $regularization->status = 'Rejected';
        $regularization->rejection_reason = trim((string) $request->input('rejection_reason', ''));
        $regularization->action_by = $user->id;
        $regularization->action_at = now();
        $regularization->save();

        if ($wasApproved) {
            $attendance = AttendanceEmployee::where('employee_id', $regularization->employee_id)
                ->where('date', $regularization->date)
                ->first();
            if ($attendance) {
                $attendance->status = AttendanceEmployee::STATUS_HALF_DAY;
                $attendance->status_reason = 'Regularization Rejected';
                $attendance->save();
            }
        }

        $employee = Employee::find($regularization->employee_id);
        $empUserId = $employee ? $employee->user_id : null;
        $formattedDate = Carbon::parse($regularization->date)->format('d M Y');
        $targetUrl = route('attendance.regularization.index');

        if ($empUserId) {
            dispatch(function () use ($empUserId, $formattedDate, $targetUrl) {
                try {
                    app(\App\Services\FcmPushService::class)->sendToUsers(
                        [$empUserId],
                        'Regularization Rejected',
                        "Your attendance regularization for {$formattedDate} was rejected.",
                        [
                            'type' => 'attendance_regularization_rejected',
                            'url'  => $targetUrl,
                        ]
                    );
                } catch (\Throwable $e) {
                    \Log::warning('Regularization rejection FCM failed: ' . $e->getMessage());
                }
            })->afterResponse();
        }

        return redirect()->route('attendance.regularization.index')
            ->with('success', __('Attendance regularization request rejected.'));
    }
}

