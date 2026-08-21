<?php

namespace App\Http\Controllers;

use App\Exports\PayslipExport;
use App\Exports\SalaryProcessingExport;
use App\Models\Allowance;
use App\Models\Commission;
use App\Models\Employee;
use App\Models\Loan;
use App\Mail\InvoiceSend;
use App\Mail\PayslipSend;
use App\Mail\PayslipSendWithAttachment;
use App\Models\AccountList;
use App\Models\Expense;
use App\Models\OtherPayment;
use App\Models\Overtime;
use App\Models\PaySlip;
use App\Models\Resignation;
use App\Models\SaturationDeduction;
use App\Models\Termination;
use App\Models\Department;
use App\Models\User;
use App\Models\Utility;
use App\Models\AttendanceEmployee;
use App\Models\Leave as LocalLeave;
use App\Models\OtherDeduction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;


class PaySlipController extends Controller
{

    public function index()
    {
        if (\Auth::user()->can('Manage Pay Slip') || \Auth::user()->type == 'employee') {
            $terminatedEmployeeIds = Termination::where('created_by', \Auth::user()->creatorId())
                ->pluck('employee_id')
                ->unique()
                ->toArray();

            $employees = Employee::where('created_by', \Auth::user()->creatorId())
                ->whereHas('user', function($query) {
                    $query->where('type', 'employee');
                })
                ->whereNotIn('id', $terminatedEmployeeIds)
                ->get();

            $month_names = [
                '01' => 'JAN',
                '02' => 'FEB',
                '03' => 'MAR',
                '04' => 'APR',
                '05' => 'MAY',
                '06' => 'JUN',
                '07' => 'JUL',
                '08' => 'AUG',
                '09' => 'SEP',
                '10' => 'OCT',
                '11' => 'NOV',
                '12' => 'DEC',
            ];

            $months_list = [];
            $previous_year = date('Y') - 1;
            $current_year = date('Y');

            // Previous year months
            foreach ($month_names as $num => $name) {
                $months_list[$previous_year . '-' . $num] = $name . ' ' . $previous_year;
            }
            // Current year months
            foreach ($month_names as $num => $name) {
                $months_list[$current_year . '-' . $num] = $name . ' ' . $current_year;
            }

            // Lower card needs month and year dropdowns
            $month = $month_names;
            $currentyear = date("Y");
            $tempyear = intval($currentyear) - 2;
            $year = [];
            for ($i = 0; $i < 10; $i++) {
                $year[$tempyear + $i] = $tempyear + $i;
            }

            return view('payslip.index', compact('employees', 'months_list', 'month', 'year'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'employee_id' => 'required',
                'months' => 'required|array',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $employee_id = $request->employee_id;
        $months = $request->months;

        $success_count = 0;
        $webhook_called = false;

        foreach ($months as $month_year) {
            $parts = explode('-', $month_year);
            if (count($parts) !== 2) {
                continue;
            }
            $year = $parts[0];
            $month = $parts[1];
            $formate_month_year = $month_year;

            $validatePaysilp = PaySlip::where('salary_month', '=', $formate_month_year)
                ->where('created_by', \Auth::user()->creatorId())
                ->pluck('employee_id')
                ->toArray();

            $query = Employee::where('created_by', \Auth::user()->creatorId())
                ->whereHas('user', function($q) {
                    $q->where('type', 'employee');
                })
                ->where('company_doj', '<=', date($year . '-' . $month . '-t'))
                ->where('salary', '>', 0) // Only include employees with salary set
                ->whereNotNull('salary'); // Ensure salary is not null

            // Exclude terminated employees
            $query->whereNotIn('id', function($q) {
                $q->select('employee_id')->from('terminations');
            });

            // Filter for specific employee if not "all"
            if ($employee_id !== 'all') {
                $query->where('id', $employee_id);
            }

            // Exclude already created payslips for this month
            $query->whereNotIn('id', $validatePaysilp);

            $employees = $query->get();

            foreach ($employees as $employee) {
                // Skip if employee doesn't have salary set (safety check)
                if (empty($employee->salary) || $employee->salary <= 0) {
                    continue;
                }

                $chek = PaySlip::where(['employee_id' => $employee->id, 'salary_month' => $formate_month_year])->first();
                $terminationDate = Termination::where('employee_id', $employee->id)
                    ->whereDate('termination_date', '<=', Carbon::create($year, $month)->endOfMonth())
                    ->exists();

                $resignationDate = Resignation::where('employee_id', $employee->id)
                    ->whereDate('resignation_date', '<=', Carbon::create($year, $month)->endOfMonth())
                    ->exists();

                if ($terminationDate || $resignationDate) {
                    continue;
                }

                if (!$chek) {
                    $payslipEmployee                       = new PaySlip();
                    $payslipEmployee->employee_id          = $employee->id;
                    $payslipEmployee->net_payble           = $employee->get_net_salary();
                    $payslipEmployee->salary_month         = $formate_month_year;
                    $payslipEmployee->status               = 0;
                    $payslipEmployee->basic_salary         = !empty($employee->salary) ? $employee->salary : 0;
                    
                    // Calculate total allowance amount (not JSON string)
                    $allowances = \App\Models\Allowance::where('employee_id', '=', $employee->id)->get();
                    $totalAllowance = 0;
                    foreach ($allowances as $allowance) {
                        if (isset($allowance->type) && $allowance->type == 'percentage') {
                            $employeeForCalc = Employee::find($allowance->employee_id);
                            $totalAllowance += $allowance->amount * ($employeeForCalc->salary ?? 0) / 100;
                        } else {
                            $totalAllowance += $allowance->amount;
                        }
                    }
                    $payslipEmployee->allowance            = (float)$totalAllowance;
                    $payslipEmployee->commission           = Employee::commission($employee->id);
                    $payslipEmployee->loan                 = Employee::loan($employee->id, $formate_month_year);
                    $payslipEmployee->saturation_deduction = Employee::saturation_deduction($employee->id);
                    $payslipEmployee->other_payment        = Employee::other_payment($employee->id);
                    $payslipEmployee->overtime             = Employee::overtime($employee->id);
                    $payslipEmployee->created_by           = \Auth::user()->creatorId();
                    $payslipEmployee->save();

                    $success_count++;

                    // Automatically send payslip email to employee with PDF attachment
                    if (!empty($employee->email)) {
                        try {
                            $payslipEmployee->name = trim(($employee->name ?? '') . ' ' . ($employee->last_name ?? ''));
                            $payslipEmployee->email = $employee->email;
                            
                            $setings = Utility::settings();
                            
                            if ($setings['new_payroll'] == 1) {
                                // Generate PDF for attachment
                                $pdfContent = $this->generatePayslipPdf($payslipEmployee->id, $formate_month_year);
                                $pdfFileName = 'Payslip_' . $employee->employee_id . '_' . $formate_month_year . '.pdf';
                                
                                $uArr = [
                                    'payslip_email' => $payslipEmployee->email,
                                    'name' => $payslipEmployee->name,
                                    'salary_month' => $payslipEmployee->salary_month,
                                    'app_name' => 'Rising Spaces Pvt. Ltd.',
                                ];
                                
                                $this->sendPayslipEmailWithAttachment($payslipEmployee->email, $uArr, $pdfContent, $pdfFileName);
                            }
                        } catch (\Exception $e) {
                            \Log::error('Failed to send payslip email to employee: ' . $employee->id, [
                                'error' => $e->getMessage()
                            ]);
                        }
                    }

                    //Slack Notification
                    $setting  = Utility::settings(\Auth::user()->creatorId());
                    if (isset($setting['monthly_payslip_notification']) && $setting['monthly_payslip_notification'] == 1) {
                        $uArr = [
                            'year' => $formate_month_year,
                        ];
                        Utility::send_slack_msg('new_monthly_payslip', $uArr);
                    }
                    //Telegram Notification
                    $setting  = Utility::settings(\Auth::user()->creatorId());
                    if (isset($setting['telegram_monthly_payslip_notification']) && $setting['telegram_monthly_payslip_notification'] == 1) {
                        $uArr = [
                            'year' => $formate_month_year,
                        ];
                        Utility::send_telegram_msg('new_monthly_payslip', $uArr);
                    }

                    //twilio
                    $setting  = Utility::settings(\Auth::user()->creatorId());
                    $emp = Employee::where('id', $payslipEmployee->employee_id)->first();
                    if (isset($setting['twilio_monthly_payslip_notification']) && $setting['twilio_monthly_payslip_notification'] == 1) {
                        $uArr = [
                            'year' => $formate_month_year,
                        ];
                        Utility::send_twilio_msg($emp->phone, 'new_monthly_payslip', $uArr);
                    }

                    //webhook
                    $module = 'New Monthly Payslip';
                    $webhook =  Utility::webhookSetting($module);
                    if ($webhook) {
                        $parameter = json_encode($payslipEmployee);
                        $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                        if ($status == true) {
                            $webhook_called = true;
                        }
                    }
                }
            }
        }

        if ($success_count > 0) {
            if ($webhook_called) {
                return redirect()->back()->with('success', __('Payslip successfully created.'));
            }
            return redirect()->route('payslip.index')->with('success', sprintf(__('%d Payslip(s) successfully created.'), $success_count));
        } else {
            return redirect()->route('payslip.index')->with('error', __('Payslip already created for selected month(s) or no active employees with salary set found.'));
        }
    }

    public function destroy($id)
    {
        $payslip = PaySlip::find($id);

        $payslip->delete();

        return true;
    }

    public function showemployee($paySlip)
    {

        $payslip = PaySlip::find($paySlip);


        return view('payslip.show', compact('payslip'));
    }

    public function search_json(Request $request)
    {
        $formate_month_year = $request->datePicker;
        $validatePaysilp    = PaySlip::where('salary_month', '=', $formate_month_year)->where('created_by', \Auth::user()->creatorId())->get()->toarray();
        $data = [];
        if (empty($validatePaysilp)) {
            $data = [];
            return;
        } else {
            $paylip_employee = PaySlip::select(
                [
                    'employees.id',
                    'employees.employee_id',
                    'employees.name',
                    'employees.salary',
                    'payslip_types.name as payroll_type',
                    'pay_slips.basic_salary',
                    'pay_slips.net_payble',
                    'pay_slips.id as pay_slip_id',
                    'pay_slips.status',
                    'employees.user_id',
                ]
            )->leftjoin(
                'employees',
                function ($join) use ($formate_month_year) {
                    $join->on('employees.id', '=', 'pay_slips.employee_id');
                    $join->on('pay_slips.salary_month', '=', \DB::raw("'" . $formate_month_year . "'"));
                    $join->leftjoin('payslip_types', 'payslip_types.id', '=', 'employees.salary_type');
                }
            )->leftjoin('users', 'users.id', '=', 'employees.user_id')
            ->where('employees.created_by', \Auth::user()->creatorId())
            ->where('users.type', 'employee')
            ->where('employees.salary', '>', 0) // Only show employees with salary set
            ->whereNotNull('employees.salary') // Ensure salary is not null
            ->get();

            foreach ($paylip_employee as $employee) {
                if (Auth::user()->type == 'employee') {
                    if (Auth::user()->id == $employee->user_id) {
                        $tmp   = [];
                        $tmp[] = $employee->id;
                        $tmp[] = $employee->full_name;
                        $tmp[] = $employee->payroll_type;
                        $tmp[] = $employee->pay_slip_id;
                        $tmp[] = !empty($employee->basic_salary) ? \Auth::user()->priceFormat($employee->salary) : '-';
                        $tmp[] = !empty($employee->net_payble) ? \Auth::user()->priceFormat($employee->net_payble) : '-';
                        if ($employee->status == 1) {
                            $tmp[] = 'paid';
                        } else {
                            $tmp[] = 'unpaid';
                        }
                        $tmp[]  = !empty($employee->pay_slip_id) ? $employee->pay_slip_id : 0;
                        $tmp['url']  = route('employee.show', Crypt::encrypt($employee->id));
                        $data[] = $tmp;
                    }
                } else {

                    $tmp   = [];
                    $tmp[] = $employee->id;
                    $tmp[] = \Auth::user()->employeeIdFormat($employee->employee_id);
                    $tmp[] = $employee->name;
                    $tmp[] = $employee->payroll_type;
                    $tmp[] = !empty($employee->basic_salary) ? \Auth::user()->priceFormat($employee->basic_salary) : '-';
                    $tmp[] = !empty($employee->net_payble) ? \Auth::user()->priceFormat($employee->net_payble) : '-';
                    if ($employee->status == 1) {
                        $tmp[] = 'Paid';
                    } else {
                        $tmp[] = 'UnPaid';
                    }
                    $tmp[]  = !empty($employee->pay_slip_id) ? $employee->pay_slip_id : 0;
                    $tmp['url']  = route('employee.show', Crypt::encrypt($employee->id));
                    $data[] = $tmp;
                }
            }

            return $data;
        }
    }

    public function paysalary($id, $date)
    {
        $employeePayslip = PaySlip::where('employee_id', '=', $id)->where('created_by', \Auth::user()->creatorId())->where('salary_month', '=', $date)->first();
        $get_employee = Employee::where('id', $id)->where('created_by', \Auth::user()->creatorId())->first();
        $get_account = AccountList::where('id', $get_employee->account_type)->where('created_by', \Auth::user()->creatorId())->first();
        $initial_balance = !empty($get_account->initial_balance) ? $get_account->initial_balance : 0;
        $net_salary = !empty($employeePayslip->net_payble) ? $employeePayslip->net_payble : 0;
        if (!empty($employeePayslip)) {
            $employeePayslip->status = 1;
            $employeePayslip->save();

            $total_balance = $initial_balance - $net_salary;
            $get_account->initial_balance = $total_balance;
            $get_account->save();

            $set_expense = new Expense();
            $set_expense->account_id = $get_account->id;
            $set_expense->amount = $employeePayslip->net_payble;
            $set_expense->date = date('Y-m-d');
            $set_expense->expense_category_id = '';
            $set_expense->payee_id = $get_employee->id;
            $set_expense->payment_type_id = '';
            $set_expense->referal_id = '';
            $set_expense->description = '';
            $set_expense->created_by = $get_employee->created_by;
            $set_expense->save();

            return redirect()->route('payslip.index')->with('success', __('Payslip Payment successfully.'));
        } else {
            return redirect()->route('payslip.index')->with('error', __('Payslip Payment failed.'));
        }
    }

    public function bulk_pay_create($date)
    {
        $Employees       = PaySlip::where('salary_month', $date)->where('created_by', \Auth::user()->creatorId())->get();
        $unpaidEmployees = PaySlip::where('salary_month', $date)->where('created_by', \Auth::user()->creatorId())->where('status', '=', 0)->get();

        return view('payslip.bulkcreate', compact('Employees', 'unpaidEmployees', 'date'));
    }

    public function bulkpayment(Request $request, $date)
    {
        $unpaidEmployees = PaySlip::where('salary_month', $date)->where('created_by', \Auth::user()->creatorId())->where('status', '=', 0)->get();

        foreach ($unpaidEmployees as $employee) {
            $employee->status = 1;
            $employee->save();
        }

        return redirect()->route('payslip.index')->with('success', __('Payslip Bulk Payment successfully.'));
    }

    public function employeepayslip()
    {
        $employees = Employee::where(
            [
                'user_id' => \Auth::user()->id,
            ]
        )->first();

        $payslip = PaySlip::where('employee_id', '=', $employees->id)->get();

        return view('payslip.employeepayslip', compact('payslip'));
    }

    public function pdf($id, $month)
{
    $payslip = PaySlip::where('employee_id', $id)
        ->where('salary_month', $month)
        ->where('created_by', \Auth::user()->creatorId())
        ->firstOrFail();

    $employee = Employee::findOrFail($payslip->employee_id);
    $employeesId = \Auth::user()->employeeIdFormat($employee->employee_id);

    // Explicitly calculate and set loan deduction
    $payslip->loan = Employee::loan($employee->id, $month);
    
    // Calculate total allowance amount (not JSON string)
    // Handle both fixed and percentage types like in get_net_salary()
    $allowances = \App\Models\Allowance::where('employee_id', '=', $employee->id)->get();
    $totalAllowance = 0;
    foreach ($allowances as $allowance) {
        if (isset($allowance->type) && $allowance->type == 'percentage') {
            $employeeForCalc = Employee::find($allowance->employee_id);
            $totalAllowance += $allowance->amount * ($employeeForCalc->salary ?? 0) / 100;
        } else {
            $totalAllowance += $allowance->amount;
        }
    }
    
    $payslip->allowance = (float)$totalAllowance;
    
    $baseNetSalary = Employee::find($employee->id)->get_net_salary();
    $payslip->net_payble = $baseNetSalary;
    
    $payslip->save(); // Save the updated values

    $payslipDetail = Utility::employeePayslipDetail($id, $month);

    return view('payslip.pdf', compact('payslip', 'employee', 'payslipDetail', 'employeesId'));
}

    public function send($id, $month)
    {
        $payslip  = PaySlip::where('employee_id', $id)->where('salary_month', $month)->where('created_by', \Auth::user()->creatorId())->first();
        $employee = Employee::find($payslip->employee_id);

        $payslip->name  = $employee->full_name;
        $payslip->email = $employee->email;

        $payslipId    = Crypt::encrypt($payslip->id);
        $payslip->url = route('payslip.payslipPdf', $payslipId);
        $setings = Utility::settings();

        if ($setings['new_payroll'] == 1) {
            $uArr = [
                'payslip_email' => $payslip->email,
                'name'  => $payslip->name,
                'url' => $payslip->url,
                'salary_month' => $payslip->salary_month,
            ];

            $resp = Utility::sendEmailTemplate('new_payroll', [$payslip->email], $uArr);
            return redirect()->back()->with('success', __('Payslip successfully sent.')  . ((!empty($resp) && $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
        }

        return redirect()->back()->with('success', __('Payslip successfully sent.'));
    }

    public function payslipPdf($id)
    {
        $payslipId = Crypt::decrypt($id);
        $payslip = PaySlip::where('id', $payslipId)->where('employee_id', $payslipId)->first();
        
        if (!$payslip) {
            abort(404, 'Payslip not found');
        }

        $month = $payslip->salary_month;
        $employee = Employee::find($payslip->employee_id);
        
        if (!$employee) {
            abort(404, 'Employee not found');
        }

        $payslipDetail = Utility::employeePayslipDetail($payslip->employee_id, $month);
        
        // Format employee ID exactly like in the show method
        $employeesId = \Auth::user()->employeeIdFormat($employee->employee_id);

        return view('payslip.payslipPdf', compact('payslip', 'employee', 'payslipDetail', 'employeesId'));
    }

    public function editEmployee($paySlip)
    {
        $payslip = PaySlip::find($paySlip);

        return view('payslip.salaryEdit', compact('payslip'));
    }

    public function updateEmployee(Request $request, $id)
    {
        if (isset($request->allowance) && !empty($request->allowance)) {
            $allowances   = $request->allowance;
            $allowanceIds = $request->allowance_id;
            foreach ($allowances as $k => $allownace) {
                $allowanceData         = Allowance::find($allowanceIds[$k]);
                $allowanceData->amount = $allownace;
                $allowanceData->save();
            }
        }


        if (isset($request->commission) && !empty($request->commission)) {
            $commissions   = $request->commission;
            $commissionIds = $request->commission_id;
            foreach ($commissions as $k => $commission) {
                $commissionData         = Commission::find($commissionIds[$k]);
                $commissionData->amount = $commission;
                $commissionData->save();
            }
        }

        if (isset($request->loan) && !empty($request->loan)) {
            $loans   = $request->loan;
            $loanIds = $request->loan_id;
            foreach ($loans as $k => $loan) {
                $loanData         = Loan::find($loanIds[$k]);
                $loanData->amount = $loan;
                $loanData->save();
            }
        }


        if (isset($request->saturation_deductions) && !empty($request->saturation_deductions)) {
            $saturation_deductionss   = $request->saturation_deductions;
            $saturation_deductionsIds = $request->saturation_deductions_id;
            foreach ($saturation_deductionss as $k => $saturation_deductions) {

                $saturation_deductionsData         = SaturationDeduction::find($saturation_deductionsIds[$k]);
                $saturation_deductionsData->amount = $saturation_deductions;
                $saturation_deductionsData->save();
            }
        }


        if (isset($request->other_payment) && !empty($request->other_payment)) {
            $other_payments   = $request->other_payment;
            $other_paymentIds = $request->other_payment_id;
            foreach ($other_payments as $k => $other_payment) {
                $other_paymentData         = OtherPayment::find($other_paymentIds[$k]);
                $other_paymentData->amount = $other_payment;
                $other_paymentData->save();
            }
        }


        if (isset($request->rate) && !empty($request->rate)) {
            $rates   = $request->rate;
            $rateIds = $request->rate_id;
            $hourses = $request->hours;

            foreach ($rates as $k => $rate) {
                $overtime        = Overtime::find($rateIds[$k]);
                $overtime->rate  = $rate;
                $overtime->hours = $hourses[$k];
                $overtime->save();
            }
        }

        $payslipEmployee                       = PaySlip::find($request->payslip_id);
        $payslipEmployee->allowance            = Employee::allowance($payslipEmployee->employee_id);
        $payslipEmployee->commission           = Employee::commission($payslipEmployee->employee_id);
        $payslipEmployee->loan                 = Employee::loan($payslipEmployee->employee_id);
        $payslipEmployee->saturation_deduction = Employee::saturation_deduction($payslipEmployee->employee_id);
        $payslipEmployee->other_payment        = Employee::other_payment($payslipEmployee->employee_id);
        $payslipEmployee->overtime             = Employee::overtime($payslipEmployee->employee_id);
        
        // Calculate total allowance amount (not JSON string)
        $allowances = \App\Models\Allowance::where('employee_id', '=', $payslipEmployee->employee_id)->get();
        $totalAllowance = 0;
        foreach ($allowances as $allowance) {
            if (isset($allowance->type) && $allowance->type == 'percentage') {
                $employeeForCalc = Employee::find($allowance->employee_id);
                $totalAllowance += $allowance->amount * ($employeeForCalc->salary ?? 0) / 100;
            } else {
                $totalAllowance += $allowance->amount;
            }
        }
        $payslipEmployee->allowance            = (float)$totalAllowance;
        $payslipEmployee->net_payble           = Employee::find($payslipEmployee->employee_id)->get_net_salary();
        $payslipEmployee->save();

        return redirect()->route('payslip.index')->with('success', __('Employee payroll successfully updated.'));
    }

    public function PayslipExport(Request $request)
    {
        $name = 'payslip_' . date('Y-m-d i:h:s');
        $data = \Excel::download(new PayslipExport($request), $name . '.xlsx');
        ob_end_clean();

        return $data;
    }

       public function employeeIdFormat($number)
    {
        $settings = Utility::settings();
        return $settings["employee_prefix"] . sprintf("%06d", $number);
    }


    public function payableDays()
    {
        if (\Auth::user()->can('Manage Pay Slip') || \Auth::user()->can('Manage Set Salary') || \Auth::user()->type == 'employee') {
            $month = [
                '01' => 'JAN',
                '02' => 'FEB',
                '03' => 'MAR',
                '04' => 'APR',
                '05' => 'MAY',
                '06' => 'JUN',
                '07' => 'JUL',
                '08' => 'AUG',
                '09' => 'SEP',
                '10' => 'OCT',
                '11' => 'NOV',
                '12' => 'DEC',
            ];
            $currentyear = date("Y");
            $tempyear = intval($currentyear) - 2;
            $year = [];
            for ($i = 0; $i < 10; $i++) {
                $year[$tempyear + $i] = $tempyear + $i;
            }

            return view('payslip.payable-days', compact('month', 'year'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function payableDaysSearch(Request $request)
    {
        $formate_month_year = $request->datePicker;
        $month = substr($formate_month_year, 5, 2);
        $year = substr($formate_month_year, 0, 4);

        $employees = Employee::where('created_by', \Auth::user()->creatorId())
            ->whereHas('user', function($query) {
                $query->where('type', 'employee');
            })
            ->where('company_doj', '<=', date($year . '-' . $month . '-t'))
            ->get();

        $data = [];

        foreach ($employees as $employee) {
            // Check if employee was terminated or resigned before the month
            $terminationDate = Termination::where('employee_id', $employee->id)
                ->whereDate('termination_date', '<', Carbon::create($year, $month)->startOfMonth())
                ->exists();

            $resignationDate = Resignation::where('employee_id', $employee->id)
                ->whereDate('resignation_date', '<', Carbon::create($year, $month)->startOfMonth())
                ->exists();

            if ($terminationDate || $resignationDate) {
                continue;
            }

            // Calculate payable days using the same method as attendance Excel export
            // This ensures consistency: Total = P + EL + SL + WO + CO
            $payableDays = $this->calculatePayableDaysFromStatusCodes($employee->id, $year, $month);

            if (Auth::user()->type == 'employee') {
                if (Auth::user()->id == $employee->user_id) {
                    $tmp = [];
                    $tmp[] = $employee->id;
                    $tmp[] = \Auth::user()->employeeIdFormat($employee->employee_id);
                    $tmp[] = $payableDays;
                    $tmp['url'] = route('employee.show', Crypt::encrypt($employee->id));
                    $data[] = $tmp;
                }
            } else {
                $tmp = [];
                $tmp[] = $employee->id;
                $tmp[] = \Auth::user()->employeeIdFormat($employee->employee_id);
                $tmp[] = $employee->name;
                $tmp[] = $payableDays;
                $tmp['url'] = route('employee.show', Crypt::encrypt($employee->id));
                $data[] = $tmp;
            }
        }

        return $data;
    }

    public function calculatePayableDays($employeeId, $year, $month)
    {
        $startDate = Carbon::create($year, $month)->startOfMonth();
        $endDate = Carbon::create($year, $month)->endOfMonth();

        // Get employee details
        $employee = Employee::find($employeeId);
        if (!$employee) {
            return 0;
        }

        // Check joining date
        $joiningDate = Carbon::parse($employee->company_doj);
        if ($joiningDate->gt($endDate)) {
            return 0; // Employee joined after this month
        }
        if ($joiningDate->gt($startDate)) {
            $startDate = $joiningDate->copy(); // Employee joined mid-month
        }

        // Check termination/resignation date
        $termination = Termination::where('employee_id', $employeeId)
            ->whereDate('termination_date', '>=', $startDate)
            ->whereDate('termination_date', '<=', $endDate)
            ->first();

        $resignation = Resignation::where('employee_id', $employeeId)
            ->whereDate('resignation_date', '>=', $startDate)
            ->whereDate('resignation_date', '<=', $endDate)
            ->first();

        if ($termination) {
            $endDate = Carbon::parse($termination->termination_date);
        } elseif ($resignation) {
            $endDate = Carbon::parse($resignation->resignation_date);
        }

        // Calculate total days in the period
        $totalDays = $startDate->diffInDays($endDate) + 1;

        // Get attendance records for the month
        $attendances = AttendanceEmployee::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        // Initialize payable days with total days
        $payableDays = $totalDays;

        // Subtract absent days and adjust for half days
        foreach ($attendances as $attendance) {
            $attendanceDate = Carbon::parse($attendance->date);
            
            // Skip if attendance is outside the valid period
            if ($attendanceDate->lt($startDate) || $attendanceDate->gt($endDate)) {
                continue;
            }

            if ($attendance->status == 'Absent') {
                $payableDays -= 1; // Subtract full day for absent
            } elseif ($attendance->status == 'Half Day') {
                $payableDays -= 0.5; // Subtract half day
            }
            // Present days are already counted in totalDays
        }

        // Ensure payable days is not negative
        return max(0, round($payableDays, 2));
    }

    /**
     * Calculate payable days using the same method as attendance Excel export
     * Total Payable Days = Present (P) + Earned Leave (EL) + Sick Leave (SL) + Weekly Off (WO)
     * This matches the calculation used in AttendanceEmployeeController export method
     */
    public function calculatePayableDaysFromStatusCodes($employeeId, $year, $month)
    {
        $startDate = Carbon::create($year, $month)->startOfMonth();
        $endDate = Carbon::create($year, $month)->endOfMonth();

        // Get employee details
        $employee = Employee::find($employeeId);
        if (!$employee) {
            return 0;
        }

        // Check joining date
        $joiningDate = Carbon::parse($employee->company_doj);
        if ($joiningDate->gt($endDate)) {
            return 0; // Employee joined after this month
        }
        if ($joiningDate->gt($startDate)) {
            $startDate = $joiningDate->copy(); // Employee joined mid-month
        }

        // Check termination/resignation date
        $termination = Termination::where('employee_id', $employeeId)
            ->whereDate('termination_date', '>=', $startDate)
            ->whereDate('termination_date', '<=', $endDate)
            ->first();

        $resignation = Resignation::where('employee_id', $employeeId)
            ->whereDate('resignation_date', '>=', $startDate)
            ->whereDate('resignation_date', '<=', $endDate)
            ->first();

        if ($termination) {
            $endDate = Carbon::parse($termination->termination_date);
        } elseif ($resignation) {
            $endDate = Carbon::parse($resignation->resignation_date);
        }

        // Get all dates in the period
        $dates = [];
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        while ($current <= $end) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }

        // Get holidays
        $holidays = \App\Models\Holiday::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->pluck('date')->toArray();

        // Get attendance records
        $attendances = AttendanceEmployee::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        // Group attendance by date
        $attendanceData = [];
        foreach ($attendances as $attendance) {
            $attendanceData[$attendance->date] = [
                'status' => $attendance->status,
                'clock_in' => $attendance->clock_in,
                'clock_out' => $attendance->clock_out,
            ];
        }

        // Build leave codes
        $leaveCodes = [];
        $codePriority = ['LOP' => 3, 'SL' => 2, 'EL' => 1];

        $leaves = LocalLeave::query()
            ->with('leaveType')
            ->where('employee_id', $employeeId)
            ->where('status', 'Approved')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                  ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate->format('Y-m-d'))
                         ->where('end_date', '>=', $endDate->format('Y-m-d'));
                  });
            })
            ->get();

        $codePriority = ['LOP' => 3, 'SL' => 2, 'EL' => 1];
        foreach ($leaves as $leave) {
            $code = $this->getLeaveCode($leave);

            $leaveStart = Carbon::parse($leave->start_date);
            $leaveEnd = Carbon::parse($leave->end_date);
            $periodStart = $leaveStart->lt(Carbon::parse($startDate)) ? Carbon::parse($startDate) : $leaveStart;
            $periodEnd = $leaveEnd->gt(Carbon::parse($endDate)) ? Carbon::parse($endDate) : $leaveEnd;

            for ($d = $periodStart->copy(); $d->lte($periodEnd); $d->addDay()) {
                $dateKey = $d->format('Y-m-d');
                $existing = $leaveCodes[$dateKey] ?? null;
                if (!$existing) {
                    $leaveCodes[$dateKey] = $code;
                    continue;
                }
                if (($codePriority[$code] ?? 0) > ($codePriority[$existing] ?? 0)) {
                    $leaveCodes[$dateKey] = $code;
                }
            }
        }

        // Build status codes (same logic as attendance export)
        $statusCodes = [];
        $weekOff = strtolower(trim((string) ('Sunday' ?? '')));
        $tz = \App\Models\Utility::getValByName('timezone') ?: config('app.timezone');
        $today = Carbon::now($tz)->startOfDay();

        foreach ($dates as $date) {
            // Skip future dates
            if (Carbon::parse($date, $tz)->startOfDay()->gt($today)) {
                $statusCodes[$date] = '';
                continue;
            }

            $dayName = strtolower(Carbon::parse($date)->format('l'));
            $isWeekOff = $weekOff !== ''
                ? ($dayName === strtolower($weekOff))
                : in_array($dayName, ['saturday', 'sunday'], true);

            $att = $attendanceData[$date] ?? null;
            $leaveCode = $leaveCodes[$date] ?? null;

            $clockIn = $att['clock_in'] ?? null;
            $clockOut = $att['clock_out'] ?? null;
            $hasPunch = !empty($clockIn) && $clockIn !== '00:00:00';
            $isToday = Carbon::parse($date, $tz)->isSameDay($today);

            // Priority 1: Week Off
            if ($isWeekOff) {
                $statusCodes[$date] = 'WO';
                continue;
            }

            // Priority 2: If employee punched
            if ($hasPunch) {
                if (empty($clockOut) || $clockOut === '00:00:00') {
                    if ($leaveCode === 'SL') {
                        $statusCodes[$date] = 'SL';
                    } else {
                        $statusCodes[$date] = 'SP';
                    }
                    continue;
                }
                
                if ($leaveCode === 'SL') {
                    $statusCodes[$date] = 'SL';
                } else {
                    $status = strtolower(trim((string) ($att['status'] ?? '')));
                    if ($status === 'present') {
                        $statusCodes[$date] = 'P';
                    } elseif ($status === 'absent') {
                        $statusCodes[$date] = 'LOP';
                    } else {
                        $statusCodes[$date] = 'P';
                    }
                }
                continue;
            }

            // Priority 3: Leave codes
            if (!empty($leaveCode)) {
                $statusCodes[$date] = $leaveCode;
                continue;
            }

            // Priority 4: Holiday
            if (in_array($date, $holidays)) {
                $statusCodes[$date] = 'H';
                continue;
            }

            // For today only: don't label as LOP yet
            if ($isToday) {
                $statusCodes[$date] = $isWeekOff ? 'WO' : '';
                continue;
            }

            // Default absent
            $status = strtolower(trim((string) ($att['status'] ?? '')));
            if ($status === 'present') {
                $statusCodes[$date] = 'P';
            } elseif ($status === 'absent' || $status === '') {
                $statusCodes[$date] = 'LOP';
            } else {
                $statusCodes[$date] = 'LOP';
            }
        }

        // Calculate payable days: P + SP + EL + SL + WO + CO + H
        $presentDays = 0;
        $elDays = 0;
        $slDays = 0;
        $woDays = 0;
        $hDays = 0;

        foreach ($statusCodes as $date => $code) {
            if (empty($code)) {
                continue;
            }

            switch ($code) {
                case 'P':
                case 'SP': // Single Punch - count as present (1 full day)
                    $presentDays++;
                    break;
                case 'WO':
                    $woDays++;
                    break;
                case 'H':
                    $hDays++;
                    break;
                case 'LOP':
                case 'LWP':
                    // Loss of pay doesn't add to payable days
                    break;
                case 'SL':
                    // Count SL as 0.5 days only if employee punched in
                    $att = $attendanceData[$date] ?? null;
                    $clockIn = $att['clock_in'] ?? null;
                    $hasPunch = !empty($clockIn) && $clockIn !== '00:00:00';
                    if ($hasPunch) {
                        $slDays += 0.5;
                    }
                    break;
                default:
                    // Any other code is a paid leave (EL, PL, CO, etc.)
                    $elDays++;
                    break;
            }
        }

        $totalPayableDays = $presentDays + $elDays + $slDays + $woDays + $hDays;
        return round($totalPayableDays, 2);
    }

    /**
     * Calculate Total Leave (only EL and SL - all other leaves are treated as LWP)
     */
    public function calculateTotalLeave($employeeId, $year, $month)
    {
        $startDate = Carbon::create($year, $month)->startOfMonth();
        $endDate = Carbon::create($year, $month)->endOfMonth();

        // Get employee details
        $employee = Employee::find($employeeId);
        if (!$employee) {
            return 0;
        }

        // Check joining date
        if (!empty($employee->company_doj)) {
            try {
                $joiningDate = Carbon::parse($employee->company_doj);
                if ($joiningDate->gt($endDate)) {
                    return 0; // Employee joined after this month
                }
                if ($joiningDate->gt($startDate)) {
                    $startDate = $joiningDate->copy(); // Employee joined mid-month
                }
            } catch (\Exception $e) {
                // If date parsing fails, use start of month
            }
        }

        // Check termination/resignation date
        $termination = Termination::where('employee_id', $employeeId)
            ->whereDate('termination_date', '>=', $startDate)
            ->whereDate('termination_date', '<=', $endDate)
            ->first();

        $resignation = Resignation::where('employee_id', $employeeId)
            ->whereDate('resignation_date', '>=', $startDate)
            ->whereDate('resignation_date', '<=', $endDate)
            ->first();

        if ($termination) {
            $endDate = Carbon::parse($termination->termination_date);
        } elseif ($resignation) {
            $endDate = Carbon::parse($resignation->resignation_date);
        }

        // Get attendance records
        $attendances = AttendanceEmployee::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        // Group attendance by date
        $attendanceData = [];
        foreach ($attendances as $attendance) {
            $attendanceData[$attendance->date] = [
                'status' => $attendance->status,
                'clock_in' => $attendance->clock_in,
                'clock_out' => $attendance->clock_out,
            ];
        }

        // Get all approved leaves
        $leaves = LocalLeave::query()
            ->with('leaveType')
            ->where('employee_id', $employeeId)
            ->where('status', 'Approved')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                  ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate->format('Y-m-d'))
                         ->where('end_date', '>=', $endDate->format('Y-m-d'));
                  });
            })
            ->get();

