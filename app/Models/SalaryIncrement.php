<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryIncrement extends Model
{
    protected $fillable = [
        'employee_id', 'old_salary', 'new_salary', 'increment_amount', 'month_of_effective_date', 'created_by'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}