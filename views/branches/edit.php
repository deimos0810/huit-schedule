<?php
$pageTitle = "Chỉnh sửa Cơ sở - HUIT Schedule Hub";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="mb-4">
    <a href="?action=branches" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách cơ sở
    </a>
    <h4 class="fw-bold mt-2 text-dark">
        <i class="bi bi-building-gear text-primary me-2"></i>Chỉnh sửa Cơ sở
    </h4>
    <p class="text-muted small mb-0">Cập nhật thông tin cơ sở: <?= htmlspecialchars($branch['branch_name']) ?></p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-buildings me-2 text-primary"></i>Thông tin Cơ sở #<?= $branch['id'] ?>
            </div>
            <div class="card-body p-4">
                <form action="?action=branch_update" method="POST">
                    <input type="hidden" name="id" value="<?= $branch['id'] ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">Tên Cơ sở <span class="text-danger">*</span></label>
                        <input type="text" name="branch_name" class="form-control" value="<?= htmlspecialchars($branch['branch_name']) ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">Địa chỉ chi tiết <span class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($branch['address']) ?>" required>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <a href="?action=branches" class="btn btn-outline-secondary">Hủy bỏ</a>
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
