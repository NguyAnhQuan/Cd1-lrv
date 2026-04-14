<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'code',
        'title',
        'scope',
        'discount_type',
        'discount_value',
        'min_order_value',
        'max_discount',
        'quantity',
        'used_count',
        'start_date',
        'end_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'min_order_value' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'used_count' => 'integer',
            'quantity' => 'integer',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }
}

