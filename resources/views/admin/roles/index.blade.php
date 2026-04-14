@extends('admin.layouts.app')

@section('title', 'Vai trò')

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
        <h1 class="adm-page-title" style="margin:0;">Vai trò & quyền</h1>
        <a class="ft-btn ft-btn--primary ft-btn--sm" href="{{ route('admin.roles.create') }}">+ Vai trò mới</a>
    </div>
    <div class="ft-table-wrap">
        <table class="ft-table">
            <thead>
                <tr><th>Tên</th><th>Mô tả</th><th>Users</th><th>Hệ thống</th><th></th></tr>
            </thead>
            <tbody>
                @foreach($roles as $r)
                    <tr>
                        <td>{{ $r['name'] ?? '' }}</td>
                        <td>{{ $r['description'] ?? '—' }}</td>
                        <td>{{ $r['users_count'] ?? 0 }}</td>
                        <td>{{ !empty($r['is_system']) ? 'Có' : '' }}</td>
                        <td>
                            <a href="{{ route('admin.roles.edit', $r['id']) }}">Sửa</a>
                            @if(empty($r['is_system']))
                                <form action="{{ route('admin.roles.destroy', $r['id']) }}" method="post" style="display:inline;" onsubmit="return confirm('Xóa vai trò?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ft-btn ft-btn--ghost ft-btn--sm" style="color:var(--color-error);">Xóa</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
