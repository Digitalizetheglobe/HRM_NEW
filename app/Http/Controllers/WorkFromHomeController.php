<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Termination;
use App\Models\User;
use App\Models\Utility;
use App\Models\WorkFromHome;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class WorkFromHomeController extends Controller
{
    private function ensureTableExists(): void
    {
        if (!Schema::hasTable('work_from_homes')) {
            Schema::create('work_from_homes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id')->index();
                $table->date('start_date')->index();
                $table->date('end_date')->index();
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

    public function index(Request $request)
    {
        $this->ensureTableExists();

        $user = Auth::user();
        $isEmployee = $user->type === 'employee';
        $canApprove = !$isEmployee && $this->canApprove();
        $statusFilter = $request->get('status', 'All');

        $query = WorkFromHome::with(['employee', 'actionUser'])
            ->where('created_by', $user->creatorId())
            ->orderByRaw("CASE WHEN status = 'Pending' THEN 0 ELSE 1 END")
            ->orderBy('start_date', 'desc')
            ->orderBy('id', 'desc');

        if ($isEmployee) {
            $query->where('employee_id', $user->employee->id ?? 0);
        }

        if (in_array($statusFilter, ['Pending', 'Approved', 'Rejected'], true)) {
            $query->where('status', $statusFilter);
        }

        $wfhRequests = $query->get();

        $countsQuery = WorkFromHome::where('created_by', $user->creatorId());
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

        $employees = collect();
        if ($canApprove) {
            $terminatedIds = Termination::pluck('employee_id')->toArray();
            $employees = Employee::where('created_by', $user->creatorId())
                ->whereNotIn('id', $terminatedIds)
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        return view('attendance.wfh', [
            'wfhRequests'  => $wfhRequests,
            'isEmployee'   => $isEmployee,
            'canApprove'   => $canApprove,
            'statusFilter' => $statusFilter,
            'counts'       => $counts,
            'employees'    => $employees,
        ]);
    }

    public function store(Request $request)
    {
        $this->ensureTableExists();

        $user = Auth::user();
        $isEmployee = $user->type === 'employee';
        $canAssignDirectly = !$isEmployee && $this->canApprove();

        if ($isEmployee) {
            $employee = $user->employee;
            if (!$employee) {
                return redirect()->back()->with('error', __('Employee profile not found.'));
            }
            $request->validate([
                'start_date' => 'required|date|after_or_equal:today',
                'end_date'   => 'required|date|after_or_equal:start_date',
                'reason'     => 'required|string|min:3|max:1000',
            ]);
            $employeeId = $employee->id;
            $status = 'Pending';
        } elseif ($canAssignDirectly) {
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'start_date'  => 'required|date',
                'end_date'    => 'required|date|after_or_equal:start_date',
                'reason'      => 'required|string|min:3|max:1000',
            ]);
            $employee = Employee::findOrFail($request->employee_id);
            $employeeId = $employee->id;
            $status = 'Approved';
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $overlap = WorkFromHome::where('employee_id', $employeeId)
            ->whereIn('status', ['Pending', 'Approved'])
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_date', [$request->start_date, $request->end_date])
                  ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                  ->orWhere(function ($q2) use ($request) {
                      $q2->where('start_date', '<=', $request->start_date)
                         ->where('end_date', '>=', $request->end_date);
                  });
            })
            ->exists();

        if ($overlap) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('A Pending or Approved WFH entry already exists for the selected date range.'));
        }

        $wfh = WorkFromHome::create([
            'employee_id' => $employeeId,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'reason'      => trim($request->reason),
            'status'      => $status,
            'action_by'   => $status === 'Approved' ? $user->id : null,
            'action_at'   => $status === 'Approved' ? now() : null,
            'created_by'  => $user->creatorId(),
        ]);

        $dateLabel = $wfh->start_date === $wfh->end_date
            ? Carbon::parse($wfh->start_date)->format('d M Y')
            : Carbon::parse($wfh->start_date)->format('d M Y') . ' - ' . Carbon::parse($wfh->end_date)->format('d M Y');
        $targetUrl = route('attendance.wfh.index');
        $employeeName = $employee->full_name ?: $employee->name;

        if ($isEmployee) {
            $creatorId = $user->creatorId();
            $companyUserIds = User::where('type', 'company')
                ->where(function ($q) use ($creatorId) {
                    $q->where('id', $creatorId)->orWhere('created_by', $creatorId);
                })
                ->pluck('id')
                ->unique()
                ->values()
                ->toArray();

            dispatch(function () use ($companyUserIds, $employeeName, $dateLabel, $targetUrl, $employee, $wfh) {
                try {
                    if (!empty($companyUserIds)) {
                        app(\App\Services\FcmPushService::class)->sendToUsers(
                            $companyUserIds,
                            'Work From Home Request',
                            "{$employeeName} requested WFH for {$dateLabel}.",
                            [
                                'type' => 'wfh_requested',
                                'url'  => $targetUrl,
                            ]
                        );
                    }
                } catch (\Throwable $e) {
                    \Log::warning('WFH request FCM failed: ' . $e->getMessage());
                }

                try {
                    $hrEmail = env('HR_NOTIFICATION_EMAIL', 'hr@digitalizetheglobe.com');
                    $uArr = [
                        'leave_email'      => $employee->email ?? '',
                        'leave_status_name'=> $employeeName,
                        'leave_status'     => 'WFH Requested',
                        'leave_reason'     => $wfh->reason,
                        'leave_start_date' => $wfh->start_date,
                        'leave_end_date'   => $wfh->end_date,
                        'total_leave_days' => Carbon::parse($wfh->start_date)->diffInDays(Carbon::parse($wfh->end_date)) + 1,
                    ];
                    Utility::sendEmailTemplate('leave_status', [$hrEmail], $uArr);
                } catch (\Throwable $e) {
                    \Log::warning('WFH request email failed: ' . $e->getMessage());
                }
            })->afterResponse();
        } else {
            // Company directly assigned Approved WFH -> notify employee
            $this->notifyEmployeeWfhStatus($employee, $wfh, 'Approved', $dateLabel, $targetUrl);
        }

        return redirect()->route('attendance.wfh.index')
            ->with('success', $isEmployee
                ? __('Work From Home request submitted successfully.')
                : __('Work From Home assigned and approved successfully.'));
    }

    public function approve($id)
    {
        $this->ensureTableExists();

        $user = Auth::user();
        if ($user->type === 'employee' || !$this->canApprove()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $wfh = WorkFromHome::where('id', $id)
            ->where('created_by', $user->creatorId())
            ->firstOrFail();

        if ($wfh->status !== 'Pending') {
            return redirect()->back()->with('error', __('This request has already been processed.'));
        }

        $wfh->status = 'Approved';
        $wfh->action_by = $user->id;
        $wfh->action_at = now();
        $wfh->save();

        $employee = Employee::find($wfh->employee_id);
        $dateLabel = $wfh->start_date === $wfh->end_date
            ? Carbon::parse($wfh->start_date)->format('d M Y')
            : Carbon::parse($wfh->start_date)->format('d M Y') . ' - ' . Carbon::parse($wfh->end_date)->format('d M Y');
        if ($employee) {
            $this->notifyEmployeeWfhStatus($employee, $wfh, 'Approved', $dateLabel, route('attendance.wfh.index'));
        }

        return redirect()->route('attendance.wfh.index')
            ->with('success', __('Work From Home request approved.'));
    }

    public function reject(Request $request, $id)
    {
        $this->ensureTableExists();

        $user = Auth::user();
        if ($user->type === 'employee' || !$this->canApprove()) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $wfh = WorkFromHome::where('id', $id)
            ->where('created_by', $user->creatorId())
            ->firstOrFail();

        if (!in_array($wfh->status, ['Pending', 'Approved'], true)) {
            return redirect()->back()->with('error', __('This request is already rejected.'));
        }

        $wfh->status = 'Rejected';
        $wfh->rejection_reason = trim((string) $request->input('rejection_reason', ''));
        $wfh->action_by = $user->id;
        $wfh->action_at = now();
        $wfh->save();

        $employee = Employee::find($wfh->employee_id);
        $dateLabel = $wfh->start_date === $wfh->end_date
            ? Carbon::parse($wfh->start_date)->format('d M Y')
            : Carbon::parse($wfh->start_date)->format('d M Y') . ' - ' . Carbon::parse($wfh->end_date)->format('d M Y');
        if ($employee) {
            $this->notifyEmployeeWfhStatus($employee, $wfh, 'Rejected', $dateLabel, route('attendance.wfh.index'));
        }

        return redirect()->route('attendance.wfh.index')
            ->with('success', __('Work From Home request rejected.'));
    }

    private function notifyEmployeeWfhStatus(Employee $employee, WorkFromHome $wfh, string $status, string $dateLabel, string $targetUrl): void
    {
        $empUserId = $employee->user_id;
        $empEmail = $employee->email;
        $empName = $employee->full_name ?: $employee->name;
        $uArr = [
            'leave_email'       => $empEmail ?? '',
            'leave_status_name' => $empName,
            'leave_status'      => 'WFH ' . $status,
            'leave_reason'      => $wfh->reason,
            'leave_start_date'  => $wfh->start_date,
            'leave_end_date'    => $wfh->end_date,
            'total_leave_days'  => Carbon::parse($wfh->start_date)->diffInDays(Carbon::parse($wfh->end_date)) + 1,
        ];

        dispatch(function () use ($empUserId, $empEmail, $status, $dateLabel, $targetUrl, $uArr) {
            try {
                if ($empUserId) {
                    app(\App\Services\FcmPushService::class)->sendToUsers(
                        [$empUserId],
                        'Work From Home ' . $status,
                        "Your WFH for {$dateLabel} has been {$status}.",
                        [
                            'type' => 'wfh_' . strtolower($status),
                            'url'  => $targetUrl,
                        ]
                    );
                }
            } catch (\Throwable $e) {
                \Log::warning('WFH status FCM failed: ' . $e->getMessage());
            }

            try {
                if (!empty($empEmail)) {
                    Utility::sendEmailTemplate('leave_status', [$empEmail], $uArr);
                }
            } catch (\Throwable $e) {
                \Log::warning('WFH status email failed: ' . $e->getMessage());
            }
        })->afterResponse();
    }
}

