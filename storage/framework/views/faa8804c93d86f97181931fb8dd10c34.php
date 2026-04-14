

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/web/pages/auth.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('title', 'Đăng nhập'); ?>

<?php $__env->startSection('content'); ?>
    <div class="ft-auth-page">
        <div class="ft-card ft-auth-card">
            <h1>Đăng nhập</h1>
            <p class="ft-auth-sub">Chào mừng bạn quay lại Ftravel</p>
            <?php if($errors->any()): ?>
                <div class="ft-alert ft-alert--error"><?php echo e($errors->first()); ?></div>
            <?php endif; ?>
            <form method="post" action="<?php echo e(route('login.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="ft-form-group">
                    <label for="email">Email</label>
                    <input class="ft-input" type="email" id="email" name="email" value="<?php echo e(old('email', config('app.demo_login_email'))); ?>" required autocomplete="username">
                </div>
                <div class="ft-form-group">
                    <label for="password">Mật khẩu</label>
                    <input class="ft-input" type="password" id="password" name="password" value="<?php echo e(old('password', config('app.demo_login_password'))); ?>" required autocomplete="current-password">
                </div>
                <label class="ft-checkbox-row" style="margin-bottom:16px;">
                    <input type="checkbox" name="remember" value="1" <?php if(old('remember')): echo 'checked'; endif; ?>> Ghi nhớ đăng nhập
                </label>
                <button type="submit" class="ft-btn ft-btn--primary" style="width:100%;">Đăng nhập</button>
            </form>
            <p style="text-align:center;margin-top:16px;font-size:0.9rem;">
                Chưa có tài khoản? <a href="<?php echo e(route('register')); ?>">Đăng ký</a>
            </p>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('web.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\BTL\CD2\f_travel_cd\f_travel_BE\resources\views/web/auth/login.blade.php ENDPATH**/ ?>