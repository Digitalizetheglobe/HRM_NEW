<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryArrears extends Model
{
    protected $fillable = [
        'employee_id',
        'arrears_month',
        'payment_month',
        'amount',
        'created_by',
    ];

    protected $dates = [
        'arrears_month',
        'payment_month',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get arrears amount for a specific employee and payment month
     * @param int $employeeId
     * @param string $paymentMonth Format: Y-m (e.g., '2024-01')
     */
    public static function getArrearsAmount($employeeId, $paymentMonth)
    {
        // Parse the payment month (format: Y-m)
        $year = date('Y', strtotime($paymentMonth . '-01'));
        $month = date('m', strtotime($paymentMonth . '-01'));
        
        return self::where('employee_id', $employeeId)
            ->whereYear('payment_month', $year)
            ->whereMonth('payment_month', $month)
            ->sum('amount');
    }
}

