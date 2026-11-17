<?php

namespace App\Controllers;

use App\Auth;
use App\Models\Branch;
use App\Models\Teacher;
use App\Models\Schedule;

class DashboardController {
    public function index(): void {
        Auth::requireAdmin();

        $selectedBranchId = !empty($_GET['branch_id']) ? (int)$_GET['branch_id'] : null;
        $selectedTeacherId = !empty($_GET['teacher_id']) ? (int)$_GET['teacher_id'] : null;

        $branches = Branch::all();
        $teachers = Teacher::all();

        // Statistics
        $totalBranches = count($branches);
        $totalTeachers = count($teachers);
        $allSchedules = Schedule::all();
        $totalSchedules = count($allSchedules);

        // Filtered Timetable Matrix
        $matrix = Schedule::getMatrixData($selectedBranchId, $selectedTeacherId);
        $days = Schedule::$daysOfWeek;
        $shifts = Schedule::$shifts;

        // Campus distribution stats
        $campusDistribution = Schedule::getCampusDistribution();

        require_once __DIR__ . '/../../views/dashboard/index.php';
    }
}
