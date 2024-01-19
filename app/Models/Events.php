<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'startingDate',
        'endingDate',
        'title',
        'description',
    ];

    protected $casts = [
        'startingDate' => 'datetime',
        'endingDate' => 'datetime',
        'title' => 'string',
        'text' => 'text',
    ];
}
