<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RoomType extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'code',
        'adult_quantity',
        'child_quantity',
        'single_bed_quantity',
        'double_bed_quantity',
        'width',
        'height',
        'hourly_price',
        'daily_price',
    ];

    protected $casts = [
        'adult_quantity' => 'integer',
        'child_quantity' => 'integer',
        'single_bed_quantity' => 'integer',
        'double_bed_quantity' => 'integer',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'hourly_price' => 'decimal:2',
        'daily_price' => 'decimal:2',
    ];

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function equipments(): BelongsToMany
    {
        return $this->belongsToMany(Equipment::class, 'room_equipment')
            ->withPivot('quantity');
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'room_type_amenities');
    }

    public function images(): HasMany
    {
        return $this->hasMany(RoomTypeImage::class);
    }


}
