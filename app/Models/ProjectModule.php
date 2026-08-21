<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'module_name',
        'description',
        'employee_ids',
        'progress',
        'status',
    ];

    protected $casts = [
        'employee_ids' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function employees()
    {
        if (empty($this->employee_ids)) {
            return collect([]);
        }
        return Employee::whereIn('id', $this->employee_ids)->get();
    }
}
