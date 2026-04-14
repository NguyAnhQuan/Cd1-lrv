@extends('web.layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/web/pages/home.css') }}">
@endpush

@section('title', 'Trang chủ')

@php
    $hero = $home['hero'] ?? [];
    $heroBg = $hero['background_image_url'] ?? '';
    $promos = $home['promo_tours'] ?? [];
    $suggested = $home['suggested_tours'] ?? [];
    $highlights = $home['highlights'] ?? [];
    $why = [
        ['icon' => '🛡️', 'title' => 'Tin cậy & An toàn', 'desc' => 'Top 10 Công ty du lịch hàng đầu Châu Á, bảo hiểm du lịch quốc tế trọn gói.'],
        ['icon' => '🎧', 'title' => 'Hỗ trợ 24/7', 'desc' => 'Tổng đài chăm sóc khách hàng và đội ngũ xử lý sự cố luôn túc trực mọi lúc mọi nơi.'],
        ['icon' => '💳', 'title' => 'Giá tốt nhất', 'desc' => 'Cam kết giá cạnh tranh nhất thị trường nhờ mạng lưới đối tác toàn cầu rộng khắp.'],
        ['icon' => '⭐', 'title' => 'Dịch vụ chuẩn 5*', 'desc' => 'Mọi chi tiết nhỏ trong hành trình đều được chăm chút kỹ lưỡng để mang lại sự hài lòng tuyệt đối.'],
    ];
@endphp

