<?php

namespace App\Support\Html;

use Illuminate\Support\Str;

/**
 * Rút gọn mô tả HTML thành plain text (dùng chung Home / Tour API).
 */
final class PlainExcerpt
{
    public static function fromHtml(?string $html, int $limit = 120): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }
        $plain = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $plain = preg_replace('/\s+/u', ' ', trim($plain));

        return Str::limit($plain, $limit, '…');
    }
}
