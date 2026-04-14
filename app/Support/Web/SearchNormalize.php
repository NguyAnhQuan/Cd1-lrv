<?php

namespace App\Support\Web;

use Illuminate\Support\Str;

/**
 * Chuẩn hóa tìm kiếm giống FE Flutter (search_normalize.dart).
 */
final class SearchNormalize
{
    public static function normalize(string $input): string
    {
        $s = Str::ascii(mb_strtolower($input, 'UTF-8'));

        return trim(preg_replace('/\s+/', ' ', $s) ?? '');
    }

    public static function matches(string $haystack, string $query): bool
    {
        $qRaw = trim($query);
        if ($qRaw === '') {
            return true;
        }
        $q = self::normalize($qRaw);
        if ($q === '') {
            return true;
        }
        $h = self::normalize($haystack);
        if (str_contains($h, $q)) {
            return true;
        }

        $hCompact = str_replace(' ', '', $h);
        $qCompact = str_replace(' ', '', $q);

        return str_contains($hCompact, $qCompact);
    }
}
