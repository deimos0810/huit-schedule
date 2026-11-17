<?php

namespace App;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $root = dirname(__DIR__);
            if (file_exists($root . '/.env')) {
                $dotenv = Dotenv::createImmutable($root);
                $dotenv->safeLoad();
            }

            $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $port = $_ENV['DB_PORT'] ?? '3306';
            $dbName = $_ENV['DB_DATABASE'] ?? 'huit_db';
            $user = $_ENV['DB_USERNAME'] ?? 'root';
            $pass = $_ENV['DB_PASSWORD'] ?? '';

            try {
                $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                die("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
            }
        }

        return self::$instance;
    }
}
