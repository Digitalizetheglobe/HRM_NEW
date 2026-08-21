<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    protected $fillable = [
        'employee_id',
        'document_id',
        'document_value',
        'created_by'
    ];

    // Relationship to Document model
    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }
}