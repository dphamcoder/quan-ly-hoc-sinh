<?php
session_start();
require_once "db.php";

$loginMessage = "";
$loginSuccess = false;
$loginPath = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');


    $sql = "SELECT * FROM TAIKHOAN
            WHERE MaTK = :u
              AND TrangThai = 1
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->execute(['u' => $username]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && $password === $row['MatKhau']) {

        $userData = $row; 
        $role = $row['ChucVu'];

        if ($role == 'GIAOVIEN') {
            $stmtGV = $conn->prepare("SELECT HoTenGV FROM GIAOVIEN WHERE MaGV = :maGV");
            $stmtGV->execute(['maGV' => $row['MaTK']]); 
            $gvInfo = $stmtGV->fetch(PDO::FETCH_ASSOC);

            if ($gvInfo) {
                $userData['TenHienThi'] = $gvInfo['HoTenGV']; 
            } else {
                $userData['TenHienThi'] = $row['TenTK']; 
            }
            $loginPath = 'danhSachLopGV.php';

        } elseif ($role == 'CANBO') {
            $userData['TenHienThi'] = $row['TenTK'];
            $loginPath = 'quanLyTaiKhoan.php';

        } elseif ($role == 'HOCSINH') {
            $userData['TenHienThi'] = $row['TenTK'];
            $loginPath = 'thongTinCaNhan.php';

        } else {

            $loginMessage = "Tài khoản không có quyền truy cập!";
            $row = null; 
        }

        if ($row) {
            $_SESSION['user'] = $userData;
            $loginSuccess = true;
            $loginMessage = "Đăng nhập thành công!";
        }

    } else {
        $loginMessage = "Sai tên đăng nhập hoặc mật khẩu!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Hệ thống</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="test.css"> <style>
        .login-result {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            font-weight: 600;
        }
        .success {
            color: #155724;
            background: #d4edda;
            border: 1px solid #c3e6cb;
        }
        .error {
            color: #721c24;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-container">

        <div class="login-sidebar">
            <div class="school-logo">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h1>HỆ THỐNG QUẢN LÝ </h1>
            <h1>HỌC SINH THPT</h1>
            <h2>Trường THPT Bình Yên</h2>
            <div class="system-info">
                <p><i class="fas fa-shield-alt"></i> Bảo mật dữ liệu chuẩn ISO</p>
                <p><i class="fas fa-bolt"></i> Tra cứu điểm số tức thì</p>
                <p><i class="fas fa-mobile-alt"></i> Hỗ trợ App Mobile</p>
            </div>
        </div>

        <div class="login-content">
            <div class="login-header">
                <h1>ĐĂNG NHẬP</h1>
                <p>Nhập thông tin tài khoản nhà trường cung cấp</p>
            </div>

            <?php if ($loginMessage): ?>
                <div class="login-result <?= $loginSuccess ? 'success' : 'error' ?>">
                    <?= $loginMessage ?>
                </div>
            <?php endif; ?>

            <form id="loginForm" method="post">
                <div class="form-group">
                    <label>Tên đăng nhập</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" name="username" id="username"
                               placeholder="Mã học sinh / Mã giáo viên" required 
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Mật khẩu</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" id="password"
                               placeholder="Nhập mật khẩu" required>
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" id="rememberMe">
                        <span class="checkmark"></span>
                        Ghi nhớ đăng nhập
                    </label>
                    <a href="#" style="color: var(--primary-color); text-decoration: none;">Quên mật khẩu?</a>
                </div>

                <button type="submit" class="login-btn" onclick="localStorage.user = username.value">
                    <i class="fas fa-sign-in-alt"></i>
                    ĐĂNG NHẬP
                </button>
            </form>

            <div class="support-info">
                <p><i class="fas fa-headset"></i> Hỗ trợ kỹ thuật: 090.123.4567</p>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    <?php if ($loginSuccess): ?>
        setTimeout(() => {
            window.location.href = "<?= $loginPath ?>";
        }, 1500);
    <?php endif; ?>
</script>

</body>
</html>