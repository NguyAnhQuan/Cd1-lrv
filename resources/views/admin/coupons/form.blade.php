@extends('admin.layouts.app')

@section('title', $mode === 'create' ? 'Thêm voucher' : 'Sửa voucher')

@section('content')
    <h1 class="adm-page-title">{{ $mode === 'create' ? 'Thêm voucher' : 'Sửa voucher' }}</h1>
    <form class="ft-card" style="padding:20px;max-width:560px;" method="post" action="{{ $mode === 'create' ? route('admin.coupons.store') : route('admin.coupons.update', $coupon) }}">
        @csrf
        @if($mode === 'edit') @method('PUT') @endif
        @if($mode === 'create')
            <div class="ft-form-group">
                <label for="code">Mã *</label>
                <input class="ft-input" id="code" name="code" value="{{ old('code') }}" required>
            </div>
        @endif
        <div class="ft-form-group">
            <label for="title">Tiêu đề</label>
            <input class="ft-input" id="title" name="title" value="{{ old('title', optional($coupon)->title) }}">
        </div>
        <div class="ft-form-group">
            <label for="scope">Phạm vi (domestic/international/...)</label>
            <input class="ft-input" id="scope" name="scope" value="{{ old('scope', optional($coupon)->scope) }}">
        </div>
        <div class="ft-form-group">
            <label for="discount_type">Loại giảm *</label>
            <input class="ft-input" id="discount_type" name="discount_type" value="{{ old('discount_type', optional($coupon)->discount_type ?? 'percent') }}" required>
        </div>
        <div class="ft-form-group">
            <label for="discount_value">Giá trị *</label>
            <input class="ft-input" type="number" step="0.01" id="discount_value" name="discount_value" value="{{ old('discount_value', optional($coupon)->discount_value) }}" required>
        </div>
        <div class="ft-form-group">
            <label for="min_order_value">Đơn tối thiểu</label>
            <input class="ft-input" type="number" step="0.01" id="min_order_value" name="min_order_value" value="{{ old('min_order_value', optional($coupon)->min_order_value) }}">
        </div>
        <div class="ft-form-group">
            <label for="max_discount">Giảm tối đa</label>
            <input class="ft-input" type="number" step="0.01" id="max_discount" name="max_discount" value="{{ old('max_discount', optional($coupon)->max_discount) }}">
        </div>
        <div class="ft-form-group">
            <label for="quantity">Số lượng</label>
            <input class="ft-input" type="number" id="quantity" name="quantity" value="{{ old('quantity', optional($coupon)->quantity) }}">
        </div>
        <div class="ft-form-group">
            <label for="start_date">Bắt đầu</label>
            <input class="ft-input" type="date" id="start_date" name="start_date" value="{{ old('start_date', optional($coupon)->start_date?->format('Y-m-d')) }}">
        </div>
        <div class="ft-form-group">
            <label for="end_date">Kết thúc</label>
            <input class="ft-input" type="date" id="end_date" name="end_date" value="{{ old('end_date', optional($coupon)->end_date?->format('Y-m-d')) }}">
        </div>
        <div class="ft-form-group">
            <label for="status">Trạng thái</label>
            <input class="ft-input" id="status" name="status" value="{{ old('status', optional($coupon)->status ?? 'active') }}">
        </div>
        <button type="submit" class="ft-btn ft-btn--primary">Lưu</button>
        <a class="ft-btn ft-btn--ghost" href="{{ route('admin.coupons.index') }}">Hủy</a>
    </form>
@endsection
