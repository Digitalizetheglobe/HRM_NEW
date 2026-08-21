<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherDeduction extends Model
{
    protected $fillable = [
        'employee_id',
        'month',
        'amount',
        'remark',
        'created_by',
    ];

    protected $dates = [
        'month',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get deduction amount for a specific employee and month
     * @param int $employeeId
     * @param string $month Format: Y-m (e.g., '2024-01')
     */
    public static function getDeductionAmount($employeeId, $month)
    {
        // Parse the month (format: Y-m)
        $year = date('Y', strtotime($month . '-01'));
        $monthNum = date('m', strtotime($month . '-01'));
        
        return self::where('employee_id', $employeeId)
            ->whereYear('month', $year)
            ->whereMonth('month', $monthNum)
            ->sum('amount');
    }
}






