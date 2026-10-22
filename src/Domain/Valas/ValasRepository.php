<?php

declare(strict_types=1);

namespace App\Domain\Valas;

use PDO;

class ValasRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM valas ORDER BY id ASC');
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM valas WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(string $code, string $name, string $flag, float $buyRate, float $sellRate): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO valas (currency_code, currency_name, flag_image, buy_rate, sell_rate) 
             VALUES (:code, :name, :flag, :buy, :sell)'
        );
        return $stmt->execute([
            ':code' => strtoupper($code),
            ':name' => $name,
            ':flag' => strtolower($flag),
            ':buy' => $buyRate,
            ':sell' => $sellRate,
        ]);
    }

    public function update(int $id, string $code, string $name, string $flag, float $buyRate, float $sellRate): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE valas 
             SET currency_code = :code, currency_name = :name, flag_image = :flag, buy_rate = :buy, sell_rate = :sell 
             WHERE id = :id'
        );
        return $stmt->execute([
            ':id' => $id,
            ':code' => strtoupper($code),
            ':name' => $name,
            ':flag' => strtolower($flag),
            ':buy' => $buyRate,
            ':sell' => $sellRate,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM valas WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
