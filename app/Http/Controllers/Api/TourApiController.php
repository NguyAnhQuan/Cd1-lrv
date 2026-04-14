<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Services\Tours\TourSeatService;
use App\Support\Html\PlainExcerpt;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TourApiController extends Controller
{
    public function __construct(
        private readonly TourSeatService $tourSeats,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $scope = $request->query('scope', 'all');
        $departureDay = $request->query('departure_date');

        $q = Tour::query()
            ->with('category')
            ->where('status', 'active');

        if ($scope === 'domestic') {
            $q->whereHas('category', fn ($c) => $c->where('slug', 'domestic'));
        } elseif ($scope === 'international') {
            $q->whereHas('category', fn ($c) => $c->where('slug', 'international'));
        }

        if ($departureDay !== null && $departureDay !== '') {
            try {
                $d = Carbon::parse($departureDay)->startOfDay();
                $q->where(function ($w) use ($d) {
                    $w->whereNull('departure_date')
                        ->orWhereDate('departure_date', $d->toDateString());
                });
            } catch (\Throwable) {
                // bỏ lọc ngày nếu sai định dạng
            }
        }

        $rows = $q->orderByDesc('id')->get()->values();
        $ids = $rows->pluck('id')->filter()->unique()->values();
        $bookedByTour = $this->tourSeats->sumBookedPeopleKeyedByTourId($ids->all());
        $tours = $rows->map(fn (Tour $t) => $this->formatTourCard($t, $bookedByTour));

        return response()->json(['data' => $tours]);
    }

    public function show(string $slug): JsonResponse
    {
        $q = Tour::query()->with(['category', 'images', 'itineraries']);

        $tour = $q->clone()->where('slug', $slug)->first();
        if (! $tour && ctype_digit($slug)) {
            $tour = $q->clone()->find((int) $slug);
        }

        if (! $tour) {
            return response()->json(['message' => 'Không tìm thấy tour'], 404);
        }

        return response()->json(['data' => $this->formatTourDetail($tour)]);
    }

    private function formatTourCard(Tour $t, $bookedByTour = null): array
    {
        $price = (float) ($t->discount_price ?? $t->price ?? 0);
        $base = (float) ($t->price ?? $price);

        $excerpt = PlainExcerpt::fromHtml($t->description, 140);
        if ($excerpt === '') {
            $excerpt = trim((string) ($t->start_location ?? ''));
        }
        if ($excerpt === '') {
            $excerpt = trim((string) ($t->category?->name ?? ''));
        }

        $remainingSeats = null;
        if ($t->max_people !== null) {
            $booked = 0;
            if ($bookedByTour !== null) {
                $row = $bookedByTour[$t->id] ?? null;
                $booked = $row ? (int) $row->booked_people : 0;
            }
            $remainingSeats = max(0, (int) $t->max_people - $booked);
        }

        return [
            'id' => $t->id,
            'slug' => $t->slug,
            'name' => $t->name,
            'thumbnail' => $t->thumbnail,
            'duration' => $t->duration,
            'duration_label' => $this->durationLabel($t),
            'rating' => (float) ($t->rating ?? 4.8),
            'price' => $base,
            'discount_price' => $t->discount_price !== null ? (float) $t->discount_price : null,
            'price_from' => $this->moneyVnd($price),
            'badge_label' => $t->badge_label,
            'badge_variant' => $t->badge_variant,
            'meta_icon1' => $t->meta_icon1 ?? 'flight',
            'meta_text1' => $t->meta_text1 ?? 'VNA',
            'meta_icon2' => $t->meta_icon2 ?? 'hotel',
            'meta_text2' => $t->meta_text2 ?? 'Khách sạn',
            'departure_date' => $t->departure_date?->format('Y-m-d'),
            'description_excerpt' => $excerpt,
            'max_people' => $t->max_people,
            'remaining_seats' => $remainingSeats,
        ];
    }

    private function formatTourDetail(Tour $t): array
    {
        $card = $this->formatTourCard($t);

        return array_merge($card, [
            'description' => $t->description,
            'start_location' => $t->start_location,
            'max_people' => $t->max_people,
            'category' => $t->category ? [
                'id' => $t->category->id,
                'name' => $t->category->name,
                'slug' => $t->category->slug,
            ] : null,
            'gallery' => $t->images->pluck('image_url')->filter()->values(),
            'itineraries' => $t->itineraries->map(fn ($i) => [
                'day_number' => $i->day_number,
                'title' => $i->title,
                'description' => $i->description,
            ]),
        ]);
    }

    private function durationLabel(Tour $t): string
    {
        $d = (int) ($t->duration ?? 0);
        if ($d <= 0) {
            return '';
        }
        $n = max(0, $d - 1);

        return "{$d}N{$n}Đ";
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
