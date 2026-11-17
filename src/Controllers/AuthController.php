<?php

namespace App\Controllers;

use App\Auth;

class AuthController {
    public function showLogin(): void {
        if (Auth::check()) {
            if (Auth::isAdmin()) {
                header('Location: ?action=dashboard');
            } else {
                header('Location: ?action=portal');
            }
            exit;
        }
        $type = $_GET['type'] ?? 'admin';
        require_once __DIR__ . '/../../views/auth/login.php';
    }

    public function handleLogin(): void {
        Auth::init();
        $type = $_POST['type'] ?? 'admin';
        $login = trim($_POST['username'] ?? $_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($login) || empty($password)) {
            $_SESSION['flash_error'] = 'Vui lòng nhập đầy đủ tài khoản/email và mật khẩu.';
            header("Location: ?action=login&type={$type}");
            exit;
        }

        if ($type === 'teacher') {
            if (Auth::loginTeacher($login, $password)) {
                $_SESSION['flash_success'] = 'Đăng nhập Cổng thông tin Giảng viên thành công!';
                header('Location: ?action=portal');
                exit;
            }
        } else {
            if (Auth::loginUser($login, $password)) {
                $_SESSION['flash_success'] = 'Đăng nhập Quản trị viên thành công!';
                header('Location: ?action=dashboard');
                exit;
            }
        }

        $_SESSION['flash_error'] = 'Thông tin đăng nhập không chính xác. Vui lòng thử lại!';
        header("Location: ?action=login&type={$type}");
        exit;
    }

    public function logout(): void {
        Auth::logout();
        header('Location: ?action=login');
        exit;
    }
}
