<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Tour;
use App\Services\Tours\TourSeatService;
use App\Support\Html\PlainExcerpt;
use Illuminate\Http\JsonResponse;

class HomeApiController extends Controller
{
    public function __construct(
        private readonly TourSeatService $tourSeats,
    ) {}

    public function index(): JsonResponse
    {
        $promoToursRaw = Tour::query()
            ->with('category')
            ->where('status', 'active')
            ->whereNotNull('discount_price')
            ->whereColumn('discount_price', '<', 'price')
            ->orderByRaw('(price - discount_price) DESC')
            ->limit(6)
            ->get()
            ->values();

        $suggestedToursRaw = Tour::query()
            ->with('category')
            ->where('status', 'active')
            ->orderByDesc('rating')
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->values();

        $ids = $promoToursRaw->pluck('id')->merge($suggestedToursRaw->pluck('id'))->filter()->unique()->values();
        $bookedByTour = $this->tourSeats->sumBookedPeopleKeyedByTourId($ids->all());

        $promoTours = $promoToursRaw->map(fn (Tour $t) => $this->formatPromoTour($t, $bookedByTour));
        $suggestedTours = $suggestedToursRaw->map(fn (Tour $t) => $this->formatPromoTour($t, $bookedByTour));

        $highlights = Category::query()
            ->whereIn('slug', ['domestic', 'international', 'europe', 'asia'])
            ->orderByRaw("FIELD(slug, 'domestic', 'europe', 'asia', 'international')")
            ->get()
            ->unique('slug')
            ->values()
            ->map(fn (Category $c) => [
                'title' => $c->name ?? '',
                'subtitle' => $c->description ?? '',
                'image_url' => $c->image ?? '',
                'is_large' => in_array($c->slug, ['domestic', 'international'], true),
                'pill_label' => $c->slug === 'domestic' ? 'Nội địa' : null,
                'category_slug' => $c->slug,
            ]);

        $heroBanner = Banner::query()->where('placement', 'hero')->first();

        return response()->json([
            'brand_name' => 'Ftravel',
            'hero' => [
                'title_line1' => 'Khám phá thế giới,',
                'title_line2' => 'theo cách của bạn',
                'subtitle' =>
                    'Hành trình vạn dặm bắt đầu từ Ftravel. Trải nghiệm dịch vụ du lịch hàng đầu Việt Nam với những hành trình được tinh tuyển dành riêng cho bạn.',
                'background_image_url' => $heroBanner?->image
                    ?? 'https://lh3.googleusercontent.com/aida-public/AB6AXuC-uKo4b1TnGQR08qw2mowJaYxFLtQtmaW4fpCdB6Ie9nCPXOE7WdAyUR_aghbqpRzP-3fH2nAM0kV19z0_carIlvOGk7iVeHMTN21OgCaZ9XauojbNCvCTzK-4WHi3fI_VuP4q6Wamx-o6Za3eoiumTGULMDfAqKtzj8PerX2EP1AydQIoIFELmoHCUnI28grNPV4nW4reDOGqrVOwinhvfX1Im3iyonurDPQCDrh7BckiMqmHX9ovFI_Ck2uElk_XKmGn_srtC4s',
            ],
            'promo_tours' => $promoTours,
            'suggested_tours' => $suggestedTours,
            'highlights' => $highlights,
        ]);
    }

    private function formatPromoTour(Tour $t, $bookedByTour = null): array
    {
        $price = (float) ($t->price ?? 0);
        $discount = (float) ($t->discount_price ?? $price);
        $badge = null;
        if ($price > 0 && $discount < $price) {
            $pct = (int) round((1 - $discount / $price) * 100);
            $badge = $pct > 0 ? "-{$pct}% OFF" : null;
        }

        $excerpt = PlainExcerpt::fromHtml($t->description, 120);
        if ($excerpt === '') {
            $excerpt = trim((string) ($t->start_location ?? ''));
        }
        if ($excerpt === '') {
            $excerpt = trim((string) ($t->category?->name ?? ''));
        }

        $remainingLabel = null;
        if ($t->max_people !== null) {
            $booked = 0;
            if ($bookedByTour !== null) {
                $row = $bookedByTour[$t->id] ?? null;
                $booked = $row ? (int) $row->booked_people : 0;
            }
            $remain = max(0, (int) $t->max_people - $booked);
            $remainingLabel = sprintf('%02d chỗ', $remain);
        }

        return [
            'id' => $t->id,
            'slug' => $t->slug,
            'title' => $t->name ?? '',
            'duration' => $this->durationLabel($t),
            'image_url' => $t->thumbnail ?? '',
            'badge' => $badge,
            'remaining_label' => $remainingLabel,
            'old_price' => $this->moneyVnd($price),
            'new_price' => $this->moneyVnd($discount),
            'description_excerpt' => $excerpt,
        ];
    }

    private function durationLabel(Tour $t): string
    {
        $d = (int) ($t->duration ?? 0);
        if ($d <= 0) {
            return '';
        }
        $n = max(0, $d - 1);

        return "{$d} Ngày {$n} Đêm";
    }

    private function moneyVnd(float $v): string
    {
        if ($v <= 0) {
            return '0đ';
        }
        $s = number_format($v, 0, ',', '.');

        return "{$s}đ";
    }
}
