<?php
// app/Console/Commands/AllocateMonthlyLeaves.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\EmployeeLeaveBalance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class AllocateMonthlyLeaves extends Command
{
    protected $signature = 'leaves:allocate-monthly';
    protected $description = 'Allocate 2 leaves monthly to all employees (1 Earned Leave + 1 Sick Leave) with continuous carry forward across years';

    public function handle()
    {
        $now = Carbon::now();
        $year = $now->year;
        $month = $now->month;
        
        // Get all employees, grouped by creator_id to handle multi-tenant scenarios
        $employees = Employee::whereNotNull('created_by')->get();
        
        if ($employees->count() == 0) {
            $this->warn('No employees found.');
            return;
        }
        
        $this->info("Processing {$employees->count()} employees...");
        
        foreach ($employees as $employee) {
            // Check if employee has completed 3 months probation
            $eligibleForLeave = false;
            if ($employee->company_doj) {
                $doj = Carbon::parse($employee->company_doj);
                $monthsSinceJoining = ($now->year - $doj->year) * 12 + ($now->month - $doj->month);
                $eligibleForLeave = $monthsSinceJoining >= 3;
            }
            
            // Get all leave types for this company
            $leaveTypes = LeaveType::where('created_by', $employee->created_by)->get();
            
            foreach ($leaveTypes as $lt) {
                if ($lt->unlimited) {
                    continue;
                }
                
                if (strtolower(trim($lt->title)) == 'sick leave') {
                    continue; // Skip sick leave since it is shared under Earned Leave
                }
                
                $allocatedDays = $eligibleForLeave ? (float)$lt->days : 0.0;
                if (strtolower(trim($lt->title)) == 'earned leave') {
                    $allocatedDays = $eligibleForLeave ? 1.5 : 0.0;
                }
                $this->allocateLeaveForEmployee($employee, $lt, $allocatedDays, $year, $month, $now);
            }
        }
        
        $this->info('Monthly leaves allocated successfully.');
    }
    
 
    private function allocateLeaveForEmployee($employee, $leaveType, $allocatedDays, $year, $month, $now)
    {
        // Use the unified getOrCreateBalance which handles recursive carry forward and syncs used_days
        $balance = EmployeeLeaveBalance::getOrCreateBalance($employee, $leaveType, $now);
        if ($balance) {
            if ($balance->allocated_days != $allocatedDays) {
                $balance->allocated_days = $allocatedDays;
                $balance->save();
                // Update carry forward for this month and future months
                EmployeeLeaveBalance::updateCarryForward($employee, $leaveType, $now);
            }
        }
    }
}