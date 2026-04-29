<?php
session_start();
include "../../backend/config/connect.php";

header('Content-Type: application/json');

// Kiểm tra kết nối DB
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Kết nối DB thất bại']);
    exit();
}

$maUser = $_SESSION['user_id'] ?? null;
if (!$maUser) {
    echo json_encode(['success' => false, 'message' => 'Session user_id không tồn tại']);
    exit();
}

// Lấy dữ liệu và khử độc dữ liệu (nếu không dùng bind_param)
$hoTen = $_POST['HoTen'] ?? '';
$sdt = $_POST['SoDienThoai'] ?? '';
$ngaySinh = !empty($_POST['NgaySinh']) ? $_POST['NgaySinh'] : null;
$gioiTinh = $_POST['GioiTinh'] ?? '';
$diaChi = $_POST['DiaChi'] ?? '';

// Câu lệnh SQL chính xác theo DB của bạn
$sql = "UPDATE benhnhan SET HoTen=?, SoDienThoai=?, NgaySinh=?, GioiTinh=?, DiaChi=? WHERE MaUser=?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(['success' => false, 'message' => 'Lỗi Prepare: ' . $conn->error]);
    exit();
}

$stmt->bind_param("sssssi", $hoTen, $sdt, $ngaySinh, $gioiTinh, $diaChi, $maUser);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        // Trường hợp nhấn lưu nhưng không thay đổi gì so với dữ liệu cũ
        echo json_encode(['success' => true, 'message' => 'Không có thay đổi nào được thực hiện']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi Execute: ' . $stmt->error]);
}
$stmt->close();
?>