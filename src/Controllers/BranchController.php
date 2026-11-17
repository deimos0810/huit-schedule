<?php

namespace App\Controllers;

use App\Auth;
use App\Models\Branch;
use App\Models\Schedule;

class BranchController {
    public function index(): void {
        Auth::requireAdmin();
        $branches = Branch::all();
        require_once __DIR__ . '/../../views/branches/index.php';
    }

    public function create(): void {
        Auth::requireAdmin();
        require_once __DIR__ . '/../../views/branches/create.php';
    }

    public function store(): void {
        Auth::requireAdmin();
        Auth::init();

        $branchName = trim($_POST['branch_name'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if (empty($branchName) || empty($address)) {
            $_SESSION['flash_error'] = 'Vui lòng điền tên cơ sở và địa chỉ.';
            header('Location: ?action=branch_create');
            exit;
        }

        Branch::create([
            'branch_name' => $branchName,
            'address' => $address,
        ]);

        $_SESSION['flash_success'] = 'Thêm cơ sở mới thành công!';
        header('Location: ?action=branches');
        exit;
    }

    public function edit(): void {
        Auth::requireAdmin();

        $id = (int)($_GET['id'] ?? 0);
        $branch = Branch::find($id);

        if (!$branch) {
            $_SESSION['flash_error'] = 'Không tìm thấy cơ sở.';
            header('Location: ?action=branches');
            exit;
        }

        require_once __DIR__ . '/../../views/branches/edit.php';
    }

    public function update(): void {
        Auth::requireAdmin();
        Auth::init();

        $id = (int)($_POST['id'] ?? 0);
        $branchName = trim($_POST['branch_name'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if (!$id || empty($branchName) || empty($address)) {
            $_SESSION['flash_error'] = 'Vui lòng nhập đầy đủ thông tin cơ sở.';
            header("Location: ?action=branch_edit&id={$id}");
            exit;
        }

        Branch::update($id, [
            'branch_name' => $branchName,
            'address' => $address
        ]);

        $_SESSION['flash_success'] = 'Cập nhật cơ sở thành công!';
        header('Location: ?action=branches');
        exit;
    }

    public function delete(): void {
        Auth::requireAdmin();
        Auth::init();

        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id) {
            Branch::delete($id);
            $_SESSION['flash_success'] = 'Đã xóa cơ sở thành công.';
        }
        header('Location: ?action=branches');
        exit;
    }

    public function view(): void {
        Auth::requireAdmin();

        $id = (int)($_GET['id'] ?? 0);
        $branch = Branch::find($id);

        if (!$branch) {
            $_SESSION['flash_error'] = 'Không tìm thấy cơ sở.';
            header('Location: ?action=branches');
            exit;
        }

        $schedules = Schedule::all(['branch_id' => $id]);
        $matrix = Schedule::getMatrixData($id, null);
        $days = Schedule::$daysOfWeek;
        $shifts = Schedule::$shifts;

        require_once __DIR__ . '/../../views/branches/view.php';
    }
}
