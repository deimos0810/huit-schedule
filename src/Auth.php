<?php

namespace App;

class Auth {
    public static function init(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function check(): bool {
        self::init();
        return isset($_SESSION['user']);
    }

    public static function user(): ?array {
        self::init();
        return $_SESSION['user'] ?? null;
    }

    public static function role(): ?string {
        self::init();
        return $_SESSION['user']['role'] ?? null;
    }

    public static function isAdmin(): bool {
        return self::role() === 'admin';
    }

    public static function isTeacher(): bool {
        return self::role() === 'teacher';
    }

    public static function loginUser(string $usernameOrEmail, string $password): bool {
        self::init();
        $pdo = Database::getConnection();

        // Check in users table (Admin/Staff)
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :login1 OR email = :login2 LIMIT 1");
        $stmt->execute(['login1' => $usernameOrEmail, 'login2' => $usernameOrEmail]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['full_name'],
                'email' => $user['email'],
                'username' => $user['username'],
                'role' => $user['role'],
                'type' => 'user'
            ];
            return true;
        }

        return false;
    }

    public static function loginTeacher(string $email, string $password): bool {
        self::init();
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM teachers WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => trim($email)]);
        $teacher = $stmt->fetch();

        if ($teacher && password_verify($password, $teacher['password'])) {
            $_SESSION['user'] = [
                'id' => $teacher['id'],
                'teacher_id' => $teacher['id'],
                'name' => $teacher['full_name'],
                'email' => $teacher['email'],
                'phone' => $teacher['phone'],
                'expertise' => $teacher['expertise'],
                'role' => 'teacher',
                'type' => 'teacher'
            ];
            return true;
        }

        return false;
    }

    public static function logout(): void {
        self::init();
        unset($_SESSION['user']);
        session_destroy();
    }

    public static function requireAdmin(): void {
        self::init();
        if (!self::isAdmin()) {
            $_SESSION['flash_error'] = 'Vui lòng đăng nhập với quyền Quản trị viên để truy cập.';
            header('Location: ?action=login');
            exit;
        }
    }

    public static function requireTeacher(): void {
        self::init();
        if (!self::isTeacher() && !self::isAdmin()) {
            $_SESSION['flash_error'] = 'Vui lòng đăng nhập để xem thông tin giảng dạy.';
            header('Location: ?action=teacher_login');
            exit;
        }
    }
}
