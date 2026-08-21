<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Unit extends Model
{

    use HasFactory;

    protected $table = 'units'; // Explicitly set the table name
protected $fillable = ['unit_name','unit_size', 'project_id', 'is_approved'];

public function project()
{
    return $this->belongsTo(Project::class);
}


public function scopeAvailable($query)
{
    return $query->where('is_approved', 0);
}

public function scopeBooked($query)
{
    return $query->where('is_approved', 1);
}


}