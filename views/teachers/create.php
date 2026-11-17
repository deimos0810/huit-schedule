<?php
$pageTitle = "Thêm Giảng viên mới - HUIT Schedule Hub";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="mb-4">
    <a href="?action=teachers" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách giảng viên
    </a>
    <h4 class="fw-bold mt-2 text-dark">
        <i class="bi bi-person-plus text-primary me-2"></i>Thêm Giảng viên mới
    </h4>
    <p class="text-muted small mb-0">Nhập đầy đủ thông tin giảng viên và chuyên môn chính để phục vụ thuật toán phân lịch</p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-person-badge me-2 text-primary"></i>Hồ sơ Giảng viên
            </div>
            <div class="card-body p-4">
                <form action="?action=teacher_store" method="POST">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Họ và tên Giảng viên <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control" placeholder="vd: ThS. Nguyễn Văn A" required autofocus>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Email liên hệ <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="vd: an.nv@huit.edu.vn" required>
                            <div class="form-text small">Dùng để giảng viên đăng nhập Cổng thông tin.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" placeholder="vd: 0912345678" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Chuyên môn chính (Expertise) <span class="text-danger">*</span></label>
                        <input type="text" name="expertise" class="form-control" placeholder="vd: Lập trình Web, PHP, Laravel, React" required>
                        <div class="form-text small">
                            Nhập các từ khóa phân cách bằng dấu phẩy. Hệ thống sẽ căn cứ vào đây để gợi ý giảng dạy môn học phù hợp.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">Mật khẩu đăng nhập</label>
                        <input type="password" name="password" class="form-control" placeholder="Mặc định: 123456 nếu để trống" value="123456">
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <a href="?action=teachers" class="btn btn-outline-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">
                            <i class="bi bi-check2 me-1"></i> Lưu thông tin
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
