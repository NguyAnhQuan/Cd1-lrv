<?php

namespace App\Services;

/**
 * VietQR Quick Link — ảnh QR từ img.vietqr.io.
 *
 * @see https://www.vietqr.io/
 */
class VietQrService
{
    public function buildImageUrl(int $amountVnd, string $addInfo): string
    {
        $bankId = (string) config('vietqr.bank_id');
        $accountNo = (string) config('vietqr.account_no');
        $template = (string) config('vietqr.template');
        $accountName = (string) config('vietqr.account_name');

        $query = http_build_query(
            [
                'amount' => $amountVnd,
                'addInfo' => $addInfo,
                'accountName' => $accountName,
            ],
            '',
            '&',
            PHP_QUERY_RFC3986
        );

        return sprintf(
            'https://img.vietqr.io/image/%s-%s-%s.png?%s',
            $bankId,
            $accountNo,
            $template,
            $query
        );
    }
}
