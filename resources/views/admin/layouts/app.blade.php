<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') — Ftravel</title>
    <link rel="stylesheet" href="{{ asset('css/web/tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('css/web/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/web/components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/layout.css') }}">
    @stack('styles')
</head>
<body>
    <div class="adm-shell">
        <aside class="adm-sidebar">
            <div class="adm-brand">Ftravel Admin</div>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">Tổng quan</a>
            <a href="{{ route('admin.statistics') }}" class="{{ request()->routeIs('admin.statistics') ? 'is-active' : '' }}">Thống kê</a>
            <a href="{{ route('admin.tours.index') }}" class="{{ request()->routeIs('admin.tours.*') ? 'is-active' : '' }}">Quản lý tour</a>
            <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.*') ? 'is-active' : '' }}">Đánh giá</a>
            <a href="{{ route('admin.coupons.index') }}" class="{{ request()->routeIs('admin.coupons.*') ? 'is-active' : '' }}">Voucher</a>
            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">Người dùng</a>
            <a href="{{ route('admin.roles.index') }}" class="{{ request()->routeIs('admin.roles.*') ? 'is-active' : '' }}">Vai trò</a>
            <a href="{{ route('home') }}">← Về site</a>
            <form action="{{ route('logout') }}" method="post" style="padding:12px 20px;">
                @csrf
                <button type="submit" class="ft-btn ft-btn--outline ft-btn--sm" style="width:100%;border-color:#64748b;color:#e2e8f0;">Thoát</button>
            </form>
        </aside>
        <div class="adm-main">
            @if(session('success'))
                <div class="ft-alert ft-alert--success" style="margin-bottom:16px;">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="ft-alert ft-alert--error" style="margin-bottom:16px;">{{ session('error') }}</div>
            @endif
            @yield('content')
        </div>
    </div>
</body>
</html>
