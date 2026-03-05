<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceGroup extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['service_name'];

    public function services()
    {
        return $this->hasMany(Service::class, 'group_id');
    }
}
