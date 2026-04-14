@extends('admin.layouts.app')

@section('title', $mode === 'create' ? 'Tạo vai trò' : 'Sửa vai trò')

@php
    $r = is_array($role) ? $role : [];
    $perms = $r['permissions'] ?? [];
@endphp

@section('content')
    <h1 class="adm-page-title">{{ $mode === 'create' ? 'Tạo vai trò' : 'Sửa vai trò' }}</h1>
    @if($errors->has('message'))
        <div class="ft-alert ft-alert--error">{{ $errors->first('message') }}</div>
    @endif
    <form class="ft-card" style="padding:20px;max-width:800px;" method="post" action="{{ $mode === 'create' ? route('admin.roles.store') : route('admin.roles.update', $r['id']) }}">
        @csrf
        @if($mode === 'edit') @method('PUT') @endif
        <div class="ft-form-group">
            <label for="name">Tên (a-z, số, _) *</label>
            <input class="ft-input" id="name" name="name" value="{{ old('name', $r['name'] ?? '') }}" @disabled($mode === 'edit' && ($r['is_system'] ?? false)) required>
        </div>
        <div class="ft-form-group">
            <label for="description">Mô tả</label>
            <input class="ft-input" id="description" name="description" value="{{ old('description', $r['description'] ?? '') }}">
        </div>
        <h2 style="font-size:1rem;margin:20px 0 12px;">Quyền theo module</h2>
        @foreach($modules as $mod)
            @php
                $key = $mod['key'] ?? '';
                $p = $perms[$key] ?? ['view' => false, 'create' => false, 'edit' => false, 'delete' => false];
            @endphp
            <fieldset style="border:1px solid var(--color-outline-variant);border-radius:12px;padding:12px 14px;margin-bottom:12px;">
                <legend style="font-weight:800;padding:0 8px;">{{ $mod['label'] ?? $key }}</legend>
                <div class="ft-checkbox-row" style="flex-direction:row;flex-wrap:wrap;">
                    <label><input type="checkbox" name="perm_{{ $key }}_view" value="1" @checked(old("perm_{$key}_view", $p['view'] ?? false))> Xem</label>
                    <label><input type="checkbox" name="perm_{{ $key }}_create" value="1" @checked(old("perm_{$key}_create", $p['create'] ?? false))> Tạo</label>
                    <label><input type="checkbox" name="perm_{{ $key }}_edit" value="1" @checked(old("perm_{$key}_edit", $p['edit'] ?? false))> Sửa</label>
                    <label><input type="checkbox" name="perm_{{ $key }}_delete" value="1" @checked(old("perm_{$key}_delete", $p['delete'] ?? false))> Xóa</label>
                </div>
            </fieldset>
        @endforeach
        <button type="submit" class="ft-btn ft-btn--primary">Lưu</button>
        <a class="ft-btn ft-btn--ghost" href="{{ route('admin.roles.index') }}">Hủy</a>
    </form>
@endsection
