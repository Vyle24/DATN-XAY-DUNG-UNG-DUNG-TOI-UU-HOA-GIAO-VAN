<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteOrder extends Model
{
    use HasFactory;

    protected $table = 'route_orders';

    public $timestamps = false; // route_orders only has created_at default

    protected $fillable = [
        'route_id',
        'order_id',
        'stop_sequence',
        'estimated_arrival_time',
    ];

    protected $casts = [
        'estimated_arrival_time' => 'datetime',
    ];

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
