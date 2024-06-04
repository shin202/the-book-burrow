<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cost',
        'effective_date',
    ];

    protected $casts = [
        'effective_date' => 'datetime',
        'cost' => 'double'
    ];
}
