import urllib.request
import urllib.parse
import http.cookiejar
import json

BASE_URL = "http://localhost:8080/huit-schedule/public"

def run_tests():
    print("=== Testing HUIT Schedule Hub Application ===")

    # Setup cookie jar session
    cj = http.cookiejar.CookieJar()
    opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cj))

    # 1. Test Login Page
    req = urllib.request.Request(f"{BASE_URL}/?action=login")
    with opener.open(req) as resp:
        content = resp.read().decode('utf-8')
        assert resp.status == 200, f"Login page failed: {resp.status}"
        assert "HUIT Schedule Hub" in content, "Title not found in login page"
    print("✓ 1. Trang đăng nhập hiển thị chính xác (200 OK)")

    # 2. Test Admin Login
    login_data = urllib.parse.urlencode({
        "type": "admin",
        "username": "admin",
        "password": "admin123"
    }).encode('utf-8')
    req = urllib.request.Request(f"{BASE_URL}/?action=login_submit", data=login_data)
    with opener.open(req) as resp:
        content = resp.read().decode('utf-8')
        assert resp.status == 200, f"Admin login failed: {resp.status}"
        assert "Quản trị viên" in content or "Tổng quan phân lịch giảng dạy" in content, "Admin dashboard not displayed"
    print("✓ 2. Đăng nhập Quản trị viên (admin/admin123) thành công")

    # 3. Test Dashboard & Timetable Matrix
    req = urllib.request.Request(f"{BASE_URL}/?action=dashboard")
    with opener.open(req) as resp:
        content = resp.read().decode('utf-8')
        assert resp.status == 200
        assert "Biểu đồ Thời khóa biểu Tuần" in content
        assert "Phân bổ tiết dạy theo Cơ sở" in content
    print("✓ 3. Dashboard, Thống kê cơ sở và Ma trận thời khóa biểu tuần tải thành công")

    # 4. Test Smart Teacher Suggestion API
    api_url = f"{BASE_URL}/?action=api_suggest_teachers&" + urllib.parse.urlencode({
        "subject": "Lập trình Web",
        "day": "Thứ 2",
        "shift": "Sáng (Tiết 1 - 3)",
        "branch_id": 1
    })
    req = urllib.request.Request(api_url)
    with opener.open(req) as resp:
        assert resp.status == 200
        data = json.loads(resp.read().decode('utf-8'))
        assert data["success"] is True, "API success false"
        assert len(data["data"]) > 0, "No suggestions returned"
        top_teacher = data["data"][0]
        print(f"✓ 4. API Gợi ý Giảng viên: Top gợi ý cho môn 'Lập trình Web' là '{top_teacher['full_name']}' - Chuyên môn: {top_teacher['expertise']} (Badge: {top_teacher['badge']})")

    # 5. Test Live Conflict & Travel Optimization API
    teacher_id = top_teacher["teacher_id"]
    chk_url = f"{BASE_URL}/?action=api_check_conflict&" + urllib.parse.urlencode({
        "teacher_id": teacher_id,
        "branch_id": 1,
        "day": "Thứ 2",
        "shift": "Sáng (Tiết 1 - 3)",
        "room": "A1-201"
    })
    req = urllib.request.Request(chk_url)
    with opener.open(req) as resp:
        assert resp.status == 200
        conflict_data = json.loads(resp.read().decode('utf-8'))
        assert "teacher_conflict" in conflict_data
        assert "travel_analysis" in conflict_data
        print(f"✓ 5. API Kiểm tra xung đột & Phân tích di chuyển: {conflict_data['travel_analysis']['message']}")

    # 6. Test Teacher Management
    req = urllib.request.Request(f"{BASE_URL}/?action=teachers")
    with opener.open(req) as resp:
        content = resp.read().decode('utf-8')
        assert resp.status == 200
        assert "Danh sách Giảng viên" in content
    print("✓ 6. Trang Quản lý Giảng viên hoạt động tốt")

    # 7. Test Branch Management
    req = urllib.request.Request(f"{BASE_URL}/?action=branches")
    with opener.open(req) as resp:
        content = resp.read().decode('utf-8')
        assert resp.status == 200
        assert "Cơ sở chính (Tân Phú)" in content
    print("✓ 7. Trang Quản lý Cơ sở trường học hoạt động tốt")

    # 8. Test Schedules Management List
    req = urllib.request.Request(f"{BASE_URL}/?action=schedules")
    with opener.open(req) as resp:
        content = resp.read().decode('utf-8')
        assert resp.status == 200
        assert "Danh sách lịch giảng dạy" in content
    print("✓ 8. Trang Quản lý danh sách lịch dạy hoạt động tốt")

    # 9. Test Teacher Portal Login & Personal Schedule
    teacher_cj = http.cookiejar.CookieJar()
    teacher_opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(teacher_cj))
    t_login_data = urllib.parse.urlencode({
        "type": "teacher",
        "email": "yen.dang1@huit.edu.vn",
        "password": "123456"
    }).encode('utf-8')
    req = urllib.request.Request(f"{BASE_URL}/?action=login_submit", data=t_login_data)
    with teacher_opener.open(req) as resp:
        content = resp.read().decode('utf-8')
        assert resp.status == 200
        assert "Thời khóa biểu cá nhân" in content or "Cổng thông tin Giảng viên" in content
        assert "Các Cơ sở Giảng viên cần đến dạy" in content
    print("✓ 9. Cổng thông tin Giảng viên (Teacher Portal) hiển thị đầy đủ lịch dạy và cơ sở cần đến dạy")

    print("\n🎉 TẤT CẢ 9/9 BỘ TEST ĐỀU VƯỢT QUA 100%!")

if __name__ == "__main__":
    run_tests()
