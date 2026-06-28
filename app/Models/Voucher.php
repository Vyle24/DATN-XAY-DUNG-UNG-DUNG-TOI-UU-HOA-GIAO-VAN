<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $table = 'vouchers';

    public $timestamps = false; // vouchers only has created_at default

    protected $fillable = [
        'code',
        'discount_percent',
        'max_discount',
        'expiry_date',
    ];

    protected $casts = [
        'discount_percent' => 'integer',
        'max_discount' => 'decimal:2',
        'expiry_date' => 'date',
    ];
}
