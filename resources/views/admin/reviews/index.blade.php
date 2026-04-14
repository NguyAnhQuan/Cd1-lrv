@extends('admin.layouts.app')

@section('title', 'Đánh giá')

@section('content')
    <h1 class="adm-page-title">Đánh giá</h1>
    <div class="ft-table-wrap">
        <table class="ft-table">
            <thead>
                <tr><th>ID</th><th>User</th><th>Tour</th><th>Điểm</th><th>Trạng thái</th><th></th></tr>
            </thead>
            <tbody>
                @foreach($reviews as $r)
                    <tr>
                        <td>{{ $r['id'] ?? '' }}</td>
                        <td>{{ $r['user']['name'] ?? $r['user']['email'] ?? '—' }}</td>
                        <td>{{ $r['tour']['name'] ?? '—' }}</td>
                        <td>{{ $r['rating'] ?? '' }}</td>
                        <td>
                            <form method="post" action="{{ route('admin.reviews.update', $r['id']) }}" style="display:flex;gap:6px;align-items:center;">
                                @csrf
                                @method('PUT')
                                <select class="ft-select" name="status" style="width:auto;min-width:120px;">
                                    <option value="pending" @selected(($r['status'] ?? '') === 'pending')>pending</option>
                                    <option value="approved" @selected(($r['status'] ?? '') === 'approved')>approved</option>
                                    <option value="rejected" @selected(($r['status'] ?? '') === 'rejected')>rejected</option>
                                </select>
                                <button type="submit" class="ft-btn ft-btn--primary ft-btn--sm">Lưu</button>
                            </form>
                        </td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
