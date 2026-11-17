<?php

namespace App\Controllers;

use App\Auth;
use App\Models\Teacher;
use App\Models\Schedule;

class PortalController {
    public function index(): void {
        Auth::requireTeacher();
        $user = Auth::user();

        // Get teacher id
        $teacherId = $user['teacher_id'] ?? $user['id'];
        $teacher = Teacher::find($teacherId);

        $schedules = Schedule::all(['teacher_id' => $teacherId]);
        $matrix = Schedule::getMatrixData(null, $teacherId);
        $days = Schedule::$daysOfWeek;
        $shifts = Schedule::$shifts;

        // Group schedules by branch to show which campuses the teacher needs to visit
        $campusesToVisit = [];
        foreach ($schedules as $s) {
            $bId = $s['branch_id'];
            if (!isset($campusesToVisit[$bId])) {
                $campusesToVisit[$bId] = [
                    'branch_name' => $s['branch_name'],
                    'branch_address' => $s['branch_address'],
                    'classes_count' => 0,
                    'classes' => []
                ];
            }
            $campusesToVisit[$bId]['classes_count']++;
            $campusesToVisit[$bId]['classes'][] = $s;
        }

        require_once __DIR__ . '/../../views/portal/my_schedule.php';
    }
}
