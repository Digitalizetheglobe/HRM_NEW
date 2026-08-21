<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyLeaveSetting extends Model
{
    protected $fillable = ['leave_type_id', 'monthly_credit', 'created_by'];

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
}