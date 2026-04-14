<?php

namespace App\Services\Bookings;

use App\Models\Booking;

/**
 * Sinh mã đặt chỗ duy nhất.
 */
final class BookingCodeGenerator
{
    public function generate(): string
    {
        for ($i = 0; $i < 8; $i++) {
            $code = 'BK'.now()->format('ymd').'-'.str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT);
            if (! Booking::query()->where('booking_code', $code)->exists()) {
                return $code;
            }
        }

        return 'BK'.strtoupper(bin2hex(random_bytes(6)));
    }
}
