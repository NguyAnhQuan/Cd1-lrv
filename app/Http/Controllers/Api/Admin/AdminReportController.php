<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Tour;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReportController extends Controller
{
    /** Đặt chỗ không hủy — tính doanh thu & thống kê chính. */
    private function baseBookingQuery()
    {
        return Booking::query()->where(function ($q) {
            $q->whereNull('status')->orWhere('status', '!=', 'cancelled');
        });
    }

    public function dashboard(): JsonResponse
    {
        $now = Carbon::now();

        $revenueTotal = (float) $this->baseBookingQuery()->sum('total_price');
        $toursCount = Tour::query()->count();
        $tours30 = Tour::query()->where('created_at', '>=', $now->copy()->subDays(30))->count();
        $toursPrev30 = Tour::query()->whereBetween('created_at', [
            $now->copy()->subDays(60),
            $now->copy()->subDays(30),
        ])->count();
        $bookingsCount = Booking::query()->count();

        $usersNew30 = User::query()->where('created_at', '>=', $now->copy()->subDays(30))->count();
        $usersNewPrev30 = User::query()->whereBetween('created_at', [
            $now->copy()->subDays(60),
            $now->copy()->subDays(30),
        ])->count();

        $rev7 = (float) $this->baseBookingQuery()
            ->where('created_at', '>=', $now->copy()->subDays(7))
            ->sum('total_price');
        $revPrev7 = (float) $this->baseBookingQuery()
            ->whereBetween('created_at', [$now->copy()->subDays(14), $now->copy()->subDays(7)])
            ->sum('total_price');

        $book7 = $this->baseBookingQuery()
            ->where('created_at', '>=', $now->copy()->subDays(7))
            ->count();
        $bookPrev7 = $this->baseBookingQuery()
            ->whereBetween('created_at', [$now->copy()->subDays(14), $now->copy()->subDays(7)])
            ->count();

        $chartDays = [];
        $toursPerDay = [];
        $usersPerDay = [];
        $ordersPerDay = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = $now->copy()->subDays($i)->startOfDay();
            $dayEnd = $d->copy()->endOfDay();
            $sum = (float) $this->baseBookingQuery()
                ->whereBetween('created_at', [$d, $dayEnd])
                ->sum('total_price');
            $chartDays[] = [
                'label' => $d->format('d/m'),
                'revenue' => $sum,
            ];
            $toursPerDay[] = (int) Tour::query()->whereBetween('created_at', [$d, $dayEnd])->count();
            $usersPerDay[] = (int) User::query()->whereBetween('created_at', [$d, $dayEnd])->count();
            $ordersPerDay[] = (int) $this->baseBookingQuery()
                ->whereBetween('created_at', [$d, $dayEnd])
                ->count();
        }
        $maxDay = max(1e-9, ...array_column($chartDays, 'revenue'));
        foreach ($chartDays as &$row) {
            $row['height'] = round($row['revenue'] / $maxDay, 4);
        }
        unset($row);

        $normSeries = function (array $vals): array {
            $m = max(1e-9, ...$vals);

            return array_map(fn ($v) => round($v / $m, 4), $vals);
        };
        $kpiSparklines = [
            'revenue' => array_column($chartDays, 'height'),
            'tours' => $normSeries($toursPerDay),
            'users' => $normSeries($usersPerDay),
            'orders' => $normSeries($ordersPerDay),
        ];

        $topTours = Booking::query()
            ->select('tour_id', DB::raw('count(*) as c'))
            ->whereNotNull('tour_id')
            ->groupBy('tour_id')
            ->orderByDesc('c')
            ->limit(4)
            ->get();

        $totalTop = max(1, (int) $topTours->sum('c'));
        $destinations = [];
        foreach ($topTours as $row) {
            $tour = Tour::query()->find($row->tour_id);
            if (! $tour) {
                continue;
            }
            $destinations[] = [
                'name' => $tour->name,
                'image_url' => $tour->thumbnail ?? '',
                'bookings' => (int) $row->c,
                'percent' => (int) round(100 * (int) $row->c / $totalTop),
            ];
        }

        /** Lịch khởi hành tour (theo cột departure_date): mỗi ngày có bao nhiêu tour. */
        $tourCalStart = $now->copy()->startOfMonth();
        $tourCalEnd = $now->copy()->addMonths(2)->endOfMonth();
        $toursByDay = [];
        if (Schema::hasColumn((new Tour)->getTable(), 'departure_date')) {
            $tourDateRows = Tour::query()
                ->whereNotNull('departure_date')
                ->whereBetween('departure_date', [$tourCalStart->toDateString(), $tourCalEnd->toDateString()])
                ->get(['departure_date']);

            foreach ($tourDateRows as $trow) {
                $d = $trow->departure_date?->format('Y-m-d');
                if ($d === null) {
                    continue;
                }
                $toursByDay[$d] = ($toursByDay[$d] ?? 0) + 1;
            }
        }

        /** Cộng thêm đặt chỗ theo ngày khởi hành (travel_date) — hiển thị cả khi tour chưa có departure_date. */
        if (Schema::hasColumn((new Booking)->getTable(), 'travel_date')) {
            $bookingRows = $this->baseBookingQuery()
                ->whereNotNull('travel_date')
                ->whereBetween('travel_date', [$tourCalStart->copy()->startOfDay(), $tourCalEnd->copy()->endOfDay()])
                ->get(['travel_date']);

            foreach ($bookingRows as $brow) {
                $d = $brow->travel_date?->format('Y-m-d');
                if ($d === null) {
                    continue;
                }
                $toursByDay[$d] = ($toursByDay[$d] ?? 0) + 1;
            }
        }
        $peakTourDay = empty($toursByDay) ? 0 : (int) max($toursByDay);
        $maxToursNorm = max(1, $peakTourDay);

        $tourDepartureDays = [];
        $cursor = $tourCalStart->copy()->startOfDay();
        $endCursor = $tourCalEnd->copy()->startOfDay();
        while ($cursor->lte($endCursor)) {
            $ds = $cursor->format('Y-m-d');
            $cnt = (int) ($toursByDay[$ds] ?? 0);
            $tourDepartureDays[] = [
                'date' => $ds,
                'tour_count' => $cnt,
                'height' => round($cnt / $maxToursNorm, 4),
            ];
            $cursor->addDay();
        }

        $recent = Booking::query()
            ->with(['tour', 'user', 'detail'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function (Booking $b) {
                $detail = $b->detail;
                $name = $detail?->customer_name ?? $b->user?->name ?? 'Khách';
                $email = $detail?->customer_email ?? $b->user?->email ?? '';
                $initials = $this->initials($name);

                return [
                    'code' => $b->booking_code ?? '#'.$b->id,
                    'customer_name' => $name,
                    'email' => $email,
                    'initials' => $initials,
                    'tour_name' => $b->tour?->name ?? '—',
                    'date_label' => $b->created_at ? $b->created_at->format('d/m/Y') : '—',
                    'total' => (float) ($b->total_price ?? 0),
                    'ui_status' => $this->uiStatus($b),
                ];
            });

        return response()->json([
            'data' => [
                'summary' => [
                    'revenue_total' => $revenueTotal,
                    'revenue_delta_pct' => $this->deltaPct($rev7, $revPrev7),
                    'tours_count' => $toursCount,
                    'tours_delta_pct' => $this->deltaPct((float) $tours30, (float) $toursPrev30),
                    'users_new_30' => $usersNew30,
                    'users_delta_pct' => $this->deltaPct((float) $usersNew30, (float) $usersNewPrev30),
                    'orders_count' => $bookingsCount,
                    'orders_delta_pct' => $this->deltaPct((float) $book7, (float) $bookPrev7),
                ],
                'chart_days' => $chartDays,
                'kpi_sparklines' => $kpiSparklines,
                'tour_departure_calendar' => [
                    'start_date' => $tourCalStart->format('Y-m-d'),
                    'end_date' => $tourCalEnd->format('Y-m-d'),
                    'days' => $tourDepartureDays,
                    'peak_tour_count' => $peakTourDay,
                    'days_with_tours' => count(array_filter($toursByDay, fn ($n) => $n > 0)),
                ],
                'destinations' => $destinations,
                'recent_bookings' => $recent,
            ],
        ]);
    }

    public function statistics(): JsonResponse
    {
        $now = Carbon::now();
        $year = (int) $now->year;
        $prevYear = $year - 1;

        $revenueAll = (float) $this->baseBookingQuery()->sum('total_price');
        $bookingsAll = $this->baseBookingQuery()->count();
        $usersAll = User::query()->count();
        $avgRating = round((float) Review::query()->where('status', 'approved')->avg('rating'), 2);
        if ($avgRating <= 0) {
            $avgRating = round((float) Review::query()->avg('rating'), 2);
        }

        $rev30 = (float) $this->baseBookingQuery()
            ->where('created_at', '>=', $now->copy()->subDays(30))
            ->sum('total_price');
        $revPrev30 = (float) $this->baseBookingQuery()
            ->whereBetween('created_at', [$now->copy()->subDays(60), $now->copy()->subDays(30)])
            ->sum('total_price');

        $b30 = $this->baseBookingQuery()
            ->where('created_at', '>=', $now->copy()->subDays(30))
            ->count();
        $bPrev30 = $this->baseBookingQuery()
            ->whereBetween('created_at', [$now->copy()->subDays(60), $now->copy()->subDays(30)])
            ->count();

        $u30 = User::query()->where('created_at', '>=', $now->copy()->subDays(30))->count();
        $uPrev30 = User::query()->whereBetween('created_at', [
            $now->copy()->subDays(60),
            $now->copy()->subDays(30),
        ])->count();

        $r30 = Review::query()->where('created_at', '>=', $now->copy()->subDays(30))->avg('rating');
        $rPrev30 = Review::query()->whereBetween('created_at', [
            $now->copy()->subDays(60),
            $now->copy()->subDays(30),
        ])->avg('rating');
        $r30 = $r30 ? round((float) $r30, 2) : $avgRating;
        $rPrev30 = $rPrev30 ? round((float) $rPrev30, 2) : $r30;

        $monthsCur = [];
        $monthsPrev = [];
        $monthsCurBookings = [];
        $monthsPrevBookings = [];
        $monthsCurUsers = [];
        $monthsPrevUsers = [];
        $monthsCurRating = [];
        $monthsPrevRating = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthsCur[] = (float) $this->baseBookingQuery()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->sum('total_price');
            $monthsPrev[] = (float) $this->baseBookingQuery()
                ->whereYear('created_at', $prevYear)
                ->whereMonth('created_at', $m)
                ->sum('total_price');

            $monthsCurBookings[] = (int) $this->baseBookingQuery()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->count();
            $monthsPrevBookings[] = (int) $this->baseBookingQuery()
                ->whereYear('created_at', $prevYear)
                ->whereMonth('created_at', $m)
                ->count();

            $monthsCurUsers[] = (int) User::query()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->count();
            $monthsPrevUsers[] = (int) User::query()
                ->whereYear('created_at', $prevYear)
                ->whereMonth('created_at', $m)
                ->count();

            $curAvg = (float) Review::query()
                ->where('status', 'approved')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->avg('rating');
            $prevAvg = (float) Review::query()
                ->where('status', 'approved')
                ->whereYear('created_at', $prevYear)
                ->whereMonth('created_at', $m)
                ->avg('rating');
            $monthsCurRating[] = $curAvg > 0 ? round($curAvg, 2) : 0.0;
            $monthsPrevRating[] = $prevAvg > 0 ? round($prevAvg, 2) : 0.0;
        }

        $maxM = max(1e-9, ...$monthsCur, ...$monthsPrev);
        $revenueMonths = [];
        $labels = ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'];
        for ($i = 0; $i < 12; $i++) {
            $revenueMonths[] = [
                'month' => $labels[$i],
                'current_height' => round($monthsCur[$i] / $maxM, 4),
                'previous_height' => round($monthsPrev[$i] / $maxM, 4),
                'current_revenue' => $monthsCur[$i],
                'previous_revenue' => $monthsPrev[$i],
            ];
        }

        $normSeries = function (array $vals): array {
            $maxV = max(1e-9, ...array_map(fn ($x) => (float) $x, $vals));
            return array_map(fn ($x) => round(((float) $x) / $maxV, 4), $vals);
        };
        $ratingSeries = function (array $vals): array {
            return array_map(function ($x) {
                $v = (float) $x;
                if ($v <= 0) {
                    return 0.0;
                }
                // rating 0–5 => normalize to 0–1
                return round(min(1.0, max(0.0, $v / 5.0)), 4);
            }, $vals);
        };
        $kpiSparklines = [
            'revenue' => array_column($revenueMonths, 'current_height'),
            'bookings' => $normSeries($monthsCurBookings),
            'users' => $normSeries($monthsCurUsers),
            'rating' => $ratingSeries($monthsCurRating),
        ];

        $byCategory = Booking::query()
            ->join('tours', 'bookings.tour_id', '=', 'tours.id')
            ->join('categories', 'tours.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('count(bookings.id) as c'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('c')
            ->limit(5)
            ->get();

        $sumCat = max(1, (int) $byCategory->sum('c'));
        $palette = [0xFFFDC00A, 0xFF64B5F6, 0xFF90A4AE, 0xFF003D7C, 0xFF77D1FF];
        $regions = [];
        foreach ($byCategory->values() as $idx => $row) {
            $regions[] = [
                'name' => $row->name,
                'percent' => round(100 * (int) $row->c / $sumCat, 1),
                'bar_color' => $palette[$idx % count($palette)],
                'trend_up' => true,
            ];
        }

        $topByRev = Booking::query()
            ->select('tour_id', DB::raw('sum(total_price) as rev'))
            ->whereNotNull('tour_id')
            ->groupBy('tour_id')
            ->orderByDesc('rev')
            ->limit(5)
            ->get();

        $topTours = [];
        foreach ($topByRev as $row) {
            $tour = Tour::query()->find($row->tour_id);
            if (! $tour) {
                continue;
            }
            $rev = (float) $row->rev;
            $rev30 = (float) $this->baseBookingQuery()
                ->where('tour_id', $tour->id)
                ->where('created_at', '>=', $now->copy()->subDays(30))
                ->sum('total_price');
            $prevRev = (float) $this->baseBookingQuery()
                ->where('tour_id', $tour->id)
                ->whereBetween('created_at', [$now->copy()->subDays(60), $now->copy()->subDays(30)])
                ->sum('total_price');
            $deltaPct = $this->deltaPct($rev30, $prevRev);
            $topTours[] = [
                'image_url' => $tour->thumbnail ?? '',
                'title' => $tour->name,
                'subtitle' => ($tour->rating ?? 0).' /5 • '.($tour->start_location ?? '—'),
                'revenue' => $rev,
                'delta_pct' => $deltaPct,
            ];
        }

        $b30all = Booking::query()->where('created_at', '>=', $now->copy()->subDays(30))->count();
        $b30paid = Booking::query()
            ->where('created_at', '>=', $now->copy()->subDays(30))
            ->where(function ($q) {
                $q->whereIn('payment_status', ['paid', 'completed'])
                    ->orWhereIn('status', ['paid', 'confirmed', 'completed']);
            })
            ->count();
        $conversionPct = $b30all > 0 ? round(100 * $b30paid / $b30all, 2) : 0.0;

        $convHeights = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = $now->copy()->subDays($i)->startOfDay();
            $dayEnd = $d->copy()->endOfDay();
            $tot = Booking::query()->whereBetween('created_at', [$d, $dayEnd])->count();
            $paid = Booking::query()
                ->whereBetween('created_at', [$d, $dayEnd])
                ->where(function ($q) {
                    $q->whereIn('payment_status', ['paid', 'completed'])
                        ->orWhereIn('status', ['paid', 'confirmed', 'completed']);
                })
                ->count();
            $convHeights[] = $tot > 0 ? round($paid / $tot, 4) : 0.0;
        }
        $maxH = max(0.01, ...$convHeights);
        foreach ($convHeights as &$h) {
            $h = round($h / $maxH, 4);
        }
        unset($h);

        $uTotal = max(1, User::query()->count());
        $u1 = User::query()->where('created_at', '>=', $now->copy()->subYear())->count();
        $u2 = User::query()->whereBetween('created_at', [$now->copy()->subYears(2), $now->copy()->subYear()])->count();
        $u3 = User::query()->where('created_at', '<', $now->copy()->subYears(2))->count();
        $demographics = [
            [
                'label' => 'Đăng ký trong 12 tháng',
                'percent' => (int) round(100 * $u1 / $uTotal),
                'bar_color' => 0xFF003D7C,
            ],
            [
                'label' => '1–2 năm trước',
                'percent' => (int) round(100 * $u2 / $uTotal),
                'bar_color' => 0xFFFDC00A,
            ],
            [
                'label' => 'Trên 2 năm',
                'percent' => (int) round(100 * $u3 / $uTotal),
                'bar_color' => 0xFFC2C6D3,
            ],
        ];

        $expiringSoon = Tour::query()
            ->where('status', 'active')
            ->count();

        return response()->json([
            'data' => [
                'kpis' => [
                    [
                        'label' => 'Tổng doanh thu',
                        'value_key' => 'revenue',
                        'value' => $revenueAll,
                        'delta_pct' => $this->deltaPct($rev30, $revPrev30),
                    ],
                    [
                        'label' => 'Tổng đặt chỗ',
                        'value_key' => 'bookings',
                        'value' => (float) $bookingsAll,
                        'delta_pct' => $this->deltaPct((float) $b30, (float) $bPrev30),
                    ],
                    [
                        'label' => 'Người dùng',
                        'value_key' => 'users',
                        'value' => (float) $usersAll,
                        'delta_pct' => $this->deltaPct((float) $u30, (float) $uPrev30),
                    ],
                    [
                        'label' => 'Đánh giá TB',
                        'value_key' => 'rating',
                        'value' => $avgRating,
                        'delta_pct' => $this->deltaPct($r30, $rPrev30),
                    ],
                ],
                'revenue_months' => $revenueMonths,
                'kpi_sparklines' => $kpiSparklines,
                'regions' => $regions,
                'top_tours' => array_slice($topTours, 0, 4),
                'conversion_pct' => $conversionPct,
                'conversion_heights' => $convHeights,
                'demographics' => $demographics,
                'insight_text' => $expiringSoon > 0
                    ? 'Có '.$expiringSoon.' tour đang hoạt động — theo dõi tỷ lệ lấp đầy và đánh giá khách.'
                    : 'Thêm tour và đơn đặt chỗ để có thống kê đầy đủ hơn.',
                'year_label' => (string) $year,
                'prev_year_label' => (string) $prevYear,
                'revenue_chart_demo' => false,
            ],
        ]);
    }

    /** Xuất CSV đặt chỗ (mở bằng Excel). */
    public function exportBookingsCsv(): StreamedResponse
    {
        $filename = 'dat-cho-'.now()->format('Y-m-d_His').'.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, [
                'Mã đặt chỗ',
                'Tour',
                'Khách hàng',
                'Email',
                'SĐT',
                'Ngày tạo',
                'Ngày đi',
                'Số người',
                'Tổng tiền (VND)',
                'Trạng thái',
                'Thanh toán',
            ]);

            $query = $this->baseBookingQuery()->with(['tour', 'user', 'detail'])->orderByDesc('created_at');

            foreach ($query->cursor() as $b) {
                /** @var Booking $b */
                $d = $b->detail;
                $name = $d?->customer_name ?? $b->user?->name ?? '';
                $email = $d?->customer_email ?? $b->user?->email ?? '';
                $phone = $d?->customer_phone ?? $b->user?->phone ?? '';
                fputcsv($out, [
                    $b->booking_code ?? '#'.$b->id,
                    $b->tour?->name ?? '—',
                    $name,
                    $email,
                    $phone,
                    $b->created_at?->format('d/m/Y H:i') ?? '',
                    $b->travel_date?->format('d/m/Y') ?? '',
                    (string) ($b->number_of_people ?? ''),
                    (string) ($b->total_price ?? '0'),
                    (string) ($b->status ?? ''),
                    (string) ($b->payment_status ?? ''),
                ]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /** Báo cáo PDF tóm tắt doanh thu & bảng theo tháng. */
    public function exportRevenuePdf()
    {
        $now = Carbon::now();
        $year = (int) $now->year;
        $prevYear = $year - 1;

        $revenueAll = (float) $this->baseBookingQuery()->sum('total_price');
        $bookingsAll = $this->baseBookingQuery()->count();
        $usersAll = User::query()->count();
        $avgRating = round((float) Review::query()->where('status', 'approved')->avg('rating'), 2);
        if ($avgRating <= 0) {
            $avgRating = round((float) Review::query()->avg('rating'), 2);
        }

        $monthsCur = [];
        $monthsPrev = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthsCur[] = (float) $this->baseBookingQuery()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->sum('total_price');
            $monthsPrev[] = (float) $this->baseBookingQuery()
                ->whereYear('created_at', $prevYear)
                ->whereMonth('created_at', $m)
                ->sum('total_price');
        }

        $labels = ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'];
        $monthRows = [];
        for ($i = 0; $i < 12; $i++) {
            $monthRows[] = [
                'label' => $labels[$i],
                'curLabel' => number_format($monthsCur[$i], 0, ',', '.'),
                'prevLabel' => number_format($monthsPrev[$i], 0, ',', '.'),
            ];
        }

        $revenueTotalLabel = number_format($revenueAll, 0, ',', '.');

        $pdf = Pdf::loadView('admin.reports.revenue_pdf', [
            'generatedAt' => $now->format('d/m/Y H:i'),
            'year' => $year,
            'prevYear' => $prevYear,
            'revenueTotalLabel' => $revenueTotalLabel,
            'bookingsCount' => $bookingsAll,
            'usersCount' => $usersAll,
            'avgRating' => number_format($avgRating, 2, ',', '.'),
            'monthRows' => $monthRows,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('bao-cao-doanh-thu-'.$year.'.pdf');
    }

    private function deltaPct(float $cur, float $prev): float
    {
        if ($prev <= 0) {
            return $cur > 0 ? 100.0 : 0.0;
        }

        return round(100 * ($cur - $prev) / $prev, 1);
    }

    private function initials(string $name): string
    {
        $parts = preg_split('/\s+/u', trim($name)) ?: [];
        if (count($parts) >= 2) {
            return mb_strtoupper(mb_substr($parts[0], 0, 1).mb_substr($parts[count($parts) - 1], 0, 1));
        }

        return mb_strtoupper(mb_substr($name, 0, 2));
    }

    private function uiStatus(Booking $b): string
    {
        $s = strtolower((string) $b->status);
        $p = strtolower((string) $b->payment_status);
        if ($s === 'cancelled') {
            return 'cancelled';
        }
        if (in_array($p, ['paid', 'completed'], true) || in_array($s, ['paid', 'confirmed', 'completed'], true)) {
            return 'paid';
        }

        return 'pending';
    }
}
