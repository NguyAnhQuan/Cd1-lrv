@unless(request()->routeIs('admin.*'))
<nav class="ft-bottom-nav" aria-label="Điều hướng nhanh">
    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'is-active' : '' }}">
        <span class="ft-ico"><i class="bi bi-house"></i></span>Trang chủ
    </a>
    <a href="{{ route('tours.domestic') }}" class="{{ request()->routeIs('tours.domestic', 'tours.international', 'tours.show') ? 'is-active' : '' }}">
        <span class="ft-ico"><i class="bi bi-airplane"></i></span>Tour
    </a>
    <a href="{{ route('vouchers') }}" class="{{ request()->routeIs('vouchers') ? 'is-active' : '' }}">
        <span class="ft-ico"><i class="bi bi-ticket-perforated"></i></span>Voucher
    </a>
    <a href="{{ auth()->check() ? route('my-trips') : route('login') }}" class="{{ request()->routeIs('my-trips') ? 'is-active' : '' }}">
        <span class="ft-ico"><i class="bi bi-card-checklist"></i></span>Chuyến của tôi
    </a>
</nav>
@endunless
