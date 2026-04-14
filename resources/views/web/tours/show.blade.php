@extends('web.layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/web/pages/tours.css') }}">
@endpush

@php
    $slug = $tour['slug'] ?? '';
    $tid = (int)($tour['id'] ?? 0);
    $dep = $tour['departure_date'] ?? '';
@endphp

@section('title', $tour['name'] ?? 'Chi tiết tour')

@section('content')
    <div class="ft-wrap" style="padding:20px 16px 40px;">
        <div class="ft-detail-hero ft-card">
            @if(!empty($tour['thumbnail']))
                <img src="{{ $tour['thumbnail'] }}" alt="">
            @endif
        </div>
        <div class="ft-detail-grid">
            <div>
                <h1 style="font-size:1.75rem;">{{ $tour['name'] ?? '' }}</h1>
                <p class="ft-tour-meta" style="margin:12px 0;">
                    <span>⭐ {{ number_format((float)($tour['rating'] ?? 4.8), 1) }}</span>
                    <span>{{ $tour['duration_label'] ?? '' }}</span>
                    @if($dep)<span>Khởi hành: {{ $dep }}</span>@endif
                </p>
                <div class="ft-price-row" style="margin-bottom:20px;">
                    <span class="ft-price-new" style="font-size:1.35rem;">{{ $tour['price_from'] ?? '' }}</span>
                </div>
                <div class="ft-card" style="padding:16px;margin-bottom:20px;">
                    {!! $tour['description'] ?? '' !!}
                </div>
                @if(!empty($tour['gallery']) && is_array($tour['gallery']))
                    <h2 style="font-size:1.1rem;">Thư viện ảnh</h2>
                    <div class="ft-gallery" style="margin-bottom:24px;">
                        @foreach($tour['gallery'] as $g)
                            <img src="{{ $g }}" alt="">
                        @endforeach
                    </div>
                @endif
                @if(!empty($tour['itineraries']) && is_array($tour['itineraries']))
                    <h2 style="font-size:1.1rem;">Lịch trình</h2>
                    @foreach($tour['itineraries'] as $it)
                        <div class="ft-itinerary">
                            <strong>Ngày {{ $it['day_number'] ?? '' }} — {{ $it['title'] ?? '' }}</strong>
                            <div style="font-size:0.9rem;color:var(--color-on-surface-variant);">{!! $it['description'] ?? '' !!}</div>
                        </div>
                    @endforeach
                @endif
            </div>
            <aside class="ft-sticky-book">
                <div class="ft-card" style="padding:18px;">
                    <h3 style="font-size:1rem;margin-bottom:12px;">Đặt tour</h3>
                    <p style="font-size:0.9rem;color:var(--color-on-surface-variant);">Giá từ <strong>{{ $tour['price_from'] ?? '' }}</strong></p>
                    @auth
                        <a class="ft-btn ft-btn--primary" style="width:100%;margin-top:14px;text-align:center;"
                           href="{{ route('booking.create', ['slug' => $slug, 'travel_date' => request('travel_date', $dep)]) }}">Tiến hành đặt</a>
                    @else
                        <a class="ft-btn ft-btn--primary" style="width:100%;margin-top:14px;text-align:center;" href="{{ route('login') }}">Đăng nhập để đặt</a>
                    @endauth
                </div>
            </aside>
        </div>
    </div>
@endsection
