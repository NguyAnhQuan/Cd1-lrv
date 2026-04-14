@extends('web.layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/web/pages/my-trips.css') }}">
@endpush

@section('title', 'Chuyến của tôi')

@section('content')
    <div class="ft-wrap" style="padding:20px 16px 40px;">
        <h1 style="font-size:1.4rem;">Quản lý chuyến đi</h1>
        @forelse($bookings as $b)
            @php
                $st = strtolower((string)($b['status'] ?? ''));
                $paySt = strtolower((string)($b['payment_row_status'] ?? $b['payment_status'] ?? ''));
                $cls = 'ft-status--pending';
                if (in_array($st, ['confirmed'], true) || in_array($paySt, ['paid'], true)) $cls = 'ft-status--confirmed';
                if (in_array($st, ['cancelled'], true) || in_array($paySt, ['cancelled'], true)) $cls = 'ft-status--cancelled';
            @endphp
            <article class="ft-card ft-trip-card">
                <div class="ft-trip-head">
                    <div>
                        <strong>{{ $b['tour_name'] ?? '' }}</strong>
                        <div style="font-size:0.85rem;color:var(--color-on-surface-variant);">Mã: {{ $b['booking_code'] ?? '' }}</div>
                    </div>
                    <span class="ft-status {{ $cls }}">{{ $b['status'] ?? '' }} / {{ $b['payment_row_status'] ?? $b['payment_status'] ?? '' }}</span>
                </div>
                @if(!empty($b['tour_thumbnail']))
                    <img class="ft-trip-thumb" src="{{ $b['tour_thumbnail'] }}" alt="">
                @endif
                <p style="font-size:0.9rem;">Ngày đi: {{ $b['travel_date'] ?? '—' }} · Tổng: {{ isset($b['total_price']) ? number_format((float)$b['total_price'], 0, ',', '.').'đ' : '' }}</p>
                @if(!empty($b['coupon_code']))
                    <p style="font-size:0.85rem;">Voucher: {{ $b['coupon_code'] }}</p>
                @endif
                @if(!empty($b['payment_id']) && ($b['payment_row_status'] ?? '') === 'pending')
                    <div class="ft-booking-actions">
                        @if(($b['payment_method'] ?? '') === 'bank_transfer')
                            <a class="ft-btn ft-btn--secondary ft-btn--sm" href="{{ route('payments.vietqr', ['payment' => $b['payment_id']]) }}">Xem VietQR</a>
                        @endif
                        <form action="{{ route('payments.confirm', ['payment' => $b['payment_id']]) }}" method="post" style="display:inline;">
                            @csrf
                            <button type="submit" class="ft-btn ft-btn--primary ft-btn--sm">Đã thanh toán</button>
                        </form>
                        <form action="{{ route('payments.cancel', ['payment' => $b['payment_id']]) }}" method="post" style="display:inline;" onsubmit="return confirm('Hủy giao dịch?');">
                            @csrf
                            <button type="submit" class="ft-btn ft-btn--outline ft-btn--sm">Hủy</button>
                        </form>
                    </div>
                @endif
                @if(!empty($b['tour_slug']))
                    <a href="{{ route('tours.show', ['slug' => $b['tour_slug']]) }}" style="font-size:0.85rem;">Xem tour</a>
                @endif
            </article>
        @empty
            <p>Bạn chưa có đặt chỗ nào. <a href="{{ route('tours.domestic') }}">Khám phá tour</a></p>
        @endforelse
    </div>
@endsection
