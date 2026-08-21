@php
// Enable detailed error logging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    \Log::info('Starting payslip generation', ['timestamp' => now()]);

    // Initialize with null checks and logging
    $employee = $employee ?? null;
    $payslip = $payslip ?? null;
    
    if (!$employee || !$payslip) {
        $errorMessage = 'Payslip Error: Missing employee or payslip data';
        \Log::error($errorMessage, [
            'employee_exists' => isset($employee),
            'payslip_exists' => isset($payslip),
            'route' => request()->fullUrl()
        ]);
        abort(404, $errorMessage);
    }

    \Log::info('Generating payslip for employee', [
        'employee_id' => $employee->id,
        'payslip_id' => $payslip->id ?? 'N/A',
        'salary_month' => $payslip->salary_month ?? 'N/A'
    ]);

    // Handle logo loading with error logging
    // Use logoUrl from controller if provided (for PDF generation), otherwise load it here
    if (!isset($logoUrl)) {
        try {
            $logo = \App\Models\Utility::get_file('uploads/logo/');
            $company_logo = Utility::getValByName('company_logo');
            if (empty($company_logo)) {
                $company_logo = 'logo-dark.png'; // Default fallback
            }
            $logoUrl = $logo . '/' . $company_logo;
            \Log::debug('Logo loaded successfully', ['logo_path' => $logoUrl, 'company_logo' => $company_logo]);
        } catch (\Exception $e) {
            \Log::error('Logo Loading Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $logo = \App\Models\Utility::get_file('uploads/logo/');
            $logoUrl = $logo . '/logo-dark.png'; // Fallback to default
            $company_logo = 'logo-dark.png';
        }
    }

    // Date calculations with error handling
    try {
        $totalDays = date('t', strtotime($payslip->salary_month . '-01'));
        if ($totalDays === false) {
            throw new \Exception('Invalid date format for salary month');
        }
        \Log::debug('Calculated total days in month', ['totalDays' => $totalDays]);
    } catch (\Exception $e) {
        \Log::error('Date Calculation Error', [
            'salary_month' => $payslip->salary_month,
            'error' => $e->getMessage()
        ]);
        $totalDays = 30; // Fallback value
    }

    // Initialize counters
    $presentDays = 0;
    $absentDays = 0;
    $leaveDays = 0;
    $weekOffDays = 0;
    $casualLeaveDays = 0;
    $unlimitedLeaveDays = 0;

    // Get employee's week off day with error handling
    try {
        $weekOffDay = 'Sunday';
        \Log::debug('Week off day retrieved', ['week_off_day' => $weekOffDay]);
    } catch (\Exception $e) {
        \Log::error('Week Off Day Error', [
            'error' => $e->getMessage(),
            'defaulting_to' => 'Sun'
        ]);
        $weekOffDay = 'Sun'; // Default fallback
    }

    // Date period setup
    try {
        $startDate = new DateTime($payslip->salary_month . '-01');
        $endDate = clone $startDate;
        $endDate->modify('last day of this month');
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($startDate, $interval, $endDate->modify('+1 day'));
        \Log::debug('Date period created successfully');
    } catch (\Exception $e) {
        \Log::error('Date Period Creation Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        abort(500, 'Failed to process date range');
    }

    // Get attendance records with error handling
    try {
        $attendanceRecords = DB::table('attendance_employees')
            ->where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();
        \Log::debug('Attendance records retrieved', ['count' => count($attendanceRecords)]);
    } catch (\Exception $e) {
        \Log::error('Attendance Records Query Error', [
            'error' => $e->getMessage(),
            'query' => 'attendance_employees for employee '.$employee->id,
            'trace' => $e->getTraceAsString()
        ]);
        $attendanceRecords = collect();
    }

    // Get approved leaves with error handling (IMPORTANT: group date-range OR conditions)
    try {
        $leaves = DB::table('leaves')
            ->join('leave_types', 'leaves.leave_type_id', '=', 'leave_types.id')
            ->where('leaves.employee_id', $employee->id)
            ->where('leaves.status', 'Approved')
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('leaves.start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                  ->orWhereBetween('leaves.end_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                  ->orWhere(function ($q2) use ($startDate, $endDate) {
                      // leaves spanning the entire month
                      $q2->where('leaves.start_date', '<=', $startDate->format('Y-m-d'))
                         ->where('leaves.end_date', '>=', $endDate->format('Y-m-d'));
                  });
            })
            ->select('leaves.*', 'leave_types.title as leave_type')
            ->get();
        \Log::debug('Leave records retrieved', ['count' => count($leaves)]);
    } catch (\Exception $e) {
        \Log::error('Leave Records Query Error', [
            'error' => $e->getMessage(),
            'query' => 'leaves for employee '.$employee->id,
            'trace' => $e->getTraceAsString()
        ]);
        $leaves = collect();
    }

    // Calculate attendance, leaves, and deductions
    try {
        // Build a per-date attendance map and treat only real presence as "attended"
        // (status Absent / empty punch should NOT count as present)
        $attendanceByDate = [];
        foreach ($attendanceRecords as $r) {
            $attendanceByDate[$r->date] = $r;
        }

        $presentDays = 0;
        foreach ($attendanceByDate as $dateKey => $r) {
            $status = strtolower(trim($r->status ?? ''));
            $clockIn = $r->clock_in ?? null;
            // Count as present if not absent AND has a valid punch in (or explicit "present"/"half day"/"single punch")
            $isAbsent = ($status === 'absent');
            $hasPunch = !empty($clockIn) && $clockIn !== '00:00:00';
            if (!$isAbsent && ($hasPunch || in_array($status, ['present', 'half day', 'single punch in', 'single_punch'], true))) {
                $presentDays++;
            }
        }
        
        foreach ($period as $date) {
            $dayOfWeek = $date->format('D');
            $dateStr = $date->format('Y-m-d');
            
            // Skip week off days
            if (strcasecmp($dayOfWeek, $weekOffDay) === 0) {
                $weekOffDays++;
                continue;
            }
            
            // Check if employee actually attended on this date
            $attended = false;
            if (isset($attendanceByDate[$dateStr])) {
                $r = $attendanceByDate[$dateStr];
                $status = strtolower(trim($r->status ?? ''));
                $clockIn = $r->clock_in ?? null;
                $isAbsent = ($status === 'absent');
                $hasPunch = !empty($clockIn) && $clockIn !== '00:00:00';
                $attended = !$isAbsent && ($hasPunch || in_array($status, ['present', 'half day', 'single punch in', 'single_punch'], true));
            }
            
            if (!$attended) {
                $onLeave = false;
                $leaveType = '';
                
                // Check if employee was on leave
                foreach ($leaves as $leave) {
                    try {
                        $leaveStart = new DateTime($leave->start_date);
                        $leaveEnd = new DateTime($leave->end_date);
                        $leavePeriod = new DatePeriod($leaveStart, $interval, $leaveEnd->modify('+1 day'));
                        
                        foreach ($leavePeriod as $leaveDay) {
                            if ($leaveDay->format('Y-m-d') == $dateStr) {
                                $onLeave = true;
                                $leaveType = strtolower($leave->leave_type ?? '');
                                break 2;
                            }
                        }
                    } catch (\Exception $e) {
                        \Log::error('Leave Date Processing Error', [
                            'leave_id' => $leave->id ?? 'N/A',
                            'error' => $e->getMessage()
                        ]);
                        continue;
                    }
                }
                
                if ($onLeave) {
                    if ($leaveType == 'unlimited leave') {
                        $unlimitedLeaveDays++;
                        $absentDays++; // Count unlimited leave as absent
                    } elseif ($leaveType == 'casual leave') {
                        $casualLeaveDays++;
                        $leaveDays++;
                    } elseif (!empty($leaveType)) {
                        $leaveDays++; // Other approved leaves
                    } else {
                        $absentDays++; // No leave, no attendance - pure absent
                    }
                } else {
                    $absentDays++; // No leave, no attendance - pure absent
                }
            }
        }
        
        \Log::info('Attendance calculations completed', [
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'leave_days' => $leaveDays,
            'week_off_days' => $weekOffDays,
            'casual_leave_days' => $casualLeaveDays,
            'unlimited_leave_days' => $unlimitedLeaveDays
        ]);
        
        // Override to ensure Payable Days equals Total Days
        $absentDays = 0;
        $casualLeaveDays = 0;

    } catch (\Exception $e) {
        \Log::error('Attendance Calculation Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        abort(500, 'Failed to calculate attendance');
    }

    // Calculate salary components with error handling
    try {
        // Ensure all values are properly converted to float
        $grossSalary = is_numeric($payslip->basic_salary) ? (float)$payslip->basic_salary : 0;
        if ($grossSalary <= 0) {
            throw new \Exception('Invalid gross salary amount: ' . $payslip->basic_salary);
        }
        
        // Log the raw values before calculation
        \Log::debug('Salary calculation inputs', [
            'basic_salary_raw' => $payslip->basic_salary,
            'type' => gettype($payslip->basic_salary),
            'loan_raw' => $payslip->loan ?? 'N/A',
            'loan_type' => isset($payslip->loan) ? gettype($payslip->loan) : 'N/A'
        ]);
        
        $basicComponent = $grossSalary * (float)0.40;
        $hraComponent = $grossSalary * (float)0.20;
        $medicalComponent = $grossSalary * (float)0.05;
        $specialComponent = $grossSalary * (float)0.35;
        
        \Log::debug('Salary components calculated', [
            'gross_salary' => $grossSalary,
            'basic' => $basicComponent,
            'hra' => $hraComponent,
            'medical' => $medicalComponent,
            'special' => $specialComponent
        ]);
    } catch (\Exception $e) {
        \Log::error('Salary Component Calculation Error', [
            'error' => $e->getMessage(),
            'basic_salary' => $payslip->basic_salary ?? 'N/A',
            'type' => isset($payslip->basic_salary) ? gettype($payslip->basic_salary) : 'N/A',
            'trace' => $e->getTraceAsString()
        ]);
        abort(500, 'Invalid salary data: ' . $e->getMessage());
    }

    // Calculate salary deductions
    try {
        $perDaySalary = $grossSalary / (float)30;
        $deductionForAbsent = (float)$absentDays * $perDaySalary;
        $deductionForCasualLeave = (float)$casualLeaveDays * $perDaySalary;
        $ptDeduction = is_numeric($payslip->professional_tax ?? 200) ? (float)($payslip->professional_tax ?? 200) : 200;
        
        \Log::debug('Deductions calculated', [
            'per_day_salary' => $perDaySalary,
            'absent_deduction' => $deductionForAbsent,
            'casual_leave_deduction' => $deductionForCasualLeave,
            'professional_tax' => $ptDeduction,
            'absent_days_type' => gettype($absentDays),
            'casual_leave_days_type' => gettype($casualLeaveDays)
        ]);
    } catch (\Exception $e) {
        \Log::error('Deduction Calculation Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'values' => [
                'grossSalary' => $grossSalary ?? 'N/A',
                'absentDays' => $absentDays ?? 'N/A',
                'casualLeaveDays' => $casualLeaveDays ?? 'N/A'
            ]
        ]);
        abort(500, 'Failed to calculate deductions: ' . $e->getMessage());
    }

    // Loan calculations with error handling
    try {
        $loanDeduction = 0;
        $remainingLoan = 0;

        if (isset($payslip->loan)) {
            // Handle case where loan is stored as JSON array
            if (is_string($payslip->loan) && str_starts_with($payslip->loan, '[')) {
                $loanArray = json_decode($payslip->loan, true);
                $loanDeduction = is_array($loanArray) ? array_sum($loanArray) : 0;
            } else {
                $loanDeduction = is_numeric($payslip->loan) ? max(0, (float)$payslip->loan) : 0;
            }

            if ($loanDeduction > 0) {
                $totalLoans = \App\Models\EmployeeLoan::where('employee_id', $employee->id)
                    ->sum('total_amount');
                $totalPaid = $totalLoans - \App\Models\EmployeeLoan::where('employee_id', $employee->id)
                    ->sum('remaining_amount');
                $remainingLoan = $totalLoans - $totalPaid - $loanDeduction;
            }
        }

        \Log::debug('Loan calculations completed', [
            'loan_deduction' => $loanDeduction,
            'remaining_loan' => $remainingLoan,
            'loan_raw_value' => $payslip->loan ?? 'N/A',
            'loan_raw_type' => isset($payslip->loan) ? gettype($payslip->loan) : 'N/A'
        ]);
    } catch (\Exception $e) {
        \Log::error('Loan Calculation Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'loan_value' => $payslip->loan ?? 'N/A'
        ]);
        $loanDeduction = 0;
        $remainingLoan = 0;
    }

    // Final calculations with strict type checking
    try {
        $loanDeduction = isset($payslip->loan) ? (float)$payslip->loan : 0;
        $arrearsAmount = isset($payslip->salary_arrears) ? (float)$payslip->salary_arrears : 0;
        $petrolAllowanceAmount = isset($payslip->petrol_allowance) ? (float)$payslip->petrol_allowance : 0;

        $totalDeductions = (float)$deductionForAbsent + (float)$deductionForCasualLeave ;
        $netSalary = (float)$grossSalary + (float)$arrearsAmount + (float)$petrolAllowanceAmount;
        
        \Log::info('Final salary calculations', [
            'total_deductions' => $totalDeductions,
            'net_salary' => $netSalary,
            'type_checks' => [
                'grossSalary' => gettype($grossSalary),
                'deductionForAbsent' => gettype($deductionForAbsent),
                'deductionForCasualLeave' => gettype($deductionForCasualLeave),
                'ptDeduction' => gettype($ptDeduction),
                'loanDeduction' => gettype($loanDeduction),
                'totalDeductions' => gettype($totalDeductions)
            ]
        ]);
    } catch (\Exception $e) {
        \Log::error('Final Calculation Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'values' => [
                'grossSalary' => $grossSalary ?? 'N/A',
                'deductionForAbsent' => $deductionForAbsent ?? 'N/A',
                'deductionForCasualLeave' => $deductionForCasualLeave ?? 'N/A',
                'ptDeduction' => $ptDeduction ?? 'N/A',
                'loanDeduction' => $loanDeduction ?? 'N/A'
            ],
            'types' => [
                'grossSalary' => isset($grossSalary) ? gettype($grossSalary) : 'N/A',
                'deductionForAbsent' => isset($deductionForAbsent) ? gettype($deductionForAbsent) : 'N/A',
                'deductionForCasualLeave' => isset($deductionForCasualLeave) ? gettype($deductionForCasualLeave) : 'N/A',
                'ptDeduction' => isset($ptDeduction) ? gettype($ptDeduction) : 'N/A',
                'loanDeduction' => isset($loanDeduction) ? gettype($loanDeduction) : 'N/A'
            ]
        ]);
        abort(500, 'Failed to calculate final salary: ' . $e->getMessage());
    }

    // Number to words conversion with error handling
    if (!function_exists('numberToWords')) {
        function numberToWords($number) {
            try {
                $number = max(0, floatval($number));
                $no = floor($number);
                $point = round(($number - $no) * 100);
                
                $words = array(
                    '0' => '', '1' => 'One', '2' => 'Two',
                    '3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six',
                    '7' => 'Seven', '8' => 'Eight', '9' => 'Nine',
                    '10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve',
                    '13' => 'Thirteen', '14' => 'Fourteen',
                    '15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen',
                    '18' => 'Eighteen', '19' => 'Nineteen', '20' => 'Twenty',
                    '30' => 'Thirty', '40' => 'Forty', '50' => 'Fifty',
                    '60' => 'Sixty', '70' => 'Seventy',
                    '80' => 'Eighty', '90' => 'Ninety'
                );
                
                $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
                
                $str = array();
                $pos = 0;
                
                while ($no > 0) {
                    $divider = ($pos == 1) ? 10 : 100;
                    $number = floor($no % $divider);
                    $no = floor($no / $divider);
                    
                    if ($number) {
                        $digitLabel = isset($digits[$pos]) ? $digits[$pos] : '';
                        
                        if ($number < 21) {
                            $str[] = trim(($words[$number] ?? '') . " " . $digitLabel);
                        } else {
                            $tens = floor($number / 10) * 10;
                            $units = $number % 10;
                            $str[] = trim(($words[$tens] ?? '') . " " . ($words[$units] ?? '') . " " . $digitLabel);
                        }
                    }
                    $pos++;
                }
                
                $str = array_reverse($str);
                $result = implode(' ', $str);
                
                $points = '';
                if ($point > 0) {
                    $points = " and ";
                    if ($point < 21) {
                        $points .= ($words[$point] ?? '') . " Paise";
                    } else {
                        $tens = floor($point / 10) * 10;
                        $units = $point % 10;
                        $points .= ($words[$tens] ?? '') . " " . ($words[$units] ?? '') . " Paise";
                    }
                }
                
                return trim($result . " Rupees" . $points) . " Only";
            } catch (\Exception $e) {
                \Log::error('Number to Words Conversion Error', [
                    'number' => $number,
                    'error' => $e->getMessage()
                ]);
                return 'Amount in words conversion failed';
            }
        }
    }

    $netSalaryInWords = numberToWords($netSalary);
    
    // Currency formatting function for PDF (ensures ₹ symbol displays correctly)
    if (!function_exists('formatCurrencyForPdf')) {
        function formatCurrencyForPdf($amount) {
            $settings = \App\Models\Utility::settings();
            $currencySymbol = !empty($settings['site_currency_symbol']) ? $settings['site_currency_symbol'] : '₹';
            $symbolPosition = !empty($settings['site_currency_symbol_position']) ? $settings['site_currency_symbol_position'] : 'pre';
            
            // Ensure ₹ symbol is used (replace any other currency symbol)
            if ($currencySymbol !== '₹' && $currencySymbol !== '&#8377;' && $currencySymbol !== '&#x20B9;') {
                $currencySymbol = '₹';
            }
            
            $formattedAmount = number_format((float)$amount, 2, '.', ',');
            
            if ($symbolPosition == 'pre') {
                return '₹' . $formattedAmount;
            } else {
                return $formattedAmount . ' ₹';
            }
        }
    }
    
    \Log::info('Payslip generation completed successfully');

} catch (\Throwable $th) {
    \Log::error('Payslip Generation Failed', [
        'error' => $th->getMessage(),
        'trace' => $th->getTraceAsString(),
        'employee_id' => $employee->id ?? 'N/A',
        'payslip_id' => $payslip->id ?? 'N/A',
        'request_data' => request()->all()
    ]);
    throw $th; // Re-throw after logging
}
@endphp

