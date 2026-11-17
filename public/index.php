<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Auth;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ScheduleController;
use App\Controllers\TeacherController;
use App\Controllers\BranchController;
use App\Controllers\PortalController;

Auth::init();

$action = $_GET['action'] ?? 'dashboard';

// Route Dispatcher
switch ($action) {
    // Auth Routes
    case 'login':
        (new AuthController())->showLogin();
        break;
    case 'login_submit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new AuthController())->handleLogin();
        } else {
            header('Location: ?action=login');
        }
        break;
    case 'logout':
        (new AuthController())->logout();
        break;

    // Dashboard
    case 'dashboard':
    case '':
        (new DashboardController())->index();
        break;

    // Schedule Management
    case 'schedules':
        (new ScheduleController())->index();
        break;
    case 'schedule_create':
        (new ScheduleController())->create();
        break;
    case 'schedule_store':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new ScheduleController())->store();
        } else {
            header('Location: ?action=schedule_create');
        }
        break;
    case 'schedule_edit':
        (new ScheduleController())->edit();
        break;
    case 'schedule_update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new ScheduleController())->update();
        } else {
            header('Location: ?action=schedules');
        }
        break;
    case 'schedule_delete':
        (new ScheduleController())->delete();
        break;

    // AJAX APIs for Smart Scheduling
    case 'api_suggest_teachers':
        (new ScheduleController())->apiSuggestTeachers();
        break;
    case 'api_check_conflict':
        (new ScheduleController())->apiCheckConflict();
        break;

    // Teacher Management
    case 'teachers':
        (new TeacherController())->index();
        break;
    case 'teacher_create':
        (new TeacherController())->create();
        break;
    case 'teacher_store':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new TeacherController())->store();
        } else {
            header('Location: ?action=teacher_create');
        }
        break;
    case 'teacher_edit':
        (new TeacherController())->edit();
        break;
    case 'teacher_update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new TeacherController())->update();
        } else {
            header('Location: ?action=teachers');
        }
        break;
    case 'teacher_delete':
        (new TeacherController())->delete();
        break;
    case 'teacher_view':
        (new TeacherController())->view();
        break;

    // Branch Management
    case 'branches':
        (new BranchController())->index();
        break;
    case 'branch_create':
        (new BranchController())->create();
        break;
    case 'branch_store':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new BranchController())->store();
        } else {
            header('Location: ?action=branch_create');
        }
        break;
    case 'branch_edit':
        (new BranchController())->edit();
        break;
    case 'branch_update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new BranchController())->update();
        } else {
            header('Location: ?action=branches');
        }
        break;
    case 'branch_delete':
        (new BranchController())->delete();
        break;
    case 'branch_view':
        (new BranchController())->view();
        break;

    // Teacher Portal
    case 'portal':
    case 'my_schedule':
        (new PortalController())->index();
        break;

    default:
        http_response_code(404);
        echo "<h2 style='text-align:center;margin-top:50px;'>404 - Trang không tồn tại</h2>";
        echo "<p style='text-align:center;'><a href='?action=dashboard'>Quay về trang chủ</a></p>";
        break;
}
