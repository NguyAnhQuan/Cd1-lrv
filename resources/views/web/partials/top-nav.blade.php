@php
    $u = auth()->user();
@endphp
<header class="ft-header">
    <div class="ft-wrap ft-header-inner">
        <a class="ft-brand" href="{{ route('home') }}">Ftravel</a>
        <nav class="ft-nav-desktop" aria-label="Menu chính">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'is-active' : '' }}">Trang chủ</a>
            <a href="{{ route('tours.domestic') }}" class="{{ request()->routeIs('tours.domestic') ? 'is-active' : '' }}">Du lịch trong nước</a>
            <a href="{{ route('tours.international') }}" class="{{ request()->routeIs('tours.international') ? 'is-active' : '' }}">Du lịch nước ngoài</a>
            <a href="{{ route('vouchers') }}" class="{{ request()->routeIs('vouchers') ? 'is-active' : '' }}">Khuyến mãi</a>
        </nav>
        <div class="ft-header-actions">
            @guest
                <a class="ft-btn ft-btn--ghost ft-btn--sm" href="{{ route('login') }}">Đăng nhập</a>
                <a class="ft-btn ft-btn--primary ft-btn--sm" href="{{ route('register') }}">Đăng ký</a>
            @else
                <a class="ft-btn ft-btn--ghost ft-btn--sm" href="{{ route('profile.edit') }}">{{ $u->name }}</a>
                @if(method_exists($u, 'isAdmin') && $u->isAdmin())
                    <a class="ft-btn ft-btn--secondary ft-btn--sm" href="{{ route('admin.dashboard') }}">Admin</a>
                @endif
                <form action="{{ route('logout') }}" method="post" style="display:inline;">
                    @csrf
                    <button type="submit" class="ft-btn ft-btn--outline ft-btn--sm">Thoát</button>
                </form>
            @endguest
            <details class="ft-mobile-nav-details">
                <summary class="ft-mobile-nav-toggle" aria-label="Mở menu"><i class="bi bi-list" aria-hidden="true"></i></summary>
                <div class="ft-mobile-sheet">
                    <a href="{{ route('home') }}">Trang chủ</a>
                    <a href="{{ route('tours.domestic') }}">Du lịch trong nước</a>
                    <a href="{{ route('tours.international') }}">Du lịch nước ngoài</a>
                    <a href="{{ route('vouchers') }}">Khuyến mãi</a>
                    @auth
                        <a href="{{ route('my-trips') }}">Quản lý chuyến</a>
                        <a href="{{ route('profile.edit') }}">Hồ sơ</a>
                    @endauth
                </div>
            </details>
        </div>
    </div>
</header>
