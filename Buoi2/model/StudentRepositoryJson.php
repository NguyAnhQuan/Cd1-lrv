<?php
declare(strict_types=1);

require_once __DIR__ . '/Student.php';

final class StudentRepositoryJson
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->ensureStorageReady();
    }

    public function getAll(): array
    {
        return $this->loadAllStudents();
    }

    public function findById(int $id): ?Student
    {
        foreach ($this->loadAllStudents() as $s) {
            if ($s->id === $id) return $s;
        }
        return null;
    }

    public function add(Student $student): Student
    {
        $students = $this->loadAllStudents();
        $nextId = $this->nextId($students);
        $student->id = $nextId;
        $students[] = $student;
        $this->saveAllStudents($students);
        return $student;
    }

    public function update(Student $student): bool
    {
        $students = $this->loadAllStudents();
        $found = false;
        foreach ($students as $i => $s) {
            if ($s->id === $student->id) {
                $students[$i] = $student;
                $found = true;
                break;
            }
        }
        if ($found) {
            $this->saveAllStudents($students);
        }
        return $found;
    }

    public function deleteById(int $id): bool
    {
        $students = $this->loadAllStudents();
        $before = count($students);
        $students = array_values(array_filter($students, fn(Student $s) => $s->id !== $id));
        if (count($students) !== $before) {
            $this->saveAllStudents($students);
            return true;
        }
        return false;
    }

    public function searchPaginated(string $q, int $page, int $perPage): array
    {
        $qNorm = mb_strtolower(trim($q));
        $all = $this->loadAllStudents();

        if ($qNorm !== '') {
            $all = array_values(array_filter($all, function (Student $s) use ($qNorm) {
                return mb_strpos(mb_strtolower($s->name), $qNorm) !== false;
            }));
        }

        usort($all, fn(Student $a, Student $b) => $a->id <=> $b->id);

        $total = count($all);
        $start = max(0, ($page - 1) * $perPage);
        $items = array_slice($all, $start, $perPage);

        return [
            'items' => $items,
            'total' => $total,
        ];
    }

    private function ensureStorageReady(): void
    {
        $dir = dirname($this->filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    private function loadAllStudents(): array
    {
        $raw = @file_get_contents($this->filePath);
        if ($raw === false || trim($raw) === '') {
            return [];
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) return [];

        $students = [];
        foreach ($data as $row) {
            if (is_array($row)) {
                $students[] = Student::fromArray($row);
            }
        }
        return $students;
    }

    private function saveAllStudents(array $students): void
    {
        $rows = array_map(fn(Student $s) => $s->toArray(), $students);
        $json = json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) $json = '[]';

        $fp = fopen($this->filePath, 'c+');
        if ($fp === false) {
            file_put_contents($this->filePath, $json);
            return;
        }

        try {
            if (flock($fp, LOCK_EX)) {
                ftruncate($fp, 0);
                rewind($fp);
                fwrite($fp, $json);
                fflush($fp);
                flock($fp, LOCK_UN);
            }
        } finally {
            fclose($fp);
        }
    }

    private function nextId(array $students): int
    {
        $max = 0;
        foreach ($students as $s) {
            if ($s->id > $max) $max = $s->id;
        }
        return $max + 1;
    }
}

