<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Faker\Factory;

// Load .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$dbHost = $_ENV['DB_HOST'] ?? '127.0.0.1';
$dbPort = $_ENV['DB_PORT'] ?? '3306';
$dbName = $_ENV['DB_DATABASE'] ?? 'huit_db';
$dbUser = $_ENV['DB_USERNAME'] ?? 'root';
$dbPass = $_ENV['DB_PASSWORD'] ?? '';

try {
    $pdo = new PDO("mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage() . "\n");
}

echo "=== Seeding HUIT Schedule Hub Data ===\n";

// Disable foreign key checks for reset
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
$pdo->exec("TRUNCATE TABLE schedules;");
$pdo->exec("TRUNCATE TABLE teachers;");
$pdo->exec("TRUNCATE TABLE branches;");
$pdo->exec("TRUNCATE TABLE users;");
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

// 1. Seed Branches (Cơ sở HUIT thực tế)
$branches = [
    [
        'branch_name' => 'Cơ sở chính (Tân Phú)',
        'address' => '140 Lê Trọng Tấn, P. Tây Thạnh, Q. Tân Phú, TP.HCM'
    ],
    [
        'branch_name' => 'Cơ sở 2 (Nguyễn Cửu Đàm)',
        'address' => '93 Tân Kỳ Tân Quý, P. Tân Sơn Nhì, Q. Tân Phú, TP.HCM'
    ],
    [
        'branch_name' => 'Cơ sở 3 (Thoại Ngọc Hầu)',
        'address' => '334 Thoại Ngọc Hầu, P. Phú Thạnh, Q. Tân Phú, TP.HCM'
    ],
    [
        'branch_name' => 'Cơ sở 4 (Chế biến & TT Thí nghiệm)',
        'address' => 'Nguyễn Quý Anh, P. Tân Sơn Nhì, Q. Tân Phú, TP.HCM'
    ],
];

$branchStmt = $pdo->prepare("INSERT INTO branches (branch_name, address) VALUES (:branch_name, :address)");
$branchIds = [];
foreach ($branches as $b) {
    $branchStmt->execute($b);
    $branchIds[] = $pdo->lastInsertId();
}
echo "✓ Đã thêm " . count($branchIds) . " cơ sở trường học.\n";

// 2. Seed Users (Admin)
$adminPassword = password_hash('admin123', PASSWORD_BCRYPT);
$userStmt = $pdo->prepare("INSERT INTO users (username, full_name, email, password, role) VALUES (:username, :full_name, :email, :password, :role)");
$userStmt->execute([
    'username' => 'admin',
    'full_name' => 'Quản Trị Viên (Phòng Đào Tạo)',
    'email' => 'admin@huit.edu.vn',
    'password' => $adminPassword,
    'role' => 'admin'
]);
echo "✓ Đã tạo tài khoản Quản trị: admin / admin123 (email: admin@huit.edu.vn)\n";

// 3. Seed Teachers with Faker
$faker = Factory::create('vi_VN');

$expertises = [
    'Lập trình Web, PHP, Laravel, NodeJS',
    'Trí tuệ nhân tạo, Học máy, Khoa học dữ liệu, Python',
    'Cơ sở dữ liệu, SQL Server, MySQL, Thiết kế CSDL',
    'Mạng máy tính, An ninh mạng, Quản trị mạng Cisco',
    'Công nghệ phần mềm, Kiểm thử phần mềm, Java',
    'Lập trình di động, Flutter, Android, React Native',
    'Cấu trúc dữ liệu & Giải thuật, Lập trình C/C++',
    'Kiến trúc máy tính, Hệ điều hành, IoT'
];

$vietnameseFirstNames = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh', 'Phan', 'Vũ', 'Võ', 'Đặng', 'Bùi', 'Đỗ', 'Hồ', 'Ngô', 'Dương'];
$vietnameseMiddleNames = ['Văn', 'Thị', 'Đình', 'Hữu', 'Minh', 'Đức', 'Thanh', 'Quang', 'Ngọc', 'Xuân', 'Kim', 'Gia'];
$vietnameseLastNames = ['An', 'Bình', 'Cường', 'Dũng', 'Hải', 'Hùng', 'Khoa', 'Long', 'Nam', 'Phúc', 'Quân', 'Thảo', 'Trang', 'Tuấn', 'Việt', 'Yến'];

$defaultTeacherPassword = password_hash('123456', PASSWORD_BCRYPT);
$teacherStmt = $pdo->prepare("INSERT INTO teachers (full_name, email, phone, expertise, password) VALUES (:full_name, :email, :phone, :expertise, :password)");

$teachers = [];
$teacherCount = 15;

function removeVietnameseAccents($str) {
    $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/u", 'a', $str);
    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/u", 'e', $str);
    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/u", 'i', $str);
    $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/u", 'o', $str);
    $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/u", 'u', $str);
    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/u", 'y', $str);
    $str = preg_replace("/(đ)/u", 'd', $str);
    $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/u", 'A', $str);
    $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/u", 'E', $str);
    $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/u", 'I', $str);
    $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/u", 'O', $str);
    $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/u", 'U', $str);
    $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/u", 'Y', $str);
    $str = preg_replace("/(Đ)/u", 'D', $str);
    return strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $str));
}

