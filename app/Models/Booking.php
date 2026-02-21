<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'customer_id',
        'booking_date',
        'staff_id',
        'total_service_amount',
        'total_room_amount',
        'surcharge_amount',
        'final_amount',
    ];

    protected $casts = [
        'booking_date' => 'datetime',
        'total_service_amount' => 'decimal:2',
        'total_room_amount' => 'decimal:2',
        'surcharge_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function bookingDetails(): HasMany
    {
        return $this->hasMany(BookingDetail::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