        // Build leave codes map (similar to calculatePayableDaysFromStatusCodes)
        // Use priority to handle overlapping leaves: LOP > SL > EL
        $leaveCodes = [];
        $codePriority = ['LOP' => 3, 'SL' => 2, 'EL' => 1];

        foreach ($leaves as $leave) {
            $code = $this->getLeaveCode($leave);

            $leaveStart = Carbon::parse($leave->start_date);
            $leaveEnd = Carbon::parse($leave->end_date);
            $periodStart = $leaveStart->lt(Carbon::parse($startDate)) ? Carbon::parse($startDate) : $leaveStart;
            $periodEnd = $leaveEnd->gt(Carbon::parse($endDate)) ? Carbon::parse($endDate) : $leaveEnd;

            // Map leave codes to dates (use priority for overlapping leaves)
            for ($d = $periodStart->copy(); $d->lte($periodEnd); $d->addDay()) {
                $dateKey = $d->format('Y-m-d');
                $existing = $leaveCodes[$dateKey] ?? null;
                if (!$existing) {
                    $leaveCodes[$dateKey] = $code;
                    continue;
                }
                // Use higher priority code if there's overlap
                if (($codePriority[$code] ?? 0) > ($codePriority[$existing] ?? 0)) {
                    $leaveCodes[$dateKey] = $code;
                }
            }
        }

