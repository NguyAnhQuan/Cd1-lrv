@extends('admin.layouts.app')

@section('title', 'Quản lý tour')

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
        <h1 class="adm-page-title" style="margin:0;">Tour</h1>
        <a class="ft-btn ft-btn--primary ft-btn--sm" href="{{ route('admin.tours.create') }}">+ Thêm tour</a>
    </div>
    <div class="ft-table-wrap">
        <table class="ft-table">
            <thead>
                <tr>
                    <th>ID</th><th>Tên</th><th>Giá</th><th>Trạng thái</th><th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($tours as $row)
                    @php $t = is_array($row) ? $row : $row->toArray(); @endphp
                    <tr>
                        <td>{{ $t['id'] ?? '' }}</td>
                        <td>{{ $t['name'] ?? '' }}</td>
                        <td>{{ isset($t['price']) ? number_format((float)$t['price'], 0, ',', '.') : '' }}</td>
                        <td>{{ $t['status'] ?? '' }}</td>
                        <td>
                            <a href="{{ route('admin.tours.edit', $t['id']) }}">Sửa</a>
                            <form action="{{ route('admin.tours.destroy', $t['id']) }}" method="post" style="display:inline;" onsubmit="return confirm('Xóa tour?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ft-btn ft-btn--ghost ft-btn--sm" style="color:var(--color-error);">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
