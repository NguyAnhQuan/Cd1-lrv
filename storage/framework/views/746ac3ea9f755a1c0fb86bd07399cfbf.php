

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/web/pages/auth.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('title', 'Đăng ký'); ?>

<?php $__env->startSection('content'); ?>
    <div class="ft-auth-page">
        <div class="ft-card ft-auth-card">
            <h1>Tạo tài khoản</h1>
            <p class="ft-auth-sub">Tham gia Ftravel để đặt tour và tích điểm</p>
            <?php if($errors->any()): ?>
                <div class="ft-alert ft-alert--error">
                    <ul style="margin:0;padding-left:18px;">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>
            <form method="post" action="<?php echo e(route('register.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="ft-form-group">
                    <label for="name">Họ tên</label>
                    <input class="ft-input" id="name" name="name" value="<?php echo e(old('name')); ?>" required>
                </div>
                <div class="ft-form-group">
                    <label for="email">Email</label>
                    <input class="ft-input" type="email" id="email" name="email" value="<?php echo e(old('email')); ?>" required>
                </div>
                <div class="ft-form-group">
                    <label for="phone">Điện thoại</label>
                    <input class="ft-input" id="phone" name="phone" value="<?php echo e(old('phone')); ?>">
                </div>
                <div class="ft-form-group">
                    <label for="password">Mật khẩu</label>
                    <input class="ft-input" type="password" id="password" name="password" required minlength="8">
                </div>
                <div class="ft-form-group">
                    <label for="password_confirmation">Xác nhận mật khẩu</label>
                    <input class="ft-input" type="password" id="password_confirmation" name="password_confirmation" required>
                </div>
                <button type="submit" class="ft-btn ft-btn--primary" style="width:100%;">Đăng ký</button>
            </form>
            <p style="text-align:center;margin-top:16px;font-size:0.9rem;">
                Đã có tài khoản? <a href="<?php echo e(route('login')); ?>">Đăng nhập</a>
            </p>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('web.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\BTL\CD2\f_travel_cd\f_travel_BE\resources\views/web/auth/register.blade.php ENDPATH**/ ?>