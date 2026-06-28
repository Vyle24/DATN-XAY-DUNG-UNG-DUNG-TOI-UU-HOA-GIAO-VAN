<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'order_code',
        'customer_id',
        'shipper_id',
        'hub_id',
        'pickup_address',
        'pickup_lat',
        'pickup_lng',
        'delivery_address',
        'delivery_lat',
        'delivery_lng',
        'total_weight',
        'shipping_fee',
        'payment_method',
        'status',
        'assignment_status',
    ];

    protected $casts = [
        'pickup_lat' => 'decimal:8',
        'pickup_lng' => 'decimal:8',
        'delivery_lat' => 'decimal:8',
        'delivery_lng' => 'decimal:8',
        'total_weight' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
    ];

    public function shipper()
    {
        return $this->belongsTo(Shipper::class, 'shipper_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function hub()
    {
        return $this->belongsTo(Hub::class, 'hub_id');
    }

    public function trackingLogs()
    {
        return $this->hasMany(TrackingLog::class, 'order_id')->orderBy('id', 'desc');
    }

    public function shipperAssignments()
    {
        return $this->hasMany(ShipperAssignment::class, 'order_id')->orderBy('id', 'desc');
    }

    public function routeOrders()
    {
        return $this->hasMany(RouteOrder::class, 'order_id');
    }
}
