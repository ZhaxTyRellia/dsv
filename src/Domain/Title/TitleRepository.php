<?php

declare(strict_types=1);

namespace App\Domain\Title;

use PDO;

class TitleRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getLatest(): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM titles ORDER BY id_information DESC LIMIT 1'
        );
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: [];
    }

    public function save(array $titles): bool
    {
        $existing = $this->getLatest();

        $params = [];
        for ($i = 1; $i <= 9; $i++) {
            $key = 'title_' . $i;
            $params[$key] = trim((string)($titles[$key] ?? ''));
        }

        if (!empty($existing['id_information'])) {
            $sql = 'UPDATE titles SET 
                title_1 = :title_1, title_2 = :title_2, title_3 = :title_3,
                title_4 = :title_4, title_5 = :title_5, title_6 = :title_6,
                title_7 = :title_7, title_8 = :title_8, title_9 = :title_9
                WHERE id_information = :id_information';
            $params['id_information'] = (int)$existing['id_information'];
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        }

        $sql = 'INSERT INTO titles (title_1, title_2, title_3, title_4, title_5, title_6, title_7, title_8, title_9)
                VALUES (:title_1, :title_2, :title_3, :title_4, :title_5, :title_6, :title_7, :title_8, :title_9)';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
}
