<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Branch extends Model
{
    protected $fillable = [
        'name','created_by'
    ];

    public function departments()
    {
        return $this->hasMany(Department::class);
    }
    
    public function employees()
    {
        return $this->hasManyThrough(Employee::class, Department::class);
    }

    
}
