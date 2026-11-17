<?php
$pageTitle = "Quản lý Giảng viên - HUIT Schedule Hub";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">
            <i class="bi bi-people text-primary me-2"></i>Danh sách Giảng viên
        </h4>
        <p class="text-muted small mb-0">Quản lý đội ngũ giảng viên, thông tin chuyên môn chính và theo dõi tải giảng dạy</p>
    </div>
    <div>
        <a href="?action=teacher_create" class="btn btn-primary btn-sm px-3 shadow-sm">
            <i class="bi bi-person-plus me-1"></i> Thêm giảng viên
        </a>
    </div>
</div>

<!-- Search Bar -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="" class="row g-2">
            <input type="hidden" name="action" value="teachers">
            <div class="col-12 col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Tìm theo tên, email, chuyên môn chính..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm">Tìm kiếm</button>
                <?php if (!empty($_GET['search'])): ?>
                    <a href="?action=teachers" class="btn btn-outline-secondary btn-sm">Xóa tìm kiếm</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Teachers Table -->
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold text-secondary small text-uppercase">
            Tổng cộng: <strong class="text-dark"><?= count($teachers) ?></strong> giảng viên
        </span>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;" class="text-center">#</th>
                    <th>Họ và tên</th>
                    <th>Email liên hệ</th>
                    <th>Số điện thoại</th>
                    <th>Chuyên môn chính</th>
                    <th class="text-center">Số tiết dạy</th>
                    <th style="width: 160px;" class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($teachers)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-person-x fs-3 d-block mb-1"></i>
                            Không tìm thấy giảng viên nào.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($teachers as $idx => $t): ?>
                        <tr>
                            <td class="text-center text-muted small"><?= $idx + 1 ?></td>
                            <td>
                                <strong class="text-dark d-block"><?= htmlspecialchars($t['full_name']) ?></strong>
                            </td>
                            <td>
                                <a href="mailto:<?= htmlspecialchars($t['email']) ?>" class="text-decoration-none">
                                    <i class="bi bi-envelope me-1 text-muted"></i><?= htmlspecialchars($t['email']) ?>
                                </a>
                            </td>
                            <td>
                                <i class="bi bi-telephone me-1 text-muted"></i><?= htmlspecialchars($t['phone']) ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-primary border border-primary-subtle fw-normal">
                                    <?= htmlspecialchars($t['expertise']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary px-2 py-1">
                                    <?= $t['schedule_count'] ?? 0 ?> lịch
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="?action=teacher_view&id=<?= $t['id'] ?>" class="btn btn-outline-info btn-sm py-1 px-2" title="Xem thời khóa biểu">
                                    <i class="bi bi-calendar3"></i>
                                </a>
                                <a href="?action=teacher_edit&id=<?= $t['id'] ?>" class="btn btn-outline-primary btn-sm py-1 px-2" title="Chỉnh sửa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="?action=teacher_delete" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa giảng viên này? Lịch dạy liên quan cũng sẽ bị xóa!');">
                                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
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
