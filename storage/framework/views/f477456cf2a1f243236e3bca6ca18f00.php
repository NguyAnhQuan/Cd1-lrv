

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/web/pages/vouchers.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('title', 'Khuyến mãi'); ?>

<?php
    $hex = fn ($c) => sprintf('#%06X', ((int) $c) & 0xFFFFFF);
?>

<?php $__env->startSection('content'); ?>
    <div class="ft-wrap" style="padding-top:20px;">
        <h1 style="font-size:1.5rem;">Khuyến mãi & voucher</h1>
        <p style="color:var(--color-on-surface-variant);">Thu thập mã để dùng khi thanh toán tour.</p>
    </div>
    <div class="ft-wrap ft-voucher-grid">
        <?php $__currentLoopData = $coupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $accent = $hex($c['accent_color'] ?? 0xFF003D7C);
                $vb = $hex($c['value_box_color'] ?? 0xFF0054A6);
                $hb = $hex($c['header_bg_color'] ?? 0xFFEFF6FF);
            ?>
            <article class="ft-voucher-card" style="--v-accent: <?php echo e($accent); ?>; --v-value-bg: <?php echo e($vb); ?>; --v-head-bg: <?php echo e($hb); ?>;">
                <div class="ft-voucher-card__head">
                    <div class="ft-voucher-cat"><?php echo e($c['category'] ?? 'all'); ?></div>
                </div>
                <div class="ft-voucher-card__value">
                    <div class="ft-voucher-card__value-box"><?php echo e($c['value_short'] ?? ''); ?></div>
                    <div class="ft-voucher-card__title"><?php echo e($c['header_title'] ?? ''); ?></div>
                </div>
                <div class="ft-voucher-card__body">
                    <p style="font-size:0.85rem;margin:0 0 8px;"><?php echo e($c['expiry_label'] ?? ''); ?></p>
                    <ul>
                        <?php $__currentLoopData = $c['bullets'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($b); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <div class="ft-voucher-card__foot">
                        <?php if(auth()->guard()->check()): ?>
                            <?php if(!empty($c['collected'])): ?>
                                <span class="ft-badge ft-badge--bestseller">Đã thu thập</span>
                            <?php else: ?>
                                <form action="<?php echo e(route('vouchers.collect')); ?>" method="post">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="code" value="<?php echo e($c['code'] ?? ''); ?>">
                                    <button type="submit" class="ft-btn ft-btn--primary ft-btn--sm">Thu thập</button>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <a class="ft-btn ft-btn--outline ft-btn--sm" href="<?php echo e(route('login')); ?>">Đăng nhập để thu thập</a>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('web.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\BTL\CD2\f_travel_cd\f_travel_BE\resources\views/web/vouchers/index.blade.php ENDPATH**/ ?>