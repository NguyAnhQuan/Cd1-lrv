<?php

namespace App\Services\Bookings;

use App\Models\Tour;

/**
 * Tổng tiền checkout — khớp công thức Flutter (BookingTourSummary.fromTourCatalogItem).
 */
final class CheckoutPricingService
{
    public function computeTotalVnd(Tour $tour, int $couponDiscountVnd = 0): int
    {
        $dp = $tour->discount_price;
        $p = $tour->price;
        $base = (float) ($dp !== null ? $dp : ($p ?? 0));
        if ($base <= 0) {
            return 0;
        }
        $tax = (int) round($base * 0.1);
        $discount = (int) round($base * 0.03);

        $total = (int) round($base + $tax - $discount - max(0, $couponDiscountVnd));

        return max(0, $total);
    }
}
