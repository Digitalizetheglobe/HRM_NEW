<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeLoan;
use App\Models\LoanDeduction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function index()
    {
        try {
            if(\Auth::user()->can('Manage Employee')) {
                $loans = EmployeeLoan::with(['employee', 'deductions' => function($q) {
                        $q->orderBy('month', 'asc');
                    }])
                    ->where('created_by', \Auth::user()->creatorId())
                    ->get();
                
                // Ensure remaining_amount is calculated correctly for each loan
                foreach ($loans as $loan) {
                    $totalDeducted = $loan->deductions->where('is_deducted', true)->sum('emi_amount');
                    $calculatedRemaining = $loan->total_amount - $totalDeducted;
                    
                    // Get the raw database value (before accessor)
                    $storedRemaining = $loan->getAttributes()['remaining_amount'] ?? $loan->total_amount;
                    
                    // Update in database if different (but don't trigger events)
                    if (abs($storedRemaining - $calculatedRemaining) > 0.01) {
                        DB::table($loan->getTable())
                            ->where('id', $loan->id)
                            ->update(['remaining_amount' => $calculatedRemaining]);
                        // Update the model's attribute
                        $loan->setAttribute('remaining_amount', $calculatedRemaining);
                    } else {
                        // Even if same, ensure the accessor has the correct value
                        $loan->setAttribute('remaining_amount', $calculatedRemaining);
                    }
                }
                    
                return view('loan.index', compact('loans'));
            }
            return redirect()->back()->with('error', __('Permission denied.'));
        } catch (\Exception $e) {
            Log::error('LoanController index error: ' . $e->getMessage());
            return redirect()->back()->with('error', __('Something went wrong.'));
        }
    }

    public function create()
    {
        try {
            if(\Auth::user()->can('Create Employee')) {
                $employees = Employee::where('created_by', \Auth::user()->creatorId())->get()->mapWithKeys(function ($employee) {
                    return [$employee->id => $employee->full_name];
                });
                return view('loan.create', compact('employees'));
            }
            return redirect()->back()->with('error', __('Permission denied.'));
        } catch (\Exception $e) {
            Log::error('LoanController create error: ' . $e->getMessage());
            return redirect()->back()->with('error', __('Something went wrong.'));
        }
    }

    public function store(Request $request)
    {
        // Authorization check
        if (!\Auth::user()->can('Create Employee')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        DB::beginTransaction();
        try {
            \Log::info('Loan Creation Request:', $request->all());

            $validator = \Validator::make(
                $request->all(), [
                    'employee_id' => 'required|exists:employees,id',
                    'total_amount' => 'required|numeric|min:1',
                    'number_of_months' => 'required|integer|min:1',
                    'start_month' => 'required|date_format:Y-m',
                    'reason' => 'nullable|string|max:255',
                ],
                [
                    'employee_id.exists' => __('Selected employee does not exist'),
                    'start_month.date_format' => __('Invalid month format. Use YYYY-MM'),
                ]
            );

            if ($validator->fails()) {
                \Log::error('Validation failed:', $validator->errors()->toArray());
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Parse start month (format: YYYY-MM)
            $startMonth = Carbon::createFromFormat('Y-m', $request->start_month)->startOfMonth();
            $monthlyEmi = round($request->total_amount / $request->number_of_months, 2);

            \Log::debug("Creating loan with params:", [
                'employee_id' => $request->employee_id,
                'start_month' => $startMonth,
                'monthly_emi' => $monthlyEmi
            ]);

            $loan = EmployeeLoan::create([
                'employee_id' => $request->employee_id,
                'total_amount' => $request->total_amount,
                'number_of_months' => $request->number_of_months,
                'monthly_emi' => $monthlyEmi,
                'start_month' => $startMonth,
                'remaining_amount' => $request->total_amount,
                'created_by' => \Auth::user()->creatorId(),
                'reason' => $request->reason,
                'extended_months' => 0,
            ]);

            \Log::info("Loan created with ID: {$loan->id}");

            // Create monthly deductions
            $deductions = [];
            for ($i = 0; $i < $request->number_of_months; $i++) {
                $deductionMonth = $startMonth->copy()->addMonths($i);
                
                $deductions[] = [
                    'loan_id' => $loan->id,
                    'month' => $deductionMonth,
                    'emi_amount' => $monthlyEmi,
                    'is_deducted' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Bulk insert for better performance
            LoanDeduction::insert($deductions);
            \Log::info("Created {$request->number_of_months} deduction records");

            DB::commit();

            return redirect()->route('loan.index')
                ->with('success', __('Loan successfully created.'));

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Loan Creation Failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);
            
            return redirect()->back()
                ->with('error', __('Loan creation failed: ') . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        try {
            if(\Auth::user()->can('Show Employee')) {
                $loan = EmployeeLoan::with(['employee', 'deductions'])->findOrFail($id);
                
                // Convert dates to Carbon instances and calculate original end month
                $loan->start_month = \Carbon\Carbon::parse($loan->start_month);
                $loan->original_end_month = $loan->start_month->copy()->addMonths($loan->number_of_months - 1);
                
                return view('loan.show', compact('loan'));
            }
            return redirect()->back()->with('error', __('Permission denied.'));
        } catch (\Exception $e) {
            Log::error('LoanController show error: ' . $e->getMessage());
            return redirect()->back()->with('error', __('Loan not found.'));
        }
    }

    public function editDeduction(LoanDeduction $deduction)
    {
        try {
            if(\Auth::user()->can('Edit Employee')) {
                return view('loan.edit_deduction', compact('deduction'));
            }
            return redirect()->back()->with('error', __('Permission denied.'));
        } catch (\Exception $e) {
            Log::error('LoanController editDeduction error: ' . $e->getMessage());
            return redirect()->back()->with('error', __('Failed to load deduction details.'));
        }
    }

public function updateDeduction(Request $request, LoanDeduction $deduction)
{
    // Authorization check
    if (!\Auth::user()->can('Edit Employee')) {
        return redirect()->back()->with('error', __('Permission denied.'));
    }

    DB::beginTransaction();
    try {
        // Eager load the loan with minimal fields
        $loan = EmployeeLoan::with(['deductions' => function($q) {
                $q->where('is_deducted', true);
            }])
            ->select('id', 'total_amount', 'remaining_amount', 'monthly_emi', 'number_of_months', 'extended_months')
            ->where('id', $deduction->loan_id)
            ->firstOrFail();

        $isDeducted = $request->is_deducted === "1";
        $currentStatus = $deduction->is_deducted;
        $currentRemark = $deduction->remark;

        \Log::debug('Deduction update initiated', [
            'loan_id' => $loan->id,
            'deduction_id' => $deduction->id,
            'action' => $isDeducted ? 'Mark as Deducted' : 'Defer Payment',
            'current_status' => $currentStatus,
            'current_remark' => $currentRemark
        ]);

        // ===== CASE 1: MARK AS DEDUCTED =====
        if ($isDeducted) {
            // If already deducted, no action needed
            if ($currentStatus) {
                DB::commit();
                return redirect()->route('loan.show', $loan->id)
                    ->with('info', __('Deduction was already marked as paid.'));
            }

            // Validate sufficient remaining amount
            if ($loan->remaining_amount < $loan->monthly_emi) {
                throw new \Exception(__('Remaining amount insufficient for this deduction.'));
            }

            // Update the deduction record
            $deduction->update([
                'is_deducted' => true,
                'remark' => null,
                'updated_at' => now()
            ]);

            // Handle deferred payment reversal
            $extendedMonthsToDecrement = 0;
            if ($currentRemark === 'No Deduction') {
                $extraDeduction = LoanDeduction::where('loan_id', $loan->id)
                    ->where('id', '!=', $deduction->id)
                    ->where('is_deducted', false)
                    ->orderByDesc('month')
                    ->first();

                if ($extraDeduction) {
                    $extraDeduction->delete();
                    $extendedMonthsToDecrement = 1;
                    \Log::debug('Removed extended deduction month', ['deduction_id' => $extraDeduction->id]);
                }
            }

            // Update loan status using direct DB update to avoid event loops
            $totalDeducted = $loan->deductions()->where('is_deducted', true)->sum('emi_amount');
            $newRemaining = $loan->total_amount - $totalDeducted;
            $currentExtended = ($loan->extended_months ?? 0) - $extendedMonthsToDecrement;
            
            DB::table($loan->getTable())
                ->where('id', $loan->id)
                ->update([
                    'remaining_amount' => $newRemaining,
                    'extended_months' => max(0, $currentExtended) // Ensure it doesn't go negative
                ]);
            
            // Refresh the loan model
            $loan->refresh();
            
            \Log::debug('Loan amounts updated after deduction', [
                'remaining_amount' => $loan->remaining_amount,
                'total_deducted' => $totalDeducted
            ]);
        }
        // ===== CASE 2: DEFER PAYMENT =====
        else {
            // If already deferred, no action needed
            if (!$currentStatus && $currentRemark === 'No Deduction') {
                DB::commit();
                return redirect()->route('loan.show', $loan->id)
                    ->with('info', __('Deduction was already deferred.'));
            }

            // Validate not already deducted
            if ($currentStatus) {
                throw new \Exception(__('Cannot defer already deducted payment.'));
            }

            // Update current deduction
            $deduction->update([
                'is_deducted' => false,
                'remark' => 'No Deduction',
                'updated_at' => now()
            ]);

            // Add new month at end
            $lastMonth = LoanDeduction::where('loan_id', $loan->id)
                ->orderByDesc('month')
                ->value('month');

            $newMonth = Carbon::parse($lastMonth)->addMonth();

            LoanDeduction::create([
                'loan_id' => $loan->id,
                'month' => $newMonth,
                'emi_amount' => $loan->monthly_emi,
                'is_deducted' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Update loan status using direct DB update to avoid event loops
            // Get current extended_months value
            $currentExtended = $loan->extended_months ?? 0;
            
            // Update extended_months and remaining_amount in one query
            $totalDeducted = $loan->deductions()->where('is_deducted', true)->sum('emi_amount');
            $newRemaining = $loan->total_amount - $totalDeducted;
            
            DB::table($loan->getTable())
                ->where('id', $loan->id)
                ->update([
                    'extended_months' => $currentExtended + 1,
                    'remaining_amount' => $newRemaining
                ]);
            
            // Refresh the loan model
            $loan->refresh();
            
            \Log::debug('Loan extended', [
                'new_month' => $newMonth->format('Y-m'),
                'extended_months' => $loan->extended_months
            ]);
        }

        DB::commit();

        return redirect()->route('loan.show', $loan->id)
            ->with('success', __('Deduction status updated successfully.'));

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Deduction update failed', [
            'error' => $e->getMessage(),
            'deduction_id' => $deduction->id,
            'trace' => $e->getTraceAsString()
        ]);
        
        return redirect()->back()
            ->with('error', __('Action failed: ') . $e->getMessage())
            ->withInput();
    }
}


    public function destroy($id)
    {
        try {
            if(\Auth::user()->can('Delete Employee')) {
                $loan = EmployeeLoan::findOrFail($id);
                $loan->delete();
                return redirect()->route('loan.index')->with('success', __('Loan successfully deleted.'));
            }
            return redirect()->back()->with('error', __('Permission denied.'));
        } catch (\Exception $e) {
            Log::error('LoanController destroy error: ' . $e->getMessage());
            return redirect()->back()->with('error', __('Failed to delete loan.'));
        }
    }

}
