<?php

namespace App\Models;

use App\Enums\ActionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

     public function canAction(string $function, string $action_name):bool
    {
        $role = $this->role;
        if(!$role)
            return false;
        $action_bit = ActionType::fromName($action_name);
        // Nếu không tìm thấy action trong enum
        if(!$action_bit)
            return false;
        // Lưu ý: Nên Cache đoạn này lại để không query DB liên tục
        $role_claim = $role->claims()->where('claim_name', $function)->first();  
        if(!$role_claim)
            return false;
        return ($role_claim->claim_value & $action_bit) === $action_bit;
    }
}
