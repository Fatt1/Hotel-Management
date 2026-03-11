<?php

namespace App\Models;

use App\Enums\ActionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Staff extends Authenticatable
{
    use HasFactory, Notifiable;

    public $timestamps = false;

    protected $table = 'staffs';

    protected $fillable = [
        'role_id',
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'is_active',
        'password',
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function reportedTickets(): HasMany
    {
        return $this->hasMany(MaintenanceTicket::class, 'reported_by_staff_id');
    }

    public function technicianTickets(): HasMany
    {
        return $this->hasMany(MaintenanceTicket::class, 'technician_id');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function canAction(string $function, string $action_name): bool
    {
        $role = $this->role;
        if (!$role)
            return false;

        $action_bit = ActionType::fromName($action_name);
        if (!$action_bit)
            return false;

        $cacheKey = "role_claims_{$role->id}";
        $claims = Cache::get($cacheKey);

        if ($claims === null) {
            $claims = $role->claims()
                ->get()
                ->pluck('claim_value', 'claim_name')
                ->map(fn ($val) => (int) $val)
                ->all();

            Cache::forever($cacheKey, $claims);
        }

        if (!isset($claims[$function]))
            return false;

        return ($claims[$function] & $action_bit) === $action_bit;
    }
}
