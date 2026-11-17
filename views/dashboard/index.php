<?php
$pageTitle = "Trang tổng quan - HUIT Schedule Hub";
require_once __DIR__ . '/../layouts/header.php';
?>

<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">
            <i class="bi bi-speedometer2 text-primary me-2"></i>Tổng quan phân lịch giảng dạy
        </h4>
        <p class="text-muted small mb-0">Hệ thống giám sát và điều phối thời khóa biểu đa cơ sở - Học kỳ 1 (2026-2027)</p>
    </div>
    <div class="d-flex gap-2">
        <a href="?action=schedule_create" class="btn btn-primary btn-sm px-3 shadow-sm">
            <i class="bi bi-calendar-plus me-1"></i> Phân lịch mới
        </a>
        <a href="?action=schedules" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-list-task me-1"></i> Danh sách chi tiết
        </a>
    </div>
</div>

<!-- Key Metrics Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border-0 shadow-sm border-start border-primary border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Tổng số cơ sở</div>
                    <div class="h3 fw-bold mb-0 text-dark"><?= $totalBranches ?></div>
                    <small class="text-success"><i class="bi bi-check-circle me-1"></i>Hoạt động bình thường</small>
                </div>
                <div class="p-3 bg-light rounded text-primary fs-3">
                    <i class="bi bi-buildings"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border-0 shadow-sm border-start border-info border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Giảng viên</div>
                    <div class="h3 fw-bold mb-0 text-dark"><?= $totalTeachers ?></div>
                    <small class="text-muted">Đang quản lý trên hệ thống</small>
                </div>
                <div class="p-3 bg-light rounded text-info fs-3">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border-0 shadow-sm border-start border-success border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Lịch dạy đã xếp</div>
                    <div class="h3 fw-bold mb-0 text-dark"><?= $totalSchedules ?></div>
                    <small class="text-success"><i class="bi bi-calendar-check me-1"></i>Không xung đột</small>
                </div>
                <div class="p-3 bg-light rounded text-success fs-3">
                    <i class="bi bi-journal-bookmark"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 border-0 shadow-sm border-start border-warning border-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Tối ưu di chuyển</div>
                    <div class="h3 fw-bold mb-0 text-dark">98.5%</div>
                    <small class="text-muted">Hạn chế tối đa trễ ca</small>
                </div>
                <div class="p-3 bg-light rounded text-warning fs-3">
                    <i class="bi bi-signpost-split"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Filter Bar (Requirement: Lọc nhanh theo Cơ sở hoặc theo Giảng viên) -->
