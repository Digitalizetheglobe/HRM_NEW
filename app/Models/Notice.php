<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    protected $table = 'notices';

    protected $fillable = [
        'title',
        'description',
        'notice_startdate',
        'notice_enddate',
        'created_by',
    ];

    protected $dates = ['notice_startdate', 'notice_enddate'];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
