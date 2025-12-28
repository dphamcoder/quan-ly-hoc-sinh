<?php
session_start();
// Kết nối database
require_once "db.php";

// 1. CHẶN QUYỀN TRUY CẬP: Chỉ cho phép HOCSINH vào
if (!isset($_SESSION['user']) || $_SESSION['user']['ChucVu'] != 'HOCSINH') {
    // Nếu chưa đăng nhập hoặc không phải học sinh -> Đuổi về trang đăng nhập
    header("Location: dangNhap.php");
    exit();
}

// 2. LẤY DỮ LIỆU TỪ SESSION
$currentUser = $_SESSION['user'];
$maHS = $currentUser['MaTK']; // Đây chính là Mã học sinh

// 3. TRUY VẤN DỮ LIỆU CHI TIẾT TỪ DB
$hocSinh = null;
try {
    $conn = getConnection();
    // Lấy thông tin HS + Tên Lớp + Tên Trường
    $sql = "SELECT H.*, L.TenLop, T.TenTruong 
            FROM HOCSINH H
            LEFT JOIN LOPHOC L ON H.MaLop = L.MaLop
            LEFT JOIN TRUONG T ON H.MaTruong = T.MaTruong
            WHERE H.MaHS = :maHS";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute(['maHS' => $maHS]);
    $hocSinh = $stmt->fetch(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("Lỗi hệ thống: " . $e->getMessage());
}

if (!$hocSinh) {
    die("Không tìm thấy hồ sơ học sinh có mã: " . htmlspecialchars($maHS));
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="test.css">
</head>
<body>
    <button id="toggleSidebar" class="toggle-btn">
        <i class="fas fa-bars"></i>
    </button>

    <div class="container">
        
        <aside id="sidebar" class="sidebar">
            <div class="teacher-card">
                <div class="avatar"></div>
                <h3><?= htmlspecialchars($hocSinh['HoTenHS'] ?? '') ?></h3>
                <p>MSHS: <?= htmlspecialchars($maHS) ?></p>
            </div>

            <nav>
                <button class="active" onclick="window.location.href='thongTinCaNhan.php'">
                    <i class="fas fa-user"></i> <span>Thông tin cá nhân</span>
                </button>
                <button onclick="window.location.href='xemThoiKhoaBieu.php'">
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
            
            <h2 class="page-title"><i class="fas fa-id-card"></i> Hồ sơ học sinh</h2>

            <div class="card info-form">
                
                <div class="profile-header">
                    <div class="profile-avatar-large"></div>
                    <h2 style="margin: 0; color: var(--text-dark);"><?= htmlspecialchars($hocSinh['HoTenHS']) ?></h2>
                    <p style="color: #666; font-weight: bold;">Lớp: <?= htmlspecialchars($hocSinh['TenLop'] ?? $hocSinh['MaLop']) ?></p>
                    <span class="status-badge status-active" style="margin-top:5px; display:inline-block;">
                        <?= htmlspecialchars($hocSinh['MaTinhTrang']) ?>
                    </span>
                </div>

                <form>
                    <div class="section-title"><i class="fas fa-user"></i> Thông tin cá nhân</div>
                    
                    <div class="row-2-cols">
                        <div class="form-group">
                            <label>Mã học sinh:</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($hocSinh['MaHS']) ?>" readonly style="background-color: #e9ecef;">
                        </div>
                        <div class="form-group">
                            <label>Mã định danh:</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($hocSinh['MaDinhDanh']) ?>" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Họ và tên:</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($hocSinh['HoTenHS']) ?>" style="font-weight: bold;" readonly>
                    </div>

                    <div class="row-3-cols">
                        <div class="form-group">
                            <label>Ngày sinh:</label>
                            <input type="text" class="form-control" value="<?= date('d/m/Y', strtotime($hocSinh['NgaySinh'])) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Giới tính:</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($hocSinh['GioiTinh']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Lớp:</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($hocSinh['MaLop']) ?>" readonly>
                        </div>
                    </div>

                    <div class="row-2-cols">
                        <div class="form-group">
                            <label>Dân tộc:</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($hocSinh['DanToc']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Tôn giáo:</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($hocSinh['TonGiao']) ?>" readonly>
                        </div>
                    </div>

                    <div class="section-title"><i class="fas fa-map-marker-alt"></i> Thông tin liên hệ</div>

                    <div class="row-2-cols">
                        <div class="form-group">
                            <label>Số điện thoại:</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($hocSinh['S0DT']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($hocSinh['Email']) ?>" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Hộ khẩu thường trú:</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($hocSinh['HoKhau']) ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label>Quê quán:</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($hocSinh['QueQuan']) ?>" readonly>
                    </div>

                    <div class="section-title"><i class="fas fa-users"></i> Thông tin gia đình</div>

                    <div class="row-2-cols">
                        <div class="form-group">
                            <label>Họ tên Bố:</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($hocSinh['HoTenBo']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Nghề nghiệp Bố:</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($hocSinh['NgheNghiepBo']) ?>" readonly>
                        </div>
                    </div>

                    <div class="row-2-cols">
                        <div class="form-group">
                            <label>Họ tên Mẹ:</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($hocSinh['HoTenMe']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Nghề nghiệp Mẹ:</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($hocSinh['NgheNghiepMe']) ?>" readonly>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="thongBao.js"></script>
    <script>
        // Xử lý Sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggleSidebar');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');

            if (toggleBtn && sidebar && mainContent) {
                // Khôi phục trạng thái sidebar từ localStorage (nếu có)
                const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (isCollapsed) {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('expanded');
                    toggleBtn.classList.add('moved');
                    toggleBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
                }

                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('expanded');
                    toggleBtn.classList.toggle('moved');

                    if (sidebar.classList.contains('collapsed')) {
                        toggleBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
                        localStorage.setItem('sidebarCollapsed', 'true');
                    } else {
                        toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
                        localStorage.setItem('sidebarCollapsed', 'false');
                    }
                });
            }
        });

        function dangXuat() {
            if(confirm('Bạn có chắc chắn muốn đăng xuất?')) window.location.href = 'dangNhap.php';
        }
    </script>
    
    <style>
        .section-title {
            color: var(--primary-color);
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin: 25px 0 15px 0;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .row-2-cols { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .row-3-cols { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
        @media (max-width: 768px) {
            .row-2-cols, .row-3-cols { grid-template-columns: 1fr; gap: 0; }
        }
    </style>
</body>
</html>