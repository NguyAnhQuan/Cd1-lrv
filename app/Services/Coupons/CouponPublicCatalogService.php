<?php

namespace App\Services\Coupons;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Danh sách coupon công khai + DTO hiển thị (trang voucher user).
 */
final class CouponPublicCatalogService
{
    public function activePublicCouponsQuery(): Builder
    {
        $now = Carbon::now();

        return Coupon::query()
            ->where(function ($q) {
                $q->whereNull('status')->orWhere('status', 'active');
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) {
                $q->whereNull('quantity')
                    ->orWhereColumn('quantity', '>', 'used_count');
            })
            ->orderByDesc('id');
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function mapRowsToPublicDtos(Collection $rows): array
    {
        return $rows->values()->map(fn (Coupon $c, int $i) => $this->toPublicDto($c, $i))->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function toPublicDto(Coupon $c, int $index): array
    {
        $scope = $this->normalizeScope($c->scope);
        $palette = $this->paletteAt($index);

        return [
            'code' => (string) ($c->code ?? ''),
            'value_short' => $this->formatDiscountShort($c),
            'header_title' => $this->headerTitle($c),
            'expiry_label' => $this->expiryLabel($c),
            'bullets' => $this->bullets($c),
            'category' => $scope,
            'collected' => false,
            'accent_color' => $palette['accent'],
            'value_box_color' => $palette['valueBox'],
            'header_bg_color' => $palette['headerBg'],
        ];
    }

    private function normalizeScope(?string $scope): string
    {
        $s = strtolower(trim((string) $scope));
        $allowed = ['domestic', 'international', 'bank', 'hotel', 'all'];
        if (in_array($s, $allowed, true)) {
            return $s;
        }

        return 'all';
    }

    private function headerTitle(Coupon $c): string
    {
        $t = trim((string) ($c->title ?? ''));
        if ($t !== '') {
            return $t;
        }
        $code = (string) ($c->code ?? 'Ưu đãi');

        return 'Ưu đãi '.$code;
    }

    private function formatDiscountShort(Coupon $c): string
    {
        $type = strtolower((string) ($c->discount_type ?? ''));
        $v = (float) ($c->discount_value ?? 0);
        if ($type === 'percent' || str_contains($type, 'percent')) {
            $n = round($v);
            if (abs($n - (int) $n) < 0.01) {
                return (string) (int) $n.'%';
            }

            return rtrim(rtrim(number_format($v, 1, '.', ''), '0'), '.').'%';
        }
        if ($v >= 1_000_000) {
            return round($v / 1_000_000, $v >= 10_000_000 ? 0 : 1).'tr';
        }
        if ($v >= 1000) {
            return round($v / 1000).'k';
        }

        return number_format($v, 0, ',', '.').'đ';
    }

    private function expiryLabel(Coupon $c): string
    {
        if ($c->end_date === null) {
            return 'Không giới hạn thời hạn';
        }
        $d = $c->end_date instanceof Carbon ? $c->end_date : Carbon::parse($c->end_date);

        return 'Hết hạn: '.$d->format('d/m/Y');
    }

    /** @return list<string> */
    private function bullets(Coupon $c): array
    {
        $out = [];
        $min = $c->min_order_value;
        if ($min !== null && (float) $min > 0) {
            $out[] = 'Đơn từ '.number_format((float) $min, 0, ',', '.').'đ';
        }
        $max = $c->max_discount;
        if ($max !== null && (float) $max > 0) {
            $out[] = 'Giảm tối đa '.number_format((float) $max, 0, ',', '.').'đ';
        }
        $qty = $c->quantity;
        if ($qty !== null) {
            $left = max(0, (int) $qty - (int) ($c->used_count ?? 0));
            $out[] = 'Còn '.$left.' lượt';
        }
        if ($out === []) {
            $out[] = 'Áp dụng theo điều kiện tại bước thanh toán';
        }

        return array_slice($out, 0, 4);
    }

    /** Màu preset xen kẽ — khớp tone app (0xAARRGGBB cho Flutter). */
    private function paletteAt(int $index): array
    {
        $palettes = [
            ['accent' => 0xFF003D7C, 'valueBox' => 0xFF0054A6, 'headerBg' => 0xFFEFF6FF],
            ['accent' => 0xFFFDC00A, 'valueBox' => 0xFFFDC00A, 'headerBg' => 0xFFF8FAFC],
            ['accent' => 0xFF00435C, 'valueBox' => 0xFF005C7C, 'headerBg' => 0xFFEFF6FF],
            ['accent' => 0xFF003D7C, 'valueBox' => 0xFF0054A6, 'headerBg' => 0xFFEFF6FF],
        ];

        return $palettes[$index % count($palettes)];
    }
}
