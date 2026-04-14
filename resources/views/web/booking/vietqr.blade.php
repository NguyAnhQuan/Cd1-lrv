@extends('web.layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/web/pages/booking.css') }}">
@endpush

@section('title', 'Mã VietQR')

@section('content')
    <div class="ft-wrap" style="padding:24px 16px;text-align:center;">
        <h1 style="font-size:1.2rem;">Thanh toán chuyển khoản</h1>
        <p>Mã đơn: {{ $data['booking_code'] ?? '' }}</p>
        <p>Số tiền: {{ isset($data['amount_vnd']) ? number_format((int)$data['amount_vnd'], 0, ',', '.').'đ' : '' }}</p>
        @if(!empty($data['vietqr_image_url']))
            <div class="ft-qr-box">
                <img src="{{ $data['vietqr_image_url'] }}" alt="VietQR">
            </div>
        @endif
        <a class="ft-btn ft-btn--ghost" href="{{ route('my-trips') }}">Về quản lý chuyến</a>
    </div>
@endsection
