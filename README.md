# HUIT Schedule Hub (Quản lý và Phân lịch Giảng dạy Đa cơ sở Thông minh)

Ứng dụng web hỗ trợ Phòng Đào tạo phân lịch, điều phối giảng viên và quản lý thời khóa biểu giữa nhiều cơ sở trường học (Cơ sở chính Tân Phú, Trung tâm Thí nghiệm Thực hành, Cơ sở Nguyễn Cửu Đàm, Cơ sở Thực phẩm,...), đồng thời hỗ trợ giảng viên tra cứu lịch cá nhân trực tuyến.

---

## 🌟 Tính năng nổi bật

1. **Phân lịch theo tiết trống & Tối ưu hóa di chuyển**:
   - Quản trị viên theo dõi toàn bộ ma trận thời khóa biểu tuần và dễ dàng phát hiện các khung giờ còn trống để bố trí lịch.
   - Thuật toán thông minh cảnh báo khi giảng viên có 2 ca dạy ở **2 cơ sở khác nhau liền kề** (dưới 60 phút di chuyển), giúp hạn chế trễ giờ và giảm áp lực đi lại.
   - Nhận diện và gợi ý gom ca cùng cơ sở để tối ưu thời gian làm việc, hạn chế khoảng trống chết (dead time).

2. **Gợi ý Giảng viên phù hợp theo thời gian thực (Real-time Suggestions)**:
   - Khi nhập môn học mới, hệ thống tự động bóc tách từ khóa chuyên môn (Expertise matching) và rà soát lịch trống tức thì.
   - Hiển thị danh sách giảng viên phù hợp nhất kèm huy hiệu trạng thái (Lý tưởng / Đúng chuyên môn / Cân nhắc di chuyển / Bận trùng lịch).
   - Chọn nhanh giảng viên chỉ bằng một cú click chuột.

3. **Cổng thông tin Giảng viên (Teacher Portal)**:
   - Tài khoản riêng cho từng giảng viên đăng nhập bằng email trường (`@huit.edu.vn`).
   - Nổi bật danh sách các **cơ sở và phòng học** mà giảng viên cần đến giảng dạy trong tuần.
   - Giao diện thời khóa biểu cá nhân trực quan, hỗ trợ xem theo tuần và in ấn nhanh (Print-friendly).

4. **Trang tổng quan & Bộ lọc nhanh**:
   - Thống kê tỷ lệ phân bổ tiết học, giảng viên và phòng học theo từng cơ sở.
   - Lọc nhanh dữ liệu toàn trường theo **Cơ sở** hoặc theo **Giảng viên**.

5. **Giao diện tối giản, chuẩn mực giáo dục**:
   - Thiết kế nhã nhặn, khoa học, dễ đọc, không rườm rà.

---

## 🛠 Yêu cầu hệ thống

- **Hệ điều hành**: Windows (chạy mượt mà trên Laragon), Linux, macOS
- **PHP**: Phiên bản 8.1 trở lên (Đã kiểm thử trên PHP 8.3)
- **Cơ sở dữ liệu**: MySQL 8.0+ / MariaDB 10.4+
- **Composer**: Phiên bản 2.x
- **Laragon**: Khuyến nghị kích hoạt Apache & MySQL

---

## 🚀 Hướng dẫn cài đặt & cấu hình chi tiết

### Bước 1: Đặt mã nguồn vào thư mục Laragon
Đặt thư mục dự án `huit-schedule` vào thư mục `laragon\www`:
```text
C:\laragon\www\huit-schedule
(hoặc đường dẫn Laragon của bạn, ví dụ: D:\Zalo\LTMNM\laragon\www\huit-schedule)
```

### Bước 2: Cài đặt các thư viện phụ thuộc qua Composer
Mở Terminal trong thư mục `huit-schedule` và chạy lệnh:
```bash
composer install
```
*Dự án sử dụng 2 thư viện mã nguồn mở theo yêu cầu đề bài:*
- `vlucas/phpdotenv`: Đọc và bảo mật biến môi trường `.env`.
- `fakerphp/faker`: Khởi tạo dữ liệu mẫu thực tế về cơ sở, giảng viên và thời khóa biểu.

### Bước 3: Cấu hình biến môi trường `.env`
Sao chép file `.env.example` thành `.env`:
```bash
cp .env.example .env
```
Mở file `.env` và kiểm tra thông tin kết nối MySQL:
```env
APP_NAME="HUIT Schedule Hub"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://huit-schedule.test

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=huit_db
DB_USERNAME=root
DB_PASSWORD=
```

