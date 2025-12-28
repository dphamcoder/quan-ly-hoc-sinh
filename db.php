<?php
$host = "localhost";
$dbname = "quanlytruonghoc";
$user = "root";
$pass = "123456789";

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $pass
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Lỗi kết nối DB: " . $e->getMessage());
}

function getConnection() {
    $host = "localhost";
    $dbname = "quanlytruonghoc";
    $username = "root";
    $password = "123456789";

    return new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );
}

?>