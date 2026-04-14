<?php

return [

    /*
    |--------------------------------------------------------------------------
    | VietQR Quick Link (img.vietqr.io)
    |--------------------------------------------------------------------------
    | https://img.vietqr.io/image/<BANK_ID>-<ACCOUNT_NO>-<TEMPLATE>.png?amount=&addInfo=&accountName=
    */

    'bank_id' => env('VIETQR_BANK_ID', 'mbbank'),

    'account_no' => env('VIETQR_ACCOUNT_NO', '33110286869999'),

    'account_name' => env('VIETQR_ACCOUNT_NAME', 'FTRAVEL DU LICH'),

    'template' => env('VIETQR_TEMPLATE', 'compact2'),

];
