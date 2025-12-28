<?php
session_start();
require_once 'db.php';

// 1. Kiểm tra quyền truy cập
if (!isset($_SESSION['user']) || $_SESSION['user']['ChucVu'] != 'CANBO') {
    header("Location: dangNhap.php");
    exit();
}

// 2. Lấy thông tin người dùng hiện tại
$currentUser = $_SESSION['user'];

// 3. Lấy danh sách lớp
$stmtLop = $conn->query("SELECT MaLop, TenLop FROM lophoc ORDER BY TenLop ASC");
$lops = $stmtLop->fetchAll(PDO::FETCH_ASSOC);
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
                <p>MSCB: <?= htmlspecialchars($currentUser['MaTK']) ?></p>
            </div>


            <nav>
                <button onclick="window.location.href='quanLyTaiKhoan.php'">
                    <i class="fas fa-users-cog"></i> <span>Quản lý tài khoản</span>
                </button>
                <button onclick="window.location.href='quanLyHocSinh.php'">
                    <i class="fas fa-user-graduate"></i> <span>Quản lý học sinh</span>
                </button>
                <button onclick="window.location.href='phanCongGiaoVien.php'">
                    <i class="fas fa-chalkboard-teacher"></i> <span>Phân công giảng dạy</span>
                </button>
                <button  onclick="window.location.href='xeplop.php'">
                    <i class="fas fa-calendar-alt"></i> <span>Xếp lớp</span>
                </button>
                <button class="active" onclick="window.location.href='danhSachLop.php'">
                    <i class="fas fa-calendar-alt"></i> <span>Xem danh sách lớp</span>
                </button>
                <button  onclick="window.location.href='chinhSuaTKB.php'">
                    <i class="fas fa-table"></i> <span>Xếp thời khóa biểu</span>
                </button>
                 <button class="logout-btn" onclick="dangXuat()">
                    <i class="fas fa-sign-out-alt"></i> <span>Đăng xuất</span>
                </button>
                
            </nav>
        </aside>

        <main id="mainContent" class="content">
            
            <div class="card" style="height: 100%; display: flex; flex-direction: column;">
                
                <div class="class-list-header">
                    <select class="class-dropdown" id="classSelect" onchange="updateClass()">
    <option value="">-- Chọn lớp --</option>
    <?php foreach($lops as $lop): ?>
        <option value="<?= htmlspecialchars($lop['MaLop']) ?>">
            Lớp <?= htmlspecialchars($lop['TenLop']) ?>
        </option>
    <?php endforeach; ?>
</select>
                    
                    <h2 class="page-title" id="classTitle">Danh sách học sinh lớp 12A1</h2>
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
                        <tbody id="tableBody">
                            </tbody>
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

    function loadUserProfile() {
        if (typeof currentUser !== 'undefined') {
            document.getElementById('userName').innerText = currentUser.name;
            document.getElementById('userCode').innerText = currentUser.code;
        }
    }

    async function updateClass() {
        const select = document.getElementById('classSelect');
        const maLop = select.value;
        const className = select.options[select.selectedIndex].text;
        const tbody = document.getElementById('tableBody');

        if (!maLop) {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding: 20px;">Vui lòng chọn một lớp cụ thể</td></tr>';
            document.getElementById('classTitle').innerText = "Danh sách học sinh";
            return;
        }

        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding: 20px;">Đang tải dữ liệu...</td></tr>';

        const formData = new FormData();
        formData.append('MaLop', maLop);

        try {
            const response = await fetch('api_danhsach.php', {
                method: 'POST',
                body: formData
            });
            
            const students = await response.json();
            renderTable(students, className);

        } catch (error) {
            console.error("Lỗi kết nối:", error);
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; color: red; padding: 20px;">Lỗi kết nối đến máy chủ!</td></tr>';
        }
    }

    function renderTable(students, className) {
        const tbody = document.getElementById('tableBody');
        tbody.innerHTML = '';

        if (students && students.length > 0) {
            students.forEach((hs, index) => {
                const row = `
                    <tr>
                        <td class="text-center">
                            <div class="cell-display">${index + 1}</div>
                        </td>
                        <td><div class="cell-display">${hs.HoTenHS}</div></td>
                        <td><div class="cell-display">${hs.MaHS}</div></td>
                        <td><div class="cell-display">${hs.GioiTinh || '---'}</div></td>
                        <td><div class="cell-display">${formatDate(hs.NgaySinh)}</div></td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding: 20px;">Lớp này hiện chưa có học sinh.</td></tr>';
        }

        document.getElementById('classTitle').innerText = "Danh sách học sinh " + className;
    }

    function formatDate(dateString) {
        if (!dateString) return '---';
        const date = new Date(dateString);
        if (isNaN(date)) return dateString;
        return date.toLocaleDateString('vi-VN');
    }

    window.onload = function() {
        loadUserProfile();

        const select = document.getElementById('classSelect');
        if (select.options.length > 1) {
            select.selectedIndex = 1; 
            updateClass();
        }
    };
</script>
</body>
</html>