for ($i = 1; $i <= $teacherCount; $i++) {
    $fn = $vietnameseFirstNames[array_rand($vietnameseFirstNames)];
    $mn = $vietnameseMiddleNames[array_rand($vietnameseMiddleNames)];
    $ln = $vietnameseLastNames[array_rand($vietnameseLastNames)];
    $fullName = "ThS. {$fn} {$mn} {$ln}";
    
    // convert name to slug email without accent
    $cleanFn = removeVietnameseAccents($fn);
    $cleanLn = removeVietnameseAccents($ln);
    $email = "{$cleanLn}.{$cleanFn}{$i}@huit.edu.vn";
    $phone = '09' . str_pad((string)mt_rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
    $expertise = $expertises[($i - 1) % count($expertises)];

    $teacherStmt->execute([
        'full_name' => $fullName,
        'email' => $email,
        'phone' => $phone,
        'expertise' => $expertise,
        'password' => $defaultTeacherPassword
    ]);
    
    $teachers[] = [
        'id' => $pdo->lastInsertId(),
        'full_name' => $fullName,
        'email' => $email,
        'expertise' => $expertise
    ];
}
echo "✓ Đã tạo {$teacherCount} giảng viên mẫu (mật khẩu mặc định: 123456).\n";

// 4. Seed Schedules
$daysOfWeek = ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
$shifts = [
    'Sáng (Tiết 1 - 3)',
    'Sáng (Tiết 4 - 6)',
    'Chiều (Tiết 7 - 9)',
    'Chiều (Tiết 10 - 12)',
    'Tối (Tiết 13 - 15)'
];

$subjects = [
    ['name' => 'Lập trình Web', 'match' => 'Lập trình Web'],
    ['name' => 'Công nghệ phần mềm', 'match' => 'Công nghệ phần mềm'],
    ['name' => 'Trí tuệ nhân tạo', 'match' => 'Trí tuệ nhân tạo'],
    ['name' => 'Hệ quản trị CSDL', 'match' => 'Cơ sở dữ liệu'],
    ['name' => 'Mạng máy tính', 'match' => 'Mạng máy tính'],
    ['name' => 'An toàn thông tin', 'match' => 'An ninh mạng'],
    ['name' => 'Phát triển ứng dụng di động', 'match' => 'Lập trình di động'],
    ['name' => 'Cấu trúc dữ liệu & Giải thuật', 'match' => 'Cấu trúc dữ liệu'],
    ['name' => 'Kiểm thử phần mềm', 'match' => 'Kiểm thử phần mềm'],
    ['name' => 'Học máy chuyên sâu', 'match' => 'Học máy']
];

$rooms = ['A1-201', 'A1-304', 'B2-105', 'B2-208', 'Lab 4.1 (Máy tính)', 'Lab 4.2 (Mạng)', 'Lab 5.1 (AI)', 'C3-201'];

$scheduleStmt = $pdo->prepare("INSERT INTO schedules (teacher_id, branch_id, subject_name, room, day_of_week, shift_or_periods, semester, notes) 
VALUES (:teacher_id, :branch_id, :subject_name, :room, :day_of_week, :shift_or_periods, :semester, :notes)");

$bookedTeacherSlots = []; // "teacher_id:day:shift" => true
$bookedRoomSlots = [];    // "branch_id:room:day:shift" => true
$scheduleCount = 0;

foreach ($teachers as $teacher) {
    // Each teacher gets 2 - 4 teaching shifts in a week
    $numClasses = mt_rand(2, 4);
    
    // Choose subject matching expertise or nearby
    $matchedSubjects = array_filter($subjects, function($s) use ($teacher) {
        return stripos($teacher['expertise'], $s['match']) !== false;
    });
    if (empty($matchedSubjects)) {
        $matchedSubjects = [$subjects[array_rand($subjects)]];
    }
    
    // Assign at 1 branch mostly, occasionally 2
    $primaryBranchId = $branchIds[array_rand($branchIds)];
    
    for ($k = 0; $k < $numClasses; $k++) {
        $subj = $matchedSubjects[array_rand($matchedSubjects)]['name'];
        $day = $daysOfWeek[array_rand($daysOfWeek)];
        $shift = $shifts[array_rand($shifts)];
        $room = $rooms[array_rand($rooms)];
        $branchId = $primaryBranchId;

        $tKey = "{$teacher['id']}:{$day}:{$shift}";
        $rKey = "{$branchId}:{$room}:{$day}:{$shift}";

        if (isset($bookedTeacherSlots[$tKey]) || isset($bookedRoomSlots[$rKey])) {
            continue; // Skip conflict
        }

        $bookedTeacherSlots[$tKey] = true;
        $bookedRoomSlots[$rKey] = true;

        $scheduleStmt->execute([
            'teacher_id' => $teacher['id'],
            'branch_id' => $branchId,
            'subject_name' => $subj,
            'room' => $room,
            'day_of_week' => $day,
            'shift_or_periods' => $shift,
            'semester' => 'HK1 (2026-2027)',
            'notes' => 'Lớp tín chỉ chính quy'
        ]);
        $scheduleCount++;
    }
}

echo "✓ Đã tạo {$scheduleCount} tiết lịch dạy mẫu không bị trùng lịch.\n";
echo "=== Hoàn thành khởi tạo dữ liệu mẫu! ===\n";
