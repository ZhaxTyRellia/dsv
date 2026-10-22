<?php

declare(strict_types=1);

namespace App\Domain\Video;

use PDO;

class VideoRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM videos ORDER BY create_at ASC, id ASC');
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM videos WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(string $fileName, ?string $createdAt = null): bool
    {
        $createdAt = $createdAt ?? date('Y-m-d H:i:s');
        $stmt = $this->pdo->prepare(
            'INSERT INTO videos (file_name, create_at) VALUES (:file_name, :create_at)'
        );
        return $stmt->execute([
            ':file_name' => $fileName,
            ':create_at' => $createdAt,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM videos WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
