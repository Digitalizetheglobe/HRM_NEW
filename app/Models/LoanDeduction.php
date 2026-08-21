<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanDeduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'month',
        'emi_amount',
        'is_deducted',
        'remark',
        'moved_from_id', // Add this field

    ];

    protected $dates = [
        'month'
    ];

    public function loan()
    {
        return $this->belongsTo(EmployeeLoan::class, 'loan_id');
    }

    // In LoanDeduction.php
    protected static function booted()
    {
        static::saved(function ($deduction) {
            // Prevent recursive saving by using withoutEvents
            if ($deduction->loan && !$deduction->loan->isDirty()) {
                $loan = $deduction->loan;
                $totalDeducted = $loan->deductions()->where('is_deducted', true)->sum('emi_amount');
                $newRemaining = $loan->total_amount - $totalDeducted;
                
                // Only update if different to prevent infinite loop
                if ($loan->remaining_amount != $newRemaining) {
                    \DB::table($loan->getTable())
                        ->where('id', $loan->id)
                        ->update(['remaining_amount' => $newRemaining]);
                }
            }
        });
        
        static::deleted(function ($deduction) {
            // Prevent recursive saving by using direct DB update
            if ($deduction->loan && !$deduction->loan->isDirty()) {
                $loan = $deduction->loan;
                $totalDeducted = $loan->deductions()->where('is_deducted', true)->sum('emi_amount');
                $newRemaining = $loan->total_amount - $totalDeducted;
                
                // Only update if different to prevent infinite loop
                if ($loan->remaining_amount != $newRemaining) {
                    \DB::table($loan->getTable())
                        ->where('id', $loan->id)
                        ->update(['remaining_amount' => $newRemaining]);
                }
            }
        });
    }

}