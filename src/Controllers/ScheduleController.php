<?php

namespace App\Controllers;

use App\Auth;
use App\Models\Branch;
use App\Models\Teacher;
use App\Models\Schedule;

class ScheduleController {
    public function index(): void {
        Auth::requireAdmin();

        $filters = [
            'branch_id' => $_GET['branch_id'] ?? null,
            'teacher_id' => $_GET['teacher_id'] ?? null,
            'day_of_week' => $_GET['day_of_week'] ?? null,
            'shift' => $_GET['shift'] ?? null,
            'search' => $_GET['search'] ?? null,
        ];

        $schedules = Schedule::all($filters);
        $branches = Branch::all();
        $teachers = Teacher::all();
        $days = Schedule::$daysOfWeek;
        $shifts = Schedule::$shifts;

        require_once __DIR__ . '/../../views/schedules/index.php';
    }

    public function create(): void {
        Auth::requireAdmin();

        $branches = Branch::all();
        $teachers = Teacher::all();
        $days = Schedule::$daysOfWeek;
        $shifts = Schedule::$shifts;

        // Default or preselected parameters
        $preselectedTeacherId = !empty($_GET['teacher_id']) ? (int)$_GET['teacher_id'] : null;
        $preselectedBranchId = !empty($_GET['branch_id']) ? (int)$_GET['branch_id'] : ($branches[0]['id'] ?? 1);
        $preselectedDay = $_GET['day'] ?? 'Thứ 2';
        $preselectedShift = $_GET['shift'] ?? 'Sáng (Tiết 1 - 3)';

        require_once __DIR__ . '/../../views/schedules/create.php';
    }

    public function store(): void {
        Auth::requireAdmin();
        Auth::init();

        $teacherId = (int)($_POST['teacher_id'] ?? 0);
        $branchId = (int)($_POST['branch_id'] ?? 0);
        $subjectName = trim($_POST['subject_name'] ?? '');
        $room = trim($_POST['room'] ?? '');
        $dayOfWeek = trim($_POST['day_of_week'] ?? '');
        $shiftOrPeriods = trim($_POST['shift_or_periods'] ?? '');
        $semester = trim($_POST['semester'] ?? 'HK1 (2026-2027)');
        $notes = trim($_POST['notes'] ?? '');

        if (!$teacherId || !$branchId || empty($subjectName) || empty($room) || empty($dayOfWeek) || empty($shiftOrPeriods)) {
            $_SESSION['flash_error'] = 'Vui lòng điền đầy đủ các thông tin bắt buộc.';
            header('Location: ?action=schedule_create');
            exit;
        }

        // 1. Kiểm tra trùng lịch giảng viên
        $teacherConflict = Schedule::checkTeacherConflict($teacherId, $dayOfWeek, $shiftOrPeriods);
        if ($teacherConflict) {
            $_SESSION['flash_error'] = "TRÙNG LỊCH: Giảng viên {$teacherConflict['teacher_name']} đã có lịch dạy môn '{$teacherConflict['subject_name']}' tại {$teacherConflict['branch_name']} ({$dayOfWeek}, {$shiftOrPeriods})!";
            header('Location: ?action=schedule_create');
            exit;
        }

        // 2. Kiểm tra trùng phòng học
        $roomConflict = Schedule::checkRoomConflict($branchId, $room, $dayOfWeek, $shiftOrPeriods);
        if ($roomConflict) {
            $_SESSION['flash_error'] = "TRÙNG PHÒNG HỌC: Phòng '{$room}' tại {$roomConflict['branch_name']} đã có lớp dạy môn '{$roomConflict['subject_name']}' ({$dayOfWeek}, {$shiftOrPeriods})!";
            header('Location: ?action=schedule_create');
            exit;
        }

        // 3. Phân tích di chuyển & tối ưu hóa
        $travelAnalysis = Schedule::checkTravelOptimization($teacherId, $branchId, $dayOfWeek, $shiftOrPeriods);

        Schedule::create([
            'teacher_id' => $teacherId,
            'branch_id' => $branchId,
            'subject_name' => $subjectName,
            'room' => $room,
            'day_of_week' => $dayOfWeek,
            'shift_or_periods' => $shiftOrPeriods,
            'semester' => $semester,
            'notes' => $notes
        ]);

        if ($travelAnalysis['status'] === 'warning') {
            $_SESSION['flash_warning'] = "Đã lưu lịch dạy thành công! Tuy nhiên: {$travelAnalysis['message']}";
        } else {
            $_SESSION['flash_success'] = 'Phân lịch giảng dạy thành công!';
        }

        header('Location: ?action=schedules');
        exit;
    }

    public function edit(): void {
        Auth::requireAdmin();

        $id = (int)($_GET['id'] ?? 0);
        $schedule = Schedule::find($id);

        if (!$schedule) {
            $_SESSION['flash_error'] = 'Lịch dạy không tồn tại.';
            header('Location: ?action=schedules');
            exit;
        }

        $branches = Branch::all();
        $teachers = Teacher::all();
        $days = Schedule::$daysOfWeek;
        $shifts = Schedule::$shifts;

        require_once __DIR__ . '/../../views/schedules/edit.php';
    }

