<?php

// app/Models/EmployeeLeaveBalance.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Leave;
use App\Models\LeaveType;

class EmployeeLeaveBalance extends Model
{
    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'year',
        'month',
        'allocated_days',
        'used_days',
        'carry_forward_days'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    // Helper method to get available days
    public function getAvailableDaysAttribute()
    {
        return ($this->allocated_days + $this->carry_forward_days) - $this->used_days;
    }

    /**
     * Get or recursively create leave balance record with carry forward and sync used_days
     */
    public static function getOrCreateBalance($employee, $leaveType, $now)
    {
        if (!$employee || !$leaveType) {
            return null;
        }

        // Redirect Sick Leave to Earned Leave for shared balance
        if (strtolower(trim($leaveType->title)) == 'sick leave') {
            $earnedLeaveType = LeaveType::where('title', 'Earned Leave')->first();
            if ($earnedLeaveType) {
                $leaveType = $earnedLeaveType;
            }
        }

        $balance = self::where('employee_id', $employee->id)
            ->where('leave_type_id', $leaveType->id)
            ->where('year', $now->year)
            ->where('month', $now->month)
            ->first();

        $eligibleForLeave = true;
        if ($employee->company_doj) {
            $doj = \Carbon\Carbon::parse($employee->company_doj);
            $monthsSinceJoining = ($now->year - $doj->year) * 12 + ($now->month - $doj->month);
            $eligibleForLeave = $monthsSinceJoining >= 3;
        }

        $defaultAllocation = $eligibleForLeave ? (float)$leaveType->days : 0.0;
        if (strtolower(trim($leaveType->title)) == 'earned leave') {
            $defaultAllocation = $eligibleForLeave ? 1.5 : 0.0;
        }

        if (!$balance) {
            $balance = self::create([
                'employee_id' => $employee->id,
                'leave_type_id' => $leaveType->id,
                'year' => $now->year,
                'month' => $now->month,
                'allocated_days' => $defaultAllocation,
                'used_days' => 0.0,
                'carry_forward_days' => 0.0,
            ]);
        } else {
            // Sync allocated_days if default changed
            if ($balance->allocated_days != $defaultAllocation && $eligibleForLeave) {
                $balance->allocated_days = $defaultAllocation;
                $balance->save();
            }
            if ($balance->allocated_days > 0 && !$eligibleForLeave) {
                $balance->allocated_days = 0.0;
                $balance->save();
            }
        }

        // Sync used_days dynamically
        // We sum approved leaves of BOTH Earned Leave and Sick Leave
        $sharedTypeIds = LeaveType::whereIn('title', ['Earned Leave', 'Sick Leave'])->pluck('id')->toArray();
        
        // Use clone of query for clean sum
        $actualUsed = Leave::where('employee_id', $employee->id)
            ->where('status', 'Approved')
            ->whereYear('start_date', $now->year)
            ->whereMonth('start_date', $now->month)
            ->where(function($query) use ($leaveType, $sharedTypeIds) {
                if (in_array($leaveType->id, $sharedTypeIds)) {
                    $query->whereIn('leave_type_id', $sharedTypeIds);
                } else {
                    $query->where('leave_type_id', $leaveType->id);
                }
            })
            ->sum('total_leave_days');

        if ($balance->used_days != $actualUsed) {
            $balance->used_days = $actualUsed;
            $balance->save();
        }

        self::updateCarryForward($employee, $leaveType, $now);
        $balance->refresh();

        return $balance;
    }

    /**
     * Calculate and update carry forward from previous month (January resets to 0)
     */
    public static function updateCarryForward($employee, $leaveType, $now)
    {
        if (!$employee || !$leaveType) {
            return;
        }

        // Redirect Sick Leave to Earned Leave
        if (strtolower(trim($leaveType->title)) == 'sick leave') {
            $earnedLeaveType = LeaveType::where('title', 'Earned Leave')->first();
            if ($earnedLeaveType) {
                $leaveType = $earnedLeaveType;
            }
        }

        $carryForward = 0.0;

        // Carry-forward only starts from June 2026 (so July 2026 or later can carry forward)
        $isAfterJune2026 = $now->year > 2026 || ($now->year == 2026 && $now->month > 6);

        // Carry-forward only happens within the current year (from January onwards, i.e., month > 1)
        if ($isAfterJune2026 && $now->month > 1) {
            $prevDate = $now->copy()->subMonth();
            // Call getOrCreateBalance recursively to calculate the previous month's balance and carry forward
            $previousMonthBalance = self::getOrCreateBalance($employee, $leaveType, $prevDate);

            if ($previousMonthBalance) {
                $previousAvailable = $previousMonthBalance->allocated_days + $previousMonthBalance->carry_forward_days - $previousMonthBalance->used_days;
                $carryForward = max(0.0, $previousAvailable);
            }
        }

        $currentMonthBalance = self::where('employee_id', $employee->id)
            ->where('leave_type_id', $leaveType->id)
            ->where('year', $now->year)
            ->where('month', $now->month)
            ->first();

        if ($currentMonthBalance) {
            if ($currentMonthBalance->carry_forward_days != $carryForward) {
                $currentMonthBalance->carry_forward_days = $carryForward;
                $currentMonthBalance->save();
            }
        }
    }
}