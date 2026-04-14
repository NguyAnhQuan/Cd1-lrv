<?php if(session('success')): ?>
    <div class="ft-wrap"><div class="ft-alert ft-alert--success"><?php echo e(session('success')); ?></div></div>
<?php endif; ?>
<?php if(session('error')): ?>
    <div class="ft-wrap"><div class="ft-alert ft-alert--error"><?php echo e(session('error')); ?></div></div>
<?php endif; ?>
<?php /**PATH D:\BTL\CD2\f_travel_cd\f_travel_BE\resources\views/web/partials/flash.blade.php ENDPATH**/ ?>