<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectDailyUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'employee_id',
        'module_id',
        'work_date',
        'work_done',
        'hours_worked',
        'progress_before',
        'progress_after',
        'comment',
        'status',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function module()
    {
        return $this->belongsTo(ProjectModule::class);
    }
}
