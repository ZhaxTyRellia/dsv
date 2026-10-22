<?php

declare(strict_types=1);

namespace App\Domain\Deposit;

use InvalidArgumentException;
use PDO;

class DepositRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function getTableName(int $depositIndex): string
    {
        if ($depositIndex < 1 || $depositIndex > 8) {
            throw new InvalidArgumentException("Invalid deposit index: {$depositIndex}. Must be between 1 and 8.");
        }
        return "deposit_{$depositIndex}";
    }

    public function getAll(int $depositIndex): array
    {
        $table = $this->getTableName($depositIndex);
        $stmt = $this->pdo->query("SELECT * FROM `{$table}` ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    public function getAllTables(): array
    {
        $data = [];
        for ($i = 1; $i <= 8; $i++) {
            $data[$i] = $this->getAll($i);
        }
        return $data;
    }

    public function getById(int $depositIndex, int $id): ?array
    {
        $table = $this->getTableName($depositIndex);
        $stmt = $this->pdo->prepare("SELECT * FROM `{$table}` WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(
        int $depositIndex,
        string $tenor,
        float $interestRate,
        float $minAmount,
        string $description
    ): bool {
        $table = $this->getTableName($depositIndex);
        $stmt = $this->pdo->prepare(
            "INSERT INTO `{$table}` (tenor, interest_rate, min_amount, description) 
             VALUES (:tenor, :interest_rate, :min_amount, :description)"
        );
        return $stmt->execute([
            ':tenor' => $tenor,
            ':interest_rate' => $interestRate,
            ':min_amount' => $minAmount,
            ':description' => $description,
        ]);
    }

    public function update(
        int $depositIndex,
        int $id,
        string $tenor,
        float $interestRate,
        float $minAmount,
        string $description
    ): bool {
        $table = $this->getTableName($depositIndex);
        $stmt = $this->pdo->prepare(
            "UPDATE `{$table}` 
             SET tenor = :tenor, interest_rate = :interest_rate, min_amount = :min_amount, description = :description 
             WHERE id = :id"
        );
        return $stmt->execute([
            ':id' => $id,
            ':tenor' => $tenor,
            ':interest_rate' => $interestRate,
            ':min_amount' => $minAmount,
            ':description' => $description,
        ]);
    }

    public function delete(int $depositIndex, int $id): bool
    {
        $table = $this->getTableName($depositIndex);
        $stmt = $this->pdo->prepare("DELETE FROM `{$table}` WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
