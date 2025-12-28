<?php
require_once 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $maLop = $_POST['MaLop'] ?? '';

    if (empty($maLop)) {
        echo json_encode([]);
        exit;
    }

    try {
        $stmt = $conn->prepare("SELECT MaHS, HoTenHS, GioiTinh, NgaySinh FROM hocsinh WHERE MaLop = ?");
        $stmt->execute([$maLop]);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($students);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}