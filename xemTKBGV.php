<?php
    session_start();
    // 1. Kiểm tra quyền Giáo viên
    if (!isset($_SESSION['user']) || $_SESSION['user']['ChucVu'] != 'GIAOVIEN') {
        header("Location: dangNhap.php");
        exit();
    }
    $currentUser = $_SESSION['user'];

    // 2. Lấy danh sách lớp phân công
    require_once __DIR__ . '/DAL/lophoc.php'; 
    $dsLopPhanCong = getLopByGiaoVien($currentUser['MaTK']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem Thời Khóa Biểu (GV)</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="test.css">
    <style>
        /* === FIX LỖI MẤT THANH CUỘN === */
        /* Đảm bảo nội dung chính có thể cuộn được nếu dài quá màn hình */
        #mainContent {
            height: 100vh;
            overflow-y: auto; /* Cho phép cuộn dọc */
            padding-bottom: 50px; /* Thêm khoảng trống dưới cùng */
        }

        /* CSS cho bảng TKB */
        .subject-badge {
            display: block; padding: 8px; border-radius: 6px; color: white;
            font-weight: 600; font-size: 13px; text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin: 2px;
        }
        /* Màu môn học */
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

        .timetable th, .timetable td { text-align: center; vertical-align: middle; height: 60px; }
        .session-row { background-color: #eaecf4; font-weight: bold; text-transform: uppercase; color: #555; }
        
        /* Style điều khiển */
        .tkb-controls {
            background: white; padding: 20px; border-radius: 8px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.05); margin-bottom: 20px;
            display: flex; flex-wrap: wrap; align-items: center; gap: 15px;
        }
        .control-group { display: flex; flex-direction: column; gap: 5px; }
        .control-group label { font-weight: bold; font-size: 0.9em; color: #555; }
        .select-custom {
            padding: 8px 15px; border: 1px solid #ccc; border-radius: 5px;
            font-weight: 600; color: #333; outline: none; min-width: 150px;
        }
        .select-custom:focus { border-color: #4e73df; box-shadow: 0 0 0 3px rgba(78,115,223,0.1); }
        
        /* Đảm bảo nút toggle sidebar luôn nổi lên trên */
        #toggleSidebar { z-index: 1000; }
    </style>
</head>
<body class="admin-page">

    <button id="toggleSidebar" class="toggle-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="container">
        <aside id="sidebar" class="sidebar">
            <div class="teacher-card">
                <div class="avatar"></div>
                <h3><?= htmlspecialchars($currentUser['TenHienThi'] ?? $currentUser['TenTK']) ?></h3> 
                <p>MSCB: <?= htmlspecialchars($currentUser['MaTK']) ?></p>
            </div>
            <nav>
                <button onclick="window.location.href='danhSachLopGV.php'"><i class="fas fa-list-ol"></i> <span>Xem danh sách lớp</span></button>
                <button onclick="window.location.href='nhapDiem.php'"><i class="fas fa-marker"></i> <span>Nhập điểm bộ môn</span></button>
                <button onclick="window.location.href='danhGiaHanhKiem.php'"><i class="fas fa-user-check"></i> <span>Đánh giá hạnh kiểm</span></button>
                <button onclick="window.location.href='tongHopKetQua.php'"><i class="fas fa-chart-line"></i> <span>Tổng hợp kết quả học tập</span></button>
                <button class="active" onclick="window.location.href='xemtkbGV.php'"><i class="fas fa-calendar-alt"></i> <span>Xem thời khóa biểu</span></button>
                <button class="logout-btn" onclick="dangXuat()"><i class="fas fa-sign-out-alt"></i> <span>Đăng xuất</span></button>
            </nav>
        </aside>

        <main id="mainContent" class="content">
            
            <h2 style="margin-bottom: 20px; color: var(--primary-color);">
                <i class="fas fa-calendar-check"></i> XEM THỜI KHÓA BIỂU
            </h2>

            <div class="tkb-controls">
                
                <div class="control-group">
                    <label for="classSelect">Lớp giảng dạy:</label>
                    <select id="classSelect" class="select-custom" onchange="changeFilter()">
                        <?php if (empty($dsLopPhanCong)): ?>
                            <option value="">(Chưa được phân công)</option>
                        <?php else: ?>
                            <?php 
                            $printedClasses = [];
                            foreach ($dsLopPhanCong as $lop): 
                                if (in_array($lop->MaLop, $printedClasses)) continue;
                                $printedClasses[] = $lop->MaLop;
                            ?>
                                <option value="<?= htmlspecialchars($lop->MaLop) ?>">
                                    Lớp <?= htmlspecialchars($lop->TenLop) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="control-group">
                    <label for="yearSelect">Năm học:</label>
                    <select id="yearSelect" class="select-custom" onchange="changeFilter()">
                        <option value="2023-2024">2023 - 2024</option>
                        <option value="2024-2025">2024 - 2025</option>
                        <option value="2025-2026">2025 - 2026</option>
                    </select>
                </div>

                <div class="control-group">
                    <label for="termSelect">Học kỳ:</label>
                    <select id="termSelect" class="select-custom" onchange="changeFilter()">
                        <option value="1">Học kỳ 1</option>
                        <option value="2">Học kỳ 2</option>
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

        </main>
    </div>

    <script src="thongBao.js"></script>
    <script>
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

        function changeFilter() {
            const maLop = document.getElementById('classSelect').value;
            const namHoc = document.getElementById('yearSelect').value;
            const hocKy = document.getElementById('termSelect').value;
            loadScheduleFromDB(maLop, hocKy, namHoc);
        }

        function loadScheduleFromDB(maLop, hocKy, namHoc) {
            const tbody = document.getElementById('tkbBody');
            if (!maLop) {
                tbody.innerHTML = '<tr><td colspan="7">Vui lòng chọn lớp giảng dạy</td></tr>';
                return;
            }
            tbody.innerHTML = '<tr><td colspan="7"><i class="fas fa-spinner fa-spin"></i> Đang tải dữ liệu...</td></tr>';

            fetch(`api/getTKBByMaLop.php?MaLop=${encodeURIComponent(maLop)}&HocKy=${encodeURIComponent(hocKy)}&NamHoc=${encodeURIComponent(namHoc)}`)
                .then(res => res.json())
                .then(data => {
                    if(!data || data.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="7" class="text-center p-3">
                            Không tìm thấy TKB cho lớp <b>${maLop}</b> - HK${hocKy} (${namHoc})
                        </td></tr>`;
                    } else {
                        renderScheduleFromDB(data);
                    }
                })
                .catch(err => {
                    console.error(err);
                    tbody.innerHTML = '<tr><td colspan="7" class="text-danger">Lỗi kết nối API</td></tr>';
                });
        }

        function renderScheduleFromDB(tkbData) {
            const tbody = document.getElementById('tkbBody');
            tbody.innerHTML = '';

            const scheduleMap = {};
            for (let i = 1; i <= 8; i++) {
                scheduleMap[i] = { period: `Tiết ${i}` };
            }

            tkbData.forEach(item => {
                const thuKey = 't' + item.Thu; 
                if (scheduleMap[item.Tiet]) { // Kiểm tra nếu tiết đó nằm trong range 1-8
                    scheduleMap[item.Tiet][thuKey] = item.TenMon;
                }
            });

            // === BUỔI SÁNG (Tiết 1 đến 4) ===
            tbody.innerHTML += `<tr class="session-row"><td colspan="7">Buổi Sáng</td></tr>`;
            for (let i = 1; i <= 4; i++) {
                if(scheduleMap[i]) tbody.appendChild(createRow(scheduleMap[i]));
            }

            // === BUỔI CHIỀU (Tiết 5 đến 8) ===
            // Nếu bạn muốn hiển thị luôn cả 4 tiết chiều kể cả khi không có lịch
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

        window.onload = function() {
            const select = document.getElementById('classSelect');
            if (select && select.options.length > 0) {
                if(select.value === "") select.selectedIndex = 0;
                document.getElementById('yearSelect').value = "2023-2024"; // Mặc định năm học khớp DB
                changeFilter();
            }
        };
    </script>
</body>
</html> 