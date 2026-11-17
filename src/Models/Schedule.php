<?php

namespace App\Models;

use App\Database;
use PDO;

class Schedule {
    public static array $daysOfWeek = [
        'Thứ 2',
        'Thứ 3',
        'Thứ 4',
        'Thứ 5',
        'Thứ 6',
        'Thứ 7'
    ];

    public static array $shifts = [
        'Sáng (Tiết 1 - 3)',
        'Sáng (Tiết 4 - 6)',
        'Chiều (Tiết 7 - 9)',
        'Chiều (Tiết 10 - 12)',
        'Tối (Tiết 13 - 15)'
    ];

    public static function all(array $filters = []): array {
        $pdo = Database::getConnection();
        $sql = "SELECT s.*, 
                       t.full_name AS teacher_name, t.email AS teacher_email, t.expertise,
                       b.branch_name, b.address AS branch_address
                FROM schedules s
                JOIN teachers t ON s.teacher_id = t.id
                JOIN branches b ON s.branch_id = b.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['branch_id'])) {
            $sql .= " AND s.branch_id = :branch_id";
            $params['branch_id'] = (int)$filters['branch_id'];
        }

        if (!empty($filters['teacher_id'])) {
            $sql .= " AND s.teacher_id = :teacher_id";
            $params['teacher_id'] = (int)$filters['teacher_id'];
        }

        if (!empty($filters['day_of_week'])) {
            $sql .= " AND s.day_of_week = :day_of_week";
            $params['day_of_week'] = $filters['day_of_week'];
        }

        if (!empty($filters['shift'])) {
            $sql .= " AND s.shift_or_periods = :shift";
            $params['shift'] = $filters['shift'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (s.subject_name LIKE :search OR s.room LIKE :search OR t.full_name LIKE :search)";
            $params['search'] = "%" . trim($filters['search']) . "%";
        }

        $sql .= " ORDER BY FIELD(s.day_of_week, 'Thứ 2','Thứ 3','Thứ 4','Thứ 5','Thứ 6','Thứ 7'), s.shift_or_periods ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT s.*, 
                                      t.full_name AS teacher_name, t.email AS teacher_email, t.expertise,
                                      b.branch_name, b.address AS branch_address
                               FROM schedules s
                               JOIN teachers t ON s.teacher_id = t.id
                               JOIN branches b ON s.branch_id = b.id
                               WHERE s.id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("INSERT INTO schedules (teacher_id, branch_id, subject_name, room, day_of_week, shift_or_periods, semester, notes) 
                               VALUES (:teacher_id, :branch_id, :subject_name, :room, :day_of_week, :shift_or_periods, :semester, :notes)");
        $stmt->execute([
            'teacher_id' => (int)$data['teacher_id'],
            'branch_id' => (int)$data['branch_id'],
            'subject_name' => trim($data['subject_name']),
            'room' => trim($data['room']),
            'day_of_week' => trim($data['day_of_week']),
            'shift_or_periods' => trim($data['shift_or_periods']),
            'semester' => !empty($data['semester']) ? trim($data['semester']) : 'HK1 (2026-2027)',
            'notes' => !empty($data['notes']) ? trim($data['notes']) : null
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE schedules SET 
                                teacher_id = :teacher_id,
                                branch_id = :branch_id,
                                subject_name = :subject_name,
                                room = :room,
                                day_of_week = :day_of_week,
                                shift_or_periods = :shift_or_periods,
                                semester = :semester,
                                notes = :notes
                               WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'teacher_id' => (int)$data['teacher_id'],
            'branch_id' => (int)$data['branch_id'],
            'subject_name' => trim($data['subject_name']),
            'room' => trim($data['room']),
            'day_of_week' => trim($data['day_of_week']),
            'shift_or_periods' => trim($data['shift_or_periods']),
            'semester' => !empty($data['semester']) ? trim($data['semester']) : 'HK1 (2026-2027)',
            'notes' => !empty($data['notes']) ? trim($data['notes']) : null
        ]);
    }

    public static function delete(int $id): bool {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM schedules WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Kiểm tra trùng lịch giảng viên (Cùng Thứ + Ca)
     */
    public static function checkTeacherConflict(int $teacherId, string $day, string $shift, ?int $excludeId = null): ?array {
        $pdo = Database::getConnection();
        $sql = "SELECT s.*, b.branch_name, t.full_name AS teacher_name 
                FROM schedules s 
                JOIN branches b ON s.branch_id = b.id
                JOIN teachers t ON s.teacher_id = t.id
                WHERE s.teacher_id = :teacher_id 
                  AND s.day_of_week = :day 
                  AND s.shift_or_periods = :shift";
        $params = [
            'teacher_id' => $teacherId,
            'day' => $day,
            'shift' => $shift
        ];
        if ($excludeId !== null) {
            $sql .= " AND s.id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        $sql .= " LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Kiểm tra trùng phòng học tại cùng Cơ sở (Cùng Cơ sở + Phòng + Thứ + Ca)
     */
    public static function checkRoomConflict(int $branchId, string $room, string $day, string $shift, ?int $excludeId = null): ?array {
        $pdo = Database::getConnection();
        $sql = "SELECT s.*, b.branch_name, t.full_name AS teacher_name 
                FROM schedules s 
                JOIN branches b ON s.branch_id = b.id
                JOIN teachers t ON s.teacher_id = t.id
                WHERE s.branch_id = :branch_id 
                  AND LOWER(s.room) = LOWER(:room)
                  AND s.day_of_week = :day 
                  AND s.shift_or_periods = :shift";
        $params = [
            'branch_id' => $branchId,
            'room' => trim($room),
            'day' => $day,
            'shift' => $shift
        ];
        if ($excludeId !== null) {
            $sql .= " AND s.id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }
        $sql .= " LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Kiểm tra & tối ưu hóa di chuyển giữa các cơ sở trong cùng ngày
     * Tránh tình trạng chồng chéo hoặc khoảng trống chết giữa các tiết học
     */
    public static function checkTravelOptimization(int $teacherId, int $targetBranchId, string $targetDay, string $targetShift, ?int $excludeId = null): array {
        $pdo = Database::getConnection();
        $sql = "SELECT s.*, b.branch_name 
                FROM schedules s 
                JOIN branches b ON s.branch_id = b.id 
                WHERE s.teacher_id = :teacher_id 
                  AND s.day_of_week = :day";
        $params = [
            'teacher_id' => $teacherId,
            'day' => $targetDay
        ];
        if ($excludeId !== null) {
            $sql .= " AND s.id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $sameDaySchedules = $stmt->fetchAll();

        if (empty($sameDaySchedules)) {
            return [
                'status' => 'success',
                'type' => 'optimal',
                'message' => "Giảng viên chưa có lịch vào {$targetDay}, hoàn toàn trống ca."
            ];
        }

        $diffCampus = [];
        $sameCampus = [];

        // Shift index to detect adjacent shifts
        $shiftIndexes = [
            'Sáng (Tiết 1 - 3)' => 1,
            'Sáng (Tiết 4 - 6)' => 2,
            'Chiều (Tiết 7 - 9)' => 3,
            'Chiều (Tiết 10 - 12)' => 4,
            'Tối (Tiết 13 - 15)' => 5
        ];

        $targetShiftIdx = $shiftIndexes[$targetShift] ?? 0;

        foreach ($sameDaySchedules as $sch) {
            if ($sch['branch_id'] != $targetBranchId) {
                $schShiftIdx = $shiftIndexes[$sch['shift_or_periods']] ?? 0;
                $gap = abs($targetShiftIdx - $schShiftIdx);

                $diffCampus[] = [
                    'schedule' => $sch,
                    'gap' => $gap
                ];
            } else {
                $sameCampus[] = $sch;
            }
        }

        if (!empty($diffCampus)) {
            // Check for adjacent shifts at different campus (high risk)
            foreach ($diffCampus as $item) {
                if ($item['gap'] === 1) {
                    return [
                        'status' => 'warning',
                        'type' => 'tight_travel',
                        'message' => "CẢNH BÁO DI CHUYỂN: Giảng viên đã có lịch tại '{$item['schedule']['branch_name']}' ({$item['schedule']['shift_or_periods']}) liền kề với ca này. Cần tối thiểu 45-60 phút di chuyển giữa 2 cơ sở, nguy cơ trễ giờ!",
                        'details' => $item['schedule']
                    ];
                }
            }

            // Different campus with gap >= 2 (dead time warning)
            $firstDiff = $diffCampus[0];
            return [
                'status' => 'info',
                'type' => 'diff_campus_gap',
                'message' => "LƯU Ý: Giảng viên cùng ngày {$targetDay} có lịch tại '{$firstDiff['schedule']['branch_name']}' ({$firstDiff['schedule']['shift_or_periods']}). Khác cơ sở nhưng có khoảng nghỉ giữa các ca.",
                'details' => $firstDiff['schedule']
            ];
        }

        if (!empty($sameCampus)) {
            return [
                'status' => 'success',
                'type' => 'same_campus_optimal',
                'message' => "TỐI ƯU TỐT: Giảng viên đã có lịch dạy tại cùng cơ sở này trong ngày. Rất thuận tiện để giảng dạy liên tục, không tốn thời gian di chuyển."
            ];
        }

        return [
            'status' => 'success',
            'type' => 'normal',
            'message' => "Lịch dạy hợp lệ."
        ];
    }

    /**
     * Thuật toán Gợi ý Giảng viên phù hợp
     * - Chuyên môn khớp môn học
     * - Trống lịch vào Thứ + Ca học
     * - Ưu tiên giảng viên đã dạy tại Cơ sở này trong ngày (tối ưu di chuyển)
     */
    public static function suggestTeachers(string $subjectName, string $day, string $shift, int $branchId): array {
        $pdo = Database::getConnection();
        $teachers = Teacher::all();

        // Keywords mapping for subject to expertise
        $keywords = self::extractKeywords($subjectName);

        $results = [];

        foreach ($teachers as $teacher) {
            // 1. Check if teacher is busy at that day & shift
            $conflict = self::checkTeacherConflict((int)$teacher['id'], $day, $shift);
            $isFree = ($conflict === null);

            // 2. Score expertise matching
            $matchScore = 0;
            $matchedWords = [];
            $expertiseLower = mb_strtolower($teacher['expertise'], 'UTF-8');

            foreach ($keywords as $kw) {
                if (mb_stripos($expertiseLower, $kw, 0, 'UTF-8') !== false) {
                    $matchScore += 30;
                    $matchedWords[] = $kw;
                }
            }

            // Direct check for general subject words
            $subjLower = mb_strtolower($subjectName, 'UTF-8');
            if (mb_stripos($expertiseLower, $subjLower, 0, 'UTF-8') !== false) {
                $matchScore += 50;
            }

            // 3. Travel factor score on the same day
            $travelAnalysis = self::checkTravelOptimization((int)$teacher['id'], $branchId, $day, $shift);
            $travelBonus = 0;
            if ($isFree) {
                if ($travelAnalysis['type'] === 'same_campus_optimal') {
                    $travelBonus = 25; // Bonus for teaching at same branch
                } elseif ($travelAnalysis['type'] === 'tight_travel') {
                    $travelBonus = -30; // Heavy penalty if adjacent shift at other campus
                } elseif ($travelAnalysis['type'] === 'optimal') {
                    $travelBonus = 10; // Free entire day
                }
            }

            $totalScore = $matchScore + $travelBonus;

            $badge = 'secondary';
            $recommendationText = 'Chưa phù hợp';

            if (!$isFree) {
                $badge = 'danger';
                $recommendationText = "BẬN: Trùng lịch dạy môn '{$conflict['subject_name']}' tại {$conflict['branch_name']}";
            } else {
                if ($matchScore > 0 && $travelAnalysis['type'] === 'same_campus_optimal') {
                    $badge = 'success';
                    $recommendationText = 'LÝ TƯỞNG: Chuyên môn phù hợp & Cùng cơ sở trong ngày';
                } elseif ($matchScore > 0 && $travelAnalysis['type'] === 'tight_travel') {
                    $badge = 'warning';
                    $recommendationText = 'CÂN NHẮC: Chuyên môn phù hợp nhưng khác cơ sở liền kề';
                } elseif ($matchScore > 0) {
                    $badge = 'primary';
                    $recommendationText = 'PHÙ HỢP: Đúng chuyên môn & Đang rảnh';
                } else {
                    $badge = 'info';
                    $recommendationText = 'Đang rảnh (khác chuyên môn)';
                }
            }

            $results[] = [
                'teacher_id' => (int)$teacher['id'],
                'full_name' => $teacher['full_name'],
                'email' => $teacher['email'],
                'phone' => $teacher['phone'],
                'expertise' => $teacher['expertise'],
                'is_free' => $isFree,
                'match_score' => $matchScore,
                'total_score' => $totalScore,
                'matched_keywords' => $matchedWords,
                'travel_analysis' => $travelAnalysis,
                'badge' => $badge,
                'recommendation_text' => $recommendationText
            ];
        }

        // Sort: Free first, then highest total score, then full_name
        usort($results, function($a, $b) {
            if ($a['is_free'] !== $b['is_free']) {
                return $b['is_free'] <=> $a['is_free'];
            }
            if ($a['total_score'] !== $b['total_score']) {
                return $b['total_score'] <=> $a['total_score'];
            }
            return strcmp($a['full_name'], $b['full_name']);
        });

        return $results;
    }

    private static function extractKeywords(string $subjectName): array {
        $clean = mb_strtolower(trim($subjectName), 'UTF-8');
        $stopWords = ['và', 'các', 'của', 'trong', 'ứng', 'dụng', 'chuyên', 'sâu', 'cơ', 'bản', 'nâng', 'cao', 'học'];
        $words = preg_split('/[\s,\.\-]+/u', $clean);
        $keywords = [];

        foreach ($words as $w) {
            if (mb_strlen($w, 'UTF-8') >= 3 && !in_array($w, $stopWords)) {
                $keywords[] = $w;
            }
        }

        // Add domain synonyms
        if (mb_stripos($clean, 'web') !== false) {
            $keywords = array_merge($keywords, ['php', 'laravel', 'nodejs', 'react', 'web']);
        }
        if (mb_stripos($clean, 'trí tuệ') !== false || mb_stripos($clean, 'ai') !== false || mb_stripos($clean, 'máy') !== false) {
            $keywords = array_merge($keywords, ['trí tuệ', 'học máy', 'python', 'dữ liệu', 'ai']);
        }
        if (mb_stripos($clean, 'csdl') !== false || mb_stripos($clean, 'dữ liệu') !== false) {
            $keywords = array_merge($keywords, ['cơ sở dữ liệu', 'sql', 'mysql']);
        }
        if (mb_stripos($clean, 'mạng') !== false || mb_stripos($clean, 'an toàn') !== false || mb_stripos($clean, 'bảo mật') !== false) {
            $keywords = array_merge($keywords, ['mạng', 'an ninh', 'bảo mật', 'cisco']);
        }
        if (mb_stripos($clean, 'di động') !== false || mb_stripos($clean, 'mobile') !== false) {
            $keywords = array_merge($keywords, ['di động', 'flutter', 'android', 'react native']);
        }
        if (mb_stripos($clean, 'phần mềm') !== false || mb_stripos($clean, 'kiểm thử') !== false) {
            $keywords = array_merge($keywords, ['công nghệ phần mềm', 'kiểm thử', 'java']);
        }

        return array_unique($keywords);
    }

    /**
     * Dữ liệu ma trận thời khóa biểu tuần
     */
    public static function getMatrixData(?int $branchId = null, ?int $teacherId = null): array {
        $schedules = self::all([
            'branch_id' => $branchId,
            'teacher_id' => $teacherId
        ]);

        $matrix = [];
        foreach (self::$shifts as $shift) {
            $matrix[$shift] = [];
            foreach (self::$daysOfWeek as $day) {
                $matrix[$shift][$day] = [];
            }
        }

        foreach ($schedules as $s) {
            $shift = $s['shift_or_periods'];
            $day = $s['day_of_week'];
            if (isset($matrix[$shift][$day])) {
                $matrix[$shift][$day][] = $s;
            }
        }

        return $matrix;
    }

    /**
     * Lấy thống kê phân bổ theo cơ sở
     */
    public static function getCampusDistribution(): array {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT b.id, b.branch_name, COUNT(s.id) AS total_schedules,
                             COUNT(DISTINCT s.teacher_id) AS total_teachers,
                             COUNT(DISTINCT s.room) AS total_rooms
                             FROM branches b
                             LEFT JOIN schedules s ON b.id = s.branch_id
                             GROUP BY b.id
                             ORDER BY b.id ASC");
        return $stmt->fetchAll();
    }
}