    public function update(): void {
        Auth::requireAdmin();
        Auth::init();

        $id = (int)($_POST['id'] ?? 0);
        $teacherId = (int)($_POST['teacher_id'] ?? 0);
        $branchId = (int)($_POST['branch_id'] ?? 0);
        $subjectName = trim($_POST['subject_name'] ?? '');
        $room = trim($_POST['room'] ?? '');
        $dayOfWeek = trim($_POST['day_of_week'] ?? '');
        $shiftOrPeriods = trim($_POST['shift_or_periods'] ?? '');
        $semester = trim($_POST['semester'] ?? 'HK1 (2026-2027)');
        $notes = trim($_POST['notes'] ?? '');

        if (!$id || !$teacherId || !$branchId || empty($subjectName) || empty($room) || empty($dayOfWeek) || empty($shiftOrPeriods)) {
            $_SESSION['flash_error'] = 'Vui lòng điền đầy đủ các thông tin bắt buộc.';
            header("Location: ?action=schedule_edit&id={$id}");
            exit;
        }

        // 1. Kiểm tra trùng lịch giảng viên (ngoại trừ lịch này)
        $teacherConflict = Schedule::checkTeacherConflict($teacherId, $dayOfWeek, $shiftOrPeriods, $id);
        if ($teacherConflict) {
            $_SESSION['flash_error'] = "TRÙNG LỊCH: Giảng viên {$teacherConflict['teacher_name']} đã có lịch dạy môn '{$teacherConflict['subject_name']}' tại {$teacherConflict['branch_name']} ({$dayOfWeek}, {$shiftOrPeriods})!";
            header("Location: ?action=schedule_edit&id={$id}");
            exit;
        }

        // 2. Kiểm tra trùng phòng học
        $roomConflict = Schedule::checkRoomConflict($branchId, $room, $dayOfWeek, $shiftOrPeriods, $id);
        if ($roomConflict) {
            $_SESSION['flash_error'] = "TRÙNG PHÒNG HỌC: Phòng '{$room}' tại {$roomConflict['branch_name']} đã có lớp dạy môn '{$roomConflict['subject_name']}' ({$dayOfWeek}, {$shiftOrPeriods})!";
            header("Location: ?action=schedule_edit&id={$id}");
            exit;
        }

        Schedule::update($id, [
            'teacher_id' => $teacherId,
            'branch_id' => $branchId,
            'subject_name' => $subjectName,
            'room' => $room,
            'day_of_week' => $dayOfWeek,
            'shift_or_periods' => $shiftOrPeriods,
            'semester' => $semester,
            'notes' => $notes
        ]);

        $_SESSION['flash_success'] = 'Cập nhật lịch dạy thành công!';
        header('Location: ?action=schedules');
        exit;
    }

    public function delete(): void {
        Auth::requireAdmin();
        Auth::init();

        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($id) {
            Schedule::delete($id);
            $_SESSION['flash_success'] = 'Đã xóa lịch dạy thành công.';
        }
        header('Location: ?action=schedules');
        exit;
    }

    /**
     * API Gợi ý Giảng viên phù hợp (JSON response for AJAX)
     */
    public function apiSuggestTeachers(): void {
        header('Content-Type: application/json; charset=utf-8');

        $subjectName = trim($_GET['subject'] ?? '');
        $day = trim($_GET['day'] ?? 'Thứ 2');
        $shift = trim($_GET['shift'] ?? 'Sáng (Tiết 1 - 3)');
        $branchId = (int)($_GET['branch_id'] ?? 1);

        $suggestions = Schedule::suggestTeachers($subjectName, $day, $shift, $branchId);

        echo json_encode([
            'success' => true,
            'data' => $suggestions,
            'query' => [
                'subject' => $subjectName,
                'day' => $day,
                'shift' => $shift,
                'branch_id' => $branchId
            ]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * API Kiểm tra xung đột & tối ưu hóa di chuyển (JSON response)
     */
    public function apiCheckConflict(): void {
        header('Content-Type: application/json; charset=utf-8');

        $teacherId = (int)($_GET['teacher_id'] ?? 0);
        $branchId = (int)($_GET['branch_id'] ?? 0);
        $day = trim($_GET['day'] ?? '');
        $shift = trim($_GET['shift'] ?? '');
        $room = trim($_GET['room'] ?? '');
        $excludeId = !empty($_GET['exclude_id']) ? (int)$_GET['exclude_id'] : null;

        $teacherConflict = $teacherId ? Schedule::checkTeacherConflict($teacherId, $day, $shift, $excludeId) : null;
        $roomConflict = ($branchId && $room) ? Schedule::checkRoomConflict($branchId, $room, $day, $shift, $excludeId) : null;
        $travelAnalysis = ($teacherId && $branchId) ? Schedule::checkTravelOptimization($teacherId, $branchId, $day, $shift, $excludeId) : null;

        echo json_encode([
            'teacher_conflict' => $teacherConflict ? [
                'has_conflict' => true,
                'message' => "Trùng lịch: Giảng viên đã dạy môn '{$teacherConflict['subject_name']}' tại {$teacherConflict['branch_name']}"
            ] : ['has_conflict' => false],
            'room_conflict' => $roomConflict ? [
                'has_conflict' => true,
                'message' => "Trùng phòng: Phòng '{$room}' đã có lớp học"
            ] : ['has_conflict' => false],
            'travel_analysis' => $travelAnalysis
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}
