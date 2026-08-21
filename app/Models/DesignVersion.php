<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'design_id',
        'version',
        'status',
        'client_visible',
        'approved_by',
        'approved_at',
        'rejected_reason',
        'created_by',
    ];

    protected $casts = [
        'client_visible' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function design()
    {
        return $this->belongsTo(Design::class);
    }

    public function links()
    {
        return $this->hasMany(DesignVersionLink::class);
    }

    public function feedbacks()
    {
        return $this->hasMany(DesignFeedback::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
