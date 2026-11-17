/**
 * HUIT Schedule Hub - Smart Scheduling Client Logic
 */

document.addEventListener('DOMContentLoaded', function () {
    initScheduleSuggestionEngine();
    initConflictLiveChecker();
});

function initScheduleSuggestionEngine() {
    const subjectInput = document.getElementById('schedule_subject_name');
    const daySelect = document.getElementById('schedule_day');
    const shiftSelect = document.getElementById('schedule_shift');
    const branchSelect = document.getElementById('schedule_branch');
    const teacherSelect = document.getElementById('schedule_teacher');
    const suggestionsContainer = document.getElementById('suggestions_list_container');
    const travelAlertBox = document.getElementById('travel_alert_box');

    if (!subjectInput || !suggestionsContainer) {
        return; // Not on the schedule form page
    }

    let debounceTimer = null;

    function fetchSuggestions() {
        const subject = subjectInput.value.trim();
        const day = daySelect ? daySelect.value : '';
        const shift = shiftSelect ? shiftSelect.value : '';
        const branchId = branchSelect ? branchSelect.value : '';

        suggestionsContainer.innerHTML = `
            <div class="text-center py-3 text-muted">
                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                Đang phân tích chuyên môn và kiểm tra tiết trống...
            </div>
        `;

        const url = `?action=api_suggest_teachers&subject=${encodeURIComponent(subject)}&day=${encodeURIComponent(day)}&shift=${encodeURIComponent(shift)}&branch_id=${encodeURIComponent(branchId)}`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (!data.success || !data.data || data.data.length === 0) {
                    suggestionsContainer.innerHTML = `
                        <div class="alert alert-light border text-muted small mb-0">
                            Chưa tìm thấy gợi ý phù hợp. Vui lòng chọn trực tiếp từ danh sách giảng viên.
                        </div>
                    `;
                    return;
                }

                renderSuggestions(data.data);
            })
            .catch(err => {
                console.error(err);
                suggestionsContainer.innerHTML = `
                    <div class="alert alert-light border text-muted small mb-0">
                        Không thể tải gợi ý tự động. Bạn vẫn có thể chọn giảng viên ở menu bên cạnh.
                    </div>
                `;
            });
    }

    function renderSuggestions(teachers) {
        let html = '';

        teachers.forEach(t => {
            const isSelected = teacherSelect && teacherSelect.value == t.teacher_id;
            const disabledClass = !t.is_free ? 'disabled-conflict' : '';
            const selectedClass = isSelected ? 'selected' : '';

            let badgeHtml = '';
            if (!t.is_free) {
                badgeHtml = `<span class="badge bg-danger">Trùng lịch</span>`;
            } else if (t.badge === 'success') {
                badgeHtml = `<span class="badge bg-success">Rất lý tưởng (Cùng cơ sở)</span>`;
            } else if (t.badge === 'primary') {
                badgeHtml = `<span class="badge bg-primary">Phù hợp chuyên môn</span>`;
            } else if (t.badge === 'warning') {
                badgeHtml = `<span class="badge bg-warning text-dark">Cân nhắc di chuyển</span>`;
            } else {
                badgeHtml = `<span class="badge bg-secondary">Khác chuyên môn</span>`;
            }

            html += `
                <div class="suggestion-item ${disabledClass} ${selectedClass}" data-teacher-id="${t.teacher_id}">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong class="text-dark">${escapeHtml(t.full_name)}</strong>
                        ${badgeHtml}
                    </div>
                    <div class="small text-muted mb-1">
                        <i class="bi bi-briefcase me-1"></i>${escapeHtml(t.expertise)}
                    </div>
                    <div class="small ${!t.is_free ? 'text-danger' : 'text-secondary'}">
                        <i class="bi ${!t.is_free ? 'bi-x-circle' : 'bi-info-circle'} me-1"></i>
                        ${escapeHtml(t.recommendation_text)}
                    </div>
                </div>
            `;
        });

        suggestionsContainer.innerHTML = html;

        // Bind click events on suggestion items
        suggestionsContainer.querySelectorAll('.suggestion-item').forEach(item => {
            item.addEventListener('click', function () {
                if (this.classList.contains('disabled-conflict')) {
                    alert('Giảng viên này đã có lịch dạy trong khung giờ đã chọn! Vui lòng chọn giảng viên đang rảnh.');
                    return;
                }

                const teacherId = this.getAttribute('data-teacher-id');
                if (teacherSelect) {
                    teacherSelect.value = teacherId;
                    // Trigger conflict and travel check
                    checkLiveConflicts();
                }

                suggestionsContainer.querySelectorAll('.suggestion-item').forEach(el => el.classList.remove('selected'));
                this.classList.add('selected');
            });
        });
    }

    // Event listeners
    if (subjectInput) {
        subjectInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchSuggestions, 300);
        });
    }

    [daySelect, shiftSelect, branchSelect].forEach(el => {
        if (el) {
            el.addEventListener('change', function () {
                fetchSuggestions();
                checkLiveConflicts();
            });
        }
    });

    if (teacherSelect) {
        teacherSelect.addEventListener('change', function () {
            checkLiveConflicts();
            // Highlight selected in list if present
            if (suggestionsContainer) {
                suggestionsContainer.querySelectorAll('.suggestion-item').forEach(el => {
                    if (el.getAttribute('data-teacher-id') === teacherSelect.value) {
                        el.classList.add('selected');
                    } else {
                        el.classList.remove('selected');
                    }
                });
            }
        });
    }

    // Initial fetch on page load
    fetchSuggestions();
}

