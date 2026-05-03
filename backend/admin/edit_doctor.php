<?php
include '../config/connect.php';

$error = '';
$success = '';
$doctor = [];

// 1. Lấy thông tin hiện tại của bác sĩ từ ID
if (isset($_GET['id'])) {
    $ma_bs = $conn->real_escape_string($_GET['id']);
    $res = $conn->query("SELECT b.*, u.Email 
                         FROM bacsi b 
                         INNER JOIN users u ON b.MaUser = u.MaUser 
                         WHERE b.MaBacSi = '$ma_bs'");
    if ($res && $res->num_rows > 0) {
        $doctor = $res->fetch_assoc();
    } else {
        echo "Không tìm thấy thông tin bác sĩ!";
        exit();
    }
} else {
    header("Location: ../../frontend/admin/admin.php?page=doctors");
    exit();
}

$chuyen_khoa_res = $conn->query("SELECT * FROM chuyenkhoa");

// 2. Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ho_ten = $conn->real_escape_string($_POST['HoTen']);
    $sdt = !empty($_POST['SoDienThoai']) ? "'" . $conn->real_escape_string($_POST['SoDienThoai']) . "'" : "NULL";
    $ma_ck = !empty($_POST['MaChuyenKhoa']) ? "'" . $conn->real_escape_string($_POST['MaChuyenKhoa']) . "'" : "NULL";
    $trang_thai = $conn->real_escape_string($_POST['TrangThai']);
    $gioi_thieu = !empty($_POST['gioithieu_banthan']) ? "'" . $conn->real_escape_string($_POST['gioithieu_banthan']) . "'" : "NULL";
    $anh_dai_dien = !empty($_POST['anh_dai_dien']) ? "'" . $conn->real_escape_string($_POST['anh_dai_dien']) . "'" : "NULL";
    $mat_khau_moi = $_POST['MatKhauMoi'];

    if (empty($ho_ten)) {
        $error = "Họ tên bác sĩ không được để trống!";
    } else {
        $conn->begin_transaction();
        try {
            // Cập nhật bảng bacsi
            $sql_update_bs = "UPDATE bacsi SET 
                                HoTen = '$ho_ten', 
                                SoDienThoai = $sdt, 
                                MaChuyenKhoa = $ma_ck, 
                                TrangThai = '$trang_thai', 
                                anh_dai_dien = $anh_dai_dien, 
                                gioithieu_banthan = $gioi_thieu 
                              WHERE MaBacSi = '$ma_bs'";
            $conn->query($sql_update_bs);

            // Cập nhật mật khẩu mới cho users nếu có nhập
            if (!empty($mat_khau_moi)) {
                $ma_user = $doctor['MaUser'];
                $hashed_password = password_hash($mat_khau_moi, PASSWORD_BCRYPT, ['cost' => 12]);
                $conn->query("UPDATE users SET MatKhau = '$hashed_password' WHERE MaUser = '$ma_user'");
            }

            $conn->commit();
            $success = "Cập nhật hồ sơ bác sĩ thành công!";
            
            // Lấy lại dữ liệu sau khi sửa
            $res = $conn->query("SELECT b.*, u.Email FROM bacsi b INNER JOIN users u ON b.MaUser = u.MaUser WHERE b.MaBacSi = '$ma_bs'");
            $doctor = $res->fetch_assoc();

            header("refresh:1.5;url=../../frontend/admin/admin.php?page=doctors");
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Lỗi hệ thống: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Bác Sĩ - Phòng khám TH</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/doctor.css">
</head>
<body>

<div class="form-card">
    <div class="form-header">
        <i class="fas fa-user-edit" style="font-size: 24px; color: var(--primary);"></i>
        <h2>Chỉnh Sửa Bác Sĩ #<?= $doctor['MaBacSi'] ?></h2>
    </div>

    <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

    <form method="POST" action="">
        <div class="form-grid">
            <div class="form-group full-width">
                <label>Họ và Tên Bác Sĩ <span style="color:red">*</span></label>
                <input type="text" name="HoTen" value="<?= htmlspecialchars($doctor['HoTen']) ?>" required>
            </div>

            <div class="form-group">
                <label>Email Đăng nhập (Không thể sửa)</label>
                <input type="text" value="<?= htmlspecialchars($doctor['Email']) ?>" disabled style="background: #f1f3f5;">
            </div>

            <div class="form-group">
                <label>Đặt lại mật khẩu mới (Nếu có)</label>
                <input type="password" name="MatKhauMoi" placeholder="Để trống nếu không đổi">
            </div>

            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="text" name="SoDienThoai" value="<?= htmlspecialchars($doctor['SoDienThoai'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Chuyên Khoa</label>
                <select name="MaChuyenKhoa">
                    <option value="">-- Chọn chuyên khoa --</option>
                    <?php while($ck = $chuyen_khoa_res->fetch_assoc()): ?>
                        <option value="<?= $ck['MaChuyenKhoa'] ?>" <?= $doctor['MaChuyenKhoa'] == $ck['MaChuyenKhoa'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ck['TenChuyenKhoa']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Trạng Thái Làm Việc</label>
                <select name="TrangThai">
                    <option value="NhanLich" <?= $doctor['TrangThai'] == 'NhanLich' ? 'selected' : '' ?>>Nhận Lịch</option>
                    <option value="KhongNhanLich" <?= $doctor['TrangThai'] == 'KhongNhanLich' ? 'selected' : '' ?>>Không Nhận Lịch</option>
                    <option value="NghiPhep" <?= $doctor['TrangThai'] == 'NghiPhep' ? 'selected' : '' ?>>Nghỉ Phép</option>
                </select>
            </div>

            <div class="form-group">
                <label>Đường dẫn Ảnh đại diện (URL)</label>
                <input type="text" name="anh_dai_dien" value="<?= htmlspecialchars($doctor['anh_dai_dien'] ?? '') ?>">
            </div>

            <div class="form-group full-width">
                <label>Giới thiệu bản thân</label>
                <textarea name="gioithieu_banthan" rows="3"><?= htmlspecialchars($doctor['gioithieu_banthan'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="btn-action-group">
            <button type="submit" class="btn btn-save">Cập nhật</button>
            <a href="../../frontend/admin/admin.php?page=doctors" class="btn btn-cancel">Quay lại</a>
        </div>
    </form>
</div>

</body>
</html>