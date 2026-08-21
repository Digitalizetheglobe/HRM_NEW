<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyPolicy extends Model
{
    protected $fillable = [
        'branch',
        'title',
        'description',
        'file',
        'created_by',
    ];

    public function branches()
    {
        return $this->hasOne('App\Models\Branch', 'id', 'branch');
    }

    /**
     * Get all acknowledgements for this policy
     */
    public function acknowledgements()
    {
        return $this->hasMany(PolicyAcknowledgement::class, 'company_policy_id');
    }

    /**
     * Get acknowledgement for a specific employee
     */
    public function getAcknowledgementForEmployee($employeeId)
    {
        return $this->acknowledgements()->where('employee_id', $employeeId)->first();
    }
}
