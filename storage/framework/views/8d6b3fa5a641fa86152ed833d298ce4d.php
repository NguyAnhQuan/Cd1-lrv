<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', 'Ftravel'); ?> — Du lịch trực tuyến</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/web/tokens.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/web/base.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/web/layout.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/web/components.css')); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <?php
        $viteHot = public_path('hot');
        $viteManifest = public_path('build/manifest.json');
    ?>
    <?php if(file_exists($viteHot) || file_exists($viteManifest)): ?>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
    <?php endif; ?>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body class="<?php echo $__env->yieldContent('body_class'); ?><?php if(!request()->routeIs('admin.*')): ?> ft-has-bottom-nav <?php endif; ?>">
    <?php echo $__env->make('web.partials.top-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <main>
        <?php echo $__env->make('web.partials.flash', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->yieldContent('content'); ?>
    </main>
    <?php echo $__env->make('web.partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('web.partials.bottom-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <button type="button" class="ft-fab-chat" title="Chat hỗ trợ" aria-label="Chat">💬</button>
    <script src="<?php echo e(asset('js/web/app.js')); ?>" defer></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\BTL\CD2\f_travel_cd\f_travel_BE\resources\views/web/layouts/app.blade.php ENDPATH**/ ?>