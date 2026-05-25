<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'title',
        'content',
        'summary',
        'category',
        'color',
        'embedding',
    ];

    protected $casts = [
        'embedding' => 'array',
    ];
}
