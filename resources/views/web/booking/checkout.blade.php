@extends('web.layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/web/pages/booking.css') }}">
@endpush

@section('title', 'Thanh toán đặt tour')

@php
    $t = $tour;
    $tid = old('tour_id', $t['id'] ?? '');
    $travelDate = old('travel_date', $preferredTravelDate ?? now()->addDays(7)->format('Y-m-d'));
@endphp

@section('content')
    <div class="ft-wrap" style="padding:20px 16px 40px;max-width:720px;">
        <h1 style="font-size:1.4rem;">Đặt tour & thanh toán</h1>
        <div class="ft-checkout-steps">
            <span class="ft-checkout-step is-active">1. Thông tin</span>
            <span class="ft-checkout-step is-active">2. Thanh toán</span>
        </div>
        @if(!$t)
            <div class="ft-alert ft-alert--error">Chưa chọn tour. <a href="{{ route('tours.domestic') }}">Xem danh sách tour</a></div>
        @endif
        @if($errors->has('booking'))
            <div class="ft-alert ft-alert--error">{{ $errors->first('booking') }}</div>
        @endif
        <form class="ft-card" style="padding:20px;" method="post" action="{{ route('booking.store') }}">
            @csrf
            <input type="hidden" name="tour_id" value="{{ $tid }}" required>
            @if($t)
                <p><strong>{{ $t['name'] ?? '' }}</strong> — {{ $t['price_from'] ?? '' }}</p>
            @endif
            <h2 style="font-size:1rem;margin:20px 0 12px;">Liên hệ</h2>
            <div class="ft-form-group">
                <label for="customer_name">Họ tên</label>
                <input class="ft-input" id="customer_name" name="customer_name" value="{{ old('customer_name', auth()->user()->name ?? '') }}" required>
            </div>
            <div class="ft-form-group">
                <label for="customer_phone">Điện thoại</label>
                <input class="ft-input" id="customer_phone" name="customer_phone" value="{{ old('customer_phone', auth()->user()->phone ?? '') }}" required>
            </div>
            <div class="ft-form-group">
                <label for="customer_email">Email</label>
                <input class="ft-input" type="email" id="customer_email" name="customer_email" value="{{ old('customer_email', auth()->user()->email ?? '') }}" required>
            </div>
            <div class="ft-form-group">
                <label for="customer_address">Địa chỉ (tuỳ chọn)</label>
                <input class="ft-input" id="customer_address" name="customer_address" value="{{ old('customer_address') }}">
            </div>
            <div class="ft-form-group">
                <label for="travel_date">Ngày khởi hành mong muốn</label>
                <input class="ft-input" type="date" id="travel_date" name="travel_date" value="{{ $travelDate }}" required>
            </div>
            <div class="ft-form-group">
                <label for="number_of_people">Số người</label>
                <input class="ft-input" type="number" id="number_of_people" name="number_of_people" min="1" max="99" value="{{ old('number_of_people', 1) }}">
            </div>
            <div class="ft-form-group">
                <label for="coupon_code">Mã voucher (tuỳ chọn)</label>
                <input class="ft-input" id="coupon_code" name="coupon_code" value="{{ old('coupon_code') }}" placeholder="Đã thu thập ở trang Khuyến mãi">
            </div>
            <div class="ft-form-group">
                <label for="payment_method">Phương thức thanh toán</label>
                <select class="ft-select" id="payment_method" name="payment_method" required>
                    <option value="card" @selected(old('payment_method', 'card') === 'card')>Thẻ</option>
                    <option value="e_wallet" @selected(old('payment_method') === 'e_wallet')>Ví điện tử</option>
                    <option value="bank_transfer" @selected(old('payment_method') === 'bank_transfer')>Chuyển khoản (VietQR)</option>
                </select>
            </div>
            <button type="submit" class="ft-btn ft-btn--primary" style="width:100%;" @disabled(!$t)>Xác nhận đặt tour</button>
        </form>
    </div>
@endsection
