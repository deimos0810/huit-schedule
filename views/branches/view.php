<?php
$pageTitle = "Thời khóa biểu cơ sở: " . htmlspecialchars($branch['branch_name']) . " - HUIT Schedule Hub";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="mb-4">
    <a href="?action=branches" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách cơ sở
    </a>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-2">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-building text-primary me-2"></i><?= htmlspecialchars($branch['branch_name']) ?>
            </h4>
            <div class="text-muted small">
                <i class="bi bi-geo-alt text-danger me-1"></i><?= htmlspecialchars($branch['address']) ?>
            </div>
        </div>
        <div>
            <a href="?action=schedule_create&branch_id=<?= $branch['id'] ?>" class="btn btn-primary btn-sm px-3 shadow-sm">
                <i class="bi bi-calendar-plus me-1"></i> Xếp lịch tại cơ sở này
            </a>
        </div>
    </div>
</div>

<!-- Branch Timetable Matrix -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-bold text-dark">
            <i class="bi bi-calendar-week me-2 text-primary"></i>Lưới thời khóa biểu toàn cơ sở trong tuần
        </span>
        <span class="badge bg-light text-dark border">
            <?= count($schedules) ?> lớp học đang diễn ra
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
                                            <div class="small text-dark mb-1">
                                                <i class="bi bi-person me-1 text-muted"></i><?= htmlspecialchars($sch['teacher_name']) ?>
                                            </div>
                                            <span class="badge-room"><i class="bi bi-door-closed me-1"></i><?= htmlspecialchars($sch['room']) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="h-100 d-flex flex-column justify-content-center align-items-center text-muted" style="opacity: 0.7; min-height: 70px;">
                                        <span class="small" style="font-size: 0.75rem;">— Trống phòng —</span>
                                        <a href="?action=schedule_create&branch_id=<?= $branch['id'] ?>&day=<?= urlencode($day) ?>&shift=<?= urlencode($shift) ?>" 
                                           class="btn btn-link btn-sm p-0 text-decoration-none mt-1" style="font-size: 0.7rem;">
                                            + Xếp lớp
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

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
