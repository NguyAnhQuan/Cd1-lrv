<?php
declare(strict_types=1);

function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$isEdit = isset($student) && $student instanceof Student;
$title = $isEdit ? 'Sửa sinh viên' : 'Thêm sinh viên';
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($title) ?></title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; padding: 24px; background: #f6f7fb; }
        .container { max-width: 680px; margin: 0 auto; background: #fff; border-radius: 12px; padding: 18px; box-shadow: 0 10px 30px rgba(0,0,0,.06); }
        h1 { font-size: 20px; margin: 0 0 14px; }
        label { display: block; font-weight: 600; margin: 12px 0 6px; }
        input[type="text"] { width: 100%; padding: 10px 12px; border: 1px solid #e3e6ef; border-radius: 10px; }
        .error { color: #b91c1c; font-size: 13px; margin-top: 6px; }
        .actions { display: flex; gap: 10px; margin-top: 16px; }
        button, a.button { padding: 10px 12px; border-radius: 10px; border: 1px solid #e3e6ef; text-decoration: none; font-weight: 600; }
        button { background: #111827; color: #fff; border-color: #111827; cursor: pointer; }
        a.button { background: #fff; color: #111827; }
        .muted { color: #6b7280; font-size: 13px; }
    </style>
</head>
<body>
<div class="container">
    <h1><?= h($title) ?></h1>
    <div class="muted"><?= $isEdit ? 'Cập nhật thông tin sinh viên.' : 'Nhập thông tin để thêm sinh viên mới.' ?></div>

    <form method="post">
        <label for="name">Họ tên</label>
        <input id="name" name="name" type="text" value="<?= h((string)($values['name'] ?? '')) ?>" autocomplete="off">
        <?php if (!empty($errors['name'])): ?>
            <div class="error"><?= h((string)$errors['name']) ?></div>
        <?php endif; ?>

        <label for="major">Ngành học</label>
        <input id="major" name="major" type="text" value="<?= h((string)($values['major'] ?? '')) ?>" autocomplete="off">
        <?php if (!empty($errors['major'])): ?>
            <div class="error"><?= h((string)$errors['major']) ?></div>
        <?php endif; ?>

        <div class="actions">
            <button type="submit"><?= $isEdit ? 'Lưu thay đổi' : 'Thêm sinh viên' ?></button>
            <a class="button" href="index.php?action=list">Quay lại</a>
        </div>
    </form>
</div>
</body>
</html>

