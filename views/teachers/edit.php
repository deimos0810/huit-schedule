<?php
$pageTitle = "Chỉnh sửa Giảng viên - HUIT Schedule Hub";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="mb-4">
    <a href="?action=teachers" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách giảng viên
    </a>
    <h4 class="fw-bold mt-2 text-dark">
        <i class="bi bi-person-gear text-primary me-2"></i>Chỉnh sửa thông tin Giảng viên
    </h4>
    <p class="text-muted small mb-0">Cập nhật hồ sơ giảng viên: <?= htmlspecialchars($teacher['full_name']) ?></p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-person-badge me-2 text-primary"></i>Hồ sơ Giảng viên #<?= $teacher['id'] ?>
            </div>
            <div class="card-body p-4">
                <form action="?action=teacher_update" method="POST">
                    <input type="hidden" name="id" value="<?= $teacher['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Họ và tên Giảng viên <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($teacher['full_name']) ?>" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Email liên hệ <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($teacher['email']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($teacher['phone']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Chuyên môn chính (Expertise) <span class="text-danger">*</span></label>
                        <input type="text" name="expertise" class="form-control" value="<?= htmlspecialchars($teacher['expertise']) ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">Đổi mật khẩu mới (nếu cần)</label>
                        <input type="password" name="password" class="form-control" placeholder="Để trống nếu giữ nguyên mật khẩu cũ">
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <a href="?action=teachers" class="btn btn-outline-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">
                            <i class="bi bi-save me-1"></i> Lưu thay đổi
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
