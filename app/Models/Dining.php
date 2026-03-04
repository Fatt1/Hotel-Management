<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dining extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'opening_hours',
        'location',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
