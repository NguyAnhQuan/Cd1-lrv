@extends('admin.layouts.app')

@section('title', 'Thống kê')

@php $kpis = $payload['kpis'] ?? []; @endphp

@section('content')
    <h1 class="adm-page-title">Thống kê & báo cáo</h1>
    <div class="adm-toolbar">
        <a class="ft-btn ft-btn--outline ft-btn--sm" href="{{ route('admin.reports.bookings-csv') }}">Xuất CSV đặt chỗ</a>
        <a class="ft-btn ft-btn--primary ft-btn--sm" href="{{ route('admin.reports.revenue-pdf') }}">Xuất PDF doanh thu</a>
    </div>
    <div class="adm-kpi-grid">
        @foreach($kpis as $k)
            <div class="adm-kpi">
                <div class="adm-kpi__label">{{ $k['label'] ?? '' }}</div>
                <div class="adm-kpi__value">
                    @if(($k['value_key'] ?? '') === 'revenue')
                        {{ number_format((float)($k['value'] ?? 0), 0, ',', '.') }}đ
                    @else
                        {{ is_numeric($k['value'] ?? null) ? number_format((float)$k['value'], 0, ',', '.') : ($k['value'] ?? '') }}
                    @endif
                </div>
                @if(isset($k['delta_pct']))
                    <div class="ft-footer-muted" style="font-size:0.75rem;">Δ {{ ($k['delta_pct'] ?? 0) >= 0 ? '+' : '' }}{{ number_format((float)$k['delta_pct'], 1) }}%</div>
                @endif
            </div>
        @endforeach
    </div>
    <p style="font-size:0.9rem;color:var(--color-on-surface-variant, #424751);">Dữ liệu đầy đủ (biểu đồ theo tháng, conversion, top tour…) lấy từ API <code>/admin/reports/statistics</code> — đồng bộ với màn thống kê trên app Flutter.</p>
@endsection
