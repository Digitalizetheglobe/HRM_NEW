<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'department_id',
        'name',
    ];

    /**
     * Get the company that owns the designation.
     */
    public function company()
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    /**
     * Get the department that owns the designation.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
