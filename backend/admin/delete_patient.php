<?php
include '../config/connect.php';

if (isset($_GET['id'])) {
    $ma_bn = $conn->real_escape_string($_GET['id']);

    // 1. Lấy tất cả MaLichHen của bệnh nhân này để xóa kết quả khám trước
    $lich_hen_res = $conn->query("SELECT MaLichHen FROM lichhen WHERE MaBenhNhan = '$ma_bn'");
    while ($row = $lich_hen_res->fetch_assoc()) {
        $ma_lh = $row['MaLichHen'];
        $conn->query("DELETE FROM ketquakham WHERE MaLichHen = '$ma_lh'");
    }

    // 2. Xóa các lịch hẹn liên quan đến bệnh nhân này
    $conn->query("DELETE FROM lichhen WHERE MaBenhNhan = '$ma_bn'");

    // 3. Xóa chính bệnh nhân trong bảng benhnhan
    $sql = "DELETE FROM benhnhan WHERE MaBenhNhan = '$ma_bn'";

    if ($conn->query($sql)) {
        // Chuyển hướng về trang danh sách bệnh nhân sau khi xóa thành công
        header("Location: ../../frontend/admin/admin.php?page=patients");
        exit();
    } else {
        echo "Lỗi khi xóa bệnh nhân: " . $conn->error;
    }
} else {
    header("Location: ../../frontend/admin/admin.php?page=patients");
    exit();
}
?>