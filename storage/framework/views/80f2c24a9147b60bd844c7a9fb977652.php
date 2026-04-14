

<?php $__env->startSection('title', 'Tổng quan'); ?>

<?php
    $s = $payload['summary'] ?? [];
    $chart = $payload['chart_days'] ?? [];
    $recent = $payload['recent_bookings'] ?? [];
?>

<?php $__env->startSection('content'); ?>
    <h1 class="adm-page-title">Bảng điều khiển</h1>
    <div class="adm-kpi-grid">
        <div class="adm-kpi">
            <div class="adm-kpi__label">Doanh thu</div>
            <div class="adm-kpi__value"><?php echo e(number_format((float)($s['revenue_total'] ?? 0), 0, ',', '.')); ?>đ</div>
            <div class="ft-footer-muted" style="font-size:0.75rem;">7 ngày: <?php echo e(($s['revenue_delta_pct'] ?? 0) >= 0 ? '+' : ''); ?><?php echo e(number_format((float)($s['revenue_delta_pct'] ?? 0), 1)); ?>%</div>
        </div>
        <div class="adm-kpi">
            <div class="adm-kpi__label">Tour</div>
            <div class="adm-kpi__value"><?php echo e($s['tours_count'] ?? 0); ?></div>
        </div>
        <div class="adm-kpi">
            <div class="adm-kpi__label">User mới 30 ngày</div>
            <div class="adm-kpi__value"><?php echo e($s['users_new_30'] ?? 0); ?></div>
        </div>
        <div class="adm-kpi">
            <div class="adm-kpi__label">Đơn đặt</div>
            <div class="adm-kpi__value"><?php echo e($s['orders_count'] ?? 0); ?></div>
        </div>
    </div>
    <h2 style="font-size:1rem;margin-bottom:8px;">Doanh thu 7 ngày</h2>
    <div class="adm-chart-bars">
        <?php $__currentLoopData = $chart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="adm-chart-bar" style="height:<?php echo e(max(8, (int) round(($day['height'] ?? 0) * 120))); ?>px;" title="<?php echo e($day['label'] ?? ''); ?>: <?php echo e(number_format((float)($day['revenue'] ?? 0), 0, ',', '.')); ?>đ"></div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <h2 style="font-size:1rem;margin-bottom:8px;">Đặt chỗ gần đây</h2>
    <div class="ft-table-wrap">
        <table class="ft-table">
            <thead>
                <tr><th>Mã</th><th>Khách</th><th>Tour</th><th>Ngày</th><th>Tổng</th><th>TT</th></tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($r['code'] ?? ''); ?></td>
                        <td><?php echo e($r['customer_name'] ?? ''); ?></td>
                        <td><?php echo e($r['tour_name'] ?? ''); ?></td>
                        <td><?php echo e($r['date_label'] ?? ''); ?></td>
                        <td><?php echo e(number_format((float)($r['total'] ?? 0), 0, ',', '.')); ?>đ</td>
                        <td><?php echo e($r['ui_status'] ?? ''); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\BTL\CD2\f_travel_cd\f_travel_BE\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>