### Bước 4: Khởi tạo Cơ sở dữ liệu (Database)
Bạn có thể sử dụng một trong hai cách sau:

#### Cách 1: Chạy script Seeder tự động bằng PHP (Khuyến nghị)
Script này sẽ tự động kết nối MySQL thông qua `.env`, áp dụng Faker để sinh dữ liệu mẫu đầy đủ:
```bash
php database/seed.php
```

#### Cách 2: Import trực tiếp file SQL mẫu
Sử dụng **HeidiSQL** (có sẵn trong Laragon) hoặc **phpMyAdmin**:
1. Tạo database tên `huit_db` với charset `utf8mb4_unicode_ci`.
2. Mở file `database/database.sql` và nhấn **Execute / Run** để nạp dữ liệu.

---

## 🌐 Truy cập ứng dụng & Tài khoản mẫu

Nếu sử dụng Laragon với Virtual Host tự động:
- **Tên miền cục bộ**: [http://huit-schedule.test](http://huit-schedule.test)
- **Hoặc truy cập qua localhost**: [http://localhost/huit-schedule](http://localhost/huit-schedule)

### Tài khoản đăng nhập kiểm thử:

| Vai trò | Đường dẫn / Chế độ | Tài khoản / Email | Mật khẩu |
| :--- | :--- | :--- | :--- |
| **Quản trị viên (Admin)** | Tab "Quản trị viên" | `admin` (hoặc `admin@huit.edu.vn`) | `admin123` |
| **Giảng viên (Teacher)** | Tab "Cổng Giảng viên" | `an.nguyen1@huit.edu.vn` (hoặc email GV bất kỳ trong danh sách) | `123456` |

---

## ⚙️ CI/CD Tự động hóa (GitHub Actions)

Dự án đã được tích hợp sẵn GitHub Actions workflow tại `.github/workflows/ci.yml`.
Mỗi khi có thao tác `git push` hoặc `pull_request` lên nhánh `main` / `master`, hệ thống sẽ tự động:
1. Thiết lập môi trường PHP 8.3 trên máy ảo Ubuntu.
2. Kiểm tra tính hợp lệ của file cấu hình `composer.json` (`composer validate --strict`).
3. Cài đặt các dependencies cần thiết.
4. Quét và kiểm tra lỗi cú pháp PHP tự động (`php -l`) trên toàn bộ mã nguồn của dự án (loại trừ thư mục `vendor/`).

---

## 📂 Cấu trúc thư mục dự án

```text
huit-schedule/
├── .github/
│   └── workflows/
│       └── ci.yml               # Workflow GitHub Actions kiểm tra php -l
├── config/                      # Cấu hình hệ thống
├── database/
│   ├── schema.sql               # Cấu trúc bảng MySQL (branches, teachers, schedules, users)
│   ├── seed.php                 # Script sinh dữ liệu mẫu bằng FakerPHP
│   └── database.sql             # Bản export SQL đầy đủ kèm dữ liệu mẫu
├── public/
│   ├── css/
│   │   └── style.css            # Giao diện tối giản, nhã nhặn
│   ├── js/
│   │   └── app.js               # Logic AJAX gợi ý GV và kiểm tra xung đột thời gian thực
│   └── index.php                # Front Controller & Điều hướng (Router)
├── src/
│   ├── Auth.php                 # Quản lý phiên đăng nhập Admin & Giảng viên
│   ├── Database.php             # Kết nối PDO Singleton & phpdotenv
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── ScheduleController.php
│   │   ├── TeacherController.php
│   │   ├── BranchController.php
│   │   └── PortalController.php
│   └── Models/
│       ├── Branch.php
│       ├── Teacher.php
│       └── Schedule.php
├── views/                       # Giao diện hiển thị (Blade/PHP views)
│   ├── layouts/
│   ├── auth/
│   ├── dashboard/
│   ├── schedules/
│   ├── teachers/
│   ├── branches/
│   └── portal/
├── .env.example
├── .gitignore
├── composer.json
├── LICENSE                      # Giấy phép mã nguồn mở MIT
└── README.md                    # Tài liệu hướng dẫn
```

---

## 📄 Bản quyền (License)

Dự án được phân phối dưới giấy phép **MIT License**. Chi tiết xem tại file [LICENSE](LICENSE).
