<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignFeedback extends Model
{
    use HasFactory;

    protected $table = 'design_feedbacks';

    protected $fillable = [
        'design_version_id',
        'title',
        'feedback_type',
        'comment',
        'status',
        'priority',
        'due_date',
        'submitted_by',
        'assigned_to',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function designVersion()
    {
        return $this->belongsTo(DesignVersion::class);
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function attachments()
    {
        return $this->hasMany(DesignFeedbackAttachment::class, 'feedback_id');
    }
}
