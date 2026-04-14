

<?php $__env->startSection('title', 'Thống kê'); ?>

<?php $kpis = $payload['kpis'] ?? []; ?>

<?php $__env->startSection('content'); ?>
    <h1 class="adm-page-title">Thống kê & báo cáo</h1>
    <div class="adm-toolbar">
        <a class="ft-btn ft-btn--outline ft-btn--sm" href="<?php echo e(route('admin.reports.bookings-csv')); ?>">Xuất CSV đặt chỗ</a>
        <a class="ft-btn ft-btn--primary ft-btn--sm" href="<?php echo e(route('admin.reports.revenue-pdf')); ?>">Xuất PDF doanh thu</a>
    </div>
    <div class="adm-kpi-grid">
        <?php $__currentLoopData = $kpis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="adm-kpi">
                <div class="adm-kpi__label"><?php echo e($k['label'] ?? ''); ?></div>
                <div class="adm-kpi__value">
                    <?php if(($k['value_key'] ?? '') === 'revenue'): ?>
                        <?php echo e(number_format((float)($k['value'] ?? 0), 0, ',', '.')); ?>đ
                    <?php else: ?>
                        <?php echo e(is_numeric($k['value'] ?? null) ? number_format((float)$k['value'], 0, ',', '.') : ($k['value'] ?? '')); ?>

                    <?php endif; ?>
                </div>
                <?php if(isset($k['delta_pct'])): ?>
                    <div class="ft-footer-muted" style="font-size:0.75rem;">Δ <?php echo e(($k['delta_pct'] ?? 0) >= 0 ? '+' : ''); ?><?php echo e(number_format((float)$k['delta_pct'], 1)); ?>%</div>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <p style="font-size:0.9rem;color:var(--color-on-surface-variant, #424751);">Dữ liệu đầy đủ (biểu đồ theo tháng, conversion, top tour…) lấy từ API <code>/admin/reports/statistics</code> — đồng bộ với màn thống kê trên app Flutter.</p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\BTL\CD2\f_travel_cd\f_travel_BE\resources\views/admin/statistics.blade.php ENDPATH**/ ?>