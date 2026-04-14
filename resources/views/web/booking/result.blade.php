@extends('web.layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/web/pages/booking.css') }}">
@endpush

@section('title', 'Kết quả đặt tour')

@section('content')
    <div class="ft-wrap" style="padding:24px 16px 40px;max-width:640px;">
        <h1 style="font-size:1.35rem;">Đặt tour thành công</h1>
        <p>Mã đơn: <strong>{{ $checkout['booking_code'] ?? '' }}</strong></p>
        <p>Số tiền: <strong>{{ isset($checkout['amount_vnd']) ? number_format((int)$checkout['amount_vnd'], 0, ',', '.').'đ' : '' }}</strong></p>
        <p>Nội dung CK: <strong>{{ $checkout['add_info'] ?? '' }}</strong></p>
        @if(!empty($checkout['vietqr_image_url']))
            <div class="ft-qr-box">
                <p style="font-weight:700;">Quét mã VietQR để thanh toán</p>
                <img src="{{ $checkout['vietqr_image_url'] }}" alt="VietQR">
            </div>
        @endif
        <div class="ft-booking-actions">
            @if(!empty($checkout['payment_id']))
                <form action="{{ route('payments.confirm', ['payment' => $checkout['payment_id']]) }}" method="post">
                    @csrf
                    <button type="submit" class="ft-btn ft-btn--primary">Đã chuyển khoản</button>
                </form>
                <form action="{{ route('payments.cancel', ['payment' => $checkout['payment_id']]) }}" method="post" onsubmit="return confirm('Hủy giao dịch chờ thanh toán?');">
                    @csrf
                    <button type="submit" class="ft-btn ft-btn--outline">Hủy giao dịch</button>
                </form>
            @endif
            <a class="ft-btn ft-btn--ghost" href="{{ route('my-trips') }}">Quản lý chuyến</a>
        </div>
    </div>
@endsection
