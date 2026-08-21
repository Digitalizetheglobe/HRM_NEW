<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryProcessingStatus extends Model
{
    protected $table = 'salary_processing_status';
    
    protected $fillable = [
        'employee_id',
        'year',
        'month',
        'status',
        'updated_by',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get or create status for an employee in a given month/year
     */
    public static function getStatus($employeeId, $year, $month)
    {
        $status = self::where('employee_id', $employeeId)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        return $status ? $status->status : 'Pending';
    }

    /**
     * Update or create status for an employee
     */
    public static function updateStatus($employeeId, $year, $month, $status, $updatedBy = null)
    {
        return self::updateOrCreate(
            [
                'employee_id' => $employeeId,
                'year' => $year,
                'month' => $month,
            ],
            [
                'status' => $status,
                'updated_by' => $updatedBy ?? \Auth::id(),
            ]
        );
    }
}
