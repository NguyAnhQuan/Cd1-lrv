@extends('web.layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/web/pages/auth.css') }}">
@endpush

@section('title', 'Đăng nhập')

@section('content')
    <div class="ft-auth-page">
        <div class="ft-card ft-auth-card">
            <h1>Đăng nhập</h1>
            <p class="ft-auth-sub">Chào mừng bạn quay lại Ftravel</p>
            @if($errors->any())
                <div class="ft-alert ft-alert--error">{{ $errors->first() }}</div>
            @endif
            <form method="post" action="{{ route('login.store') }}">
                @csrf
                <div class="ft-form-group">
                    <label for="email">Email</label>
                    <input class="ft-input" type="email" id="email" name="email" value="{{ old('email', config('app.demo_login_email')) }}" required autocomplete="username">
                </div>
                <div class="ft-form-group">
                    <label for="password">Mật khẩu</label>
                    <input class="ft-input" type="password" id="password" name="password" value="{{ old('password', config('app.demo_login_password')) }}" required autocomplete="current-password">
                </div>
                <label class="ft-checkbox-row" style="margin-bottom:16px;">
                    <input type="checkbox" name="remember" value="1" @checked(old('remember'))> Ghi nhớ đăng nhập
                </label>
                <button type="submit" class="ft-btn ft-btn--primary" style="width:100%;">Đăng nhập</button>
            </form>
            <p style="text-align:center;margin-top:16px;font-size:0.9rem;">
                Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký</a>
            </p>
        </div>
    </div>
@endsection
