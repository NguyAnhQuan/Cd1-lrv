<?php
declare(strict_types=1);

require_once __DIR__ . '/../model/Student.php';
require_once __DIR__ . '/../model/StudentRepositoryJson.php';

final class StudentController
{
    private StudentRepositoryJson $repo;

    public function __construct(StudentRepositoryJson $repo)
    {
        $this->repo = $repo;
    }

    public function index(): void
    {
        $q = trim((string)($_GET['q'] ?? ''));
        $page = (int)($_GET['page'] ?? 1);
        if ($page < 1) $page = 1;
        $perPage = 5;

        $result = $this->repo->searchPaginated($q, $page, $perPage);

        $students = $result['items'];
        $total = $result['total'];
        $totalPages = (int)ceil(max(1, $total) / $perPage);

        require __DIR__ . '/../view/student_list.php';
    }

    public function add(): void
    {
        $errors = [];
        $values = ['name' => '', 'major' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $values['name'] = trim((string)($_POST['name'] ?? ''));
            $values['major'] = trim((string)($_POST['major'] ?? ''));

            $errors = $this->validate($values['name'], $values['major']);
            if (!$errors) {
                $student = Student::createNew($values['name'], $values['major']);
                $this->repo->add($student);
                $this->redirectToList();
                return;
            }
        }

        require __DIR__ . '/../view/student_form.php';
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $student = $this->repo->findById($id);
        if (!$student) {
            $this->redirectToList();
            return;
        }

        $errors = [];
        $values = ['name' => $student->name, 'major' => $student->major];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $values['name'] = trim((string)($_POST['name'] ?? ''));
            $values['major'] = trim((string)($_POST['major'] ?? ''));

            $errors = $this->validate($values['name'], $values['major']);
            if (!$errors) {
                $updated = new Student($student->id, $values['name'], $values['major']);
                $this->repo->update($updated);
                $this->redirectToList();
                return;
            }
        }

        require __DIR__ . '/../view/student_form.php';
    }

    public function delete(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->repo->deleteById($id);
        }
        $this->redirectToList();
    }

    private function validate(string $name, string $major): array
    {
        $errors = [];

        if ($name === '') {
            $errors['name'] = 'Họ tên không được để trống.';
        } elseif (mb_strlen($name) < 3) {
            $errors['name'] = 'Họ tên phải từ 3 ký tự trở lên.';
        }

        if ($major === '') {
            $errors['major'] = 'Ngành học không được để trống.';
        }

        return $errors;
    }

    private function redirectToList(): void
    {
        header('Location: index.php?action=list');
        exit;
    }
}

