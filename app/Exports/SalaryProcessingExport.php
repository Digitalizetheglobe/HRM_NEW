<?php

namespace App\Exports;

use App\Models\Employee;
use App\Models\Termination;
use App\Models\Resignation;
use App\Models\SalaryArrears;
use App\Models\PetrolAllowance;
use App\Models\LoanDeduction;
use App\Models\EmployeeLoan;
use App\Models\AttendanceEmployee;
use App\Models\Leave as LocalLeave;
use App\Models\OtherDeduction;
use App\Models\SalaryProcessingStatus;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Carbon;

class SalaryProcessingExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $year;
    protected $month;
    protected $departmentId;
    protected $paySlipController;

    public function __construct($year, $month, $paySlipController, $departmentId = null)
    {
        $this->year = $year;
        $this->month = $month;
        $this->departmentId = $departmentId;
        $this->paySlipController = $paySlipController;
    }

    public function collection()
    {
        $employees = Employee::where('created_by', \Auth::user()->creatorId())
            ->whereHas('user', function($query) {
                $query->where('type', 'employee');
            });

        // Filter by company_doj only if it's not null
        // If company_doj is null, include the employee (they might have been added without a joining date)
        $employees->where(function($query) {
            $query->whereNull('company_doj')
                  ->orWhere('company_doj', '<=', date($this->year . '-' . $this->month . '-t'));
        });

        // Filter by department if provided
        if (!empty($this->departmentId) && $this->departmentId !== '0' && $this->departmentId !== '') {
            $employees->where('department_id', (int)$this->departmentId);
        }

        $employees = $employees->get();

        $result = [];

        foreach ($employees as $employee) {
            // Check if employee was terminated or resigned before the month
            $terminationDate = Termination::where('employee_id', $employee->id)
                ->whereDate('termination_date', '<', Carbon::create($this->year, $this->month)->startOfMonth())
                ->exists();

            $resignationDate = Resignation::where('employee_id', $employee->id)
                ->whereDate('resignation_date', '<', Carbon::create($this->year, $this->month)->startOfMonth())
                ->exists();

            if ($terminationDate || $resignationDate) {
                continue;
            }

            // Calculate Monthly Days
            $startDate = Carbon::create($this->year, $this->month)->startOfMonth();
            $endDate = Carbon::create($this->year, $this->month)->endOfMonth();
            
            // Handle null or empty company_doj
            if (empty($employee->company_doj)) {
                // If no joining date, assume employee was present for the entire month
                $joiningDate = $startDate->copy();
            } else {
                $joiningDate = Carbon::parse($employee->company_doj);
                if ($joiningDate->gt($endDate)) {
                    continue;
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
            $payableDays = $this->paySlipController->calculatePayableDaysFromStatusCodes($employee->id, $this->year, $this->month);

            // Calculate Total Leave (only EL, SL, and CO)
            $totalLeave = $this->paySlipController->calculateTotalLeave($employee->id, $this->year, $this->month);

            // Calculate LOP
            $lopDays = $this->paySlipController->calculateLOPDays($employee->id, $this->year, $this->month);

            // Get Actual Salary
            $actualSalary = $employee->salary ?? 0;

            // Calculate Monthly Salary using standard 30 days
            $standardMonthlyDays = 30;
            $monthlySalary = $standardMonthlyDays > 0 ? ($actualSalary * ($payableDays / $standardMonthlyDays)) : 0;

            // Calculate Monthly Salary breakdown (percentages of Monthly Salary)
            $basicPay = round($monthlySalary * 0.41, 2); // 41%
            $hra = round($monthlySalary * 0.25, 2); // 25%
            $conveyanceAllowance = round($monthlySalary * 0.21, 2); // 21%
            $specialAllowance = round($monthlySalary * 0.10, 2); // 10%
            $medicalAllowance = round($monthlySalary * 0.03, 2); // 3%

            // Get Salary Advance
            $salaryAdvance = $this->getSalaryAdvance($employee->id, $this->year, $this->month);

            // Get Salary Arrears
            $salaryArrears = SalaryArrears::getArrearsAmount($employee->id, $this->year . '-' . $this->month);

            // Get Petrol Allowance
            $petrolAllowance = PetrolAllowance::getPetrolAllowanceAmount($employee->id, $this->year . '-' . $this->month);

            // Calculate Gross Salary: Monthly Salary + Salary Arrears + Petrol Allowance
            $grossSalary = $monthlySalary + $salaryArrears + $petrolAllowance;

            // Calculate LOP deduction amount (LOP days * daily salary)
            $dailySalary = $standardMonthlyDays > 0 ? ($actualSalary / $standardMonthlyDays) : 0;
            $lopDeductionAmount = round($lopDays * $dailySalary, 2);

            // Professional Tax (PT) - ₹200 fixed for all employees
            $professionalTax = 200;

            // Get Other Deductions from other_deductions table
            $otherDeductions = OtherDeduction::getDeductionAmount($employee->id, $this->year . '-' . $this->month);

            // Calculate Net Amount Payable (Total Deductions): LOP deduction + PT + Salary Advance + Other Deductions
            $netAmountPayable = $lopDeductionAmount + $professionalTax + $salaryAdvance + $otherDeductions;

            // Calculate Final Salary: Gross Salary - Net Amount Payable
            $finalPayableSalary = $grossSalary - $netAmountPayable;

            // Status
            $status = SalaryProcessingStatus::getStatus($employee->id, $this->year, $this->month);

            $result[] = [
                'employee_code' => \Auth::user()->employeeIdFormat($employee->employee_id),
                'employee_name' => trim(($employee->name ?? '') . ' ' . ($employee->last_name ?? '')),
                'monthly_days' => number_format($monthlyDays, 2),
                'payable_days' => number_format($payableDays, 2),
                'total_leave' => number_format($totalLeave, 2),
                'actual_salary' => number_format($actualSalary, 2),
                'monthly_salary' => number_format($monthlySalary, 2),
                'basic_pay' => number_format($basicPay, 2),
                'hra' => number_format($hra, 2),
                'conveyance_allowance' => number_format($conveyanceAllowance, 2),
                'special_allowance' => number_format($specialAllowance, 2),
                'medical_allowance' => number_format($medicalAllowance, 2),
                'salary_arrears' => number_format($salaryArrears, 2),
                'petrol_allowance' => number_format($petrolAllowance, 2),
                'gross_salary' => number_format($grossSalary, 2),
                'lop_days' => number_format($lopDays, 2),
                'lop_deduction_amount' => number_format($lopDeductionAmount, 2),
                'professional_tax' => number_format($professionalTax, 2),
                'salary_advance' => number_format($salaryAdvance, 2),
                'other_deductions' => number_format($otherDeductions, 2),
                'net_amount_payable' => number_format($netAmountPayable, 2),
                'final_salary' => number_format($finalPayableSalary, 2),
                'status' => $status,
            ];
        }

        return collect($result);
    }

    public function headings(): array
    {
        return [
            'Employee Code',
            'Employee Name',
            'Monthly Days',
            'Payable Days',
            'Total Leave',
            'Actual Salary',
            'Monthly Salary',
            'Basic Pay (41%)',
            'HRA (25%)',
            'Conveyance Allowance (21%)',
            'Special Allowance (10%)',
            'Medical Allowance (3%)',
            'Salary Arrears',
            'Petrol Allowance',
            'Gross Salary',
            'LOP Days',
            'LOP Deduction Amount',
            'Professional Tax (PT)',
            'Salary Advance',
            'Other Deductions',
            'Net Amount Payable',
            'Final Salary',
            'Status',
        ];
    }

    public function title(): string
    {
        $monthName = Carbon::create($this->year, $this->month)->format('F Y');
        return 'Salary Processing ' . $monthName;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // Employee Code
            'B' => 25, // Employee Name
            'C' => 12, // Monthly Days
            'D' => 12, // Payable Days
            'E' => 12, // Total Leave
            'F' => 15, // Actual Salary
            'G' => 15, // Monthly Salary
            'H' => 15, // Basic Pay
            'I' => 12, // HRA
            'J' => 20, // Conveyance Allowance
            'K' => 18, // Special Allowance
            'L' => 15, // Medical Allowance
            'M' => 15, // Salary Arrears
            'N' => 18, // Petrol Allowance
            'O' => 15, // Gross Salary
            'P' => 12, // LOP Days
            'Q' => 18, // LOP Deduction Amount
            'R' => 18, // Professional Tax
            'S' => 15, // Salary Advance
            'T' => 18, // Other Deductions
            'U' => 18, // Net Amount Payable
            'V' => 15, // Final Salary
            'W' => 12, // Status
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastColumn = $sheet->getHighestColumn();

        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'], // Blue background
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],
            // Data rows styling
            'A2:' . $lastColumn . $lastRow => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            // Number columns (right align)
            'C2:V' . $lastRow => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
            ],
            // Monthly Salary, Gross Salary, Net Amount Payable, and Final Salary columns - bold
            'G2:G' . $lastRow => [
                'font' => [
                    'bold' => true,
                ],
            ],
            'O2:O' . $lastRow => [
                'font' => [
                    'bold' => true,
                ],
            ],
            'U2:U' . $lastRow => [
                'font' => [
                    'bold' => true,
                ],
            ],
            'V2:V' . $lastRow => [
                'font' => [
                    'bold' => true,
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E8F5E9'], // Light green background
                ],
            ],
        ];
    }

    private function getSalaryAdvance($employeeId, $year, $month)
    {
        $totalAdvance = LoanDeduction::whereHas('loan', function($query) use ($employeeId) {
                $query->where('employee_id', $employeeId);
            })
            ->whereYear('month', $year)
            ->whereMonth('month', $month)
            ->where('is_deducted', true)
            ->sum('emi_amount');

        return $totalAdvance;
    }
}

