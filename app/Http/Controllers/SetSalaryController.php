<?php

namespace App\Http\Controllers;

use App\Models\AccountList;
use App\Models\Allowance;
use App\Models\AllowanceOption;
use App\Models\Commission;
use App\Models\DeductionOption;
use App\Models\Employee;
use App\Models\Loan;
use App\Models\LoanOption;
use App\Models\OtherPayment;
use App\Models\Overtime;
use App\Models\PayslipType;
use App\Models\SaturationDeduction;
use App\Models\IncrementLetter;
use App\Models\SalaryIncrement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Barryvdh\DomPDF\Facade\Pdf;

class SetSalaryController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('Manage Set Salary')) {
            $employees = Employee::where('created_by', \Auth::user()->creatorId())
                ->whereHas('user', function($query) {
                    $query->where('type', 'employee');
                })
                ->get();

            return view('setsalary.index', compact('employees'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($id)
    {
        if (\Auth::user()->can('Edit Set Salary')) {

            $payslip_type      = PayslipType::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $allowance_options = AllowanceOption::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $loan_options      = LoanOption::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $deduction_options = DeductionOption::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            if (\Auth::user()->type == 'employee') {
                $currentEmployee      = Employee::where('user_id', '=', \Auth::user()->id)->first();
                $allowances           = Allowance::where('employee_id', $currentEmployee->id)->get();
                $commissions          = Commission::where('employee_id', $currentEmployee->id)->get();
                $loans                = Loan::where('employee_id', $currentEmployee->id)->get();
                $saturationdeductions = SaturationDeduction::where('employee_id', $currentEmployee->id)->get();
                $otherpayments        = OtherPayment::where('employee_id', $currentEmployee->id)->get();
                $overtimes            = Overtime::where('employee_id', $currentEmployee->id)->get();
                $employee             = Employee::where('user_id', '=', \Auth::user()->id)->first();

                return view('setsalary.employee_salary', compact('employee', 'payslip_type', 'allowance_options', 'commissions', 'loan_options', 'overtimes', 'otherpayments', 'saturationdeductions', 'loans', 'deduction_options', 'allowances'));
            } else {
                $allowances           = Allowance::where('employee_id', $id)->get();
                $commissions          = Commission::where('employee_id', $id)->get();
                $loans                = Loan::where('employee_id', $id)->get();
                $saturationdeductions = SaturationDeduction::where('employee_id', $id)->get();
                $otherpayments        = OtherPayment::where('employee_id', $id)->get();
                $overtimes            = Overtime::where('employee_id', $id)->get();
                $employee             = Employee::find($id);

                return view('setsalary.edit', compact('employee', 'payslip_type', 'allowance_options', 'commissions', 'loan_options', 'overtimes', 'otherpayments', 'saturationdeductions', 'loans', 'deduction_options', 'allowances'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show($id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
        
        $payslip_type      = PayslipType::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $allowance_options = AllowanceOption::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $loan_options      = LoanOption::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $deduction_options = DeductionOption::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        if (\Auth::user()->type == 'employee') {
            $currentEmployee      = Employee::where('user_id', '=', \Auth::user()->id)->first();
            $allowances           = Allowance::where('employee_id', $currentEmployee->id)->get();
            $commissions          = Commission::where('employee_id', $currentEmployee->id)->get();
            $loans                = Loan::where('employee_id', $currentEmployee->id)->get();
            $saturationdeductions = SaturationDeduction::where('employee_id', $currentEmployee->id)->get();
            $otherpayments        = OtherPayment::where('employee_id', $currentEmployee->id)->get();
            $overtimes            = Overtime::where('employee_id', $currentEmployee->id)->get();
            $employee             = Employee::where('user_id', '=', \Auth::user()->id)->first();

            foreach ($allowances as  $value) {
                if ($value->type == 'percentage') {
                    $employee          = Employee::find($value->employee_id);
                    $empsal  = $value->amount * $employee->salary / 100;
                    $value->tota_allow = $empsal;
                }
            }

            foreach ($commissions as  $value) {
                if ($value->type == 'percentage') {
                    $employee          = Employee::find($value->employee_id);
                    $empsal  = $value->amount * $employee->salary / 100;
                    $value->tota_allow = $empsal;
                }
            }

            foreach ($loans as  $value) {
                if ($value->type == 'percentage') {
                    $employee          = Employee::find($value->employee_id);
                    $empsal  = $value->amount * $employee->salary / 100;
                    $value->tota_allow = $empsal;
                }
            }

            foreach ($saturationdeductions as  $value) {
                if ($value->type == 'percentage') {
                    $employee          = Employee::find($value->employee_id);
                    $empsal  = $value->amount * $employee->salary / 100;
                    $value->tota_allow = $empsal;
                }
            }

            foreach ($otherpayments as  $value) {
                if ($value->type == 'percentage') {
                    $employee          = Employee::find($value->employee_id);
                    $empsal  = $value->amount * $employee->salary / 100;
                    $value->tota_allow = $empsal;
                }
            }


            return view('setsalary.employee_salary', compact('employee', 'payslip_type', 'allowance_options', 'commissions', 'loan_options', 'overtimes', 'otherpayments', 'saturationdeductions', 'loans', 'deduction_options', 'allowances'));
        } else {
            $allowances           = Allowance::where('employee_id', $id)->get();
            $commissions          = Commission::where('employee_id', $id)->get();
            $loans                = Loan::where('employee_id', $id)->get();
            $saturationdeductions = SaturationDeduction::where('employee_id', $id)->get();
            $otherpayments        = OtherPayment::where('employee_id', $id)->get();
            $overtimes            = Overtime::where('employee_id', $id)->get();
            $employee             = Employee::find($id);

            foreach ($allowances as  $value) {
                if ($value->type == 'percentage') {
                    $employee          = Employee::find($value->employee_id);
                    $empsal  = $value->amount * $employee->salary / 100;
                    $value->tota_allow = $empsal;
                }
            }

            foreach ($commissions as  $value) {
                if ($value->type == 'percentage') {
                    $employee          = Employee::find($value->employee_id);
                    $empsal  = $value->amount * $employee->salary / 100;
                    $value->tota_allow = $empsal;
                }
            }

            foreach ($loans as  $value) {
                if ($value->type == 'percentage') {
                    $employee          = Employee::find($value->employee_id);
                    $empsal  = $value->amount * $employee->salary / 100;
                    $value->tota_allow = $empsal;
                }
            }

            foreach ($saturationdeductions as  $value) {
                if ($value->type == 'percentage') {
                    $employee          = Employee::find($value->employee_id);
                    $empsal  = $value->amount * $employee->salary / 100;
                    $value->tota_allow = $empsal;
                }
            }

            foreach ($otherpayments as  $value) {
                if ($value->type == 'percentage') {
                    $employee          = Employee::find($value->employee_id);
                    $empsal  = $value->amount * $employee->salary / 100;
                    $value->tota_allow = $empsal;
                }
            }

            return view('setsalary.employee_salary', compact('employee', 'payslip_type', 'allowance_options', 'commissions', 'loan_options', 'overtimes', 'otherpayments', 'saturationdeductions', 'loans', 'deduction_options', 'allowances'));
        }
    }


    public function employeeUpdateSalary(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'salary_type' => 'nullable|exists:payslip_types,id',
                'salary' => 'required',
                'account_type' => 'nullable|exists:account_lists,id',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            // Return JSON response for AJAX requests
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $messages->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            return redirect()->back()->with('error', $messages->first());
        }
        $employee = Employee::findOrFail($id);
        
        // Only update specific fields instead of using fill() with all request data
        $employee->salary = $request->salary;
        
        // Only update salary_type if it's provided
        if ($request->has('salary_type')) {
            $employee->salary_type = $request->salary_type;
        }
        
        // Only update account_type if it's provided
        if ($request->has('account_type')) {
            $employee->account_type = $request->account_type;
        }
        
        $employee->save();

        // Return JSON response for AJAX requests
        if ($request->ajax()) {
            // Refresh the employee data to get updated values
            $employee->refresh();
            return response()->json([
                'success' => true,
                'message' => 'Employee Salary Updated.',
                'salary' => \Auth::user()->priceFormat($employee->salary),
                'net_salary' => !empty($employee->get_net_salary()) ? \Auth::user()->priceFormat($employee->get_net_salary()) : '-',
                'salary_type' => !empty($employee->salary_type()) ? $employee->salary_type() : '-'
            ]);
        }

        return redirect()->back()->with('success', 'Employee Salary Updated.');
    }

    public function employeeSalary()
    {
        if (\Auth::user()->type == "employee") {
            $employees = Employee::where('user_id', \Auth::user()->id)->get();
            return view('setsalary.index', compact('employees'));
        }
    }

    public function employeeBasicSalary($id)
    {
        $payslip_type = PayslipType::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $payslip_type->prepend('Select Payslip Type', '');
        $accounts = AccountList::where('created_by', \Auth::user()->creatorId())->get()->pluck('account_name', 'id');
        $accounts->prepend('Select Account Type', '');

        $employee     = Employee::find($id);

        return view('setsalary.basic_salary', compact('employee', 'payslip_type', 'accounts'));
    }

    public function showIncrementForm($employee_id)
    {
        $employee = Employee::findOrFail($employee_id);
        return view('setsalary.increment_form', compact('employee'));
    }

    public function storeIncrement(Request $request, $employee_id)
    {
        $employee = Employee::findOrFail($employee_id);

        $request->validate([
            'new_salary' => 'required|numeric|min:0',
            'month_of_effective_date' => 'required|date',
        ]);

        $old_salary = $employee->salary;
        $new_salary = $request->new_salary;
        $increment_amount = $new_salary - $old_salary;

        // Save increment record
        $increment = SalaryIncrement::create([
            'employee_id' => $employee->id,
            'old_salary' => $old_salary,
            'new_salary' => $new_salary,
            'increment_amount' => $increment_amount,
            'month_of_effective_date' => $request->month_of_effective_date,
            'created_by' => auth()->id(),
        ]);

        // Update employee salary
        $employee->salary = $new_salary;
        $employee->save();

        return redirect()->back()->with('success', 'Salary incremented successfully.');
    }

    public function incrementLetterPdf($id)
    {
        // Get the increment data (adapt from your existing downloadIncrementLetter method)
        $increment = SalaryIncrement::find($id);
        $employee = $increment->employee;
        $app_name = env('APP_NAME');
        $date = date('d-m-Y'); // DD-MM-YYYY format

        // Get first name only
        $employeeFirstName = !empty($employee->name) ? explode(' ', $employee->name)[0] : '';
        
        // Format amounts as ₹amount/-
        $formattedOldSalary = '₹' . number_format($increment->old_salary, 2) . '/-';
        $formattedNewSalary = '₹' . number_format($increment->new_salary, 2) . '/-';
        $formattedIncrementAmount = '₹' . number_format($increment->increment_amount, 2) . '/-';

        // Get the increment letter template (you'll need to create this)
        $incrementLetter = IncrementLetter::where('created_by', \Auth::user()->creatorId())
                                        ->where('lang', \Auth::user()->currentLanguage())
                                        ->first();

        // Prepare content with variables
        $content = IncrementLetter::replaceVariable($incrementLetter->content, [
            'employee_name' => $employeeFirstName,
            'designation' => $employee->designation->name ?? '',
            'department' => $employee->department->name ?? '',
            'date' => $date,
            'app_name' => $app_name,
            'old_salary' => $formattedOldSalary,
            'new_salary' => $formattedNewSalary,
            'increment_amount' => $formattedIncrementAmount,
            'month_of_effective_date' => $increment->month_of_effective_date
        ]);

        return view('setsalary.increment_letter_pdf', [
            'content' => $content,
            'employee' => $employee,
            'increment' => $increment,
            'app_name' => $app_name,
            'date' => $date,
            'employeeFirstName' => $employeeFirstName,
            'formattedOldSalary' => $formattedOldSalary,
            'formattedNewSalary' => $formattedNewSalary,
            'formattedIncrementAmount' => $formattedIncrementAmount
        ]);
    }

    public function incrementLetterDoc($id)
    {
        // Same logic as above but return DOC view
        $increment = SalaryIncrement::find($id);
        $employee = $increment->employee;
        $app_name = env('APP_NAME');
        $date = date('d-m-Y'); // DD-MM-YYYY format

        // Get first name only
        $employeeFirstName = !empty($employee->name) ? explode(' ', $employee->name)[0] : '';
        
        // Format amounts as ₹amount/-
        $formattedOldSalary = '₹' . number_format($increment->old_salary, 2) . '/-';
        $formattedNewSalary = '₹' . number_format($increment->new_salary, 2) . '/-';
        $formattedIncrementAmount = '₹' . number_format($increment->increment_amount, 2) . '/-';

        $incrementLetter = IncrementLetter::where('created_by', \Auth::user()->creatorId())
                                        ->where('lang', \Auth::user()->currentLanguage())
                                        ->first();

        $content = IncrementLetter::replaceVariable($incrementLetter->content, [
            'employee_name' => $employeeFirstName,
            'designation' => $employee->designation->name ?? '',
            'department' => $employee->department->name ?? '',
            'date' => $date,
            'app_name' => $app_name,
            'old_salary' => $formattedOldSalary,
            'new_salary' => $formattedNewSalary,
            'increment_amount' => $formattedIncrementAmount,
            'month_of_effective_date' => $increment->month_of_effective_date
        ]);

        return view('setsalary.increment_letter_doc', [
            'content' => $content,
            'employee' => $employee,
            'increment' => $increment,
            'app_name' => $app_name,
            'date' => $date,
            'employeeFirstName' => $employeeFirstName,
            'formattedOldSalary' => $formattedOldSalary,
            'formattedNewSalary' => $formattedNewSalary,
            'formattedIncrementAmount' => $formattedIncrementAmount
        ]);
    }

    public function salaryPopup($id)
    {
        try {
            $id = Crypt::decrypt($id);
        } catch (\Throwable $th) {
            return response()->json(['error' => __('Permission Denied.')], 403);
        }
        
        $payslip_type      = PayslipType::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $allowance_options = AllowanceOption::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $loan_options      = LoanOption::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        $deduction_options = DeductionOption::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
        
        $allowances           = Allowance::where('employee_id', $id)->get();
        $commissions          = Commission::where('employee_id', $id)->get();
        $loans                = Loan::where('employee_id', $id)->get();
        $saturationdeductions = SaturationDeduction::where('employee_id', $id)->get();
        $otherpayments        = OtherPayment::where('employee_id', $id)->get();
        $overtimes            = Overtime::where('employee_id', $id)->get();
        $employee             = Employee::find($id);

        // Calculate percentage-based amounts
        foreach ($allowances as $value) {
            if ($value->type == 'percentage') {
                $empsal = $value->amount * $employee->salary / 100;
                $value->tota_allow = $empsal;
            }
        }

        foreach ($commissions as $value) {
            if ($value->type == 'percentage') {
                $empsal = $value->amount * $employee->salary / 100;
                $value->tota_allow = $empsal;
            }
        }

        foreach ($loans as $value) {
            if ($value->type == 'percentage') {
                $empsal = $value->amount * $employee->salary / 100;
                $value->tota_allow = $empsal;
            }
        }

        foreach ($saturationdeductions as $value) {
            if ($value->type == 'percentage') {
                $empsal = $value->amount * $employee->salary / 100;
                $value->tota_allow = $empsal;
            }
        }

        foreach ($otherpayments as $value) {
            if ($value->type == 'percentage') {
                $empsal = $value->amount * $employee->salary / 100;
                $value->tota_allow = $empsal;
            }
        }

        return view('setsalary.salary_popup', compact('employee', 'payslip_type', 'allowance_options', 'commissions', 'loan_options', 'overtimes', 'otherpayments', 'saturationdeductions', 'loans', 'deduction_options', 'allowances'));
    }


}