<style>
    .modal-body {
        overflow-x: auto;
    }
    #printableArea {
        width: 100%;
        max-width: 100%;
    }
</style>

<div class="modal-body">
    <div class="text-md-end mb-2">
        <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="bottom"
            title="{{ __('Download') }}" onclick="saveAsPDF()"><span class="fa fa-download"></span></a>

        @if (\Auth::user()->type == 'company' || \Auth::user()->type == 'hr')
            <a title="Mail Send" href="{{ route('payslip.send', [$employee->id, $payslip->salary_month]) }}" 
                class="btn btn-sm btn-warning"><span class="fa fa-paper-plane"></span></a>
        @endif
    </div>
    
    <div class="invoice" id="printableArea">
        <div class="row">
            <div class="col-form-label">
                <!-- Main Container with Border -->
                <div style="border: 3px solid #000; padding: 0; font-family: Arial, sans-serif;">
                    
                    <!-- Header Section -->
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="width: 25%; border-right: 2px solid #000; text-align: center; vertical-align: middle;">
                                <img src="{{ asset('/storage/uploads/logo/logo-main.webp') }}" style="max-width: 120px; max-height: 80px; object-fit: contain;" alt="Company Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div style="width: 100px; height: 100px; border: 1px solid #ccc; display: none; margin: 0 auto;"></div>
                            </td>
                            <td style="padding: 15px; text-align: center;">
                                <h2 style="margin: 0; font-size: 24px; font-weight: bold;">{{ \Utility::getValByName('company_name') }}</h2>
                                <div style="font-size: 14px; margin: 8px 0;">
                                    <strong>Office Address :</strong> {{ \Utility::getValByName('company_address') }}, {{ \Utility::getValByName('company_city') }}
                                </div>
                                
                            </td>
                        </tr>
                    </table>

                    <!-- Salary Slip Title -->
                    <div style="border-top: 2px solid #000; border-bottom: 1px solid #000; padding: 10px; text-align: center; background-color: #f8f9fa;">
                        <h3 style="margin: 0; font-size: 18px; font-weight: bold;">Salary Slip for {{ strtoupper(date('F - Y', strtotime($payslip->salary_month))) }}</h3>
                    </div>

                    <div style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 10px; text-align: center; background-color: #f8f9fa;">
                        <!-- <h3 style="margin: 0; font-size: 18px; font-weight: bold;">Salary Slip for {{ strtoupper(date('F - Y', strtotime($payslip->salary_month))) }}</h3> -->
                    </div>

                    <!-- Employee Details Section -->
                    <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                        <tr>
                            <!-- Left Column -->
                            <td class="mobile-stack mobile-stack-border" style="width: 33.33%; border-right: 2px solid #000; padding: 0; vertical-align: top;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <tr style="border-bottom: 1px solid #000;">
                                        <td style="padding: 8px; font-weight: bold;">Employee Name :</td>
                                        <td style="padding: 8px; border-left: 1px solid #000;">{{ $employee->full_name }}</td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #000;">
                                        <td style="padding: 8px; font-weight: bold;">Designation:</td>
                                        <td style="padding: 8px; border-left: 1px solid #000;">{{ $employee->designation->name ?? 'Assistant Manager - Talent Acquisition' }}</td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #000;">
                                        <td style="padding: 8px; font-weight: bold;">Total Day:</td>
                                        <td style="padding: 8px; border-left: 1px solid #000;">{{ $totalDays }}</td>
                                    </tr>
                                    <tr style="">
                                        <td style="padding: 8px; font-weight: bold;">Date of Joining:</td>
                                        <td style="padding: 8px; border-left: 1px solid #000;">{{ \Auth::user()->dateFormat($employee->company_doj) }}</td>
                                    </tr>
                                    
                                </table>
                            </td>
                            
                            <!-- Middle Column -->
                            <td class="mobile-stack" style="width: 33.33%;  padding: 0; vertical-align: top;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <tr style="border-bottom: 1px solid #000;">
                                        <td style="padding: 8px; font-weight: bold;">Employee ID :</td>
                                        <td style="padding: 8px; border-left: 1px solid #000;">{{ $employeesId ?? 'N/A' }}</td>   
                                    </tr>
                                    <tr style="border-bottom: 1px solid #000;">
                                        <td style="padding: 8px; font-weight: bold;">Salary Month :</td>
                                        <td style="padding: 8px; border-left: 1px solid #000;">{{ strtoupper(date('F - Y', strtotime($payslip->salary_month))) }}</td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #000;">
                                        <td style="padding: 8px; font-weight: bold;">Payable Days :</td>
                                        <td style="padding: 8px; border-left: 1px solid #000;">{{ $totalDays - $absentDays - $casualLeaveDays }}</td>
                                    </tr>
       
                                </table>
                            </td>
                        </tr>
                    </table>

                    <div style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 10px; text-align: center; background-color: #f8f9fa;">
                        <table>
                            <tr>
                            </tr>
                        </table>
                    </div>

                    <!-- Earnings and Deductions Section -->
                    <div style="border-top: 0px solid #000;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <!-- Earnings Column -->
                                <td class="mobile-stack mobile-stack-border" style="width: 50%; border-right: 2px solid #000; padding: 0; vertical-align: top;">
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <tr style="background-color: #f8f9fa;">
                                            <th colspan="2" style="padding: 10px; text-align: center; font-size: 16px; font-weight: bold; border-bottom: 1px solid #000;">Earnings</th>
                                        </tr>
                                        <tr style="background-color: #f8f9fa;">
                                            <th style="padding: 8px; font-size: 12px; font-weight: bold; border-bottom: 1px solid #000; border-right: 1px solid #000;">Components</th>
                                            <th style="padding: 8px; font-size: 12px; font-weight: bold; border-bottom: 1px solid #000; text-align: right;">Amount (Rs.)</th>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px; font-size: 12px; border-bottom: 1px solid #000; border-right: 1px solid #000;">Basic</td>
                                            <td style="padding: 8px; font-size: 12px; border-bottom: 1px solid #000; text-align: right;">{{ formatCurrencyForPdf($basicComponent) }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px; font-size: 12px; border-bottom: 1px solid #000; border-right: 1px solid #000;">HRA</td>
                                            <td style="padding: 8px; font-size: 12px; border-bottom: 1px solid #000; text-align: right;">{{ formatCurrencyForPdf($hraComponent) }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px; font-size: 12px; border-bottom: 1px solid #000; border-right: 1px solid #000;">Medical</td>
                                            <td style="padding: 8px; font-size: 12px; border-bottom: 1px solid #000; text-align: right;">{{ formatCurrencyForPdf($medicalComponent) }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px; font-size: 12px; border-bottom: 1px solid #000; border-right: 1px solid #000;">Special Allowance</td>
                                            <td style="padding: 8px; font-size: 12px; border-bottom: 1px solid #000; text-align: right;">{{ formatCurrencyForPdf($specialComponent) }}</td>
                                        </tr>
                                    </table>
                                </td>
                                
                                <!-- Deductions Column -->
                                <td class="mobile-stack" style="width: 50%; padding: 0; vertical-align: top;">
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <tr style="background-color: #f8f9fa;">
                                            <th colspan="2" style="padding: 10px; text-align: center; font-size: 16px; font-weight: bold; border-bottom: 1px solid #000;">Deductions</th>
                                        </tr>
                                        <tr style="background-color: #f8f9fa;">
                                            <th style="padding: 8px; font-size: 12px; font-weight: bold; border-bottom: 1px solid #000; border-right: 1px solid #000;">Common Deductions</th>
                                            <th style="padding: 8px; font-size: 12px; font-weight: bold; border-bottom: 1px solid #000; text-align: right;">Amount (Rs.)</th>
                                        </tr>

                                        <tr>
                                            <td style="padding: 8px; font-size: 12px; border-bottom: 1px solid #000; border-right: 1px solid #000;">Absent Days ({{ $absentDays }} days)</td>
                                            <td style="padding: 8px; font-size: 12px; border-bottom: 1px solid #000; text-align: right;">{{ formatCurrencyForPdf($deductionForAbsent) }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px; font-size: 12px; border-bottom: 1px solid #000; border-right: 1px solid #000;">Casual Leave ({{ $casualLeaveDays }} days)</td>
                                            <td style="padding: 8px; font-size: 12px; border-bottom: 1px solid #000; text-align: right;">{{ formatCurrencyForPdf($deductionForCasualLeave) }}</td>
                                        </tr>

                                    </table>
                                </td>
                            </tr>
                            
                            <!-- Bottom Aligned Totals Row -->
                            <tr class="mobile-stack" style="background-color: #f8f9fa; border-top: 1px solid #000; border-bottom: 1px solid #000;">
                                <td class="mobile-stack mobile-stack-border" style="width: 50%; border-right: 2px solid #000; padding: 0;">
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <tr>
                                            <td style="padding: 10px; font-size: 14px; font-weight: bold; border-right: 1px solid #000;">Gross Earning (A)</td>
                                            <td style="padding: 10px; font-size: 14px; font-weight: bold; text-align: right; width: 120px;">{{ formatCurrencyForPdf($grossSalary + $arrearsAmount + $petrolAllowanceAmount) }}</td>
                                        </tr>
                                    </table>
                                </td>
                                <td class="mobile-stack" style="width: 50%; padding: 0;">
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <tr>
                                            <td style="padding: 10px; font-size: 14px; font-weight: bold; border-right: 1px solid #000;">Total Deductions (B)</td>
                                            <td style="padding: 10px; font-size: 14px; font-weight: bold; text-align: right; width: 120px;">{{ formatCurrencyForPdf($totalDeductions) }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>


                    <div style="border-top: 1px solid #000;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr style="background-color: #f8f9fa;">
                                <td style="padding: 12px; font-size: 16px; font-weight: bold;  "></td>
                                <td style="padding: 12px; font-size: 16px; font-weight: bold; text-align: right; "></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Net Pay Section -->
                    <div style="border-top: 2px solid #000;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr style="background-color: #f8f9fa;">
                                <td style="padding: 12px; font-size: 16px; font-weight: bold; border-right: 1px solid #000; border-bottom: 1px solid #000;">Net (A - B)</td>
                                <td style="padding: 12px; font-size: 16px; font-weight: bold; text-align: left; border-bottom: 1px solid #000;">{{ formatCurrencyForPdf($netSalary) }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px; font-size: 12px; font-weight: bold; border-right: 1px solid #000;">Total Pay</td>
                                <td style="padding: 10px; font-size: 12px;">{{ ucwords($netSalaryInWords) }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Footer Note -->
                    <div style="border-top: 2px solid #000; padding: 15px; text-align: center; font-size: 12px; font-weight: bold;">
                        Note: This is a Computer Generated Slip and does not require signature
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="{{ asset('js/html2pdf.bundle.min.js') }}"></script>
<script>
    function saveAsPDF() {
        var element = document.getElementById('printableArea');
        var opt = {
            margin: 0.3,
            filename: '{{ $employee->full_name }}_{{ $payslip->salary_month }}_payslip',
            image: {
                type: 'jpeg',
                quality: 1
            },
            html2canvas: {
                scale: 2,
                useCORS: true,
                windowWidth: element.scrollWidth
            },
            jsPDF: {
                unit: 'in',
                format: 'A4',
                orientation: 'portrait'
            }
        };
        html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
            var isAndroid = navigator.userAgent.toLowerCase().indexOf("android") > -1;
            
            if (isAndroid && navigator.share) {
                try {
                    var blob = pdf.output('blob');
                    var file = new File([blob], opt.filename + ".pdf", { type: "application/pdf" });
                    
                    navigator.share({
                        files: [file],
                        title: opt.filename,
                        text: 'Payslip Document'
                    }).catch(function(err) {
                        alert("Error sharing: " + err.message);
                    });
                } catch(e) {
                    alert("Error preparing document: " + e.message);
                }
            } else {
                if (isAndroid) {
                    alert("Your app does not support direct PDF downloads. Please open the app in Google Chrome to download.");
                } else {
                    pdf.save(opt.filename + ".pdf");
                }
            }
        });
    }
</script>