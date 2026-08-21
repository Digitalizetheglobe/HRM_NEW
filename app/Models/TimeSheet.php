<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TimeSheet extends Model
{
    use HasFactory;
    
    protected $table = 'time_sheets';
    
    protected $fillable = [
        'employee_id',
        'date',
        'hours',
        'remark',
        'created_by',
        'follow_up_date',
    ];
    
    protected $casts = [];

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function isVisibleTo($userId)
    {
        // 1. First check if user is admin/director - they see everything
        if (auth()->check() && (auth()->user()->type == 'company' || auth()->user()->type == 'Director')) {
            return true;
        }

        // 2. Original creator can always see their own timesheets
        if ($this->employee_id == $userId) {
            return true;
        }

        return false;
    }
}