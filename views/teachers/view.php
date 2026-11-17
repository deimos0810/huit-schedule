<?php
$pageTitle = "Lịch giảng dạy: " . htmlspecialchars($teacher['full_name']) . " - HUIT Schedule Hub";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="mb-4">
    <a href="?action=teachers" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách giảng viên
    </a>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-2">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-person-video3 text-primary me-2"></i><?= htmlspecialchars($teacher['full_name']) ?>
            </h4>
            <div class="text-muted small">
                <span class="me-3"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($teacher['email']) ?></span>
                <span class="me-3"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($teacher['phone']) ?></span>
                <span><i class="bi bi-award me-1"></i>Chuyên môn: <strong><?= htmlspecialchars($teacher['expertise']) ?></strong></span>
            </div>
        </div>
        <div>
            <a href="?action=schedule_create&teacher_id=<?= $teacher['id'] ?>" class="btn btn-primary btn-sm px-3 shadow-sm">
                <i class="bi bi-calendar-plus me-1"></i> Phân lịch thêm cho GV này
            </a>
        </div>
    </div>
</div>

<!-- Teacher Weekly Matrix (Ma trận tiết trống & tiết dạy) -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-bold text-dark">
            <i class="bi bi-calendar-week me-2 text-primary"></i>Biểu đồ tiết trống & lịch giảng dạy trong tuần
        </span>
        <span class="badge bg-light text-dark border">
            Tổng cộng: <?= count($schedules) ?> ca dạy / tuần
        </span>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table-matrix mb-0">
            <thead>
                <tr>
                    <th style="width: 140px;" class="bg-light text-secondary">Khung giờ / Ca</th>
                    <?php foreach ($days as $day): ?>
                        <th class="text-center"><?= htmlspecialchars($day) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($shifts as $shift): ?>
                    <tr>
                        <td class="shift-header">
                            <div class="fw-bold text-primary small"><?= htmlspecialchars(explode('(', $shift)[0]) ?></div>
                            <div class="text-muted" style="font-size: 0.72rem;"><?= htmlspecialchars('(' . (explode('(', $shift)[1] ?? '')) ?></div>
                        </td>
                        <?php foreach ($days as $day): ?>
                            <?php $cellSchedules = $matrix[$shift][$day] ?? []; ?>
                            <td>
                                <?php if (!empty($cellSchedules)): ?>
                                    <?php foreach ($cellSchedules as $sch): ?>
                                        <div class="schedule-card">
                                            <span class="subj-title"><?= htmlspecialchars($sch['subject_name']) ?></span>
                                            <div class="d-flex flex-wrap gap-1 align-items-center mt-1">
                                                <span class="badge-room"><i class="bi bi-door-closed me-1"></i><?= htmlspecialchars($sch['room']) ?></span>
                                                <span class="badge-branch"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($sch['branch_name']) ?></span>
                                            </div>
                                            <div class="text-end mt-1">
                                                <a href="?action=schedule_edit&id=<?= $sch['id'] ?>" class="text-secondary small" title="Sửa lịch">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="h-100 d-flex flex-column justify-content-center align-items-center text-muted" style="opacity: 0.7; min-height: 70px;">
                                        <span class="text-success small fw-semibold" style="font-size: 0.75rem;">
                                            <i class="bi bi-check-circle me-1"></i>Trống
                                        </span>
                                        <a href="?action=schedule_create&teacher_id=<?= $teacher['id'] ?>&day=<?= urlencode($day) ?>&shift=<?= urlencode($shift) ?>" 
                                           class="btn btn-link btn-sm p-0 text-decoration-none mt-1" style="font-size: 0.7rem;">
                                            + Xếp ca này
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Detailed List of Schedules for this Teacher -->
<div class="card shadow-sm">
    <div class="card-header bg-white fw-bold">
        <i class="bi bi-list-check me-2 text-primary"></i>Danh sách chi tiết các lớp học phần đảm nhiệm
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Môn học</th>
                    <th>Thời gian</th>
                    <th>Cơ sở</th>
                    <th>Phòng học</th>
                    <th>Ghi chú</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($schedules)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-3 text-muted">Giảng viên chưa được phân công lịch dạy nào.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($schedules as $s): ?>
                        <tr>
                            <td><strong class="text-primary"><?= htmlspecialchars($s['subject_name']) ?></strong></td>
                            <td>
                                <span class="badge bg-light text-dark border me-1"><?= htmlspecialchars($s['day_of_week']) ?></span>
                                <span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($s['shift_or_periods']) ?></span>
                            </td>
                            <td><i class="bi bi-geo-alt text-muted me-1"></i><?= htmlspecialchars($s['branch_name']) ?></td>
                            <td><i class="bi bi-door-closed text-muted me-1"></i><strong><?= htmlspecialchars($s['room']) ?></strong></td>
                            <td class="text-muted small"><?= htmlspecialchars($s['notes'] ?? '—') ?></td>
                            <td class="text-end">
                                <a href="?action=schedule_edit&id=<?= $s['id'] ?>" class="btn btn-outline-primary btn-sm py-1 px-2">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
