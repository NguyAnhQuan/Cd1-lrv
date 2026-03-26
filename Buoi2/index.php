<?php
declare(strict_types=1);

require_once __DIR__ . '/controller/StudentController.php';
require_once __DIR__ . '/model/StudentRepositoryJson.php';

$repo = new StudentRepositoryJson(__DIR__ . '/data/students.json');
$controller = new StudentController($repo);

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'add':
        $controller->add();
        break;
    case 'edit':
        $controller->edit();
        break;
    case 'delete':
        $controller->delete();
        break;
    case 'list':
    default:
        $controller->index();
        break;
}

