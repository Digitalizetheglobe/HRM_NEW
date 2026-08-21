<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    // Define the table name if it's different from the plural form of the model name
    protected $table = 'tasks';

    // Define the fillable columns for mass assignment
    protected $fillable = [
        'task_name',
        'assigned_to',
        'project_id',
        'deadline',
        'status',
        'description',
    ];

    // Relationship with Project model
    // public function project()
    // {
    //     return $this->belongsTo(Project::class, 'project_id');
    // }

    // Relationship with User model (Assignee)
    // public function assignedUser()
    // {
    //     return $this->belongsTo(User::class, 'assigned_to');
    // }
}
