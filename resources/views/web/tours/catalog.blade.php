@extends('web.layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/web/pages/tours.css') }}">
@endpush

@php
    $navIdx = $menuIndex;
    $qparams = array_filter([
        'q' => $filterQuery['q'] ?? '',
        'departure_date' => $filterQuery['departure_date'] ?? '',
        'sort' => $filterQuery['sort'] ?? 0,
        'price_0' => $filterQuery['price_0'] ?? null,
        'price_1' => $filterQuery['price_1'] ?? null,
        'price_2' => $filterQuery['price_2'] ?? null,
        'duration' => $filterQuery['duration'] ?? null,
    ], fn ($v) => $v !== null && $v !== '');
    $routeName = $scope === 'international' ? 'tours.international' : 'tours.domestic';
@endphp

@section('title', $title)

@section('content')
    <div class="ft-catalog-layout ft-wrap">
        <aside class="ft-filter-panel">
            <h3>Bộ lọc</h3>
            <form method="get" action="{{ route($routeName) }}">
                <div class="ft-form-group">
                    <label for="q">Bạn muốn đi đâu?</label>
                    <input class="ft-input" id="q" name="q" value="{{ $filterQuery['q'] ?? '' }}" placeholder="Tìm theo tên tour...">
                </div>
                <div class="ft-form-group">
                    <label for="departure_date">Ngày khởi hành</label>
                    <input class="ft-input" type="date" id="departure_date" name="departure_date" value="{{ $filterQuery['departure_date'] ?? '' }}">
                </div>
                <div class="ft-form-group">
                    <label>Khoảng giá (đ)</label>
                    <div class="ft-checkbox-row">
                        <label><input type="checkbox" name="price_0" value="1" @checked(!empty($filterQuery['price_0']))> Dưới 5 triệu</label>
                        <label><input type="checkbox" name="price_1" value="1" @checked(!empty($filterQuery['price_1']))> 5 – 10 triệu</label>
                        <label><input type="checkbox" name="price_2" value="1" @checked(!empty($filterQuery['price_2']))> 10 – 20 triệu</label>
                    </div>
                </div>
                <div class="ft-form-group">
                    <label for="duration">Số ngày</label>
                    <select class="ft-select" id="duration" name="duration">
                        <option value="">Tất cả</option>
                        <option value="0" @selected(($filterQuery['duration'] ?? '') === '0' || ($filterQuery['duration'] ?? null) === 0)>1–3 ngày</option>
                        <option value="1" @selected(($filterQuery['duration'] ?? '') === '1')>4–7 ngày</option>
                        <option value="2" @selected(($filterQuery['duration'] ?? '') === '2')>Trên 7 ngày</option>
                    </select>
                </div>
                <input type="hidden" name="sort" value="{{ (int)($filterQuery['sort'] ?? 0) }}">
                <button type="submit" class="ft-btn ft-btn--primary" style="width:100%;">Áp dụng</button>
            </form>
        </aside>
        <div>
            <h1 style="font-size:1.5rem;margin-bottom:8px;">{{ $title }}</h1>
            <div class="ft-catalog-toolbar">
                <div class="ft-pill-filter">
                    @php
                        $qsNew = $qparams; $qsNew['sort'] = 0;
                        $qsPrice = $qparams; $qsPrice['sort'] = 1;
                    @endphp
                    <a href="{{ route($routeName, $qsNew) }}" class="{{ (int)($filterQuery['sort'] ?? 0) === 0 ? 'is-active' : '' }}">Mới nhất</a>
                    <a href="{{ route($routeName, $qsPrice) }}" class="{{ (int)($filterQuery['sort'] ?? 0) === 1 ? 'is-active' : '' }}">Giá tốt</a>
                    <span class="ft-footer-muted" style="align-self:center;font-size:0.8rem;">Gần bạn / Phổ biến — bản web (sắp xếp như app)</span>
                </div>
            </div>
            <div class="ft-tour-grid">
                @forelse($tours as $t)
                    @php
                        $variant = $t['badge_variant'] ?? '';
                        $badgeClass = match($variant) {
                            'hot' => 'ft-badge--hot',
                            'bestseller' => 'ft-badge--bestseller',
                            'newest' => 'ft-badge--newest',
                            default => 'ft-badge--newest',
                        };
                        $slug = $t['slug'] ?? '';
                    @endphp
                    <article class="ft-card">
                        <div class="ft-tour-card__img-wrap">
                            @if(!empty($t['thumbnail']))
                                <img class="ft-tour-card__img" src="{{ $t['thumbnail'] }}" alt="">
                            @endif
                            @if(!empty($t['badge_label']))
                                <span class="ft-badge {{ $badgeClass }} ft-tour-card__badge">{{ $t['badge_label'] }}</span>
                            @endif
                        </div>
                        <div class="ft-tour-card__body">
                            <div class="ft-tour-meta">
                                <span>⭐ {{ number_format((float)($t['rating'] ?? 4.8), 1) }}</span>
                                <span>{{ $t['duration_label'] ?? '' }}</span>
                            </div>
                            <h3 style="font-size:1rem;margin:0 0 8px;">{{ $t['name'] ?? '' }}</h3>
                            <p style="font-size:0.85rem;color:var(--color-on-surface-variant);margin:0 0 10px;">{{ \Illuminate\Support\Str::limit($t['description_excerpt'] ?? '', 120) }}</p>
                            <div class="ft-price-row">
                                <span class="ft-price-new">{{ $t['price_from'] ?? '' }}</span>
                            </div>
                            @if($slug)
                                <a class="ft-btn ft-btn--primary ft-btn--sm" style="margin-top:12px;display:inline-block;" href="{{ route('tours.show', ['slug' => $slug]) }}">Chi tiết</a>
                            @endif
                        </div>
                    </article>
                @empty
                    <p>Không có tour phù hợp. Thử đổi bộ lọc.</p>
                @endforelse
            </div>
            @if($pagination['total_pages'] > 1)
                <nav class="ft-pagination" aria-label="Phân trang">
                    @for($p = 1; $p <= $pagination['total_pages']; $p++)
                        @php $qs = array_merge($qparams, ['page' => $p]); @endphp
                        <a href="{{ route($routeName, $qs) }}" class="{{ $p === $pagination['page'] ? 'is-current' : '' }}">{{ $p }}</a>
                    @endfor
                </nav>
            @endif
        </div>
    </div>
@endsection
