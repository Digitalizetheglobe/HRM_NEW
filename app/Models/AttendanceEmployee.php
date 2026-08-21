<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceEmployee extends Model
{

    const STATUS_PRESENT = 'Present';
    const STATUS_HALF_DAY = 'Half Day';
    const STATUS_EARLY_CLOCK_OUT = 'Early Clock-Out';
    const STATUS_ABSENT = 'Absent';
    const STATUS_SINGLE_PUNCH = 'Single Punch In';
    const REQUIRED_WORKING_HOURS = 8.0; // 8 hours
    const EARLY_LEAVING_HOURS = 5.0; // 5 hours
    const SATURDAY_REQUIRED_WORKING_HOURS = 4.5; // 4.5 hours
    const SATURDAY_EARLY_LEAVING_HOURS = 3.0; // 3 hours


    protected $fillable = [
        'employee_id',
        'date',
        'status',
        'clock_in',
        'clock_out',
        'late',
        'early_leaving',
        'overtime',
        'total_rest',
        'created_by',
        'clock_in_2',
        'clock_out_2',
    ];

    public function employees()
    {
        return $this->hasOne('App\Models\Employee', 'user_id', 'employee_id');
    }

    public function employee()
    {
        return $this->hasOne('App\Models\Employee', 'id', 'employee_id');
    }

        
    
}