@section('content')
    <section class="ft-hero" style="background-image: url('{{ e($heroBg) }}');">
        <div class="ft-wrap ft-hero__inner">
            <h1>{{ $hero['title_line1'] ?? 'Khám phá thế giới,' }}<br>{{ $hero['title_line2'] ?? 'theo cách của bạn' }}</h1>
            <p class="ft-hero__lead">{{ $hero['subtitle'] ?? '' }}</p>

            <form
                class="ft-hero-search"
                method="get"
                action="{{ route('tours.domestic') }}"
                data-ft-hero-search
                data-action-domestic="{{ route('tours.domestic') }}"
                data-action-international="{{ route('tours.international') }}"
            >
                <div class="ft-hero-search__shell">
                    <div class="ft-hero-search__grid">
                        <div class="ft-hero-search__field">
                            <span class="ft-hero-search__ico" aria-hidden="true"><i class="bi bi-geo-alt"></i></span>
                            <div class="ft-hero-search__field-main">
                                <label class="ft-hero-search__k" for="ft-hero-q">Điểm đến</label>
                                <input
                                    class="ft-hero-search__input"
                                    type="text"
                                    name="q"
                                    id="ft-hero-q"
                                    placeholder="Bạn muốn đi đâu?"
                                    autocomplete="off"
                                >
                            </div>
                        </div>
                        <div class="ft-hero-search__field">
                            <span class="ft-hero-search__ico" aria-hidden="true"><i class="bi bi-calendar3"></i></span>
                            <div class="ft-hero-search__field-main">
                                <label class="ft-hero-search__k" for="ft-hero-date">Ngày khởi hành</label>
                                <input
                                    class="ft-hero-search__input"
                                    type="date"
                                    name="departure_date"
                                    id="ft-hero-date"
                                >
                            </div>
                        </div>
                        <div class="ft-hero-search__field">
                            <span class="ft-hero-search__ico" aria-hidden="true"><i class="bi bi-globe2"></i></span>
                            <div class="ft-hero-search__field-main">
                                <label class="ft-hero-search__k" for="ft-hero-scope">Phạm vi tour</label>
                                <select class="ft-hero-search__select" id="ft-hero-scope" aria-label="Phạm vi tour">
                                    <option value="domestic">Trong nước</option>
                                    <option value="international">Nước ngoài</option>
                                </select>
                            </div>
                        </div>
                        <div class="ft-hero-search__actions">
                            <button type="submit" class="ft-btn ft-btn--primary ft-hero-search__submit">
                                <span aria-hidden="true"><i class="bi bi-search"></i></span> Tìm kiếm
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <p class="ft-hero__cta">
                <a class="ft-btn ft-btn--secondary" href="{{ route('tours.domestic') }}">Khám phá tour</a>
            </p>
        </div>
    </section>

    <section class="ft-section ft-wrap">
        <h2 class="ft-section-title">Gợi ý cho bạn</h2>
        <div class="ft-slider" data-ft-slider>
            <button class="ft-slider__nav ft-slider__nav--prev" type="button" aria-label="Trước" data-ft-slider-prev>
                <i class="bi bi-chevron-left" aria-hidden="true"></i>
            </button>
            <div class="ft-slider__viewport" data-ft-slider-viewport>
                <div class="ft-slider__track">
                    @foreach($suggested as $p)
                        @php $slug = $p['slug'] ?? ''; @endphp
                        <article class="ft-card ft-promo-card ft-slider__slide">
                            @if(!empty($p['image_url']))
                                <img class="ft-promo-card__img" src="{{ $p['image_url'] }}" alt="">
                            @endif
                            <div class="ft-promo-card__body">
                                <div class="ft-promo-card__meta">{{ $p['duration'] ?? '' }} @if(!empty($p['remaining_label'])) · {{ $p['remaining_label'] }} @endif</div>
                                <h3 class="ft-promo-card__title">{{ $p['title'] ?? '' }}</h3>
                                <div class="ft-price-row">
                                    @if(!empty($p['old_price']))<span class="ft-price-old">{{ $p['old_price'] }}</span>@endif
                                    <span class="ft-price-new">{{ $p['new_price'] ?? '' }}</span>
                                </div>
                                @if($slug)
                                    <a class="ft-btn ft-btn--primary ft-btn--sm" style="margin-top:12px;" href="{{ route('tours.show', ['slug' => $slug]) }}">Xem chi tiết</a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
            <button class="ft-slider__nav ft-slider__nav--next" type="button" aria-label="Sau" data-ft-slider-next>
                <i class="bi bi-chevron-right" aria-hidden="true"></i>
            </button>
        </div>
    </section>

    <section class="ft-section ft-wrap">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
            <h2 class="ft-section-title" style="margin:0;">Ưu đãi nổi bật</h2>
            <a class="ft-btn ft-btn--outline ft-btn--sm" href="{{ route('tours.domestic') }}">Xem tất cả</a>
        </div>
        <div class="ft-slider" data-ft-slider>
            <button class="ft-slider__nav ft-slider__nav--prev" type="button" aria-label="Trước" data-ft-slider-prev>
                <i class="bi bi-chevron-left" aria-hidden="true"></i>
            </button>
            <div class="ft-slider__viewport" data-ft-slider-viewport>
                <div class="ft-slider__track">
                    @foreach($promos as $p)
                        @php $slug = $p['slug'] ?? ''; @endphp
                        <article class="ft-card ft-promo-card ft-slider__slide">
                            @if(!empty($p['image_url']))
                                <img class="ft-promo-card__img" src="{{ $p['image_url'] }}" alt="">
                            @endif
                            <div class="ft-promo-card__body">
                                @if(!empty($p['badge']))
                                    <span class="ft-badge ft-badge--hot" style="margin-bottom:8px;">{{ $p['badge'] }}</span>
                                @endif
                                <div class="ft-promo-card__meta">{{ $p['duration'] ?? '' }} @if(!empty($p['remaining_label'])) · {{ $p['remaining_label'] }} @endif</div>
                                <h3 class="ft-promo-card__title">{{ $p['title'] ?? '' }}</h3>
                                <div class="ft-price-row">
                                    @if(!empty($p['old_price']))<span class="ft-price-old">{{ $p['old_price'] }}</span>@endif
                                    <span class="ft-price-new">{{ $p['new_price'] ?? '' }}</span>
                                </div>
                                @if($slug)
                                    <a class="ft-btn ft-btn--primary ft-btn--sm" style="margin-top:12px;" href="{{ route('tours.show', ['slug' => $slug]) }}">Xem chi tiết</a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
            <button class="ft-slider__nav ft-slider__nav--next" type="button" aria-label="Sau" data-ft-slider-next>
                <i class="bi bi-chevron-right" aria-hidden="true"></i>
            </button>
        </div>
    </section>

    <section class="ft-section ft-wrap">
        <h2 class="ft-section-title">Điểm đến</h2>
        <div class="ft-bento">
            @foreach($highlights as $h)
                @php
                    $slugCat = strtolower(trim($h['category_slug'] ?? ''));
                    $href = route('tours.domestic');
                    if ($slugCat === 'international') $href = route('tours.international');
                    elseif ($slugCat === 'europe') $href = route('tours.international', ['q' => 'Châu Âu']);
                    elseif ($slugCat === 'asia') $href = route('tours.international', ['q' => 'Châu Á']);
                    elseif ($slugCat === 'domestic') $href = route('tours.domestic');
                    $large = $slugCat === 'domestic';
                    $wide = $slugCat === 'international';
                @endphp
                <a href="{{ $href }}" class="ft-bento__tile {{ $large ? 'ft-bento__large' : '' }} {{ $wide ? 'ft-bento__wide' : '' }}">
                    @if(!empty($h['image_url']))
                        <span class="ft-bento__media-wrap" aria-hidden="true">
                            <img
                                class="ft-bento__media"
                                src="{{ $h['image_url'] }}"
                                alt="{{ $h['title'] ?? 'Điểm đến' }}"
                                loading="lazy"
                                decoding="async"
                            >
                        </span>
                    @endif
                    <span class="ft-bento__scrim" aria-hidden="true"></span>
                    <div class="ft-bento__content">
                        @if(!empty($h['pill_label']))
                            <span class="ft-bento__pill">{{ $h['pill_label'] }}</span>
                        @endif
                        <h3 style="margin:0;font-size:1.25rem;">{{ $h['title'] ?? '' }}</h3>
                        <p style="margin:6px 0 0;font-size:0.9rem;opacity:0.95;">{{ $h['subtitle'] ?? '' }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <section class="ft-section ft-wrap">
        <h2 class="ft-section-title">Vì sao chọn Ftravel</h2>
        <div class="ft-why-grid">
            @foreach($why as $w)
                <div class="ft-card ft-why-card">
                    <h4><span>{{ $w['icon'] }}</span> {{ $w['title'] }}</h4>
                    <p>{{ $w['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>
@endsection
