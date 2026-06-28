<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipper extends Model
{
    use HasFactory;

    protected $table = 'shippers';

    protected $fillable = [
        'user_id',
        'license_no',
        'vehicle_type',
        'wallet_balance',
        'current_lat',
        'current_lng',
        'is_active',
        'region',
    ];

    protected $casts = [
        'wallet_balance' => 'decimal:2',
        'current_lat' => 'decimal:8',
        'current_lng' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'shipper_id');
    }

    public function routes()
    {
        return $this->hasMany(Route::class, 'shipper_id');
    }
}
