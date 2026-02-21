<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'group_id',
        'unit_price',
        'unit',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(ServiceGroup::class, 'group_id');
    }

    public function serviceUsages(): HasMany
    {
        return $this->hasMany(ServiceUsage::class);
    }
}
