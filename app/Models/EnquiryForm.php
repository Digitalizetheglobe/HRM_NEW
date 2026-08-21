<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnquiryForm extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'full_name',
        'mobile_no',
        'alternate_no',
        'email_id',
        'address',
        'recommended_by',
        'primary_reason',
        'square_feet_range',
        'price_range',
        'customer_opinion',
        'executive_remark',
        'executive_name',
    ];

    public function employee()
    {
        return $this->hasOne('App\Models\User', 'id', 'employee_id');
    }

    public function employees()
    {
        return $this->hasOne('App\Models\Employee', 'id', 'employee_id');
    }
}

