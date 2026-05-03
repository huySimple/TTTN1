<?php
// backend/admin/confirm_appointment.php

// Thiết lập trả về dữ liệu dạng JSON
header('Content-Type: application/json; charset=utf-8');

// Kết nối CSDL
$host = 'localhost';
$db   = 'phongkhamchuyennghiep';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (\PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi kết nối CSDL']);
    exit();
}

// Lấy ID lịch hẹn
$maLichHen = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($maLichHen <= 0) {
    echo json_encode(['success' => false, 'message' => 'Mã lịch hẹn không hợp lệ.']);
    exit();
}

try {
    // Cập nhật trạng thái thành 'DaXacNhan'
    $sqlUpdate = "UPDATE `lichhen` SET `TrangThai` = 'DaXacNhan' WHERE `MaLichHen` = :id";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->execute(['id' => $maLichHen]);

    // Trả về JSON thành công, không chuyển trang
    echo json_encode(['success' => true, 'message' => 'Xác nhận thành công!']);
    exit();

} catch (\PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
    exit();
}