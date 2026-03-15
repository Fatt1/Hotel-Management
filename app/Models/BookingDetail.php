<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'room_id',
        'booking_id',
        'checkin_date',
        'checkout_date',
        'checkout_status',
        'hourly_price',
        'daily_price',
        'room_amount',
        'service_amount',
        'surcharge_amount',
    ];

    protected $casts = [
        'checkin_date' => 'datetime',
        'checkout_date' => 'datetime',
        'hourly_price' => 'decimal:2',
        'daily_price' => 'decimal:2',
        'room_amount' => 'decimal:2',
        'service_amount' => 'decimal:2',
        'surcharge_amount' => 'decimal:2',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function serviceUsages(): HasMany
    {
        return $this->hasMany(ServiceUsage::class);
    }
}
