<?php

namespace App\Models;

use App\Database;
use PDO;

class Branch {
    public static function all(): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT b.*, COUNT(s.id) AS schedule_count 
                             FROM branches b 
                             LEFT JOIN schedules s ON b.id = s.branch_id 
                             GROUP BY b.id 
                             ORDER BY b.id ASC");
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM branches WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO branches (branch_name, address) VALUES (:branch_name, :address)");
        $stmt->execute([
            'branch_name' => trim($data['branch_name']),
            'address' => trim($data['address']),
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE branches SET branch_name = :branch_name, address = :address WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'branch_name' => trim($data['branch_name']),
            'address' => trim($data['address']),
        ]);
    }

    public static function delete(int $id): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM branches WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
