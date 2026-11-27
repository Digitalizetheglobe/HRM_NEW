<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRegularization extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'clock_in',
        'clock_out',
        'reason',
        'status',
        'action_by',
        'action_at',
        'rejection_reason',
        'created_by',
    ];

    protected $casts = [
        'action_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function actionUser()
    {
        return $this->belongsTo(User::class, 'action_by');
    }
}

