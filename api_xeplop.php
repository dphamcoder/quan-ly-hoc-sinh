<?php
require_once 'db.php';
header('Content-Type: application/json');

error_reporting(0);
ini_set('display_errors', 0);

$action = $_POST['action'] ?? '';

if ($action == 'get_students') {
    $maLopXet = $_POST['MaLop'] ?? '';
    
    try {
        $sqlAdded = "SELECT h.MaHS, h.HoTenHS, l.TenLop as TenLopHienTai 
                     FROM hocsinh h 
                     LEFT JOIN lophoc l ON h.MaLop = l.MaLop 
                     WHERE h.MaLop = ?";
        $stmt1 = $conn->prepare($sqlAdded);
        $stmt1->execute([$maLopXet]);
        $added = $stmt1->fetchAll(PDO::FETCH_ASSOC);

        $sqlAvail = "SELECT h.MaHS, h.HoTenHS, l.TenLop as TenLopHienTai 
                     FROM hocsinh h 
                     LEFT JOIN lophoc l ON h.MaLop = l.MaLop 
                     WHERE h.MaLop != ? OR h.MaLop IS NULL OR h.MaLop = ''";
        $stmt2 = $conn->prepare($sqlAvail);
        $stmt2->execute([$maLopXet]);
        $available = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        $stmt3 = $conn->prepare("SELECT MaGV_CN FROM lophoc WHERE MaLop = ?");
        $stmt3->execute([$maLopXet]);
        $gvcn = $stmt3->fetchColumn();

        echo json_encode([
            'status' => 'success',
            'added' => $added,
            'available' => $available,
            'gvcn' => $gvcn ? $gvcn : ""
        ]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action == 'save_class') {
    $maLop = $_POST['MaLop'];
    $gvcn = $_POST['MaGVCN'];
    $studentIds = json_decode($_POST['studentIds']);

    try {
        $conn->beginTransaction();

        $stmt1 = $conn->prepare("UPDATE lophoc SET MaGV_CN = ? WHERE MaLop = ?");
        $stmt1->execute([$gvcn, $maLop]);

        $stmt2 = $conn->prepare("UPDATE hocsinh SET MaLop = NULL WHERE MaLop = ?");
        $stmt2->execute([$maLop]);

        if (!empty($studentIds)) {
            $placeholders = implode(',', array_fill(0, count($studentIds), '?'));
            $sql = "UPDATE hocsinh SET MaLop = ? WHERE MaHS IN ($placeholders)";
            $stmt3 = $conn->prepare($sql);
            $stmt3->execute(array_merge([$maLop], $studentIds));
        }

        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Lưu dữ liệu thành công!']);
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}