<?php
$pageTitle = "Quản lý lịch dạy - HUIT Schedule Hub";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">
            <i class="bi bi-calendar-week text-primary me-2"></i>Danh sách lịch giảng dạy
        </h4>
        <p class="text-muted small mb-0">Quản lý, tìm kiếm và điều phối lịch học các lớp học phần</p>
    </div>
    <div>
        <a href="?action=schedule_create" class="btn btn-primary btn-sm px-3 shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Phân lịch mới
        </a>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="" class="row g-2">
            <input type="hidden" name="action" value="schedules">

            <!-- Search box -->
            <div class="col-12 col-md-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Môn học, phòng, giảng viên..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </div>
            </div>

            <!-- Branch filter -->
            <div class="col-6 col-md-2">
                <select name="branch_id" class="form-select form-select-sm">
                    <option value="">-- Tất cả cơ sở --</option>
                    <?php foreach ($branches as $b): ?>
                        <option value="<?= $b['id'] ?>" <?= (!empty($_GET['branch_id']) && $_GET['branch_id'] == $b['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($b['branch_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Teacher filter -->
            <div class="col-6 col-md-3">
                <select name="teacher_id" class="form-select form-select-sm">
                    <option value="">-- Tất cả giảng viên --</option>
                    <?php foreach ($teachers as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= (!empty($_GET['teacher_id']) && $_GET['teacher_id'] == $t['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['full_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Day filter -->
            <div class="col-6 col-md-2">
                <select name="day_of_week" class="form-select form-select-sm">
                    <option value="">-- Tất cả thứ --</option>
                    <?php foreach ($days as $day): ?>
                        <option value="<?= $day ?>" <?= (!empty($_GET['day_of_week']) && $_GET['day_of_week'] == $day) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($day) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Buttons -->
            <div class="col-6 col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                    <i class="bi bi-filter"></i> Lọc
                </button>
                <a href="?action=schedules" class="btn btn-outline-secondary btn-sm" title="Đặt lại bộ lọc">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Schedules Table -->
<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold text-secondary small text-uppercase">
            Tổng cộng: <strong class="text-dark"><?= count($schedules) ?></strong> lịch dạy
        </span>
        <div class="small text-muted">Học kỳ 1 (2026-2027)</div>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;" class="text-center">#</th>
                    <th>Môn học / Học phần</th>
                    <th>Giảng viên</th>
                    <th>Thời gian (Thứ & Ca)</th>
                    <th>Phòng & Cơ sở</th>
                    <th style="width: 140px;" class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($schedules)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                            Không tìm thấy lịch giảng dạy nào phù hợp với điều kiện tìm kiếm.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($schedules as $idx => $s): ?>
                        <tr>
                            <td class="text-center text-muted small"><?= $idx + 1 ?></td>
                            <td>
                                <strong class="text-primary d-block"><?= htmlspecialchars($s['subject_name']) ?></strong>
                                <?php if (!empty($s['notes'])): ?>
                                    <small class="text-muted"><i class="bi bi-info-circle me-1"></i><?= htmlspecialchars($s['notes']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><?= htmlspecialchars($s['teacher_name']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($s['teacher_email']) ?></small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border me-1 fw-bold"><?= htmlspecialchars($s['day_of_week']) ?></span>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><?= htmlspecialchars($s['shift_or_periods']) ?></span>
                            </td>
                            <td>
                                <div><i class="bi bi-door-closed text-secondary me-1"></i><strong><?= htmlspecialchars($s['room']) ?></strong></div>
                                <small class="text-muted"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($s['branch_name']) ?></small>
                            </td>
                            <td class="text-end">
                                <a href="?action=schedule_edit&id=<?= $s['id'] ?>" class="btn btn-outline-primary btn-sm py-1 px-2" title="Chỉnh sửa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="?action=schedule_delete" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa lịch dạy này?');">
                                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
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
