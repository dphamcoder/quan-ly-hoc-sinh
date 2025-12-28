<?php
session_start();
require_once "db.php";

// 1. Kiểm tra quyền HOCSINH
if (!isset($_SESSION['user']) || $_SESSION['user']['ChucVu'] != 'HOCSINH') {
    header("Location: dangNhap.php");
    exit();
}

$maHS = $_SESSION['user']['MaTK'];

// 2. Lấy thông tin LỚP của học sinh này từ DB
$studentInfo = null;
try {
    $conn = getConnection();
    $sql = "SELECT HoTenHS, MaLop FROM HOCSINH WHERE MaHS = :maHS";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['maHS' => $maHS]);
    $studentInfo = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Lỗi kết nối: " . $e->getMessage());
}

// Nếu không tìm thấy HS hoặc HS chưa được xếp lớp
if (!$studentInfo) {
    die("Không tìm thấy thông tin học sinh.");
}
$maLop = $studentInfo['MaLop']; // Lớp 10A1, 10A2...
$hoTen = $studentInfo['HoTenHS'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem Thời Khóa Biểu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="test.css">
    <style>
        /* CSS cho bảng TKB */
        .subject-badge {
            display: block; padding: 8px; border-radius: 6px; color: white;
            font-weight: 600; font-size: 13px; text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin: 2px;
        }
        /* Màu sắc môn học */
        .sub-math { background: linear-gradient(135deg, #4e73df, #224abe); } 
        .sub-lit { background: linear-gradient(135deg, #e74a3b, #c0392b); } 
        .sub-eng { background: linear-gradient(135deg, #f6c23e, #dda20a); color: #333; } 
        .sub-phys { background: linear-gradient(135deg, #36b9cc, #258391); } 
        .sub-chem { background: linear-gradient(135deg, #1cc88a, #13855c); } 
        .sub-bio { background: linear-gradient(135deg, #20c9a6, #168f76); } 
        .sub-hist { background: linear-gradient(135deg, #858796, #60616f); } 
        .sub-geo { background: linear-gradient(135deg, #fd7e14, #d35400); } 
        .sub-gdcd { background: linear-gradient(135deg, #6610f2, #520dc2); } 
        .sub-tech { background: linear-gradient(135deg, #6f42c1, #59359a); } 
        .sub-pe { background: linear-gradient(135deg, #28a745, #1e7e34); } 
        .sub-shcn { background: #333; } 
        .sub-default { background: #f8f9fa; color: #333; border: 1px solid #ddd; } 

        .timetable th, .timetable td {
            text-align: center; vertical-align: middle; height: 60px; 
        }
        .session-row {
            background-color: #eaecf4; font-weight: bold; 
            text-transform: uppercase; color: #555;
        }
        
        .tkb-controls {
            background: white; padding: 15px; border-radius: 8px; margin-bottom: 20px;
            display: flex; align-items: center; gap: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

    <button id="toggleSidebar" class="toggle-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="container">
        <aside id="sidebar" class="sidebar">
            <div class="teacher-card">
                <div class="avatar"></div>
                <h3><?= htmlspecialchars($hoTen) ?></h3> 
                <p>MSHS: <?= htmlspecialchars($maHS) ?></p>
            </div>

            <nav>
                <button onclick="window.location.href='thongTinCaNhan.php'">
                    <i class="fas fa-user"></i> <span>Thông tin cá nhân</span>
                </button>
                <button class="active" onclick="window.location.href='xemThoiKhoaBieu.php'">
                    <i class="fas fa-calendar-alt"></i> <span>Thời khóa biểu</span>
                </button>
                <button onclick="window.location.href='xemDiem.php'">
                    <i class="fas fa-chart-line"></i> <span>Kết quả học tập</span>
                </button>
                <button class="logout-btn" onclick="dangXuat()">
                    <i class="fas fa-sign-out-alt"></i> <span>Đăng xuất</span>
                </button>
            </nav>
        </aside>

        <main id="mainContent" class="content">
            
            <h2 style="margin-bottom: 20px; color: var(--primary-color);">
                <i class="fas fa-calendar-check"></i> THỜI KHÓA BIỂU
            </h2>

            <div class="tkb-controls">
                <div>
                    <strong>Lớp:</strong> <span class="badge bg-primary" style="font-size:1.1em"><?= htmlspecialchars($maLop) ?></span>
                </div>
                <div>
                    <strong>Chọn kỳ:</strong>
                    <select id="semesterSelect" onchange="loadSchedule()" style="padding: 5px; border-radius: 4px;">
                        <option value="1">Học kì 1</option>
                        <option value="2">Học kì 2</option>
                    </select>
                </div>
                <div>
                    <strong>Năm học:</strong>
                    <select id="yearSelect" onchange="loadSchedule()" style="padding: 5px; border-radius: 4px;">
                        <option value="2023-2024">2023 - 2024</option>
                        <option value="2024-2025">2024 - 2025</option>
                        <option value="2025-2026">2025 - 2026</option>
                    </select>
                </div>
            </div>

            <div class="card">
                <div class="table-responsive">
                    <table class="custom-table timetable">
                        <thead>
                            <tr>
                                <th style="width: 50px;">Tiết</th>
                                <th>Thứ 2</th>
                                <th>Thứ 3</th>
                                <th>Thứ 4</th>
                                <th>Thứ 5</th>
                                <th>Thứ 6</th>
                                <th>Thứ 7</th>
                            </tr>
                        </thead>
                        <tbody id="tkbBody">
                            <tr><td colspan="7">Đang tải dữ liệu...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card">
                <h3><i class="fas fa-sticky-note" style="color: #f6c23e;"></i> Ghi chú cá nhân</h3>
                <textarea class="form-control" rows="3" placeholder="Ví dụ: Kiểm tra 15p Toán thứ 4..."></textarea>
                <div style="margin-top: 10px; text-align: right;">
                    <button class="btn btn-primary"><i class="fas fa-save"></i> Lưu ghi chú</button>
                </div>
            </div>

        </main>
    </div>

    <script src="thongBao.js"></script>
    <script>
        // Lấy Mã Lớp từ PHP truyền sang JS
        const myClass = "<?= $maLop ?>";

        // 1. Map tên môn học sang Class màu sắc
        function getSubjectClass(subjectName) {
            if (!subjectName) return "sub-default";
            const lowerName = subjectName.toLowerCase();
            if (lowerName.includes("toán")) return "sub-math";
            if (lowerName.includes("văn")) return "sub-lit";
            if (lowerName.includes("anh")) return "sub-eng";
            if (lowerName.includes("lý") || lowerName.includes("vật lý")) return "sub-phys";
            if (lowerName.includes("hóa")) return "sub-chem";
            if (lowerName.includes("sinh")) return "sub-bio";
            if (lowerName.includes("sử")) return "sub-hist";
            if (lowerName.includes("địa")) return "sub-geo";
            if (lowerName.includes("gdcd")) return "sub-gdcd";
            if (lowerName.includes("tin") || lowerName.includes("công nghệ")) return "sub-tech";
            if (lowerName.includes("thể dục")) return "sub-pe";
            if (lowerName.includes("shcn") || lowerName.includes("chào cờ")) return "sub-shcn";
            return "sub-math";
        }

        // 2. Hàm gọi API
        function loadSchedule() {
            const hocKy = document.getElementById('semesterSelect').value;
            const namHoc = document.getElementById('yearSelect').value;
            
            const tbody = document.getElementById('tkbBody');
            tbody.innerHTML = '<tr><td colspan="7"><i class="fas fa-spinner fa-spin"></i> Đang tải dữ liệu...</td></tr>';

            if (!myClass) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-danger">Bạn chưa được xếp lớp. Vui lòng liên hệ nhà trường.</td></tr>';
                return;
            }

            // Gọi API lấy TKB (đã có sẵn trong thư mục api/)
            fetch(`api/getTKBByMaLop.php?MaLop=${encodeURIComponent(myClass)}&HocKy=${encodeURIComponent(hocKy)}&NamHoc=${encodeURIComponent(namHoc)}`)
                .then(res => res.json())
                .then(data => {
                    if (!data || data.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="7">Chưa có TKB cho lớp <b>${myClass}</b> (${namHoc})</td></tr>`;
                    } else {
                        renderScheduleFromDB(data);
                    }
                })
                .catch(err => {
                    console.error(err);
                    tbody.innerHTML = '<tr><td colspan="7" class="text-danger">Lỗi kết nối API</td></tr>';
                });
        }

        // 3. Render bảng
        function renderScheduleFromDB(tkbData) {
            const tbody = document.getElementById('tkbBody');
            tbody.innerHTML = '';

            const scheduleMap = {};
            for (let i = 1; i <= 8; i++) {
                scheduleMap[i] = { period: `Tiết ${i}` };
            }

            tkbData.forEach(item => {
                const thuKey = 't' + item.Thu; 
                if (scheduleMap[item.Tiet]) {
                    scheduleMap[item.Tiet][thuKey] = item.TenMon;
                }
            });

            // Sáng (1-4)
            tbody.innerHTML += `<tr class="session-row"><td colspan="7">Buổi Sáng</td></tr>`;
            for (let i = 1; i <= 4; i++) {
                if(scheduleMap[i]) tbody.appendChild(createRow(scheduleMap[i]));
            }

            // Chiều (5-8)
            tbody.innerHTML += `<tr class="session-row"><td colspan="7">Buổi Chiều</td></tr>`;
            for (let i = 5; i <= 8; i++) {
                if(scheduleMap[i]) tbody.appendChild(createRow(scheduleMap[i]));
            }
        }

        function createRow(data) {
            const tr = document.createElement('tr');
            const days = ['t2', 't3', 't4', 't5', 't6', 't7'];
            let html = `<td><strong>${data.period}</strong></td>`;
            days.forEach(day => {
                const subject = data[day];
                if (subject) {
                    const colorClass = getSubjectClass(subject);
                    html += `<td><span class="subject-badge ${colorClass}">${subject}</span></td>`;
                } else {
                    html += `<td></td>`;
                }
            });
            tr.innerHTML = html;
            return tr;
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleSidebar');
            if(sidebar) {
                sidebar.classList.toggle('collapsed');
                if (sidebar.classList.contains('collapsed')) {
                    if(toggleBtn) toggleBtn.classList.add('moved');
                } else {
                    if(toggleBtn) toggleBtn.classList.remove('moved');
                }
            }
        }

        function dangXuat() { if(confirm('Đăng xuất?')) window.location.href = 'dangNhap.php'; }

        // Tự động load khi vào trang
        window.onload = function() {
            // Mặc định chọn năm 2023-2024 để khớp DB mẫu
            document.getElementById('yearSelect').value = "2023-2024";
            loadSchedule();
        };
    </script>
</body>
</html>