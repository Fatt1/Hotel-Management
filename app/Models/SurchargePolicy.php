<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurchargePolicy extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'room_type_id',
        'policy_type',
        'hour_mark',
        'price',
    ];

    protected $casts = [
        'hour_mark' => 'decimal:2',
        'price' => 'decimal:2',
    ];

   
}
