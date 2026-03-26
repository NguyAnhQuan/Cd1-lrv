<?php
declare(strict_types=1);

final class Student
{
    public int $id;
    public string $name;
    public string $major;

    public function __construct(int $id, string $name, string $major)
    {
        $this->id = $id;
        $this->name = $name;
        $this->major = $major;
    }

    public static function createNew(string $name, string $major): self
    {
        return new self(0, $name, $major);
    }

    public static function fromArray(array $row): self
    {
        return new self(
            (int)($row['id'] ?? 0),
            (string)($row['name'] ?? ''),
            (string)($row['major'] ?? '')
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'major' => $this->major,
        ];
    }
}

