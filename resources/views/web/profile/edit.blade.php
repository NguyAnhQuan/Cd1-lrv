@extends('web.layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/web/pages/auth.css') }}">
@endpush

@section('title', 'Hồ sơ')

@section('content')
    <div class="ft-auth-page" style="align-items:flex-start;padding-top:32px;">
        <div class="ft-card ft-auth-card" style="max-width:480px;">
            <h1>Hồ sơ</h1>
            <p class="ft-auth-sub">Điểm tích lũy: <strong>{{ (int)($user->loyalty_points ?? 0) }}</strong></p>
            @if(session('success'))
                <div class="ft-alert ft-alert--success">{{ session('success') }}</div>
            @endif
            <form method="post" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')
                <div class="ft-form-group">
                    <label for="name">Họ tên</label>
                    <input class="ft-input" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                </div>
                <div class="ft-form-group">
                    <label for="email">Email</label>
                    <input class="ft-input" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                </div>
                <div class="ft-form-group">
                    <label for="phone">Điện thoại</label>
                    <input class="ft-input" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                </div>
                <h2 style="font-size:0.95rem;margin:20px 0 8px;">Đổi mật khẩu</h2>
                <div class="ft-form-group">
                    <label for="current_password">Mật khẩu hiện tại</label>
                    <input class="ft-input" type="password" id="current_password" name="current_password" autocomplete="current-password">
                </div>
                <div class="ft-form-group">
                    <label for="password">Mật khẩu mới</label>
                    <input class="ft-input" type="password" id="password" name="password" autocomplete="new-password">
                </div>
                <div class="ft-form-group">
                    <label for="password_confirmation">Xác nhận mật khẩu mới</label>
                    <input class="ft-input" type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                </div>
                <button type="submit" class="ft-btn ft-btn--primary" style="width:100%;">Lưu thay đổi</button>
            </form>
            <hr style="margin:28px 0;border:none;border-top:1px solid var(--color-outline-variant);">
            <h2 style="font-size:0.95rem;color:var(--color-error);">Xóa tài khoản</h2>
            <form method="post" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Xóa vĩnh viễn tài khoản?');">
                @csrf
                @method('DELETE')
                <div class="ft-form-group">
                    <label for="del_password">Mật khẩu xác nhận</label>
                    <input class="ft-input" type="password" id="del_password" name="password" required>
                </div>
                <button type="submit" class="ft-btn ft-btn--outline" style="width:100%;border-color:var(--color-error);color:var(--color-error);">Xóa tài khoản</button>
            </form>
        </div>
    </div>
@endsection
