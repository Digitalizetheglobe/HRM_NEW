<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToDoList extends Model
{
    use HasFactory;

    /**
     * The name of the table associated with the model.
     */
    protected $table = 'to_do_lists';  // Explicitly specify the table name

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id', 
        'task', 
        'priority', 
        'is_completed', 
        'expires_at',
        'start_date',
        'end_date',
        'description'
    ];

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = true;

    /**
     * Accessor for is_completed (convert 0/1 to boolean).
     */
    public function getIsCompletedAttribute($value)
    {
        return (bool) $value;
    }
}
