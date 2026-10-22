<?php

declare(strict_types=1);

namespace App\Domain\Identity;

use PDO;

class IdentityRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function get(): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM identity ORDER BY id ASC LIMIT 1');
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function update(string $logo, string $title, string $name, string $marquee, int $id = 1): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE identity SET logo = :logo, title = :title, name = :name, marquee = :marquee WHERE id = :id'
        );
        return $stmt->execute([
            ':logo' => $logo,
            ':title' => $title,
            ':name' => $name,
            ':marquee' => $marquee,
            ':id' => $id,
        ]);
    }

    public function updateOption(string $option, int $id = 1): bool
    {
        $stmt = $this->pdo->prepare('UPDATE identity SET options = :options WHERE id = :id');
        return $stmt->execute([
            ':options' => $option,
            ':id' => $id,
        ]);
    }

    public function updateTheme(string $theme, int $id = 1): bool
    {
        $stmt = $this->pdo->prepare('UPDATE identity SET themes = :themes WHERE id = :id');
        return $stmt->execute([
            ':themes' => $theme,
            ':id' => $id,
        ]);
    }
}
