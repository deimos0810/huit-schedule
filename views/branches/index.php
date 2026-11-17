<?php
$pageTitle = "Quản lý Cơ sở trường - HUIT Schedule Hub";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">
            <i class="bi bi-buildings text-primary me-2"></i>Danh sách Cơ sở Trường học
        </h4>
        <p class="text-muted small mb-0">Quản lý các cơ sở đào tạo, trung tâm thực hành và phòng máy của nhà trường</p>
    </div>
    <div>
        <a href="?action=branch_create" class="btn btn-primary btn-sm px-3 shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Thêm cơ sở mới
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold text-secondary small text-uppercase">
            Tổng cộng: <strong class="text-dark"><?= count($branches) ?></strong> cơ sở
        </span>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;" class="text-center">#</th>
                    <th>Tên Cơ sở</th>
                    <th>Địa chỉ chi tiết</th>
                    <th class="text-center">Số lịch học</th>
                    <th style="width: 160px;" class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($branches)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="bi bi-buildings fs-3 d-block mb-1"></i>
                            Chưa có dữ liệu cơ sở trường học nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($branches as $idx => $b): ?>
                        <tr>
                            <td class="text-center text-muted small"><?= $idx + 1 ?></td>
                            <td>
                                <strong class="text-dark fs-6 d-block"><?= htmlspecialchars($b['branch_name']) ?></strong>
                            </td>
                            <td>
                                <i class="bi bi-geo-alt text-danger me-1"></i>
                                <?= htmlspecialchars($b['address']) ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                    <?= $b['schedule_count'] ?? 0 ?> tiết học
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="?action=branch_view&id=<?= $b['id'] ?>" class="btn btn-outline-info btn-sm py-1 px-2" title="Xem thời khóa biểu cơ sở">
                                    <i class="bi bi-calendar3"></i>
                                </a>
                                <a href="?action=branch_edit&id=<?= $b['id'] ?>" class="btn btn-outline-primary btn-sm py-1 px-2" title="Chỉnh sửa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="?action=branch_delete" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa cơ sở này? Toàn bộ lịch dạy tại cơ sở này sẽ bị xóa!');">
                                    <input type="hidden" name="id" value="<?= $b['id'] ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-2" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
