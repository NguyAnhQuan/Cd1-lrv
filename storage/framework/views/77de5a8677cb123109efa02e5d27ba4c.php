<?php
    $u = auth()->user();
?>
<header class="ft-header">
    <div class="ft-wrap ft-header-inner">
        <a class="ft-brand" href="<?php echo e(route('home')); ?>">Ftravel</a>
        <nav class="ft-nav-desktop" aria-label="Menu chính">
            <a href="<?php echo e(route('home')); ?>" class="<?php echo e(request()->routeIs('home') ? 'is-active' : ''); ?>">Trang chủ</a>
            <a href="<?php echo e(route('tours.domestic')); ?>" class="<?php echo e(request()->routeIs('tours.domestic') ? 'is-active' : ''); ?>">Du lịch trong nước</a>
            <a href="<?php echo e(route('tours.international')); ?>" class="<?php echo e(request()->routeIs('tours.international') ? 'is-active' : ''); ?>">Du lịch nước ngoài</a>
            <a href="<?php echo e(route('vouchers')); ?>" class="<?php echo e(request()->routeIs('vouchers') ? 'is-active' : ''); ?>">Khuyến mãi</a>
        </nav>
        <div class="ft-header-actions">
            <?php if(auth()->guard()->guest()): ?>
                <a class="ft-btn ft-btn--ghost ft-btn--sm" href="<?php echo e(route('login')); ?>">Đăng nhập</a>
                <a class="ft-btn ft-btn--primary ft-btn--sm" href="<?php echo e(route('register')); ?>">Đăng ký</a>
            <?php else: ?>
                <a class="ft-btn ft-btn--ghost ft-btn--sm" href="<?php echo e(route('profile.edit')); ?>"><?php echo e($u->name); ?></a>
                <?php if(method_exists($u, 'isAdmin') && $u->isAdmin()): ?>
                    <a class="ft-btn ft-btn--secondary ft-btn--sm" href="<?php echo e(route('admin.dashboard')); ?>">Admin</a>
                <?php endif; ?>
                <form action="<?php echo e(route('logout')); ?>" method="post" style="display:inline;">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="ft-btn ft-btn--outline ft-btn--sm">Thoát</button>
                </form>
            <?php endif; ?>
            <details class="ft-mobile-nav-details">
                <summary class="ft-mobile-nav-toggle" aria-label="Mở menu"><i class="bi bi-list" aria-hidden="true"></i></summary>
                <div class="ft-mobile-sheet">
                    <a href="<?php echo e(route('home')); ?>">Trang chủ</a>
                    <a href="<?php echo e(route('tours.domestic')); ?>">Du lịch trong nước</a>
                    <a href="<?php echo e(route('tours.international')); ?>">Du lịch nước ngoài</a>
                    <a href="<?php echo e(route('vouchers')); ?>">Khuyến mãi</a>
                    <?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(route('my-trips')); ?>">Quản lý chuyến</a>
                        <a href="<?php echo e(route('profile.edit')); ?>">Hồ sơ</a>
                    <?php endif; ?>
                </div>
            </details>
        </div>
    </div>
</header>
<?php /**PATH D:\BTL\CD2\f_travel_cd\f_travel_BE\resources\views/web/partials/top-nav.blade.php ENDPATH**/ ?>