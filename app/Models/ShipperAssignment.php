<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipperAssignment extends Model
{
    use HasFactory;

    protected $table = 'shipper_assignments';

    public $timestamps = false; // shipper_assignments has custom timestamp fields: assigned_at, responded_at

    protected $fillable = [
        'shipper_id',
        'order_id',
        'route_id',
        'status',
        'assigned_at',
        'responded_at',
    ];

    protected $dates = [
        'assigned_at',
        'responded_at',
    ];

    public function shipper()
    {
        return $this->belongsTo(Shipper::class, 'shipper_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }
}
