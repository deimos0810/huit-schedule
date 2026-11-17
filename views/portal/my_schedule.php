<?php
$pageTitle = "Cổng thông tin Giảng viên - Thời khóa biểu cá nhân";
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- Welcome Banner -->
<div class="card shadow-sm border-0 mb-4 bg-white border-start border-primary border-4">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-2">Cổng thông tin Giảng viên HUIT</span>
                <h4 class="fw-bold mb-1 text-dark">
                    <?= htmlspecialchars($teacher['full_name']) ?>
                </h4>
                <div class="text-muted small">
                    <span class="me-3"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($teacher['email']) ?></span>
                    <span class="me-3"><i class="bi bi-telephone me-1"></i><?= htmlspecialchars($teacher['phone']) ?></span>
                    <span><i class="bi bi-award me-1"></i>Chuyên môn: <strong><?= htmlspecialchars($teacher['expertise']) ?></strong></span>
                </div>
            </div>
            <div class="d-flex gap-2 no-print">
                <button onclick="window.print()" class="btn btn-outline-secondary btn-sm px-3">
                    <i class="bi bi-printer me-1"></i> In thời khóa biểu
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Campuses to Visit Highlight Cards (Nắm bắt cơ sở mình cần đến dạy) -->
<div class="mb-4">
    <h6 class="fw-bold text-dark text-uppercase small letter-spacing-1 mb-3">
        <i class="bi bi-geo-alt-fill text-danger me-2"></i>Các Cơ sở Giảng viên cần đến dạy trong tuần
    </h6>
    <div class="row g-3">
        <?php if (empty($campusesToVisit)): ?>
            <div class="col-12">
                <div class="alert alert-light border text-muted">
                    Hiện Thầy/Cô chưa có lịch phân công giảng dạy trong học kỳ này.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($campusesToVisit as $bId => $c): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold text-primary mb-0"><?= htmlspecialchars($c['branch_name']) ?></h6>
                                <span class="badge bg-primary"><?= $c['classes_count'] ?> ca dạy</span>
                            </div>
                            <p class="text-muted small mb-3">
                                <i class="bi bi-geo-alt text-danger me-1"></i><?= htmlspecialchars($c['branch_address']) ?>
                            </p>
                            <div class="border-top pt-2">
                                <div class="text-secondary small fw-semibold mb-1">Các môn học tại cơ sở này:</div>
                                <ul class="list-unstyled mb-0 small text-muted">
                                    <?php foreach ($c['classes'] as $cls): ?>
                                        <li class="mb-1">
                                            • <strong><?= htmlspecialchars($cls['subject_name']) ?></strong> 
                                            (<?= htmlspecialchars($cls['day_of_week']) ?>, <?= htmlspecialchars($cls['shift_or_periods']) ?> - Phòng: <?= htmlspecialchars($cls['room']) ?>)
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Weekly Timetable Grid -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-bold text-dark">
            <i class="bi bi-calendar3 me-2 text-primary"></i>Lịch dạy cá nhân theo tuần - Học kỳ 1 (2026-2027)
        </span>
        <span class="badge bg-light text-dark border">
            Tổng số: <?= count($schedules) ?> ca / tuần
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
                                        <div class="schedule-card" style="border-left-width: 4px; border-left-color: #0f3d75;">
                                            <strong class="subj-title"><?= htmlspecialchars($sch['subject_name']) ?></strong>
                                            <div class="d-flex flex-wrap gap-1 align-items-center mt-1">
                                                <span class="badge-room"><i class="bi bi-door-closed me-1"></i><?= htmlspecialchars($sch['room']) ?></span>
                                                <span class="badge-branch"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($sch['branch_name']) ?></span>
                                            </div>
                                            <?php if (!empty($sch['notes'])): ?>
                                                <div class="text-muted mt-1" style="font-size: 0.7rem;">
                                                    <i class="bi bi-info-circle me-1"></i><?= htmlspecialchars($sch['notes']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="h-100 d-flex justify-content-center align-items-center text-muted" style="opacity: 0.5; min-height: 60px;">
                                        <span class="small" style="font-size: 0.75rem;">—</span>
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

<!-- Detailed Table View -->
<div class="card shadow-sm">
    <div class="card-header bg-white fw-bold">
        <i class="bi bi-table me-2 text-primary"></i>Danh sách chi tiết các buổi học
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;" class="text-center">#</th>
                    <th>Môn học / Học phần</th>
                    <th>Thời gian</th>
                    <th>Phòng học</th>
                    <th>Cơ sở giảng dạy</th>
                    <th>Địa chỉ cơ sở</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($schedules)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            Chưa có dữ liệu lịch dạy.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($schedules as $idx => $s): ?>
                        <tr>
                            <td class="text-center text-muted small"><?= $idx + 1 ?></td>
                            <td><strong class="text-primary"><?= htmlspecialchars($s['subject_name']) ?></strong></td>
                            <td>
                                <span class="badge bg-light text-dark border me-1 fw-bold"><?= htmlspecialchars($s['day_of_week']) ?></span>
                                <span class="badge bg-primary-subtle text-primary"><?= htmlspecialchars($s['shift_or_periods']) ?></span>
                            </td>
                            <td><strong><?= htmlspecialchars($s['room']) ?></strong></td>
                            <td><span class="badge bg-warning-subtle text-dark border"><?= htmlspecialchars($s['branch_name']) ?></span></td>
                            <td class="text-muted small"><i class="bi bi-geo-alt me-1 text-danger"></i><?= htmlspecialchars($s['branch_address']) ?></td>
                            <td class="text-muted small"><?= htmlspecialchars($s['notes'] ?? '—') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
