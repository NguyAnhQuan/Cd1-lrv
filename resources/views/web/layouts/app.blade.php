<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Ftravel') — Du lịch trực tuyến</title>
    <link rel="stylesheet" href="{{ asset('css/web/tokens.css') }}">
    <link rel="stylesheet" href="{{ asset('css/web/base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/web/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/web/components.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @php
        $viteHot = public_path('hot');
        $viteManifest = public_path('build/manifest.json');
    @endphp
    @if(file_exists($viteHot) || file_exists($viteManifest))
        @vite(['resources/css/app.css'])
    @endif
    @stack('styles')
</head>
<body class="@yield('body_class')@if(!request()->routeIs('admin.*')) ft-has-bottom-nav @endif">
    @include('web.partials.top-nav')
    <main>
        @include('web.partials.flash')
        @yield('content')
    </main>
    @include('web.partials.footer')
    @include('web.partials.bottom-nav')
    <button type="button" class="ft-fab-chat" title="Chat hỗ trợ" aria-label="Chat"><i class="bi bi-chat-dots-fill" aria-hidden="true"></i></button>
    <script src="{{ asset('js/web/app.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
