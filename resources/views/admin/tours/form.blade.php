@extends('admin.layouts.app')

@section('title', $mode === 'create' ? 'Thêm tour' : 'Sửa tour')

@section('content')
    <h1 class="adm-page-title">{{ $mode === 'create' ? 'Thêm tour' : 'Sửa tour' }}</h1>
    @if($errors->has('message'))
        <div class="ft-alert ft-alert--error">{{ $errors->first('message') }}</div>
    @endif
    @php $t = $tour; @endphp
    <form class="ft-card" style="padding:20px;max-width:720px;" method="post" action="{{ $mode === 'create' ? route('admin.tours.store') : route('admin.tours.update', optional($t)->id) }}">
        @csrf
        @if($mode === 'edit') @method('PUT') @endif
        <div class="ft-form-group">
            <label for="category_id">Danh mục</label>
            <select class="ft-select" id="category_id" name="category_id">
                <option value="">—</option>
                @foreach($categories as $c)
                    <option value="{{ $c['id'] ?? '' }}" @selected(old('category_id', optional($t)->category_id) == ($c['id'] ?? null))>{{ $c['name'] ?? '' }}</option>
                @endforeach
            </select>
        </div>
        <div class="ft-form-group">
            <label for="name">Tên tour *</label>
            <input class="ft-input" id="name" name="name" value="{{ old('name', optional($t)->name) }}" required>
        </div>
        <div class="ft-form-group">
            <label for="slug">Slug</label>
            <input class="ft-input" id="slug" name="slug" value="{{ old('slug', optional($t)->slug) }}">
        </div>
        <div class="ft-form-group">
            <label for="description">Mô tả (HTML)</label>
            <textarea class="ft-textarea" id="description" name="description">{{ old('description', optional($t)->description) }}</textarea>
        </div>
        <div class="ft-form-group">
            <label for="price">Giá</label>
            <input class="ft-input" type="number" step="0.01" id="price" name="price" value="{{ old('price', optional($t)->price) }}">
        </div>
        <div class="ft-form-group">
            <label for="discount_price">Giá khuyến mãi</label>
            <input class="ft-input" type="number" step="0.01" id="discount_price" name="discount_price" value="{{ old('discount_price', optional($t)->discount_price) }}">
        </div>
        <div class="ft-form-group">
            <label for="duration">Số ngày</label>
            <input class="ft-input" type="number" id="duration" name="duration" value="{{ old('duration', optional($t)->duration) }}">
        </div>
        <div class="ft-form-group">
            <label for="departure_date">Ngày khởi hành</label>
            <input class="ft-input" type="date" id="departure_date" name="departure_date" value="{{ old('departure_date', optional($t)->departure_date?->format('Y-m-d')) }}">
        </div>
        <div class="ft-form-group">
            <label for="start_location">Điểm xuất phát</label>
            <input class="ft-input" id="start_location" name="start_location" value="{{ old('start_location', optional($t)->start_location) }}">
        </div>
        <div class="ft-form-group">
            <label for="max_people">Số chỗ tối đa</label>
            <input class="ft-input" type="number" id="max_people" name="max_people" value="{{ old('max_people', optional($t)->max_people) }}">
        </div>
        <div class="ft-form-group">
            <label for="thumbnail">URL ảnh</label>
            <input class="ft-input" id="thumbnail" name="thumbnail" value="{{ old('thumbnail', optional($t)->thumbnail) }}">
        </div>
        <div class="ft-form-group">
            <label for="status">Trạng thái</label>
            <input class="ft-input" id="status" name="status" value="{{ old('status', optional($t)->status ?? 'active') }}">
        </div>
        <div class="ft-form-group">
            <label for="rating">Đánh giá</label>
            <input class="ft-input" type="number" step="0.1" id="rating" name="rating" value="{{ old('rating', optional($t)->rating ?? '4.8') }}">
        </div>
        <div class="ft-form-group">
            <label for="badge_label">Nhãn badge</label>
            <input class="ft-input" id="badge_label" name="badge_label" value="{{ old('badge_label', optional($t)->badge_label) }}">
        </div>
        <div class="ft-form-group">
            <label for="badge_variant">Loại badge (hot/bestseller/newest)</label>
            <input class="ft-input" id="badge_variant" name="badge_variant" value="{{ old('badge_variant', optional($t)->badge_variant) }}">
        </div>
        <div class="ft-form-group">
            <label for="meta_text1">Meta 1</label>
            <input class="ft-input" id="meta_icon1" name="meta_icon1" value="{{ old('meta_icon1', optional($t)->meta_icon1 ?? 'flight') }}" placeholder="icon">
            <input class="ft-input" style="margin-top:6px;" id="meta_text1" name="meta_text1" value="{{ old('meta_text1', optional($t)->meta_text1) }}">
        </div>
        <div class="ft-form-group">
            <label for="meta_text2">Meta 2</label>
            <input class="ft-input" id="meta_icon2" name="meta_icon2" value="{{ old('meta_icon2', optional($t)->meta_icon2 ?? 'hotel') }}">
            <input class="ft-input" style="margin-top:6px;" id="meta_text2" name="meta_text2" value="{{ old('meta_text2', optional($t)->meta_text2) }}">
        </div>
        <button type="submit" class="ft-btn ft-btn--primary">Lưu</button>
        <a class="ft-btn ft-btn--ghost" href="{{ route('admin.tours.index') }}">Hủy</a>
    </form>
@endsection
