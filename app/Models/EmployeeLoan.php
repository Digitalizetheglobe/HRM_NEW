<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EmployeeLoan extends Model
{
    protected $fillable = [
        'employee_id',
        'total_amount',
        'number_of_months',
        'monthly_emi',
        'start_month',
        'remaining_amount',
        'reason',
        'created_by',
        'extended_months',
    ];

    protected $dates = [
        'start_month'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function deductions()
    {
        return $this->hasMany(LoanDeduction::class, 'loan_id'); // ✅ correct column name
    }

        protected static function booted()
        {
            static::saving(function ($loan) {
                // Only auto-calculate if remaining_amount is not being explicitly set
                if (!$loan->isDirty('remaining_amount') || $loan->getOriginal('remaining_amount') === null) {
                    $deducted = $loan->deductions()->where('is_deducted', true)->sum('emi_amount');
                    $loan->remaining_amount = $loan->total_amount - $deducted;
                }
            });
        }

        // Make sure this method is used consistently
        public function calculateRemainingAmount()
        {
            $totalDeducted = $this->deductions()
                ->where('is_deducted', true)
                ->sum('emi_amount');
            
            $this->remaining_amount = $this->total_amount - $totalDeducted;
            return $this;
        }

        public function scopeWithDeductions($query)
        {
            return $query->with(['deductions' => function($q) {
                $q->orderBy('month', 'asc');
            }]);
        }

    public function updateRemainingAmount()
    {
        $totalDeducted = $this->deductions()
            ->where('is_deducted', true)
            ->sum('emi_amount');
            
        $this->remaining_amount = $this->total_amount - $totalDeducted;
        $this->save();
    }

    public function getTotalDeductedAttribute()
    {
        return $this->deductions()->where('is_deducted', true)->sum('emi_amount');
    }

    public function getOriginalMonthCountAttribute()
    {
        return $this->number_of_months;
    }

    public function getActualMonthCountAttribute()
    {
        return $this->number_of_months + $this->extended_months;
    }

    public function getDeductedMonthCountAttribute()
    {
        return $this->deductions()->where('is_deducted', true)->count();
    }

    /**
     * Get the dynamically calculated remaining amount
     * This ensures the remaining amount is always accurate based on current deductions
     */
    public function getRemainingAmountAttribute($value)
    {
        // If deductions are already loaded, calculate dynamically from them
        if ($this->relationLoaded('deductions')) {
            $totalDeducted = $this->deductions->where('is_deducted', true)->sum('emi_amount');
            $calculated = $this->attributes['total_amount'] - $totalDeducted;
            // Update the attribute so it's available for the rest of the request
            $this->attributes['remaining_amount'] = $calculated;
            return $calculated;
        }
        
        // Otherwise, use the stored value
        return $value ?? ($this->attributes['total_amount'] ?? 0);
    }

}