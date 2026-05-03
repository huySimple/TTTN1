<?php
include '../config/connect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['Email']);
    $mat_khau = $_POST['MatKhau'];
    $role = $conn->real_escape_string($_POST['Role']);
    $trang_thai_email = $conn->real_escape_string($_POST['TrangThaiEmail']);

    if (empty($email) || empty($mat_khau) || empty($role)) {
        $error = "Vui lòng điền đầy đủ các thông tin bắt buộc!";
    } else {
        // Kiểm tra Email đã tồn tại chưa
        $check_email = $conn->query("SELECT MaUser FROM users WHERE Email = '$email'");
        if ($check_email->num_rows > 0) {
            $error = "Email này đã tồn tại trên hệ thống!";
        } else {
            // Mã hóa mật khẩu chuẩn Bcrypt giống file SQL của bạn
            $hashed_password = password_hash($mat_khau, PASSWORD_BCRYPT, ['cost' => 12]);
            
            $sql = "INSERT INTO users (Email, MatKhau, Role, TrangThaiEmail, Created_at) 
                    VALUES ('$email', '$hashed_password', '$role', '$trang_thai_email', NOW())";
            
            if ($conn->query($sql)) {
                $success = "Thêm tài khoản thành công!";
                header("refresh:1.5;url=../../frontend/admin/admin.php?page=users");
            } else {
                $error = "Lỗi hệ thống: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Tài Khoản - Phòng khám TH</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/user.css">
</head>
<body>

<div class="form-card">
    <div class="form-header">
        <i class="fas fa-user-plus" style="font-size: 24px; color: var(--primary);"></i>
        <h2>Tạo Tài Khoản Mới</h2>
    </div>

    <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Địa chỉ Email <span style="color:red">*</span></label>
            <input type="email" name="Email" placeholder="Ví dụ: user@gmail.com" required>
        </div>

        <div class="form-group">
            <label>Mật khẩu <span style="color:red">*</span></label>
            <input type="password" name="MatKhau" placeholder="Nhập ít nhất 6 ký tự" required>
        </div>

        <div class="form-group">
            <label>Vai trò / Phân quyền <span style="color:red">*</span></label>
            <select name="Role" required>
                <option value="BenhNhan">Bệnh Nhân (BenhNhan)</option>
                <option value="BacSi">Bác Sĩ (BacSi)</option>
            </select>
        </div>

        <!-- <div class="form-group">
            <label>Trạng thái Email</label>
            <select name="TrangThaiEmail">
                <option value="DaXacThuc">Đã Xác Thực</option>
                <option value="ChuaXacThuc">Chưa Xác Thực</option>
            </select>
        </div> -->

        <div class="btn-action-group">
            <button type="submit" class="btn btn-save">Tạo tài khoản</button>
            <a href="../../frontend/admin/admin.php?page=users" class="btn btn-cancel">Quay lại</a>
        </div>
    </form>
</div>

</body>
</html>