<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['name', 'group_id', 'unit_price', 'unit'];

    public function group()
    {
        return $this->belongsTo(ServiceGroup::class, 'group_id');
    }
}
