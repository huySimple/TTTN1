<?php
include '../config/connect.php';

if (isset($_GET['id'])) {
    $ma_user = $conn->real_escape_string($_GET['id']);

    // 1. Lấy vai trò của user này trước khi xóa
    $user_res = $conn->query("SELECT Role FROM users WHERE MaUser = '$ma_user'");
    if ($user_res && $user_res->num_rows > 0) {
        $user = $user_res->fetch_assoc();
        $role = $user['Role'];

        $conn->begin_transaction();
        try {
            if ($role == 'BenhNhan') {
                // Xóa thông tin liên quan đến bệnh nhân
                $bn_res = $conn->query("SELECT MaBenhNhan FROM benhnhan WHERE MaUser = '$ma_user'");
                if ($bn_res && $bn_res->num_rows > 0) {
                    $bn = $bn_res->fetch_assoc();
                    $ma_bn = $bn['MaBenhNhan'];

                    // Xóa kết quả khám và lịch hẹn
                    $lh_res = $conn->query("SELECT MaLichHen FROM lichhen WHERE MaBenhNhan = '$ma_bn'");
                    while($lh = $lh_res->fetch_assoc()) {
                        $ma_lh = $lh['MaLichHen'];
                        $conn->query("DELETE FROM ketquakham WHERE MaLichHen = '$ma_lh'");
                    }
                    $conn->query("DELETE FROM lichhen WHERE MaBenhNhan = '$ma_bn'");
                    $conn->query("DELETE FROM benhnhan WHERE MaBenhNhan = '$ma_bn'");
                }
            } elseif ($role == 'BacSi') {
                // Xóa thông tin liên quan đến bác sĩ
                $bs_res = $conn->query("SELECT MaBacSi FROM bacsi WHERE MaUser = '$ma_user'");
                if ($bs_res && $bs_res->num_rows > 0) {
                    $bs = $bs_res->fetch_assoc();
                    $ma_bs = $bs['MaBacSi'];

                    $conn->query("DELETE FROM lichlamviec WHERE MaBacSi = '$ma_bs'");
                    $lh_res = $conn->query("SELECT MaLichHen FROM lichhen WHERE MaBacSi = '$ma_bs'");
                    while($lh = $lh_res->fetch_assoc()) {
                        $ma_lh = $lh['MaLichHen'];
                        $conn->query("DELETE FROM ketquakham WHERE MaLichHen = '$ma_lh'");
                    }
                    $conn->query("DELETE FROM lichhen WHERE MaBacSi = '$ma_bs'");
                    $conn->query("DELETE FROM bacsi WHERE MaBacSi = '$ma_bs'");
                }
            } elseif ($role == 'Admin') {
                // Xóa thông tin trong bảng admin
                $conn->query("DELETE FROM admin WHERE MaUser = '$ma_user'");
            }

            // 2. Cuối cùng xóa tài khoản trong bảng users
            $conn->query("DELETE FROM users WHERE MaUser = '$ma_user'");

            $conn->commit();
            header("Location: ../../frontend/admin/admin.php?page=users");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            echo "Lỗi khi xóa tài khoản: " . $e->getMessage();
        }
    } else {
        header("Location: ../../frontend/admin/admin.php?page=users");
        exit();
    }
} else {
    header("Location: ../../frontend/admin/admin.php?page=users");
    exit();
}
?>