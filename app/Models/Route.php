<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $table = 'routes';

    protected $fillable = [
        'shipper_id',
        'optimized_path',
        'total_distance',
        'estimated_time',
    ];

    protected $casts = [
        'optimized_path' => 'array',
        'total_distance' => 'decimal:2',
    ];

    public function shipper()
    {
        return $this->belongsTo(Shipper::class, 'shipper_id');
    }

    public function routeOrders()
    {
        return $this->hasMany(RouteOrder::class, 'route_id')->orderBy('stop_sequence', 'asc');
    }
}
