<?php
include '../config/connect.php';

if (isset($_GET['id'])) {
    $ma_bs = $conn->real_escape_string($_GET['id']);

    // Tìm MaUser để xóa tài khoản liên quan
    $check = $conn->query("SELECT MaUser FROM bacsi WHERE MaBacSi = '$ma_bs'");
    if ($check && $check->num_rows > 0) {
        $doctor = $check->fetch_assoc();
        $ma_user = $doctor['MaUser'];

        $conn->begin_transaction();
        try {
            // 1. Xóa tất cả lịch làm việc của bác sĩ
            $conn->query("DELETE FROM lichlamviec WHERE MaBacSi = '$ma_bs'");

            // 2. Lấy ra tất cả các MaLichHen của bác sĩ này để xóa kết quả khám trước
            $lich_hen_res = $conn->query("SELECT MaLichHen FROM lichhen WHERE MaBacSi = '$ma_bs'");
            while($lh = $lich_hen_res->fetch_assoc()){
                $ma_lh = $lh['MaLichHen'];
                $conn->query("DELETE FROM ketquakham WHERE MaLichHen = '$ma_lh'");
            }
            
            // 3. Xóa các lịch hẹn
            $conn->query("DELETE FROM lichhen WHERE MaBacSi = '$ma_bs'");

            // 4. Xóa thông tin bác sĩ
            $conn->query("DELETE FROM bacsi WHERE MaBacSi = '$ma_bs'");

            // 5. Xóa tài khoản users của bác sĩ
            if ($ma_user) {
                $conn->query("DELETE FROM users WHERE MaUser = '$ma_user'");
            }

            $conn->commit();
            header("Location: ../../frontend/admin/admin.php?page=doctors");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            echo "Lỗi khi xóa bác sĩ: " . $e->getMessage();
        }
    } else {
        echo "Không tìm thấy bác sĩ!";
    }
} else {
    header("Location: ../../frontend/admin/admin.php?page=doctors");
    exit();
}
?>