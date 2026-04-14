<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', 'Admin'); ?> — Ftravel</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/web/tokens.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/web/base.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/web/components.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/admin/layout.css')); ?>">
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <div class="adm-shell">
        <aside class="adm-sidebar">
            <div class="adm-brand">Ftravel Admin</div>
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="<?php echo e(request()->routeIs('admin.dashboard') ? 'is-active' : ''); ?>">Tổng quan</a>
            <a href="<?php echo e(route('admin.statistics')); ?>" class="<?php echo e(request()->routeIs('admin.statistics') ? 'is-active' : ''); ?>">Thống kê</a>
            <a href="<?php echo e(route('admin.tours.index')); ?>" class="<?php echo e(request()->routeIs('admin.tours.*') ? 'is-active' : ''); ?>">Quản lý tour</a>
            <a href="<?php echo e(route('admin.reviews.index')); ?>" class="<?php echo e(request()->routeIs('admin.reviews.*') ? 'is-active' : ''); ?>">Đánh giá</a>
            <a href="<?php echo e(route('admin.coupons.index')); ?>" class="<?php echo e(request()->routeIs('admin.coupons.*') ? 'is-active' : ''); ?>">Voucher</a>
            <a href="<?php echo e(route('admin.users.index')); ?>" class="<?php echo e(request()->routeIs('admin.users.*') ? 'is-active' : ''); ?>">Người dùng</a>
            <a href="<?php echo e(route('admin.roles.index')); ?>" class="<?php echo e(request()->routeIs('admin.roles.*') ? 'is-active' : ''); ?>">Vai trò</a>
            <a href="<?php echo e(route('home')); ?>">← Về site</a>
            <form action="<?php echo e(route('logout')); ?>" method="post" style="padding:12px 20px;">
                <?php echo csrf_field(); ?>
                <button type="submit" class="ft-btn ft-btn--outline ft-btn--sm" style="width:100%;border-color:#64748b;color:#e2e8f0;">Thoát</button>
            </form>
        </aside>
        <div class="adm-main">
            <?php if(session('success')): ?>
                <div class="ft-alert ft-alert--success" style="margin-bottom:16px;"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="ft-alert ft-alert--error" style="margin-bottom:16px;"><?php echo e(session('error')); ?></div>
            <?php endif; ?>
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>
</body>
</html>
<?php /**PATH D:\BTL\CD2\f_travel_cd\f_travel_BE\resources\views/admin/layouts/app.blade.php ENDPATH**/ ?>