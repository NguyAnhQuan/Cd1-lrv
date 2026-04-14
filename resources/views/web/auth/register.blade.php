@extends('web.layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/web/pages/auth.css') }}">
@endpush

@section('title', 'Đăng ký')

@section('content')
    <div class="ft-auth-page">
        <div class="ft-card ft-auth-card">
            <h1>Tạo tài khoản</h1>
            <p class="ft-auth-sub">Tham gia Ftravel để đặt tour và tích điểm</p>
            @if($errors->any())
                <div class="ft-alert ft-alert--error">
                    <ul style="margin:0;padding-left:18px;">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif
            <form method="post" action="{{ route('register.store') }}">
                @csrf
                <div class="ft-form-group">
                    <label for="name">Họ tên</label>
                    <input class="ft-input" id="name" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="ft-form-group">
                    <label for="email">Email</label>
                    <input class="ft-input" type="email" id="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div class="ft-form-group">
                    <label for="phone">Điện thoại</label>
                    <input class="ft-input" id="phone" name="phone" value="{{ old('phone') }}">
                </div>
                <div class="ft-form-group">
                    <label for="password">Mật khẩu</label>
                    <input class="ft-input" type="password" id="password" name="password" required minlength="8">
                </div>
                <div class="ft-form-group">
                    <label for="password_confirmation">Xác nhận mật khẩu</label>
                    <input class="ft-input" type="password" id="password_confirmation" name="password_confirmation" required>
                </div>
                <button type="submit" class="ft-btn ft-btn--primary" style="width:100%;">Đăng ký</button>
            </form>
            <p style="text-align:center;margin-top:16px;font-size:0.9rem;">
                Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a>
            </p>
        </div>
    </div>
@endsection
