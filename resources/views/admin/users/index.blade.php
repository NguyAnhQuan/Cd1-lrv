@extends('admin.layouts.app')

@section('title', 'Người dùng')

@section('content')
    <h1 class="adm-page-title">Người dùng</h1>
    <div class="ft-table-wrap">
        <table class="ft-table">
            <thead>
                <tr><th>ID</th><th>Tên</th><th>Email</th><th>Vai trò</th><th></th></tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                    <tr>
                        <td>{{ $u['id'] ?? '' }}</td>
                        <td>{{ $u['name'] ?? '' }}</td>
                        <td>{{ $u['email'] ?? '' }}</td>
                        <td>
                            @foreach($u['roles'] ?? [] as $r)
                                <span class="ft-badge ft-badge--newest" style="margin-right:4px;">{{ $r }}</span>
                            @endforeach
                        </td>
                        <td><a href="{{ route('admin.users.edit', $u['id']) }}">Sửa</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
