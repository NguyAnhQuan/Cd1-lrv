

<?php $__env->startSection('title', 'Quản lý tour'); ?>

<?php $__env->startSection('content'); ?>
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:16px;">
        <h1 class="adm-page-title" style="margin:0;">Tour</h1>
        <a class="ft-btn ft-btn--primary ft-btn--sm" href="<?php echo e(route('admin.tours.create')); ?>">+ Thêm tour</a>
    </div>
    <div class="ft-table-wrap">
        <table class="ft-table">
            <thead>
                <tr>
                    <th>ID</th><th>Tên</th><th>Giá</th><th>Trạng thái</th><th></th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $tours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $t = is_array($row) ? $row : $row->toArray(); ?>
                    <tr>
                        <td><?php echo e($t['id'] ?? ''); ?></td>
                        <td><?php echo e($t['name'] ?? ''); ?></td>
                        <td><?php echo e(isset($t['price']) ? number_format((float)$t['price'], 0, ',', '.') : ''); ?></td>
                        <td><?php echo e($t['status'] ?? ''); ?></td>
                        <td>
                            <a href="<?php echo e(route('admin.tours.edit', $t['id'])); ?>">Sửa</a>
                            <form action="<?php echo e(route('admin.tours.destroy', $t['id'])); ?>" method="post" style="display:inline;" onsubmit="return confirm('Xóa tour?');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="ft-btn ft-btn--ghost ft-btn--sm" style="color:var(--color-error);">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\BTL\CD2\f_travel_cd\f_travel_BE\resources\views/admin/tours/index.blade.php ENDPATH**/ ?>