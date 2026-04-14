@extends('admin.layouts.app')

@section('title', 'Voucher')

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
        <h1 class="adm-page-title" style="margin:0;">Voucher</h1>
        <a class="ft-btn ft-btn--primary ft-btn--sm" href="{{ route('admin.coupons.create') }}">+ Thêm</a>
    </div>
    <div class="ft-table-wrap">
        <table class="ft-table">
            <thead>
                <tr><th>Mã</th><th>Tiêu đề</th><th>Loại giảm</th><th>Giá trị</th><th></th></tr>
            </thead>
            <tbody>
                @foreach($coupons as $c)
                    @php $c = is_array($c) ? $c : $c->toArray(); @endphp
                    <tr>
                        <td>{{ $c['code'] ?? '' }}</td>
                        <td>{{ $c['title'] ?? '—' }}</td>
                        <td>{{ $c['discount_type'] ?? '' }}</td>
                        <td>{{ $c['discount_value'] ?? '' }}</td>
                        <td>
                            <a href="{{ route('admin.coupons.edit', $c['id']) }}">Sửa</a>
                            <form action="{{ route('admin.coupons.destroy', $c['id']) }}" method="post" style="display:inline;" onsubmit="return confirm('Xóa?');">
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
