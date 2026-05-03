<?php
include '../config/connect.php';

if (isset($_GET['id'])) {
    $ma_ck = $conn->real_escape_string($_GET['id']);

    $conn->begin_transaction();
    try {
        // 1. Cập nhật MaChuyenKhoa = NULL cho tất cả bác sĩ đang thuộc chuyên khoa này
        $conn->query("UPDATE bacsi SET MaChuyenKhoa = NULL WHERE MaChuyenKhoa = '$ma_ck'");

        // 2. Tiến hành xóa chuyên khoa khỏi database
        $conn->query("DELETE FROM chuyenkhoa WHERE MaChuyenKhoa = '$ma_ck'");

        $conn->commit();
        header("Location: ../../frontend/admin/admin.php?page=specialties");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "Lỗi khi xóa chuyên khoa: " . $e->getMessage();
    }
} else {
    header("Location: ../../frontend/admin/admin.php?page=specialties");
    exit();
}
?>