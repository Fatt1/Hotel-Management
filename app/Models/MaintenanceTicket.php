<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceTicket extends Model
{
    protected $fillable = [
        'room_id',
        'equipment_id',
        'reported_date',
        'issue_description',
        'technician_note',
        'status',
        'repair_cost',
        'reported_by_staff_id',
        'technician_id',
    ];

    protected $casts = [
        'reported_date' => 'date',
        'repair_cost' => 'decimal:2',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function reportedByStaff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'reported_by_staff_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'technician_id');
    }
}
