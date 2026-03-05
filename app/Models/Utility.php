<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Utility extends Model
{
    protected $table = 'amenities';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'icon',
    ];
}
