@extends('web.layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/web/pages/vouchers.css') }}">
@endpush

@section('title', 'Khuyến mãi')

@php
    $hex = fn ($c) => sprintf('#%06X', ((int) $c) & 0xFFFFFF);
@endphp

@section('content')
    <div class="ft-wrap" style="padding-top:20px;">
        <h1 style="font-size:1.5rem;">Khuyến mãi & voucher</h1>
        <p style="color:var(--color-on-surface-variant);">Thu thập mã để dùng khi thanh toán tour.</p>
    </div>
    <div class="ft-wrap ft-voucher-grid">
        @foreach($coupons as $c)
            @php
                $accent = $hex($c['accent_color'] ?? 0xFF003D7C);
                $vb = $hex($c['value_box_color'] ?? 0xFF0054A6);
                $hb = $hex($c['header_bg_color'] ?? 0xFFEFF6FF);
            @endphp
            <article class="ft-voucher-card" style="--v-accent: {{ $accent }}; --v-value-bg: {{ $vb }}; --v-head-bg: {{ $hb }};">
                <div class="ft-voucher-card__head">
                    <div class="ft-voucher-cat">{{ $c['category'] ?? 'all' }}</div>
                </div>
                <div class="ft-voucher-card__value">
                    <div class="ft-voucher-card__value-box">{{ $c['value_short'] ?? '' }}</div>
                    <div class="ft-voucher-card__title">{{ $c['header_title'] ?? '' }}</div>
                </div>
                <div class="ft-voucher-card__body">
                    <p style="font-size:0.85rem;margin:0 0 8px;">{{ $c['expiry_label'] ?? '' }}</p>
                    <ul>
                        @foreach($c['bullets'] ?? [] as $b)
                            <li>{{ $b }}</li>
                        @endforeach
                    </ul>
                    <div class="ft-voucher-card__foot">
                        @auth
                            @if(!empty($c['collected']))
                                <span class="ft-badge ft-badge--bestseller">Đã thu thập</span>
                            @else
                                <form action="{{ route('vouchers.collect') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="code" value="{{ $c['code'] ?? '' }}">
                                    <button type="submit" class="ft-btn ft-btn--primary ft-btn--sm">Thu thập</button>
                                </form>
                            @endif
                        @else
                            <a class="ft-btn ft-btn--outline ft-btn--sm" href="{{ route('login') }}">Đăng nhập để thu thập</a>
                        @endauth
                    </div>
                </div>
            </article>
        @endforeach
    </div>
@endsection
