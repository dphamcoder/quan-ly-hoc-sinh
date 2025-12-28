<?php
require_once '../db.php';
header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? '';

// Nhận dữ liệu
$maHS = $_POST['MaHS'] ?? '';
$tenHS = $_POST['HoTenHS'] ?? '';
$maLop = $_POST['MaLop'] ?? '';
$gioiTinh = $_POST['GioiTinh'] ?? '';
$sdt = $_POST['SoDT'] ?? '';
$email = $_POST['Email'] ?? '';
$hoKhau = $_POST['HoKhau'] ?? '';
$danToc = $_POST['DanToc'] ?? '';
$tonGiao = $_POST['TonGiao'] ?? '';
$hoTenBo = $_POST['HoTenBo'] ?? '';
$ngheBo = $_POST['NgheNghiepBo'] ?? '';
$hoTenMe = $_POST['HoTenMe'] ?? '';
$ngheMe = $_POST['NgheNghiepMe'] ?? '';
$maTruong = 'THPT01'; 

try {
    if ($action === 'add') {
        // --- 1. THÊM MỚI ---
        if (empty($maHS) || empty($tenHS)) {
            echo "Vui lòng nhập Mã HS và Họ tên!"; exit;
        }

        // Kiểm tra trùng mã
        $check = $conn->prepare("SELECT MaHS FROM HOCSINH WHERE MaHS = ?");
        $check->execute([$maHS]);
        if ($check->rowCount() > 0) {
            echo "Lỗi: Mã học sinh '$maHS' đã tồn tại!"; exit;
        }

        $sql = "INSERT INTO HOCSINH (MaHS, HoTenHS, MaLop, GioiTinh, S0DT, Email, HoKhau, DanToc, TonGiao, HoTenBo, NgheNghiepBo, HoTenMe, NgheNghiepMe, MaTruong, MaTinhTrang) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Đang học')";
        
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([$maHS, $tenHS, $maLop, $gioiTinh, $sdt, $email, $hoKhau, $danToc, $tonGiao, $hoTenBo, $ngheBo, $hoTenMe, $ngheMe, $maTruong]);

        if ($result) echo "Thêm học sinh thành công!";
        else echo "Thêm thất bại!";
    } 
    
    elseif ($action === 'edit') {
        // --- 2. CẬP NHẬT ---
        // Lúc này MaHS bị khóa ở giao diện, nên ta dùng chính MaHS đó để tìm dòng cần sửa
        $sql = "UPDATE HOCSINH SET 
                HoTenHS=?, MaLop=?, GioiTinh=?, S0DT=?, Email=?, HoKhau=?, 
                DanToc=?, TonGiao=?, HoTenBo=?, NgheNghiepBo=?, HoTenMe=?, NgheNghiepMe=?
                WHERE MaHS=?";
        
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([$tenHS, $maLop, $gioiTinh, $sdt, $email, $hoKhau, $danToc, $tonGiao, $hoTenBo, $ngheBo, $hoTenMe, $ngheMe, $maHS]);

        if ($result) echo "Cập nhật thành công!";
        else echo "Cập nhật thất bại (Có thể dữ liệu không thay đổi)!";
    } 
    
    elseif ($action === 'delete') {
        // --- 3. XÓA ---
        try {
            // Xóa tài khoản liên quan (nếu có)
            $conn->prepare("DELETE FROM TAIKHOAN WHERE MaTK = ?")->execute([$maHS]);
            
            // Xóa học sinh
            $stmt = $conn->prepare("DELETE FROM HOCSINH WHERE MaHS = ?");
            if ($stmt->execute([$maHS])) echo "Xóa thành công!";
            else echo "Xóa thất bại!";
        } catch (PDOException $e) {
            echo "Không thể xóa: Học sinh này đang có điểm số hoặc dữ liệu liên quan!";
        }
    } 
    else {
        echo "Hành động không hợp lệ!";
    }

} catch (PDOException $e) {
    echo "Lỗi Database: " . $e->getMessage();
}
?>