<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'employee_id',
        'loan_option',
        'title',
        'amount',
        'start_date',
        'end_date',
        'reason',
        'created_by',
    ];

    protected $casts = [
        'month' => 'date',
    ];

    public function deductions()
    {
        return $this->hasMany(LoanDeduction::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function loan_option()
    {
        return $this->hasOne('App\Models\LoanOption', 'id', 'loan_option')->first();
    }
    public static $Loantypes=[
        'fixed'=>'Fixed',
        'percentage'=> 'Percentage',
    ];

    
}
