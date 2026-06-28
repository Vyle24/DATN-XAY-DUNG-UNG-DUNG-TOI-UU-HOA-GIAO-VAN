<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistrictShippingRate extends Model
{
    use HasFactory;

    protected $table = 'districts_shipping_rates';

    public $timestamps = false;

    protected $fillable = [
        'district_name',
        'city',
        'base_price',
        'price_per_km',
    ];

    protected $casts = [
        'base_price'   => 'decimal:2',
        'price_per_km' => 'decimal:2',
    ];
}
