<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'design_id',
        'design_version_id',
        'user_id',
        'activity_type',
        'description',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function design()
    {
        return $this->belongsTo(Design::class);
    }

    public function designVersion()
    {
        return $this->belongsTo(DesignVersion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
