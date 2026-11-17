<?php

namespace App\Models;

use App\Database;
use PDO;

class Teacher {
    public static function all(?string $search = null): array {
        $pdo = Database::getConnection();
        if ($search) {
            $stmt = $pdo->prepare("SELECT t.*, COUNT(s.id) AS schedule_count 
                                 FROM teachers t 
                                 LEFT JOIN schedules s ON t.id = s.teacher_id 
                                 WHERE t.full_name LIKE :s OR t.email LIKE :s OR t.expertise LIKE :s
                                 GROUP BY t.id 
                                 ORDER BY t.full_name ASC");
            $stmt->execute(['s' => "%{$search}%"]);
        } else {
            $stmt = $pdo->query("SELECT t.*, COUNT(s.id) AS schedule_count 
                                 FROM teachers t 
                                 LEFT JOIN schedules s ON t.id = s.teacher_id 
                                 GROUP BY t.id 
                                 ORDER BY t.full_name ASC");
        }
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM teachers WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByEmail(string $email): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM teachers WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => trim($email)]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int {
        $pdo = Database::getConnection();
        $password = !empty($data['password']) 
            ? password_hash($data['password'], PASSWORD_BCRYPT) 
            : password_hash('123456', PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("INSERT INTO teachers (full_name, email, phone, expertise, password) 
                               VALUES (:full_name, :email, :phone, :expertise, :password)");
        $stmt->execute([
            'full_name' => trim($data['full_name']),
            'email' => trim($data['email']),
            'phone' => trim($data['phone']),
            'expertise' => trim($data['expertise']),
            'password' => $password
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $pdo = Database::getConnection();
        $params = [
            'id' => $id,
            'full_name' => trim($data['full_name']),
            'email' => trim($data['email']),
            'phone' => trim($data['phone']),
            'expertise' => trim($data['expertise']),
        ];

        $sql = "UPDATE teachers SET full_name = :full_name, email = :email, phone = :phone, expertise = :expertise";
        if (!empty($data['password'])) {
            $sql .= ", password = :password";
            $params['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        $sql .= " WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public static function delete(int $id): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM teachers WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
