<?php
use App\Auth;
$currentUser = Auth::user();
$currentAction = $_GET['action'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'HUIT Schedule Hub - Quản lý phân lịch giảng dạy') ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Custom Minimalist Styles -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Top Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary-huit shadow-sm py-2">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center gap-2 fw-bold text-white" href="?action=<?= Auth::isTeacher() ? 'portal' : 'dashboard' ?>">
            <span class="brand-badge">HUIT</span>
            <span>Schedule Hub</span>
            <small class="badge bg-light text-primary ms-2 fw-normal d-none d-md-inline" style="font-size: 0.72rem;">Đa cơ sở thông minh</small>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if (Auth::isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= in_array($currentAction, ['dashboard', '']) ? 'active' : '' ?>" href="?action=dashboard">
                            <i class="bi bi-grid-1x2 me-1"></i> Trang tổng quan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentAction, 'schedule') === 0 ? 'active' : '' ?>" href="?action=schedules">
                            <i class="bi bi-calendar3 me-1"></i> Quản lý lịch dạy
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentAction, 'teacher') === 0 ? 'active' : '' ?>" href="?action=teachers">
                            <i class="bi bi-people me-1"></i> Giảng viên
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($currentAction, 'branch') === 0 ? 'active' : '' ?>" href="?action=branches">
                            <i class="bi bi-buildings me-1"></i> Cơ sở trường
                        </a>
                    </li>
                <?php elseif (Auth::isTeacher()): ?>
                    <li class="nav-item">
                        <a class="nav-link active" href="?action=portal">
                            <i class="bi bi-calendar-check me-1"></i> Thời khóa biểu cá nhân
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <?php if ($currentUser): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                            <span><?= htmlspecialchars($currentUser['name']) ?></span>
                            <span class="badge bg-light text-dark" style="font-size: 0.7rem;">
                                <?= Auth::isAdmin() ? 'Quản trị viên' : 'Giảng viên' ?>
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li class="dropdown-header text-muted">
                                <?= htmlspecialchars($currentUser['email']) ?>
                            </li>
                            <?php if (Auth::isAdmin()): ?>
                                <li><a class="dropdown-item" href="?action=schedules&action_type=new"><i class="bi bi-plus-circle me-2"></i>Phân lịch mới</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="?action=logout">
                                    <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="?action=login&type=admin" class="btn btn-light btn-sm fw-medium">Đăng nhập Admin</a>
                    <a href="?action=login&type=teacher" class="btn btn-outline-light btn-sm">Cổng Giảng viên</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Main Container -->
<main class="container-fluid px-4 py-4">

    <!-- Flash Messages -->
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= htmlspecialchars($_SESSION['flash_success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_warning'])): ?>
        <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= htmlspecialchars($_SESSION['flash_warning']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_warning']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-x-circle-fill me-2"></i>
            <?= htmlspecialchars($_SESSION['flash_error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>
