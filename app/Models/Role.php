<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(RoleClaim::class);
    }

    
}
