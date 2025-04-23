<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomJob extends Model
{
    use HasFactory;
    protected $fillable = [
        'class',
        'method',
        'params',
        'chain',
        'run_at',
        'priority',
        'attempts',
        'last_error',
        'status',
    ];
    protected $casts = [
        'params' => 'array',
        'chain' => 'array',
        'run_at' => 'datetime',
    ];
}
