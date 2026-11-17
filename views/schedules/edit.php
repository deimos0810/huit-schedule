<?php
$pageTitle = "Chỉnh sửa lịch dạy - HUIT Schedule Hub";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="mb-4">
    <a href="?action=schedules" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách lịch dạy
    </a>
    <h4 class="fw-bold mt-2 text-dark">
        <i class="bi bi-pencil-square text-primary me-2"></i>Chỉnh sửa lịch giảng dạy
    </h4>
    <p class="text-muted small mb-0">Cập nhật thông tin phân lịch môn học #<?= $schedule['id'] ?></p>
</div>

<div class="row g-4 justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-calendar-event me-2 text-primary"></i>Thông tin chi tiết lịch học phần
            </div>
            <div class="card-body p-4">
                <form action="?action=schedule_update" method="POST" id="schedule_form">
                    <input type="hidden" name="id" id="schedule_id" value="<?= $schedule['id'] ?>">

                    <!-- Alert Box -->
                    <div id="travel_alert_box"></div>

                    <!-- Subject Name -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">
                            Tên môn học / Học phần <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               name="subject_name" 
                               id="schedule_subject_name" 
                               class="form-control" 
                               value="<?= htmlspecialchars($schedule['subject_name']) ?>" 
                               required>
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Branch -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Cơ sở giảng dạy <span class="text-danger">*</span>
                            </label>
                            <select name="branch_id" id="schedule_branch" class="form-select" required>
                                <?php foreach ($branches as $b): ?>
                                    <option value="<?= $b['id'] ?>" <?= $schedule['branch_id'] == $b['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($b['branch_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Room -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Phòng học <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="room" id="schedule_room" class="form-control" value="<?= htmlspecialchars($schedule['room']) ?>" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Day of week -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Thứ trong tuần <span class="text-danger">*</span>
                            </label>
                            <select name="day_of_week" id="schedule_day" class="form-select" required>
                                <?php foreach ($days as $d): ?>
                                    <option value="<?= $d ?>" <?= $schedule['day_of_week'] === $d ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($d) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Shift / Periods -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Buổi / Ca học <span class="text-danger">*</span>
                            </label>
                            <select name="shift_or_periods" id="schedule_shift" class="form-select" required>
                                <?php foreach ($shifts as $s): ?>
                                    <option value="<?= $s ?>" <?= $schedule['shift_or_periods'] === $s ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($s) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Teacher Select -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark">
                            Giảng viên phụ trách <span class="text-danger">*</span>
                        </label>
                        <select name="teacher_id" id="schedule_teacher" class="form-select" required>
                            <?php foreach ($teachers as $t): ?>
                                <option value="<?= $t['id'] ?>" <?= $schedule['teacher_id'] == $t['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['full_name']) ?> - [<?= htmlspecialchars($t['expertise']) ?>]
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Học kỳ</label>
                            <input type="text" name="semester" class="form-control" value="<?= htmlspecialchars($schedule['semester'] ?? 'HK1 (2026-2027)') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Ghi chú</label>
                            <input type="text" name="notes" class="form-control" value="<?= htmlspecialchars($schedule['notes'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <a href="?action=schedules" class="btn btn-outline-secondary">Hủy bỏ</a>
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
