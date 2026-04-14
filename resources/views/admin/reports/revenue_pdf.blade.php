<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; }
        h1 { font-size: 18px; margin: 0 0 6px 0; color: #003d7c; }
        .muted { color: #555; font-size: 10px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f0f4fa; font-size: 10px; }
        .num { text-align: right; font-variant-numeric: tabular-nums; }
        .kpi { margin-bottom: 14px; }
        .kpi-row { margin: 4px 0; }
    </style>
</head>
<body>
    <h1>Báo cáo doanh thu — F Travel</h1>
    <div class="muted">Xuất lúc: {{ $generatedAt }}</div>

    <div class="kpi">
        <div class="kpi-row"><strong>Tổng doanh thu (đặt chỗ không hủy):</strong> {{ $revenueTotalLabel }} đ</div>
        <div class="kpi-row"><strong>Tổng đặt chỗ:</strong> {{ $bookingsCount }}</div>
        <div class="kpi-row"><strong>Người dùng:</strong> {{ $usersCount }}</div>
        <div class="kpi-row"><strong>Đánh giá trung bình:</strong> {{ $avgRating }}</div>
    </div>

    <p><strong>Doanh thu theo tháng ({{ $year }} so với {{ $prevYear }})</strong></p>
    <table>
        <thead>
            <tr>
                <th>Tháng</th>
                <th class="num">Năm {{ $year }} (đ)</th>
                <th class="num">Năm {{ $prevYear }} (đ)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($monthRows as $row)
                <tr>
                    <td>{{ $row['label'] }}</td>
                    <td class="num">{{ $row['curLabel'] }}</td>
                    <td class="num">{{ $row['prevLabel'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
