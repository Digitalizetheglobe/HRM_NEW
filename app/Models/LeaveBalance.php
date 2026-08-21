<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    protected $fillable = [
        'user_id', 'leave_type_id', 'total_earned', 
        'used', 'remaining', 'carried_forward', 'year'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
}