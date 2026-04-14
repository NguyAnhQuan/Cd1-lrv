<?php if (! (request()->routeIs('admin.*'))): ?>
<nav class="ft-bottom-nav" aria-label="Điều hướng nhanh">
    <a href="<?php echo e(route('home')); ?>" class="<?php echo e(request()->routeIs('home') ? 'is-active' : ''); ?>">
        <span class="ft-ico"><i class="bi bi-house"></i></span>Trang chủ
    </a>
    <a href="<?php echo e(route('tours.domestic')); ?>" class="<?php echo e(request()->routeIs('tours.domestic', 'tours.international', 'tours.show') ? 'is-active' : ''); ?>">
        <span class="ft-ico"><i class="bi bi-airplane"></i></span>Tour
    </a>
    <a href="<?php echo e(route('vouchers')); ?>" class="<?php echo e(request()->routeIs('vouchers') ? 'is-active' : ''); ?>">
        <span class="ft-ico"><i class="bi bi-ticket-perforated"></i></span>Voucher
    </a>
    <a href="<?php echo e(auth()->check() ? route('my-trips') : route('login')); ?>" class="<?php echo e(request()->routeIs('my-trips') ? 'is-active' : ''); ?>">
        <span class="ft-ico"><i class="bi bi-card-checklist"></i></span>Chuyến của tôi
    </a>
</nav>
<?php endif; ?>
<?php /**PATH D:\BTL\CD2\f_travel_cd\f_travel_BE\resources\views/web/partials/bottom-nav.blade.php ENDPATH**/ ?>