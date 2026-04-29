<?php
session_start();
include "../../backend/config/connect.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Hết phiên làm việc']);
    exit();
}

$maUser = $_SESSION['user_id'];
$current_pwd = $_POST['current_pwd'] ?? '';
$new_pwd = $_POST['new_pwd'] ?? '';

// 1. Lấy mật khẩu hiện tại từ DB
$sql = "SELECT MatKhau FROM users WHERE MaUser = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $maUser);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// 2. Kiểm tra mật khẩu cũ (Sử dụng password_verify vì trong DB của bạn đang dùng Bcrypt)
if (!password_verify($current_pwd, $user['MatKhau'])) {
    echo json_encode(['success' => false, 'message' => 'Mật khẩu hiện tại không chính xác']);
    exit();
}

// 3. Mã hóa mật khẩu mới và cập nhật
$hashed_pwd = password_hash($new_pwd, PASSWORD_BCRYPT);
$update_sql = "UPDATE users SET MatKhau = ? WHERE MaUser = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("si", $hashed_pwd, $maUser);

if ($update_stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống, vui lòng thử lại sau']);
}
?>