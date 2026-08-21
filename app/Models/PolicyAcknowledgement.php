<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PolicyAcknowledgement extends Model
{
    protected $fillable = [
        'company_policy_id',
        'employee_id',
        'user_id',
        'has_previewed',
        'has_downloaded',
        'previewed_at',
        'downloaded_at',
        'acknowledged_at',
        'ip_address',
    ];

    protected $casts = [
        'has_previewed' => 'boolean',
        'has_downloaded' => 'boolean',
        'previewed_at' => 'datetime',
        'downloaded_at' => 'datetime',
        'acknowledged_at' => 'datetime',
    ];

    /**
     * Get the company policy that this acknowledgement belongs to
     */
    public function companyPolicy()
    {
        return $this->belongsTo(CompanyPolicy::class, 'company_policy_id');
    }

    /**
     * Get the employee who acknowledged
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the user who acknowledged
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Check if employee can acknowledge (must have previewed or downloaded)
     */
    public function canAcknowledge()
    {
        return $this->has_previewed || $this->has_downloaded;
    }

    /**
     * Check if already acknowledged
     */
    public function isAcknowledged()
    {
        return !is_null($this->acknowledged_at);
    }
}


