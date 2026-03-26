<?php
declare(strict_types=1);

/** @var Student[] $students */
/** @var string $q */
/** @var int $page */
/** @var int $perPage */
/** @var int $total */
/** @var int $totalPages */

function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$baseParams = ['action' => 'list'];
if ($q !== '') $baseParams['q'] = $q;

function buildUrl(array $params): string
{
    return 'index.php?' . http_build_query($params);
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý sinh viên</title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; padding: 24px; background: #f6f7fb; }
        .container { max-width: 960px; margin: 0 auto; background: #fff; border-radius: 12px; padding: 18px 18px 8px; box-shadow: 0 10px 30px rgba(0,0,0,.06); }
        .row { display: flex; gap: 12px; align-items: center; justify-content: space-between; flex-wrap: wrap; }
        h1 { font-size: 20px; margin: 0; }
        a.button, button { display: inline-block; padding: 10px 12px; border-radius: 10px; border: 1px solid #e3e6ef; background: #111827; color: #fff; text-decoration: none; font-weight: 600; }
        a.button.secondary { background: #fff; color: #111827; }
        table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        th, td { padding: 12px 10px; border-bottom: 1px solid #eef1f7; text-align: left; }
        th { color: #374151; font-size: 13px; text-transform: uppercase; letter-spacing: .04em; }
        td.actions a { margin-right: 8px; }
        .muted { color: #6b7280; font-size: 13px; }
        form.search { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        input[type="text"] { padding: 10px 12px; border: 1px solid #e3e6ef; border-radius: 10px; min-width: 260px; }
        .pager { display: flex; gap: 8px; align-items: center; justify-content: flex-end; padding: 12px 0 6px; }
        .pager a { padding: 8px 10px; border: 1px solid #e3e6ef; border-radius: 10px; text-decoration: none; color: #111827; background: #fff; }
        .pager .current { padding: 8px 10px; border-radius: 10px; background: #111827; color: #fff; border: 1px solid #111827; }
        .danger { color: #b91c1c; }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div>
            <h1>Danh sách sinh viên</h1>
            <div class="muted">Tổng: <strong><?= (int)$total ?></strong> sinh viên</div>
        </div>
        <div class="row">
            <a class="button" href="index.php?action=add">+ Thêm sinh viên</a>
        </div>
    </div>

    <div style="margin-top: 14px;">
        <form class="search" method="get" action="index.php">
            <input type="hidden" name="action" value="list">
            <input type="text" name="q" value="<?= h($q) ?>" placeholder="Tìm theo tên...">
            <button type="submit">Tìm</button>
            <?php if ($q !== ''): ?>
                <a class="button secondary" href="index.php?action=list">Xóa lọc</a>
            <?php endif; ?>
        </form>
    </div>

    <table>
        <thead>
        <tr>
            <th style="width: 70px;">ID</th>
            <th>Họ tên</th>
            <th>Ngành học</th>
            <th style="width: 180px;">Thao tác</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!$students): ?>
            <tr>
                <td colspan="4" class="muted">Chưa có dữ liệu.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($students as $s): ?>
                <tr>
                    <td><?= (int)$s->id ?></td>
                    <td><?= h($s->name) ?></td>
                    <td><?= h($s->major) ?></td>
                    <td class="actions">
                        <a href="index.php?action=edit&id=<?= (int)$s->id ?>">Edit</a>
                        <a class="danger" href="index.php?action=delete&id=<?= (int)$s->id ?>" onclick="return confirm('Xóa sinh viên #<?= (int)$s->id ?>?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="pager">
        <?php
        $prev = $page - 1;
        $next = $page + 1;
        $canPrev = $page > 1;
        $canNext = $page < $totalPages;
        ?>
        <?php if ($canPrev): ?>
            <a href="<?= h(buildUrl($baseParams + ['page' => $prev])) ?>">&laquo; Trước</a>
        <?php endif; ?>
        <span class="current">Trang <?= (int)$page ?>/<?= (int)$totalPages ?></span>
        <?php if ($canNext): ?>
            <a href="<?= h(buildUrl($baseParams + ['page' => $next])) ?>">Sau &raquo;</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>

