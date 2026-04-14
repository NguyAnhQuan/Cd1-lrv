

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/web/pages/tours.css')); ?>">
<?php $__env->stopPush(); ?>

<?php
    $navIdx = $menuIndex;
    $qparams = array_filter([
        'q' => $filterQuery['q'] ?? '',
        'departure_date' => $filterQuery['departure_date'] ?? '',
        'sort' => $filterQuery['sort'] ?? 0,
        'price_0' => $filterQuery['price_0'] ?? null,
        'price_1' => $filterQuery['price_1'] ?? null,
        'price_2' => $filterQuery['price_2'] ?? null,
        'duration' => $filterQuery['duration'] ?? null,
    ], fn ($v) => $v !== null && $v !== '');
    $routeName = $scope === 'international' ? 'tours.international' : 'tours.domestic';
?>

<?php $__env->startSection('title', $title); ?>

<?php $__env->startSection('content'); ?>
    <div class="ft-catalog-layout ft-wrap">
        <aside class="ft-filter-panel">
            <h3>Bộ lọc</h3>
            <form method="get" action="<?php echo e(route($routeName)); ?>">
                <div class="ft-form-group">
                    <label for="q">Bạn muốn đi đâu?</label>
                    <input class="ft-input" id="q" name="q" value="<?php echo e($filterQuery['q'] ?? ''); ?>" placeholder="Tìm theo tên tour...">
                </div>
                <div class="ft-form-group">
                    <label for="departure_date">Ngày khởi hành</label>
                    <input class="ft-input" type="date" id="departure_date" name="departure_date" value="<?php echo e($filterQuery['departure_date'] ?? ''); ?>">
                </div>
                <div class="ft-form-group">
                    <label>Khoảng giá (đ)</label>
                    <div class="ft-checkbox-row">
                        <label><input type="checkbox" name="price_0" value="1" <?php if(!empty($filterQuery['price_0'])): echo 'checked'; endif; ?>> Dưới 5 triệu</label>
                        <label><input type="checkbox" name="price_1" value="1" <?php if(!empty($filterQuery['price_1'])): echo 'checked'; endif; ?>> 5 – 10 triệu</label>
                        <label><input type="checkbox" name="price_2" value="1" <?php if(!empty($filterQuery['price_2'])): echo 'checked'; endif; ?>> 10 – 20 triệu</label>
                    </div>
                </div>
                <div class="ft-form-group">
                    <label for="duration">Số ngày</label>
                    <select class="ft-select" id="duration" name="duration">
                        <option value="">Tất cả</option>
                        <option value="0" <?php if(($filterQuery['duration'] ?? '') === '0' || ($filterQuery['duration'] ?? null) === 0): echo 'selected'; endif; ?>>1–3 ngày</option>
                        <option value="1" <?php if(($filterQuery['duration'] ?? '') === '1'): echo 'selected'; endif; ?>>4–7 ngày</option>
                        <option value="2" <?php if(($filterQuery['duration'] ?? '') === '2'): echo 'selected'; endif; ?>>Trên 7 ngày</option>
                    </select>
                </div>
                <input type="hidden" name="sort" value="<?php echo e((int)($filterQuery['sort'] ?? 0)); ?>">
                <button type="submit" class="ft-btn ft-btn--primary" style="width:100%;">Áp dụng</button>
            </form>
        </aside>
        <div>
            <h1 style="font-size:1.5rem;margin-bottom:8px;"><?php echo e($title); ?></h1>
            <div class="ft-catalog-toolbar">
                <div class="ft-pill-filter">
                    <?php
                        $qsNew = $qparams; $qsNew['sort'] = 0;
                        $qsPrice = $qparams; $qsPrice['sort'] = 1;
                    ?>
                    <a href="<?php echo e(route($routeName, $qsNew)); ?>" class="<?php echo e((int)($filterQuery['sort'] ?? 0) === 0 ? 'is-active' : ''); ?>">Mới nhất</a>
                    <a href="<?php echo e(route($routeName, $qsPrice)); ?>" class="<?php echo e((int)($filterQuery['sort'] ?? 0) === 1 ? 'is-active' : ''); ?>">Giá tốt</a>
                    <span class="ft-footer-muted" style="align-self:center;font-size:0.8rem;">Gần bạn / Phổ biến — bản web (sắp xếp như app)</span>
                </div>
            </div>
            <div class="ft-tour-grid">
                <?php $__empty_1 = true; $__currentLoopData = $tours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $variant = $t['badge_variant'] ?? '';
                        $badgeClass = match($variant) {
                            'hot' => 'ft-badge--hot',
                            'bestseller' => 'ft-badge--bestseller',
                            'newest' => 'ft-badge--newest',
                            default => 'ft-badge--newest',
                        };
                        $slug = $t['slug'] ?? '';
                    ?>
                    <article class="ft-card">
                        <div class="ft-tour-card__img-wrap">
                            <?php if(!empty($t['thumbnail'])): ?>
                                <img class="ft-tour-card__img" src="<?php echo e($t['thumbnail']); ?>" alt="">
                            <?php endif; ?>
                            <?php if(!empty($t['badge_label'])): ?>
                                <span class="ft-badge <?php echo e($badgeClass); ?> ft-tour-card__badge"><?php echo e($t['badge_label']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="ft-tour-card__body">
                            <div class="ft-tour-meta">
                                <span>⭐ <?php echo e(number_format((float)($t['rating'] ?? 4.8), 1)); ?></span>
                                <span><?php echo e($t['duration_label'] ?? ''); ?></span>
                            </div>
                            <h3 style="font-size:1rem;margin:0 0 8px;"><?php echo e($t['name'] ?? ''); ?></h3>
                            <p style="font-size:0.85rem;color:var(--color-on-surface-variant);margin:0 0 10px;"><?php echo e(\Illuminate\Support\Str::limit($t['description_excerpt'] ?? '', 120)); ?></p>
                            <div class="ft-price-row">
                                <span class="ft-price-new"><?php echo e($t['price_from'] ?? ''); ?></span>
                            </div>
                            <?php if($slug): ?>
                                <a class="ft-btn ft-btn--primary ft-btn--sm" style="margin-top:12px;display:inline-block;" href="<?php echo e(route('tours.show', ['slug' => $slug])); ?>">Chi tiết</a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p>Không có tour phù hợp. Thử đổi bộ lọc.</p>
                <?php endif; ?>
            </div>
            <?php if($pagination['total_pages'] > 1): ?>
                <nav class="ft-pagination" aria-label="Phân trang">
                    <?php for($p = 1; $p <= $pagination['total_pages']; $p++): ?>
                        <?php $qs = array_merge($qparams, ['page' => $p]); ?>
                        <a href="<?php echo e(route($routeName, $qs)); ?>" class="<?php echo e($p === $pagination['page'] ? 'is-current' : ''); ?>"><?php echo e($p); ?></a>
                    <?php endfor; ?>
                </nav>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('web.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\BTL\CD2\f_travel_cd\f_travel_BE\resources\views/web/tours/catalog.blade.php ENDPATH**/ ?>