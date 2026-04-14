@extends('admin.layouts.app')

@section('title', 'Sửa người dùng')

@section('content')
    <h1 class="adm-page-title">Sửa: {{ $editUser->name }}</h1>
    @if($errors->has('message'))
        <div class="ft-alert ft-alert--error">{{ $errors->first('message') }}</div>
    @endif
    <form class="ft-card" style="padding:20px;max-width:560px;" method="post" action="{{ route('admin.users.update', $editUser) }}">
        @csrf
        @method('PUT')
        <div class="ft-form-group">
            <label for="name">Tên</label>
            <input class="ft-input" id="name" name="name" value="{{ old('name', $editUser->name) }}">
        </div>
        <div class="ft-form-group">
            <label for="email">Email</label>
            <input class="ft-input" type="email" id="email" name="email" value="{{ old('email', $editUser->email) }}">
        </div>
        <div class="ft-form-group">
            <label for="phone">Điện thoại</label>
            <input class="ft-input" id="phone" name="phone" value="{{ old('phone', $editUser->phone) }}">
        </div>
        <div class="ft-form-group">
            <label for="status">Trạng thái</label>
            <input class="ft-input" id="status" name="status" value="{{ old('status', $editUser->status) }}">
        </div>
        <div class="ft-form-group">
            <label>Vai trò</label>
            <div class="ft-checkbox-row">
                @foreach($roles as $role)
                    <label>
                        <input type="checkbox" name="role_names[]" value="{{ $role->name }}"
                            @checked(in_array($role->name, old('role_names', $editUser->roles->pluck('name')->all()), true))>
                        {{ $role->name }}
                    </label>
                @endforeach
            </div>
        </div>
        <button type="submit" class="ft-btn ft-btn--primary">Lưu</button>
        <a class="ft-btn ft-btn--ghost" href="{{ route('admin.users.index') }}">Quay lại</a>
    </form>
    <form class="ft-card" style="padding:20px;max-width:560px;margin-top:20px;" method="post" action="{{ route('admin.users.destroy', $editUser) }}" onsubmit="return confirm('Xóa user này?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="ft-btn ft-btn--outline" style="border-color:var(--color-error);color:var(--color-error);">Xóa người dùng</button>
    </form>
@endsection
