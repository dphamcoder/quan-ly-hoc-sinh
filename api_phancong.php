<?php
require_once 'db.php';
$action = $_POST['action'] ?? '';

try {
    if ($action === 'assign') {
        $maGV = $_POST['MaGV'];
        $maLop = $_POST['MaLop'];
        $maMon = $_POST['MaMon'];

        $sqlDel = "DELETE FROM PCGD WHERE MaLop = :malop AND MaMon = :mamon";
        $stmtDel = $conn->prepare($sqlDel);
        $stmtDel->execute([':malop' => $maLop, ':mamon' => $maMon]);

        $sql = "INSERT INTO PCGD (MaGV, MaLop, MaMon) VALUES (:magv, :malop, :mamon)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':magv' => $maGV, ':malop' => $maLop, ':mamon' => $maMon]);
        echo "Phân công thành công!";
    }

    if ($action === 'remove') {
        $maLop = $_POST['MaLop'];
        $maMon = $_POST['MaMon'];
        $sql = "DELETE FROM PCGD WHERE MaLop = :malop AND MaMon = :mamon";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':malop' => $maLop, ':mamon' => $maMon]);
        echo "Đã xóa phân công!";
    }
    
    if ($action === 'fetch_assigned') {
        $maLop = $_POST['MaLop'];
        $sql = "SELECT p.*, g.HoTenGV FROM PCGD p JOIN giaovien g ON p.MaGV = g.MaGV WHERE p.MaLop = :malop";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':malop' => $maLop]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
} catch (PDOException $e) {
    echo "Lỗi: " . $e->getMessage();
}