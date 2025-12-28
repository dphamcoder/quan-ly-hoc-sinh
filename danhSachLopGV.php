<?php
    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']['ChucVu'] != 'GIAOVIEN') {
        header("Location: dangNhap.php");
        exit();
    }
    $currentUser = $_SESSION['user']; 

    require_once __DIR__ . '/DAL/lophoc.php';
    $dsLopPhanCong = getLopByGiaoVien($currentUser['MaTK']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem Danh Sách Lớp</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="test.css">
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
                <p>MS: <?= htmlspecialchars($currentUser['MaTK']) ?></p>
            </div>

            <nav>
                <button class="active" onclick="window.location.href='danhSachLopGV.php'">
                    <i class="fas fa-list-ol"></i> <span>Xem danh sách lớp</span>
                </button>
                <button onclick="window.location.href='nhapDiem.php'">
                    <i class="fas fa-marker"></i> <span>Nhập điểm bộ môn</span>
                </button>
                <button onclick="window.location.href='danhGiaHanhKiem.php'">
                    <i class="fas fa-user-check"></i> <span>Đánh giá hạnh kiểm</span>
                </button>
                <button onclick="window.location.href='tongHopKetQua.php'">
                    <i class="fas fa-chart-line"></i> <span>Tổng hợp kết quả học tập</span>
                </button>
                <button onclick="window.location.href='xemtkbGV.php'">
                    <i class="fas fa-calendar-alt"></i> <span>Xem thời khóa biểu</span>
                </button>
                <button class="logout-btn" onclick="dangXuat()">
                    <i class="fas fa-sign-out-alt"></i> <span>Đăng xuất</span>
                </button>
            </nav>
        </aside>

        <main id="mainContent" class="content">
            <div class="card" style="height: 100%; display: flex; flex-direction: column;">
                <div class="class-list-header">
                    <select class="class-dropdown" id="classSelect" onchange="loadHocSinhByLop(this.value)">
                        <?php if (empty($dsLopPhanCong)): ?>
                            <option value="">Chưa được phân công lớp nào</option>
                        <?php else: ?>
                            <?php foreach ($dsLopPhanCong as $lop): ?>
                                <option value="<?= htmlspecialchars($lop->MaLop) ?>">
                                    Lớp <?= htmlspecialchars($lop->TenLop) ?> (Môn: <?= htmlspecialchars($lop->TenMon) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    
                    <h2 class="page-title" id="classTitle">Danh sách học sinh</h2>
                </div>

                <div class="table-scroll">
                    <table class="placement-table view-table">
                        <thead>
                            <tr>
                                <th style="width: 60px;">STT</th>
                                <th>Họ và Tên Học Sinh</th>
                                <th style="width: 150px;">Mã học sinh</th>
                                <th style="width: 100px;">Giới tính</th>
                                <th style="width: 150px;">Ngày sinh</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody"></tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="thongBao.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleSidebar');
            sidebar.classList.toggle('collapsed');
            if (sidebar.classList.contains('collapsed')) {
                toggleBtn.classList.add('moved');
            } else {
                toggleBtn.classList.remove('moved');
            }
        }

        function loadHocSinhByLop(maLop) {
            const tbody = document.getElementById('tableBody');
            const title = document.getElementById('classTitle');

            if (!maLop) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center p-3">Chưa chọn lớp</td></tr>';
                return;
            }

            fetch(`api/getHocSinhByLop.php?MaLop=${maLop}`)
                .then(res => res.json())
                .then(data => {
                    if (!data.length) {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center p-3">Lớp chưa có học sinh</td></tr>';
                    } else {
                        tbody.innerHTML = data.map((hs, index) => `
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td>${hs.HoTenHS}</td>
                                <td>${hs.MaHS}</td>
                                <td>${hs.GioiTinh}</td>
                                <td>${hs.NgaySinh}</td>
                            </tr>
                        `).join('');
                    }
                    title.textContent = "Danh sách học sinh Lớp " + maLop;
                })
                .catch(err => {
    console.error('Chi tiết lỗi:', err); 
    tbody.innerHTML = `<tr><td colspan="5" style="text-align:center; padding:20px; color:red;">
        Lỗi kết nối API. Vui lòng kiểm tra Console (F12) để xem chi tiết.
    </td></tr>`;
});
        }

        function dangXuat() {
            if(confirm('Bạn có chắc chắn muốn đăng xuất?')) window.location.href = 'dangNhap.php';
        }

        window.onload = function() {
            const select = document.getElementById('classSelect');
            if (select.options.length > 0 && select.value !== "") {
                select.selectedIndex = 0;
                loadHocSinhByLop(select.value);
            }
        };
    </script>
</body>
</html>