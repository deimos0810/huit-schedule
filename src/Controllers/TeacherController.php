<?php

namespace App\Controllers;

use App\Auth;
use App\Models\Teacher;
use App\Models\Schedule;

class TeacherController {
    public function index(): void {
        Auth::requireAdmin();

        $search = $_GET['search'] ?? null;
        $teachers = Teacher::all($search);

        require_once __DIR__ . '/../../views/teachers/index.php';
    }

    public function create(): void {
        Auth::requireAdmin();
        require_once __DIR__ . '/../../views/teachers/create.php';
    }

    public function store(): void {
        Auth::requireAdmin();
        Auth::init();

        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $expertise = trim($_POST['expertise'] ?? '');
        $password = trim($_POST['password'] ?? '123456');

        if (empty($fullName) || empty($email) || empty($phone) || empty($expertise)) {
            $_SESSION['flash_error'] = 'Vui lòng điền đầy đủ các thông tin bắt buộc.';
            header('Location: ?action=teacher_create');
            exit;
        }

        // Check email unique
        if (Teacher::findByEmail($email)) {
            $_SESSION['flash_error'] = 'Email này đã tồn tại trong hệ thống.';
            header('Location: ?action=teacher_create');
            exit;
        }

        Teacher::create([
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'expertise' => $expertise,
            'password' => $password
        ]);

        $_SESSION['flash_success'] = 'Thêm giảng viên mới thành công!';
        header('Location: ?action=teachers');
        exit;
    }

    public function edit(): void {
        Auth::requireAdmin();

        $id = (int)($_GET['id'] ?? 0);
        $teacher = Teacher::find($id);

        if (!$teacher) {
            $_SESSION['flash_error'] = 'Không tìm thấy giảng viên.';
            header('Location: ?action=teachers');
            exit;
        }

        require_once __DIR__ . '/../../views/teachers/edit.php';
    }

    public function update(): void {
        Auth::requireAdmin();
        Auth::init();

        $id = (int)($_POST['id'] ?? 0);
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $expertise = trim($_POST['expertise'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (!$id || empty($fullName) || empty($email) || empty($phone) || empty($expertise)) {
            $_SESSION['flash_error'] = 'Vui lòng điền đầy đủ các trường thông tin bắt buộc.';
            header("Location: ?action=teacher_edit&id={$id}");
            exit;
        }

        $existing = Teacher::findByEmail($email);
        if ($existing && $existing['id'] != $id) {
            $_SESSION['flash_error'] = 'Email này đã được sử dụng bởi giảng viên khác.';
            header("Location: ?action=teacher_edit&id={$id}");
            exit;
        }

        $data = [
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'expertise' => $expertise
        ];
        if (!empty($password)) {
            $data['password'] = $password;
        }

        Teacher::update($id, $data);

        $_SESSION['flash_success'] = 'Cập nhật thông tin giảng viên thành công!';
        header('Location: ?action=teachers');
        exit;
    }

    public function delete(): void {
        Auth::requireAdmin();
        Auth::init();

        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id) {
            Teacher::delete($id);
            $_SESSION['flash_success'] = 'Đã xóa giảng viên thành công.';
        }
        header('Location: ?action=teachers');
        exit;
    }

    public function view(): void {
        Auth::requireAdmin();

        $id = (int)($_GET['id'] ?? 0);
        $teacher = Teacher::find($id);

        if (!$teacher) {
            $_SESSION['flash_error'] = 'Không tìm thấy giảng viên.';
            header('Location: ?action=teachers');
            exit;
        }

        $schedules = Schedule::all(['teacher_id' => $id]);
        $matrix = Schedule::getMatrixData(null, $id);
        $days = Schedule::$daysOfWeek;
        $shifts = Schedule::$shifts;

        require_once __DIR__ . '/../../views/teachers/view.php';
    }
}
