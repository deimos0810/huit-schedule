<?php
$pageTitle = "Đăng nhập - HUIT Schedule Hub";
$loginType = $_GET['type'] ?? 'admin';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center py-5" style="min-height: 100vh;">

<div class="container" style="max-width: 460px;">
    <!-- Logo & Brand Header -->
    <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center bg-primary-huit text-white rounded p-3 mb-2 shadow-sm">
            <i class="bi bi-calendar2-range fs-1"></i>
        </div>
        <h4 class="fw-bold mb-1" style="color: var(--huit-primary);">HUIT Schedule Hub</h4>
        <p class="text-muted small mb-0">Hệ thống Quản lý và Phân lịch Giảng dạy Đa cơ sở</p>
    </div>

    <!-- Login Card -->
    <div class="card shadow-sm border">
        <div class="card-header bg-white p-0 border-bottom">
            <ul class="nav nav-tabs nav-fill border-0" id="loginTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link py-3 fw-semibold <?= $loginType === 'admin' ? 'active text-primary' : 'text-muted' ?>" 
                       href="?action=login&type=admin">
                        <i class="bi bi-shield-lock me-1"></i> Quản trị viên
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-3 fw-semibold <?= $loginType === 'teacher' ? 'active text-primary' : 'text-muted' ?>" 
                       href="?action=login&type=teacher">
                        <i class="bi bi-person-workspace me-1"></i> Cổng Giảng viên
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <!-- Flash Errors -->
            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger py-2 px-3 small mb-3">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    <?= htmlspecialchars($_SESSION['flash_error']) ?>
                </div>
                <?php unset($_SESSION['flash_error']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['flash_success'])): ?>
                <div class="alert alert-success py-2 px-3 small mb-3">
                    <i class="bi bi-check-circle me-1"></i>
                    <?= htmlspecialchars($_SESSION['flash_success']) ?>
                </div>
                <?php unset($_SESSION['flash_success']); ?>
            <?php endif; ?>

            <form action="?action=login_submit" method="POST">
                <input type="hidden" name="type" value="<?= htmlspecialchars($loginType) ?>">

                <?php if ($loginType === 'admin'): ?>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Tên đăng nhập / Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-person"></i></span>
                            <input type="text" name="username" class="form-control" placeholder="admin hoặc admin@huit.edu.vn" value="admin" required autofocus>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Email Giảng viên</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="vd: gv@huit.edu.vn" value="yen.dang1@huit.edu.vn" required autofocus>
                        </div>
                        <div class="form-text small">Email công tác do trường cấp kết thúc bằng @huit.edu.vn</div>
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-key"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" value="<?= $loginType === 'admin' ? 'admin123' : '123456' ?>" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold" style="background-color: var(--huit-primary); border-color: var(--huit-primary);">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Đăng nhập hệ thống
                </button>
            </form>

            <!-- Test Credentials Hint -->
            <div class="mt-4 p-2 bg-light rounded border text-muted small">
                <div class="fw-semibold text-dark mb-1"><i class="bi bi-info-circle me-1"></i>Tài khoản mẫu dùng thử:</div>
                <?php if ($loginType === 'admin'): ?>
                    <div>• <strong>Tài khoản:</strong> <code>admin</code> | <strong>Mật khẩu:</strong> <code>admin123</code></div>
                <?php else: ?>
                    <div>• <strong>Email GV:</strong> <code>yen.dang1@huit.edu.vn</code> (hoặc xem danh sách GV)</div>
                    <div>• <strong>Mật khẩu:</strong> <code>123456</code></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="text-center text-muted small mt-3">
        &copy; <?= date('Y') ?> Trường Đại học Công Thương TP. Hồ Chí Minh
    </div>
</div>

</body>
</html>
