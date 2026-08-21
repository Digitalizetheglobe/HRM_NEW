<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LeaveType;
use App\Models\LeaveBalance;
use App\Models\MonthlyLeavePolicy;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LeaveCronController extends Controller
{
    public function creditMonthlyLeave()
    {
        Log::info('Starting monthly leave credit process...');
        
        $currentYear = now()->year;
        $defaultPolicies = MonthlyLeavePolicy::where('is_default', true)->get();
        
        $users = User::where('is_active', true)->get();
        
        foreach ($users as $user) {
            foreach ($defaultPolicies as $policy) {
                // Get user-specific policy if exists
                $userPolicy = MonthlyLeavePolicy::where('leave_type_id', $policy->leave_type_id)
                    ->where('user_id', $user->id)
                    ->first();
                    
                $daysToCredit = $userPolicy ? $userPolicy->days : $policy->days;
                
                // Get or create leave balance record
                $balance = LeaveBalance::firstOrNew([
                    'user_id' => $user->id,
                    'leave_type_id' => $policy->leave_type_id,
                    'year' => $currentYear,
                ]);
                
                // Handle carry forward if applicable
                $carryForward = 0;
                $leaveType = LeaveType::find($policy->leave_type_id);
                
                if ($leaveType->allow_carry_forward && $balance->exists) {
                    $carryForward = min(
                        $balance->remaining,
                        $leaveType->carry_forward_limit ?? PHP_FLOAT_MAX
                    );
                }
                
                // Update balance
                $balance->fill([
                    'total_earned' => ($balance->total_earned ?? 0) + $daysToCredit,
                    'remaining' => ($balance->remaining ?? 0) + $daysToCredit + $carryForward,
                    'carried_forward' => $carryForward,
                ])->save();
            }
        }
        
        Log::info('Monthly leave credit process completed.');
        return response()->json(['message' => 'Leave credited successfully']);
    }
}