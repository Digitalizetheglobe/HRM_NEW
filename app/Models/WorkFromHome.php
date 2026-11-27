<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkFromHome extends Model
{
    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
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

