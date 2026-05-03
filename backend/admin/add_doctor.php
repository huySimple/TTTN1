<?php
include '../config/connect.php';

$error = '';
$success = '';

// Lấy danh sách chuyên khoa để hiển thị trong select box
$chuyen_khoa_res = $conn->query("SELECT * FROM chuyenkhoa");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ho_ten = $conn->real_escape_string($_POST['HoTen']);
    $email = $conn->real_escape_string($_POST['Email']);
    $mat_khau = $_POST['MatKhau'];
    $sdt = !empty($_POST['SoDienThoai']) ? "'" . $conn->real_escape_string($_POST['SoDienThoai']) . "'" : "NULL";
    $ma_ck = !empty($_POST['MaChuyenKhoa']) ? "'" . $conn->real_escape_string($_POST['MaChuyenKhoa']) . "'" : "NULL";
    $trang_thai = $conn->real_escape_string($_POST['TrangThai']);
    $gioi_thieu = !empty($_POST['gioithieu_banthan']) ? "'" . $conn->real_escape_string($_POST['gioithieu_banthan']) . "'" : "NULL";
    $anh_dai_dien = !empty($_POST['anh_dai_dien']) ? "'" . $conn->real_escape_string($_POST['anh_dai_dien']) . "'" : "NULL";

    if (empty($ho_ten) || empty($email) || empty($mat_khau)) {
        $error = "Vui lòng nhập đầy đủ Họ tên, Email và Mật khẩu!";
    } else {
        // Kiểm tra xem Email đã tồn tại trong bảng users chưa
        $check_email = $conn->query("SELECT MaUser FROM users WHERE Email = '$email'");
        if ($check_email->num_rows > 0) {
            $error = "Email này đã được sử dụng bởi một tài khoản khác!";
        } else {
            $conn->begin_transaction();
            try {
                // 1. Mã hóa mật khẩu và tạo tài khoản users
                $hashed_password = password_hash($mat_khau, PASSWORD_BCRYPT, ['cost' => 12]);
                $sql_user = "INSERT INTO users (Email, MatKhau, Role, TrangThaiEmail, Created_at) 
                             VALUES ('$email', '$hashed_password', 'BacSi', 'DaXacThuc', NOW())";
                $conn->query($sql_user);
                $ma_user = $conn->insert_id;

                // 2. Chèn thông tin vào bảng bacsi
                $sql_doctor = "INSERT INTO bacsi (MaUser, HoTen, SoDienThoai, MaChuyenKhoa, TrangThai, anh_dai_dien, gioithieu_banthan) 
                               VALUES ($ma_user, '$ho_ten', $sdt, $ma_ck, '$trang_thai', $anh_dai_dien, $gioi_thieu)";
                $conn->query($sql_doctor);

                $conn->commit();
                $success = "Thêm bác sĩ và tạo tài khoản thành công!";
                header("refresh:1.5;url=../../frontend/admin/admin.php?page=doctors");
            } catch (Exception $e) {
                $conn->rollback();
                $error = "Lỗi hệ thống: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Bác Sĩ - Phòng khám TH</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/doctor.css">
</head>
<body>

<div class="form-card">
    <div class="form-header">
        <i class="fas fa-user-md" style="font-size: 24px; color: var(--primary);"></i>
        <h2>Thêm Bác Sĩ Mới</h2>
    </div>

    <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

    <form method="POST" action="">
        <div class="form-grid">
            <div class="form-group full-width" style="border-bottom: 1px dashed #ddd; padding-bottom: 10px;">
                <strong><i class="fas fa-key"></i> Thông tin tài khoản</strong>
            </div>

            <div class="form-group">
                <label>Email Đăng nhập <span style="color:red">*</span></label>
                <input type="email" name="Email" placeholder="Ví dụ: bs.nguyen@phongkham.com" required>
            </div>

            <div class="form-group">
                <label>Mật khẩu <span style="color:red">*</span></label>
                <input type="password" name="MatKhau" placeholder="Nhập ít nhất 6 ký tự" required>
            </div>

            <div class="form-group full-width" style="border-bottom: 1px dashed #ddd; padding-bottom: 10px; margin-top: 10px;">
                <strong><i class="fas fa-id-card"></i> Thông tin cá nhân bác sĩ</strong>
            </div>

            <div class="form-group full-width">
                <label>Họ và Tên Bác Sĩ <span style="color:red">*</span></label>
                <input type="text" name="HoTen" placeholder="Ví dụ: BS. Nguyễn Văn A" required>
            </div>

            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="text" name="SoDienThoai" placeholder="Ví dụ: 0912345678">
            </div>

            <div class="form-group">
                <label>Chuyên Khoa</label>
                <select name="MaChuyenKhoa">
                    <option value="">-- Chọn chuyên khoa --</option>
                    <?php while($ck = $chuyen_khoa_res->fetch_assoc()): ?>
                        <option value="<?= $ck['MaChuyenKhoa'] ?>"><?= htmlspecialchars($ck['TenChuyenKhoa']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Trạng Thái Làm Việc</label>
                <select name="TrangThai">
                    <option value="NhanLich">Nhận Lịch</option>
                    <option value="KhongNhanLich">Không Nhận Lịch</option>
                    <option value="NghiPhep">Nghỉ Phép</option>
                </select>
            </div>

            <!-- <div class="form-group">
                <label>Đường dẫn Ảnh đại diện (URL)</label>
                <input type="text" name="anh_dai_dien" placeholder="http://localhost:8000/storage/avatars/...">
            </div> -->

            <div class="form-group full-width">
                <label>Giới thiệu bản thân</label>
                <textarea name="gioithieu_banthan" rows="3" placeholder="Mô tả kinh nghiệm, học vấn..."></textarea>
            </div>
        </div>

        <div class="btn-action-group">
            <button type="submit" class="btn btn-save">Lưu Bác Sĩ</button>
            <a href="../../frontend/admin/admin.php?page=doctors" class="btn btn-cancel">Quay lại</a>
        </div>
    </form>
</div>

</body>
</html>