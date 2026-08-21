<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignFeedbackAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'feedback_id',
        'file_name',
        'file_path',
        'uploaded_by',
    ];

    public function feedback()
    {
        return $this->belongsTo(DesignFeedback::class, 'feedback_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