function initConflictLiveChecker() {
    const roomInput = document.getElementById('schedule_room');
    if (roomInput) {
        roomInput.addEventListener('input', checkLiveConflicts);
    }
}

function checkLiveConflicts() {
    const teacherSelect = document.getElementById('schedule_teacher');
    const branchSelect = document.getElementById('schedule_branch');
    const daySelect = document.getElementById('schedule_day');
    const shiftSelect = document.getElementById('schedule_shift');
    const roomInput = document.getElementById('schedule_room');
    const alertBox = document.getElementById('travel_alert_box');
    const scheduleIdInput = document.getElementById('schedule_id');

    if (!alertBox || !teacherSelect) return;

    const teacherId = teacherSelect.value;
    const branchId = branchSelect ? branchSelect.value : '';
    const day = daySelect ? daySelect.value : '';
    const shift = shiftSelect ? shiftSelect.value : '';
    const room = roomInput ? roomInput.value.trim() : '';
    const excludeId = scheduleIdInput ? scheduleIdInput.value : '';

    if (!teacherId && !room) {
        alertBox.innerHTML = '';
        return;
    }

    const url = `?action=api_check_conflict&teacher_id=${encodeURIComponent(teacherId)}&branch_id=${encodeURIComponent(branchId)}&day=${encodeURIComponent(day)}&shift=${encodeURIComponent(shift)}&room=${encodeURIComponent(room)}&exclude_id=${encodeURIComponent(excludeId)}`;

    fetch(url)
        .then(res => res.json())
        .then(data => {
            let messages = [];

            if (data.teacher_conflict && data.teacher_conflict.has_conflict) {
                messages.push(`
                    <div class="alert alert-danger py-2 mb-2 small d-flex align-items-center">
                        <i class="bi bi-x-octagon-fill fs-5 me-2"></i>
                        <div><strong>Lỗi trùng lịch:</strong> ${escapeHtml(data.teacher_conflict.message)}</div>
                    </div>
                `);
            }

            if (data.room_conflict && data.room_conflict.has_conflict) {
                messages.push(`
                    <div class="alert alert-danger py-2 mb-2 small d-flex align-items-center">
                        <i class="bi bi-x-octagon-fill fs-5 me-2"></i>
                        <div><strong>Lỗi trùng phòng:</strong> ${escapeHtml(data.room_conflict.message)}</div>
                    </div>
                `);
            }

            if (data.travel_analysis) {
                const ta = data.travel_analysis;
                if (ta.status === 'warning') {
                    messages.push(`
                        <div class="alert alert-warning py-2 mb-2 small d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                            <div>${escapeHtml(ta.message)}</div>
                        </div>
                    `);
                } else if (ta.status === 'info') {
                    messages.push(`
                        <div class="alert alert-info py-2 mb-2 small d-flex align-items-center">
                            <i class="bi bi-info-circle-fill fs-5 me-2"></i>
                            <div>${escapeHtml(ta.message)}</div>
                        </div>
                    `);
                } else if (ta.type === 'same_campus_optimal') {
                    messages.push(`
                        <div class="alert alert-success py-2 mb-2 small d-flex align-items-center">
                            <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                            <div>${escapeHtml(ta.message)}</div>
                        </div>
                    `);
                }
            }

            alertBox.innerHTML = messages.join('');
        })
        .catch(err => console.error(err));
}

function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.toString().replace(/[&<>"']/g, m => map[m]);
}
