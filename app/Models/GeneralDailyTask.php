<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralDailyTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'project_name',
        'work_date',
        'duration',
        'task_title',
        'task_description',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
