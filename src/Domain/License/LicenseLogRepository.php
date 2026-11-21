<?php

declare(strict_types=1);

namespace App\Domain\License;

use PDO;

class LicenseLogRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getRecent(int $limit = 50): array
    {
        $limit = max(1, min(500, $limit));
        $stmt = $this->pdo->prepare('SELECT * FROM "license_logs" ORDER BY "id" DESC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM "license_logs" ORDER BY "id" DESC');
        return $stmt->fetchAll();
    }
}
