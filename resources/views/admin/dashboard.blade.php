@extends('admin.layouts.app')

@section('title', 'Tổng quan')

@php
    $s = $payload['summary'] ?? [];
    $chart = $payload['chart_days'] ?? [];
    $recent = $payload['recent_bookings'] ?? [];
@endphp

@section('content')
    <h1 class="adm-page-title">Bảng điều khiển</h1>
    <div class="adm-kpi-grid">
        <div class="adm-kpi">
            <div class="adm-kpi__label">Doanh thu</div>
            <div class="adm-kpi__value">{{ number_format((float)($s['revenue_total'] ?? 0), 0, ',', '.') }}đ</div>
            <div class="ft-footer-muted" style="font-size:0.75rem;">7 ngày: {{ ($s['revenue_delta_pct'] ?? 0) >= 0 ? '+' : '' }}{{ number_format((float)($s['revenue_delta_pct'] ?? 0), 1) }}%</div>
        </div>
        <div class="adm-kpi">
            <div class="adm-kpi__label">Tour</div>
            <div class="adm-kpi__value">{{ $s['tours_count'] ?? 0 }}</div>
        </div>
        <div class="adm-kpi">
            <div class="adm-kpi__label">User mới 30 ngày</div>
            <div class="adm-kpi__value">{{ $s['users_new_30'] ?? 0 }}</div>
        </div>
        <div class="adm-kpi">
            <div class="adm-kpi__label">Đơn đặt</div>
            <div class="adm-kpi__value">{{ $s['orders_count'] ?? 0 }}</div>
        </div>
    </div>
    <h2 style="font-size:1rem;margin-bottom:8px;">Doanh thu 7 ngày</h2>
    <div class="adm-chart-bars">
        @foreach($chart as $day)
            <div class="adm-chart-bar" style="height:{{ max(8, (int) round(($day['height'] ?? 0) * 120)) }}px;" title="{{ $day['label'] ?? '' }}: {{ number_format((float)($day['revenue'] ?? 0), 0, ',', '.') }}đ"></div>
        @endforeach
    </div>
    <h2 style="font-size:1rem;margin-bottom:8px;">Đặt chỗ gần đây</h2>
    <div class="ft-table-wrap">
        <table class="ft-table">
            <thead>
                <tr><th>Mã</th><th>Khách</th><th>Tour</th><th>Ngày</th><th>Tổng</th><th>TT</th></tr>
            </thead>
            <tbody>
                @foreach($recent as $r)
                    <tr>
                        <td>{{ $r['code'] ?? '' }}</td>
                        <td>{{ $r['customer_name'] ?? '' }}</td>
                        <td>{{ $r['tour_name'] ?? '' }}</td>
                        <td>{{ $r['date_label'] ?? '' }}</td>
                        <td>{{ number_format((float)($r['total'] ?? 0), 0, ',', '.') }}đ</td>
                        <td>{{ $r['ui_status'] ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
