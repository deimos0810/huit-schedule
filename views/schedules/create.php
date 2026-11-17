<?php
$pageTitle = "Phân lịch giảng dạy mới - HUIT Schedule Hub";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="mb-4">
    <a href="?action=schedules" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách lịch dạy
    </a>
    <h4 class="fw-bold mt-2 text-dark">
        <i class="bi bi-calendar-plus text-primary me-2"></i>Thêm mới lịch giảng dạy
    </h4>
    <p class="text-muted small mb-0">Hệ thống sẽ tự động kiểm tra tiết trống, gợi ý giảng viên đúng chuyên môn và cảnh báo di chuyển</p>
</div>

<div class="row g-4">
    <!-- Left Column: Schedule Form -->
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">
                <i class="bi bi-pencil-square me-2 text-primary"></i>Thông tin lịch học phần
            </div>
            <div class="card-body p-4">
                <form action="?action=schedule_store" method="POST" id="schedule_form">
                    
                    <!-- Alert / Live Conflict Container -->
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
                               placeholder="vd: Lập trình Web, Trí tuệ nhân tạo, Mạng máy tính..." 
                               value="<?= htmlspecialchars($_GET['subject'] ?? '') ?>"
                               required 
                               autocomplete="off">
                        <div class="form-text small">
                            <i class="bi bi-magic me-1 text-primary"></i>Hệ thống sẽ dựa vào tên môn để gợi ý giảng viên có chuyên môn tương ứng.
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <!-- Branch -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">
                                Cơ sở giảng dạy <span class="text-danger">*</span>
                            </label>
                            <select name="branch_id" id="schedule_branch" class="form-select" required>
                                <?php foreach ($branches as $b): ?>
                                    <option value="<?= $b['id'] ?>" <?= $preselectedBranchId == $b['id'] ? 'selected' : '' ?>>
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
                            <input type="text" name="room" id="schedule_room" class="form-control" placeholder="vd: A1-201, Lab 4.1..." required>
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
                                    <option value="<?= $d ?>" <?= $preselectedDay === $d ? 'selected' : '' ?>>
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
                                    <option value="<?= $s ?>" <?= $preselectedShift === $s ? 'selected' : '' ?>>
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
                            <option value="">-- Chọn hoặc bấm vào danh sách gợi ý bên phải --</option>
                            <?php foreach ($teachers as $t): ?>
                                <option value="<?= $t['id'] ?>" <?= $preselectedTeacherId == $t['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['full_name']) ?> - [<?= htmlspecialchars($t['expertise']) ?>]
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text small">
                            Có thể chọn trực tiếp tại đây hoặc click vào các thẻ giảng viên được gợi ý bên cạnh.
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Học kỳ</label>
                            <input type="text" name="semester" class="form-control" value="HK1 (2026-2027)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Ghi chú</label>
                            <input type="text" name="notes" class="form-control" placeholder="vd: Lớp chính quy, phòng máy thực hành...">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <a href="?action=schedules" class="btn btn-outline-secondary">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold" id="btn_submit_schedule">
                            <i class="bi bi-check2-circle me-1"></i> Lưu & Phân lịch
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Smart Teacher Suggestions & Optimization Panel -->
    <div class="col-lg-5">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark">
                    <i class="bi bi-stars text-warning me-2"></i>Gợi ý Giảng viên thông minh
                </span>
                <span class="badge bg-light text-muted border">Tự động phân tích</span>
            </div>
            <div class="card-body p-3">
                <div class="p-2 mb-3 bg-light rounded border small text-muted">
                    <div class="fw-semibold text-dark mb-1"><i class="bi bi-lightbulb me-1 text-warning"></i>Tiêu chuẩn gợi ý:</div>
                    <div>1. Giảng viên có <strong>chuyên môn phù hợp</strong> với tên môn học.</div>
                    <div>2. Giảng viên <strong>đang trống lịch</strong> trong Thứ & Ca học đã chọn.</div>
                    <div>3. Ưu tiên giảng viên <strong>đã có lịch cùng ngày tại cơ sở này</strong> để giảm thiểu di chuyển.</div>
                </div>

                <div class="small fw-semibold text-secondary text-uppercase mb-2">
                    Danh sách giảng viên phù hợp (click để chọn):
                </div>

                <!-- Live Container -->
                <div id="suggestions_list_container" style="max-height: 480px; overflow-y: auto;">
                    <div class="text-center py-4 text-muted small">
                        <i class="bi bi-keyboard fs-3 d-block mb-1"></i>
                        Nhập tên môn học để hệ thống bắt đầu gợi ý giảng viên tối ưu nhất.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
