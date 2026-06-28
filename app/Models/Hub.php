<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hub extends Model
{
    use HasFactory;

    protected $table = 'hubs';

    protected $fillable = [
        'name',
        'city',
        'address',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude'  => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'hub_id');
    }
}