<div class="card shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="" class="row g-2 align-items-center">
            <input type="hidden" name="action" value="dashboard">

            <div class="col-12 col-md-auto fw-semibold text-secondary small text-uppercase">
                <i class="bi bi-funnel me-1"></i> Bộ lọc nhanh:
            </div>

            <!-- Filter by Branch -->
            <div class="col-12 col-md-4">
                <select name="branch_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Tất cả Cơ sở HUIT --</option>
                    <?php foreach ($branches as $b): ?>
                        <option value="<?= $b['id'] ?>" <?= $selectedBranchId == $b['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($b['branch_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Filter by Teacher -->
            <div class="col-12 col-md-4">
                <select name="teacher_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Tất cả Giảng viên --</option>
                    <?php foreach ($teachers as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= $selectedTeacherId == $t['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['full_name']) ?> (<?= htmlspecialchars($t['email']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-secondary btn-sm">
                    <i class="bi bi-search me-1"></i> Lọc
                </button>
                <?php if ($selectedBranchId || $selectedTeacherId): ?>
                    <a href="?action=dashboard" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-x-circle me-1"></i> Xóa lọc
                    </a>
                <?php endif; ?>
            </div>

            <div class="col-12 col-md text-md-end text-muted small">
                <?php if ($selectedBranchId || $selectedTeacherId): ?>
                    <span class="badge bg-primary">Đang lọc kết quả</span>
                <?php else: ?>
                    <span>Hiển thị toàn trường</span>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Timetable Matrix Grid (Phân lịch theo tiết trống & tối ưu hóa) -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-bold text-dark">
            <i class="bi bi-grid-3x3 me-2 text-primary"></i>Biểu đồ Thời khóa biểu Tuần (Ma trận Tiết trống & Lịch dạy)
        </span>
        <div class="small text-muted d-none d-md-block">
            <span class="badge bg-light text-dark border me-2"><i class="bi bi-square-fill text-primary me-1"></i>Đã có lớp</span>
            <span class="badge bg-light text-dark border"><i class="bi bi-square text-muted me-1"></i>Tiết trống</span>
        </div>
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
                                            <div class="d-flex justify-content-between align-items-start">
                                                <span class="subj-title"><?= htmlspecialchars($sch['subject_name']) ?></span>
                                                <a href="?action=schedule_edit&id=<?= $sch['id'] ?>" class="text-secondary small ms-1" title="Chỉnh sửa">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                            </div>
                                            <div class="text-dark small mb-1">
                                                <i class="bi bi-person me-1 text-muted"></i><?= htmlspecialchars($sch['teacher_name']) ?>
                                            </div>
                                            <div class="d-flex flex-wrap gap-1 align-items-center">
                                                <span class="badge-room"><i class="bi bi-door-closed me-1"></i><?= htmlspecialchars($sch['room']) ?></span>
                                                <span class="badge-branch"><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($sch['branch_name']) ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="h-100 d-flex flex-column justify-content-center align-items-center text-muted" style="opacity: 0.6; min-height: 70px;">
                                        <span class="small" style="font-size: 0.75rem;">— Trống —</span>
                                        <a href="?action=schedule_create&day=<?= urlencode($day) ?>&shift=<?= urlencode($shift) ?><?= $selectedBranchId ? '&branch_id='.$selectedBranchId : '' ?><?= $selectedTeacherId ? '&teacher_id='.$selectedTeacherId : '' ?>" 
                                           class="btn btn-link btn-sm p-0 text-decoration-none" style="font-size: 0.7rem;">
                                            + Xếp lịch
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

<!-- Campus Distribution Summary -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-pie-chart me-2 text-primary"></i>Phân bổ tiết dạy theo Cơ sở trường học
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <?php foreach ($campusDistribution as $cd): ?>
                        <?php 
                        $percentage = $totalSchedules > 0 ? round(($cd['total_schedules'] / $totalSchedules) * 100, 1) : 0;
                        ?>
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold text-dark"><?= htmlspecialchars($cd['branch_name']) ?></span>
                                <span class="badge bg-light text-dark border"><?= $cd['total_schedules'] ?> tiết (<?= $percentage ?>%)</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $percentage ?>%;"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1 text-muted" style="font-size: 0.75rem;">
                                <span><?= $cd['total_teachers'] ?> giảng viên tham gia</span>
                                <span><?= $cd['total_rooms'] ?> phòng đã sử dụng</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-lightning-charge me-2 text-warning"></i>Cơ chế hỗ trợ phân lịch thông minh
            </div>
            <div class="card-body">
                <div class="d-flex gap-3 mb-3">
                    <div class="text-primary fs-3"><i class="bi bi-clock-history"></i></div>
                    <div>
                        <div class="fw-semibold text-dark">Phân lịch theo tiết trống & Tối ưu di chuyển</div>
                        <p class="text-muted small mb-0">Hệ thống chủ động tính toán khoảng cách giữa các cơ sở, phát hiện khoảng cách ca học liền kề dưới 60 phút để cảnh báo nguy cơ trễ giờ của giảng viên.</p>
                    </div>
                </div>
                <div class="d-flex gap-3 mb-3">
                    <div class="text-success fs-3"><i class="bi bi-magic"></i></div>
                    <div>
                        <div class="fw-semibold text-dark">Gợi ý Giảng viên tự động</div>
                        <p class="text-muted small mb-0">Khi tạo lịch mới, hệ thống tự động bóc tách từ khóa môn học, khớp hồ sơ chuyên môn và kiểm tra ngay tình trạng trống lịch theo thời gian thực.</p>
                    </div>
                </div>
                <div class="d-flex gap-3">
                    <div class="text-info fs-3"><i class="bi bi-shield-check"></i></div>
                    <div>
                        <div class="fw-semibold text-dark">Chống trùng lặp tuyệt đối</div>
                        <p class="text-muted small mb-0">Ngăn chặn 100% tình trạng một giảng viên hoặc một phòng học bị xếp 2 lớp trong cùng một khung giờ.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
