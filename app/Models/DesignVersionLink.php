<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DesignVersionLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'design_version_id',
        'title',
        'url',
    ];

    public function designVersion()
    {
        return $this->belongsTo(DesignVersion::class);
    }
}
