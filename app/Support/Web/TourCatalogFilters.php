<?php

namespace App\Support\Web;

/**
 * Lọc/sắp xếp danh sách tour từ API giống tour_catalog_page.dart.
 *
 * @param  list<array<string, mixed>>  $tours
 * @return list<array<string, mixed>>
 */
final class TourCatalogFilters
{
    /**
     * @param  array<string, mixed>  $query
     * @return list<array<string, mixed>>
     */
    public static function apply(array $tours, array $query): array
    {
        $q = trim((string) ($query['q'] ?? ''));
        $departureFilter = isset($query['departure_date']) ? trim((string) $query['departure_date']) : '';

        $priceChecks = [
            isset($query['price_0']),
            isset($query['price_1']),
            isset($query['price_2']),
        ];
        $anyPrice = $priceChecks[0] || $priceChecks[1] || $priceChecks[2];

        $durationBand = $query['duration'] ?? null;
        $durationBand = $durationBand === '' || $durationBand === null ? null : (int) $durationBand;

        $list = [];
        foreach ($tours as $t) {
            if (! is_array($t)) {
                continue;
            }
            $list[] = $t;
        }

        if ($q !== '') {
            $list = array_values(array_filter($list, function (array $e) use ($q): bool {
                $title = (string) ($e['name'] ?? '');
                $m1 = (string) ($e['meta_text1'] ?? '');
                $m2 = (string) ($e['meta_text2'] ?? '');
                $ex = (string) ($e['description_excerpt'] ?? '');

                return SearchNormalize::matches($title, $q)
                    || SearchNormalize::matches($m1, $q)
                    || SearchNormalize::matches($m2, $q)
                    || ($ex !== '' && SearchNormalize::matches($ex, $q));
            }));
        }

        if ($departureFilter !== '') {
            $list = array_values(array_filter($list, function (array $e) use ($departureFilter): bool {
                $dd = $e['departure_date'] ?? null;
                if ($dd === null || $dd === '') {
                    return true;
                }

                return (string) $dd === $departureFilter;
            }));
        }

        if ($anyPrice) {
            $list = array_values(array_filter($list, function (array $e) use ($priceChecks): bool {
                $p = self::priceVndOf($e);
                $ok = false;
                if ($priceChecks[0] && $p < 5_000_000) {
                    $ok = true;
                }
                if ($priceChecks[1] && $p >= 5_000_000 && $p < 10_000_000) {
                    $ok = true;
                }
                if ($priceChecks[2] && $p >= 10_000_000 && $p <= 20_000_000) {
                    $ok = true;
                }

                return $ok;
            }));
        }

        if ($durationBand !== null) {
            $list = array_values(array_filter($list, function (array $e) use ($durationBand): bool {
                $d = self::durationDaysOf($e);
                if ($d <= 0) {
                    return false;
                }
                return match ($durationBand) {
                    0 => $d >= 1 && $d <= 3,
                    1 => $d >= 4 && $d <= 7,
                    2 => $d > 7,
                    default => true,
                };
            }));
        }

        $sort = (int) ($query['sort'] ?? 0);
        if ($sort === 1) {
            usort($list, function (array $a, array $b): int {
                return self::priceVndOf($b) <=> self::priceVndOf($a);
            });
        }

        return $list;
    }

    /**
     * @param  array<string, mixed>  $e
     */
    public static function paginate(array $list, int $page, int $perPage): array
    {
        $page = max(1, $page);
        $total = count($list);
        $totalPages = max(1, (int) ceil($total / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $start = ($page - 1) * $perPage;
        $slice = array_slice($list, $start, $perPage);

        return [
            'items' => $slice,
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => $totalPages,
        ];
    }

    /**
     * @param  array<string, mixed>  $e
     */
    private static function priceVndOf(array $e): int
    {
        $dp = $e['discount_price'] ?? null;
        $bp = $e['price'] ?? null;
        if (is_numeric($dp) && (float) $dp > 0) {
            return (int) round((float) $dp);
        }
        if (is_numeric($bp) && (float) $bp > 0) {
            return (int) round((float) $bp);
        }
        $from = (string) ($e['price_from'] ?? '');
        $digits = preg_replace('/\D/', '', $from);

        return (int) ($digits !== '' ? $digits : 0);
    }

    /**
     * @param  array<string, mixed>  $e
     */
    private static function durationDaysOf(array $e): int
    {
        $d = $e['duration'] ?? null;
        if (is_numeric($d) && (int) $d > 0) {
            return (int) $d;
        }
        $label = trim((string) ($e['duration_label'] ?? ''));
        if (preg_match('/^(\d+)/', $label, $m)) {
            return (int) $m[1];
        }

        return 0;
    }
}
