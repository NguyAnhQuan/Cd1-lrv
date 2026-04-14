<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'tour_id',
        'coupon_id',
        'coupon_code',
        'coupon_discount_vnd',
        'booking_code',
        'total_price',
        'status',
        'payment_status',
        'booking_date',
        'travel_date',
        'number_of_people',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'coupon_discount_vnd' => 'integer',
            'booking_date' => 'datetime',
            'travel_date' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detail(): HasOne
    {
        return $this->hasOne(BookingDetail::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