        // Count only EL and SL days
        $elDays = 0;
        $slDays = 0;

        foreach ($leaveCodes as $date => $code) {
            // Exclude LOP/LWP
            if (in_array($code, ['LOP', 'LWP'])) {
                continue;
            }

            // Check if employee punched in (for SL calculation)
            $att = $attendanceData[$date] ?? null;
            $clockIn = $att['clock_in'] ?? null;
            $hasPunch = !empty($clockIn) && $clockIn !== '00:00:00';

            if ($code === 'SL') {
                if ($hasPunch) {
                    $slDays += 0.5;
                }
            } else {
                $elDays += 1;
            }
        }

        $totalLeave = $elDays + $slDays;
        return round($totalLeave, 2);
    }

    /**
     * Display Salary Processing page
     */
    public function salaryProcessing()
    {
        if (\Auth::user()->can('Manage Pay Slip') || \Auth::user()->can('Manage Set Salary') || \Auth::user()->type == 'employee') {
            $month = [
                '01' => 'JAN',
                '02' => 'FEB',
                '03' => 'MAR',
                '04' => 'APR',
                '05' => 'MAY',
                '06' => 'JUN',
                '07' => 'JUL',
                '08' => 'AUG',
                '09' => 'SEP',
                '10' => 'OCT',
                '11' => 'NOV',
                '12' => 'DEC',
            ];
            $currentyear = date("Y");
            $tempyear = intval($currentyear) - 2;
            $year = [];
            for ($i = 0; $i < 10; $i++) {
                $year[$tempyear + $i] = $tempyear + $i;
            }

            $departments = Department::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $departments->prepend('All', '');

            return view('payslip.salary-processing', compact('month', 'year', 'departments'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Get salary processing data for selected month/year
     */
    public function salaryProcessingSearch(Request $request)
    {
        $formate_month_year = $request->datePicker;
        $month = substr($formate_month_year, 5, 2);
        $year = substr($formate_month_year, 0, 4);

        $employees = Employee::where('created_by', \Auth::user()->creatorId())
            ->whereHas('user', function($query) {
                $query->where('type', 'employee');
            });

        // Filter by company_doj only if it's not null
        // If company_doj is null, include the employee (they might have been added without a joining date)
        $employees->where(function($query) use ($year, $month) {
            $query->whereNull('company_doj')
                  ->orWhere('company_doj', '<=', date($year . '-' . $month . '-t'));
        });

        // Filter by department if provided
        $departmentId = $request->input('department_id');
        if (!empty($departmentId) && $departmentId !== '0') {
            $employees->where('department_id', (int)$departmentId);
        }

        $employees = $employees->get();
        
        // Debug: Log the query results (remove in production)
        \Log::info('Salary Processing Search', [
            'user_id' => \Auth::user()->id,
            'user_type' => \Auth::user()->type,
            'creator_id' => \Auth::user()->creatorId(),
            'department_id' => $departmentId,
            'year' => $year,
            'month' => $month,
            'employee_count' => $employees->count(),
            'employee_ids' => $employees->pluck('id')->toArray(),
            'employee_names' => $employees->pluck('name')->toArray(),
            'employee_departments' => $employees->pluck('department_id')->toArray()
        ]);
        
        // Also check if current logged-in employee is in the results
        if (\Auth::user()->type == 'employee' && \Auth::user()->employee) {
            $currentEmployeeId = \Auth::user()->employee->id;
            $isInResults = $employees->contains('id', $currentEmployeeId);
            \Log::info('Current Employee Check', [
                'current_employee_id' => $currentEmployeeId,
                'current_employee_name' => \Auth::user()->employee->name ?? 'N/A',
                'current_employee_department_id' => \Auth::user()->employee->department_id ?? 'N/A',
                'is_in_results' => $isInResults
            ]);
        }

        $data = [];

        foreach ($employees as $employee) {
            // Check if employee was terminated or resigned before the month
            $terminationDate = Termination::where('employee_id', $employee->id)
                ->whereDate('termination_date', '<', Carbon::create($year, $month)->startOfMonth())
                ->exists();

            $resignationDate = Resignation::where('employee_id', $employee->id)
                ->whereDate('resignation_date', '<', Carbon::create($year, $month)->startOfMonth())
                ->exists();

            if ($terminationDate || $resignationDate) {
                \Log::info('Employee excluded - terminated/resigned', [
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->name,
                    'terminated' => $terminationDate,
                    'resigned' => $resignationDate
                ]);
                continue;
            }

            // Calculate Monthly Days (considering joining/termination dates)
            $startDate = Carbon::create($year, $month)->startOfMonth();
            $endDate = Carbon::create($year, $month)->endOfMonth();
            
            // Handle null or empty company_doj
            if (empty($employee->company_doj)) {
                // If no joining date, assume employee was present for the entire month
                $joiningDate = $startDate->copy();
            } else {
                try {
                    // Try to parse the date - handle different formats
                    $joiningDate = Carbon::parse($employee->company_doj);
                    if ($joiningDate->gt($endDate)) {
                        \Log::info('Employee excluded - joined after month', [
                            'employee_id' => $employee->id,
                            'employee_name' => $employee->name,
                            'company_doj' => $employee->company_doj,
                            'month_end' => $endDate->format('Y-m-d')
                        ]);
                        continue; // Employee joined after this month
                    }
                } catch (\Exception $e) {
                    // If date parsing fails, log it and assume employee was present for entire month
                    \Log::warning('Failed to parse company_doj', [
                        'employee_id' => $employee->id,
                        'employee_name' => $employee->name,
                        'company_doj' => $employee->company_doj,
                        'error' => $e->getMessage()
                    ]);
                    $joiningDate = $startDate->copy();
                }
            }
            if ($joiningDate->gt($startDate)) {
                $startDate = $joiningDate->copy();
            }

            $termination = Termination::where('employee_id', $employee->id)
                ->whereDate('termination_date', '>=', $startDate)
                ->whereDate('termination_date', '<=', $endDate)
                ->first();

            $resignation = Resignation::where('employee_id', $employee->id)
                ->whereDate('resignation_date', '>=', $startDate)
                ->whereDate('resignation_date', '<=', $endDate)
                ->first();

            if ($termination) {
                $endDate = Carbon::parse($termination->termination_date);
            } elseif ($resignation) {
                $endDate = Carbon::parse($resignation->resignation_date);
            }

            $monthlyDays = $startDate->diffInDays($endDate) + 1;

            // Calculate Payable Days
            $payableDays = $this->calculatePayableDaysFromStatusCodes($employee->id, $year, $month);

            // Calculate LOP (Leave Without Pay)
            $lopDays = $this->calculateLOPDays($employee->id, $year, $month);

            // Calculate Total Leave (only EL and SL - all other leaves are treated as LWP)
            $totalLeave = $this->calculateTotalLeave($employee->id, $year, $month);

            // Get Actual Salary
            $actualSalary = $employee->salary ?? 0;

            // Use Standard Monthly Days (30) for fair salary calculation across all months
            // This ensures same payable days = same salary regardless of month length (28/29/30/31 days)
            $standardMonthlyDays = 30;
            
            // Calculate Monthly Salary: Actual Salary × (Payable Days / Standard Monthly Days)
            // This ensures fairness: working same days in any month = same salary
            $monthlySalary = $standardMonthlyDays > 0 ? ($actualSalary * ($payableDays / $standardMonthlyDays)) : 0;

            // Calculate Monthly Salary breakdown (percentages of Monthly Salary)
            $basicPay = round($monthlySalary * 0.41, 2); // 41%
            $hra = round($monthlySalary * 0.25, 2); // 25%
            $conveyanceAllowance = round($monthlySalary * 0.21, 2); // 21%
            $specialAllowance = round($monthlySalary * 0.10, 2); // 10%
            $medicalAllowance = round($monthlySalary * 0.03, 2); // 3%

            // Get Salary Advance (loan deductions for the selected month)
            $salaryAdvance = $this->getSalaryAdvance($employee->id, $year, $month);

            // Get Salary Arrears
            $salaryArrears = SalaryArrears::getArrearsAmount($employee->id, $year . '-' . $month);

            // Get Petrol Allowance
            $petrolAllowance = PetrolAllowance::getPetrolAllowanceAmount($employee->id, $year . '-' . $month);

            // Calculate Gross Salary: Monthly Salary + Salary Arrears + Petrol Allowance
            $grossSalary = $monthlySalary + $salaryArrears + $petrolAllowance;

            // Calculate LOP deduction amount (LOP days * daily salary)
            $dailySalary = $standardMonthlyDays > 0 ? ($actualSalary / $standardMonthlyDays) : 0;
            $lopDeductionAmount = round($lopDays * $dailySalary, 2);

            // Professional Tax (PT) - ₹200 fixed for all employees
            $professionalTax = 200;

            // Get Other Deductions from other_deductions table
            $otherDeductions = OtherDeduction::getDeductionAmount($employee->id, $year . '-' . $month);

            // Calculate Net Amount Payable (Total Deductions): LOP deduction + PT + Salary Advance + Other Deductions
            $netAmountPayable = $lopDeductionAmount + $professionalTax + $salaryAdvance + $otherDeductions;

            // Calculate Final Salary: Gross Salary - Net Amount Payable
            $finalPayableSalary = $grossSalary - $netAmountPayable;

            // Get status from database
            $status = SalaryProcessingStatus::getStatus($employee->id, $year, $month);

            $tmp = [];
            $tmp[] = $employee->id;
            $tmp[] = \Auth::user()->employeeIdFormat($employee->employee_id);
            $tmp[] = trim(($employee->name ?? '') . ' ' . ($employee->last_name ?? ''));
            $tmp[] = round($monthlyDays, 2);
            $tmp[] = round($payableDays, 2);
            $tmp[] = round($totalLeave, 2);
            $tmp[] = round($actualSalary, 2);
            $tmp[] = round($monthlySalary, 2);
            $tmp[] = round($basicPay, 2);
            $tmp[] = round($hra, 2);
            $tmp[] = round($conveyanceAllowance, 2);
            $tmp[] = round($specialAllowance, 2);
            $tmp[] = round($medicalAllowance, 2);
            $tmp[] = round($salaryArrears, 2);
            $tmp[] = round($petrolAllowance, 2);
            $tmp[] = round($grossSalary, 2);
            $tmp[] = round($lopDays, 2);
            $tmp[] = round($lopDeductionAmount, 2);
            $tmp[] = round($professionalTax, 2);
            $tmp[] = round($salaryAdvance, 2);
            $tmp[] = round($otherDeductions, 2);
            $tmp[] = round($netAmountPayable, 2);
            $tmp[] = round($finalPayableSalary, 2);
            $tmp[] = $status;
            $tmp['url'] = route('employee.show', Crypt::encrypt($employee->id));
            $data[] = $tmp;
        }

        return $data;
    }

    /**
     * Calculate LOP (Leave Without Pay) days for an employee in a given month
     */
    public function calculateLOPDays($employeeId, $year, $month)
    {
        $startDate = Carbon::create($year, $month)->startOfMonth();
        $endDate = Carbon::create($year, $month)->endOfMonth();

        // Get employee details
        $employee = Employee::find($employeeId);
        if (!$employee) {
            return 0;
        }

        // Check joining date
        $joiningDate = Carbon::parse($employee->company_doj);
        if ($joiningDate->gt($endDate)) {
            return 0;
        }
        if ($joiningDate->gt($startDate)) {
            $startDate = $joiningDate->copy();
        }

        // Check termination/resignation date
        $termination = Termination::where('employee_id', $employeeId)
            ->whereDate('termination_date', '>=', $startDate)
            ->whereDate('termination_date', '<=', $endDate)
            ->first();

        $resignation = Resignation::where('employee_id', $employeeId)
            ->whereDate('resignation_date', '>=', $startDate)
            ->whereDate('resignation_date', '<=', $endDate)
            ->first();

        if ($termination) {
            $endDate = Carbon::parse($termination->termination_date);
        } elseif ($resignation) {
            $endDate = Carbon::parse($resignation->resignation_date);
        }

        // Get all dates in the period
        $dates = [];
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        while ($current <= $end) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }

        // Get holidays
        $holidays = \App\Models\Holiday::whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])->pluck('date')->toArray();

        // Get attendance records
        $attendances = AttendanceEmployee::where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        // Group attendance by date
        $attendanceData = [];
        foreach ($attendances as $attendance) {
            $attendanceData[$attendance->date] = [
                'status' => $attendance->status,
                'clock_in' => $attendance->clock_in,
                'clock_out' => $attendance->clock_out,
            ];
        }

        // Build leave codes (same logic as calculatePayableDaysFromStatusCodes)
        $leaveCodes = [];
        $codePriority = ['LOP' => 3, 'SL' => 2, 'EL' => 1];

        $leaves = LocalLeave::query()
            ->with('leaveType')
            ->where('employee_id', $employeeId)
            ->where('status', 'Approved')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                  ->orWhereBetween('end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      $q2->where('start_date', '<=', $startDate->format('Y-m-d'))
                         ->where('end_date', '>=', $endDate->format('Y-m-d'));
                  });
            })
            ->get();

        foreach ($leaves as $leave) {
            $code = $this->getLeaveCode($leave);

            $leaveStart = Carbon::parse($leave->start_date);
            $leaveEnd = Carbon::parse($leave->end_date);
            $periodStart = $leaveStart->lt($startDate) ? $startDate : $leaveStart;
            $periodEnd = $leaveEnd->gt($endDate) ? $endDate : $leaveEnd;

            for ($d = $periodStart->copy(); $d->lte($periodEnd); $d->addDay()) {
                $dateKey = $d->format('Y-m-d');
                $existing = $leaveCodes[$dateKey] ?? '';
                $newPrio = ($code === 'LOP') ? 3 : (($code === 'SL') ? 2 : 1);
                $oldPrio = ($existing === 'LOP') ? 3 : (($existing === 'SL') ? 2 : 1);
                if (!isset($leaveCodes[$dateKey]) || $newPrio > $oldPrio) {
                    $leaveCodes[$dateKey] = $code;
                }
            }
        }

        // Build status codes (same logic as calculatePayableDaysFromStatusCodes)
        $statusCodes = [];
        $weekOff = strtolower(trim((string) ('Sunday' ?? '')));
        $tz = 'Asia/Kolkata';
        $today = Carbon::now($tz)->startOfDay();

        foreach ($dates as $date) {
            if (Carbon::parse($date, $tz)->startOfDay()->gt($today)) {
                $statusCodes[$date] = '';
                continue;
            }

            $dayName = strtolower(Carbon::parse($date)->format('l'));
            $isWeekOff = $weekOff !== ''
                ? ($dayName === strtolower($weekOff))
                : in_array($dayName, ['saturday', 'sunday'], true);

            $att = $attendanceData[$date] ?? null;
            $leaveCode = $leaveCodes[$date] ?? null;

            $clockIn = $att['clock_in'] ?? null;
            $clockOut = $att['clock_out'] ?? null;
            $hasPunch = !empty($clockIn) && $clockIn !== '00:00:00';
            $isToday = Carbon::parse($date, $tz)->isSameDay($today);

            if ($isWeekOff) {
                $statusCodes[$date] = 'WO';
                continue;
            }

            if ($hasPunch) {
                if (empty($clockOut) || $clockOut === '00:00:00') {
                    if ($leaveCode === 'SL') {
                        $statusCodes[$date] = 'SL';
                    } else {
                        $statusCodes[$date] = 'SP';
                    }
                    continue;
                }
                
                if ($leaveCode === 'SL') {
                    $statusCodes[$date] = 'SL';
                } else {
                    $status = strtolower(trim((string) ($att['status'] ?? '')));
                    if ($status === 'present') {
                        $statusCodes[$date] = 'P';
                    } elseif ($status === 'absent') {
                        $statusCodes[$date] = 'LOP';
                    } else {
                        $statusCodes[$date] = 'P';
                    }
                }
                continue;
            }

            if (!empty($leaveCode)) {
                $statusCodes[$date] = $leaveCode;
                continue;
            }

            if (in_array($date, $holidays)) {
                $statusCodes[$date] = 'H';
                continue;
            }

            if ($isToday) {
                $statusCodes[$date] = $isWeekOff ? 'WO' : '';
                continue;
            }

            $status = strtolower(trim((string) ($att['status'] ?? '')));
            if ($status === 'present') {
                $statusCodes[$date] = 'P';
            } elseif ($status === 'absent' || $status === '') {
                $statusCodes[$date] = 'LOP';
            } else {
                $statusCodes[$date] = 'LOP';
            }
        }

        // Count LOP days
        $lopDays = 0;
        foreach ($statusCodes as $date => $code) {
            if ($code === 'LOP') {
                $lopDays += 1;
            }
        }

        return round($lopDays, 2);
    }

    /**
     * Get Salary Advance (loan deductions) for an employee in a given month
     */
    private function getSalaryAdvance($employeeId, $year, $month)
    {
        $monthDate = $year . '-' . $month . '-01';
        
        $totalAdvance = LoanDeduction::whereHas('loan', function($query) use ($employeeId) {
                $query->where('employee_id', $employeeId);
            })
            ->whereYear('month', $year)
            ->whereMonth('month', $month)
            ->where('is_deducted', true)
            ->sum('emi_amount');

        return $totalAdvance;
    }

    /**
     * Export Salary Processing data to Excel
     */
    public function salaryProcessingExport(Request $request)
    {
        $formate_month_year = $request->datePicker;
        $month = substr($formate_month_year, 5, 2);
        $year = substr($formate_month_year, 0, 4);
        $departmentId = $request->department_id ?? null;

        $fileName = 'salary_processing_' . date('F_Y', strtotime($year . '-' . $month . '-01')) . '_' . date('Y-m-d_His') . '.xlsx';
        
        $export = new SalaryProcessingExport($year, $month, $this, $departmentId);
        
        return \Excel::download($export, $fileName);
    }

    /**
     * Generate PDF content for payslip
     */
    private function generatePayslipPdf($payslipId, $month)
    {
        try {
            $payslip = PaySlip::findOrFail($payslipId);
            $employee = Employee::findOrFail($payslip->employee_id);
            $employeesId = \Auth::user()->employeeIdFormat($employee->employee_id);
            
            // Calculate and set loan deduction
            $payslip->loan = Employee::loan($employee->id, $month);
            
            // Calculate and set salary arrears
            $arrearsAmount = \App\Models\SalaryArrears::getArrearsAmount($employee->id, $month);
            $payslip->salary_arrears = $arrearsAmount;
            
            // Calculate and set petrol allowance
            $petrolAllowanceAmount = \App\Models\PetrolAllowance::getPetrolAllowanceAmount($employee->id, $month);
            $payslip->petrol_allowance = $petrolAllowanceAmount;
            
            // Calculate total allowance
            $allowances = \App\Models\Allowance::where('employee_id', '=', $employee->id)->get();
            $totalAllowance = 0;
            foreach ($allowances as $allowance) {
                if (isset($allowance->type) && $allowance->type == 'percentage') {
                    $employeeForCalc = Employee::find($allowance->employee_id);
                    $totalAllowance += $allowance->amount * ($employeeForCalc->salary ?? 0) / 100;
                } else {
                    $totalAllowance += $allowance->amount;
                }
            }
            $payslip->allowance = (float)$totalAllowance + (float)$petrolAllowanceAmount;
            
            // Update net_payble
            $baseNetSalary = Employee::find($employee->id)->get_net_salary();
            $payslip->net_payble = $baseNetSalary + $arrearsAmount + $petrolAllowanceAmount;
            
            $payslipDetail = Utility::employeePayslipDetail($employee->id, $month);
            
            // Get logo for PDF (convert to base64 for reliable PDF rendering)
            $logoBase64 = null;
            try {
                $logo = Utility::get_file('uploads/logo/');
                $company_logo = Utility::getValByName('company_logo');
                if (empty($company_logo)) {
                    $company_logo = 'logo-dark.png';
                }
                
                $settings = Utility::settings();
                $storageSetting = $settings['storage_setting'] ?? 'local';
                
                if ($storageSetting == 'local') {
                    $fullPath = storage_path('app/public/uploads/logo/' . $company_logo);
                    if (file_exists($fullPath)) {
                        $imageData = file_get_contents($fullPath);
                        $imageType = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
                        if ($imageType == 'svg') {
                            $imageType = 'svg+xml';
                        }
                        $logoBase64 = 'data:image/' . $imageType . ';base64,' . base64_encode($imageData);
                    }
                } else {
                    // For cloud storage, use URL
                    $logoPath = $logo . '/' . $company_logo;
                    if (!filter_var($logoPath, FILTER_VALIDATE_URL)) {
                        $logoBase64 = url($logoPath);
                    } else {
                        $logoBase64 = $logoPath;
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Logo base64 conversion failed: ' . $e->getMessage());
                $logoBase64 = null;
            }
            
            // Generate HTML content
            $html = view('payslip.pdf', compact('payslip', 'employee', 'payslipDetail', 'employeesId'))->render();
            
            // Replace logo URL with base64 if available
            if ($logoBase64) {
                // Find and replace the logo src in the HTML
                $html = preg_replace('/(<img[^>]*src=["\'])([^"\']*logo[^"\']*)(["\'][^>]*>)/i', '$1' . $logoBase64 . '$3', $html);
            }
            
            // Use DomPDF if available
            if (class_exists('Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \PDF::loadHTML($html);
                return $pdf->output();
            } elseif (class_exists('Dompdf\Dompdf')) {
                require_once app_path('Libraries/dompdf/autoload.inc.php');
                $dompdf = new \Dompdf\Dompdf();
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                return $dompdf->output();
            } else {
                // Fallback: try to use mPDF if available
                if (class_exists('Mpdf\Mpdf')) {
                    $mpdf = new \Mpdf\Mpdf();
                    $mpdf->WriteHTML($html);
                    return $mpdf->Output('', 'S'); // Return as string
                }
                throw new \Exception('No PDF library available');
            }
        } catch (\Exception $e) {
            \Log::error('PDF generation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send payslip email with PDF attachment
     */
    private function sendPayslipEmailWithAttachment($email, $uArr, $pdfContent, $pdfFileName)
    {
        try {
            $template = \App\Models\EmailTemplate::where('slug', 'new_payroll')->first();
            
            if (!$template) {
                throw new \Exception('Email template not found');
            }
            
            $is_active = \App\Models\UserEmailTemplate::where('template_id', '=', $template->id)->first();
            if (!$is_active || $is_active->is_active != 1) {
                throw new \Exception('Email template is not active');
            }
            
            $settings = Utility::settings();
            $data = Utility::getSetting();
            
            $setting = [
                'mail_driver' => '',
                'mail_host' => '',
                'mail_port' => '',
                'mail_encryption' => '',
                'mail_username' => '',
                'mail_password' => '',
                'mail_from_address' => '',
                'mail_from_name' => '',
            ];
            
            foreach ($data as $row) {
                $setting[$row->name] = $row->value;
            }
            
            $usr = \Auth::user();
            if ($usr) {
                $content = \App\Models\EmailTemplateLang::where('parent_id', '=', $template->id)->where('lang', 'LIKE', $usr->lang)->first();
            } else {
                $content = \App\Models\EmailTemplateLang::where('parent_id', '=', $template->id)->where('lang', 'LIKE', 'en')->first();
            }
            
            if (!$content || empty($content->content)) {
                throw new \Exception('Email template content not found');
            }
            
            $content->from = $template->from;
            $content->content = Utility::replaceVariable($content->content, $uArr);
            
            // Override app_name if provided in uArr
            if (isset($uArr['app_name'])) {
                $content->content = str_replace('{app_name}', $uArr['app_name'], $content->content);
                $content->content = str_replace('{<strong>app_name</strong>}', '<strong>' . $uArr['app_name'] . '</strong>', $content->content);
            }
            
            // Configure mail settings
            config([
                'mail.driver' => $settings['mail_driver'] ? $settings['mail_driver'] : $setting['mail_driver'],
                'mail.host' => $settings['mail_host'] ? $settings['mail_host'] : $setting['mail_host'],
                'mail.port' => $settings['mail_port'] ? $settings['mail_port'] : $setting['mail_port'],
                'mail.encryption' => $settings['mail_encryption'] ? $settings['mail_encryption'] : $setting['mail_encryption'],
                'mail.username' => $settings['mail_username'] ? $settings['mail_username'] : $setting['mail_username'],
                'mail.password' => $settings['mail_password'] ? $settings['mail_password'] : $setting['mail_password'],
                'mail.from.address' => $settings['mail_from_address'] ? $settings['mail_from_address'] : $setting['mail_from_address'],
                'mail.from.name' => $settings['mail_from_name'] ? $settings['mail_from_name'] : $setting['mail_from_name'],
            ]);
            
            // Send email with PDF attachment
            Mail::to($email)->send(new PayslipSendWithAttachment($content, $settings, $email, $pdfContent, $pdfFileName));
            
        } catch (\Exception $e) {
            \Log::error('Failed to send payslip email with attachment: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update salary processing status (only for Finance & Accounts employees, NOT company users)
     */
    public function updateSalaryProcessingStatus(Request $request)
    {
        // IMPORTANT: Company users are NOT allowed to change status
        if (\Auth::user()->type == 'company') {
            return response()->json(['error' => __('Permission denied. Only Finance & Accounts department employees can update payment status.')], 403);
        }

        // Check if user is from Finance & Accounts department (employees only)
        $isFinanceAccounts = false;
        if (\Auth::user()->type == 'employee') {
            try {
                // Method 1: Check by email pattern (accounts@, finance@)
                $userEmail = strtolower(\Auth::user()->email ?? '');
                if (strpos($userEmail, 'accounts@') !== false || 
                    strpos($userEmail, 'finance@') !== false ||
                    strpos($userEmail, '@accounts') !== false ||
                    strpos($userEmail, '@finance') !== false) {
                    $isFinanceAccounts = true;
                }
                
                // Method 2: Check by department name
                if (!$isFinanceAccounts) {
                    $employee = Employee::where('user_id', \Auth::user()->id)->first();
                    
                    if ($employee && !empty($employee->department_id)) {
                        // Load department directly by ID
                        $department = Department::where('id', $employee->department_id)
                            ->where('created_by', \Auth::user()->creatorId())
                            ->first();
                        
                        if ($department) {
                            $deptName = strtolower(trim($department->name));
                            // Check for various possible department name formats
                            $isFinanceAccounts = (
                                $deptName == 'finance & accounts' || 
                                $deptName == 'finance and accounts' ||
                                $deptName == 'finance & account' ||
                                $deptName == 'finance & accounts team' ||
                                $deptName == 'finance and accounts team' ||
                                (strpos($deptName, 'finance') !== false && (strpos($deptName, 'account') !== false || strpos($deptName, 'accounts') !== false))
                            );
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Error checking Finance & Accounts department: ' . $e->getMessage());
            }
        }

        if (!$isFinanceAccounts) {
            return response()->json(['error' => __('Permission denied. Only Finance & Accounts department employees can update payment status.')], 403);
        }

        $request->validate([
            'employee_id' => 'required|integer|exists:employees,id',
            'year' => 'required|string',
            'month' => 'required|string',
            'status' => 'required|in:Pending,Done',
        ]);

        try {
            SalaryProcessingStatus::updateStatus(
                $request->employee_id,
                $request->year,
                $request->month,
                $request->status,
                \Auth::id()
            );

            return response()->json([
                'success' => true,
                'message' => __('Status updated successfully.')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => __('Failed to update status: ') . $e->getMessage()
            ], 500);
        }
    }
    private function getLeaveCode($leave)
    {
        if (!$leave || !$leave->leaveType) {
            return 'EL';
        }
        $lt = $leave->leaveType;
        $typeTitle = strtolower(trim($lt->title));
        if ($lt->unlimited) {
            return 'LOP';
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

}

