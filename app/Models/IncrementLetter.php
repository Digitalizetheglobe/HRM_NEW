<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncrementLetter extends Model
{
    protected $fillable = [
        'lang',
        'content',
        'created_by'
    ];

    public static function replaceVariable($content, $obj)
    {
        foreach ($obj as $key => $value) {
            $content = str_replace('{' . $key . '}', $value, $content);
        }
        return $content;
    }
}