<?php

namespace App\Services\Coupons;

use App\Models\Coupon;
use App\Models\Tour;
use Carbon\Carbon;

/**
 * Nghiệp vụ voucher/coupon dùng chung (validate, giảm giá, tra cứu theo mã).
 */
final class CouponService
{
    public function normalizeCode(string $code): string
    {
        return strtoupper(trim($code));
    }

    public function findByNormalizedCode(string $normalizedCode): ?Coupon
    {
        if ($normalizedCode === '') {
            return null;
        }

        return Coupon::query()->whereRaw('UPPER(code) = ?', [$normalizedCode])->first();
    }

    public function isActiveNow(Coupon $coupon): bool
    {
        $now = Carbon::now();
        $status = strtolower(trim((string) ($coupon->status ?? 'active')));
        if ($status !== '' && $status !== 'active') {
            return false;
        }
        if ($coupon->start_date !== null) {
            $sd = $coupon->start_date instanceof Carbon ? $coupon->start_date : Carbon::parse($coupon->start_date);
            if ($sd->gt($now)) {
                return false;
            }
        }
        if ($coupon->end_date !== null) {
            $ed = $coupon->end_date instanceof Carbon ? $coupon->end_date : Carbon::parse($coupon->end_date);
            if ($ed->lt($now)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Còn lượt dùng toàn hệ thống (theo quantity vs used_count).
     */
    public function hasGlobalUsesRemaining(Coupon $coupon): bool
    {
        if ($coupon->quantity === null) {
            return true;
        }

        return (int) $coupon->quantity > (int) ($coupon->used_count ?? 0);
    }

    /**
     * Giảm giá VND áp vào giá tour (cùng logic với Flutter checkout).
     */
    public function computeDiscountVndForTour(Coupon $coupon, Tour $tour): int
    {
        $dp = $tour->discount_price;
        $p = $tour->price;
        $base = (float) ($dp !== null ? $dp : ($p ?? 0));
        if ($base <= 0) {
            return 0;
        }
        $min = (float) ($coupon->min_order_value ?? 0);
        if ($min > 0 && $base < $min) {
            return 0;
        }
        $type = strtolower(trim((string) ($coupon->discount_type ?? '')));
        $value = (float) ($coupon->discount_value ?? 0);
        $raw = 0.0;
        if ($type === 'percent' || str_contains($type, 'percent')) {
            $raw = $base * ($value / 100.0);
        } else {
            $raw = $value;
        }
        $max = (float) ($coupon->max_discount ?? 0);
        if ($max > 0) {
            $raw = min($raw, $max);
        }
        $raw = max(0.0, min($raw, $base));

        return (int) round($raw);
    }
}
