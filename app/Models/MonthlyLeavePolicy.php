<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyLeavePolicy extends Model
{
    protected $fillable = ['leave_type_id', 'user_id', 'days', 'is_default'];
    
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}