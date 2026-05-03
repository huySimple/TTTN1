<?php
include '../config/connect.php';

$error = '';
$success = '';
$user = [];

if (isset($_GET['id'])) {
    $ma_user = $conn->real_escape_string($_GET['id']);
    $res = $conn->query("SELECT * FROM users WHERE MaUser = '$ma_user'");
    if ($res && $res->num_rows > 0) {
        $user = $res->fetch_assoc();
    } else {
        echo "Không tìm thấy tài khoản!";
        exit();
    }
} else {
    header("Location: ../../frontend/admin/admin.php?page=users");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $conn->real_escape_string($_POST['Role']);
    $trang_thai_email = $conn->real_escape_string($_POST['TrangThaiEmail']);
    $mat_khau_moi = $_POST['MatKhauMoi'];

    // Cập nhật thông tin cơ bản
    $sql_update = "UPDATE users SET Role = '$role', TrangThaiEmail = '$trang_thai_email' WHERE MaUser = '$ma_user'";
    
    if ($conn->query($sql_update)) {
        // Nếu có nhập mật khẩu mới thì cập nhật thêm mật khẩu
        if (!empty($mat_khau_moi)) {
            $hashed_password = password_hash($mat_khau_moi, PASSWORD_BCRYPT, ['cost' => 12]);
            $conn->query("UPDATE users SET MatKhau = '$hashed_password' WHERE MaUser = '$ma_user'");
        }

        $success = "Cập nhật tài khoản thành công!";
        // Load lại dữ liệu
        $res = $conn->query("SELECT * FROM users WHERE MaUser = '$ma_user'");
        $user = $res->fetch_assoc();
        header("refresh:1.5;url=../../frontend/admin/admin.php?page=users");
    } else {
        $error = "Lỗi hệ thống: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Tài Khoản - Phòng khám TH</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/user.css">
</head>
<body>

<div class="form-card">
    <div class="form-header">
        <i class="fas fa-user-edit" style="font-size: 24px; color: var(--primary);"></i>
        <h2>Chỉnh Sửa Tài Khoản #<?= $user['MaUser'] ?></h2>
    </div>

    <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Địa chỉ Email (Không thể sửa)</label>
            <input type="text" value="<?= htmlspecialchars($user['Email']) ?>" disabled style="background: #f1f3f5;">
        </div>

        <div class="form-group">
            <label>Đổi mật khẩu mới (Nếu có)</label>
            <input type="password" name="MatKhauMoi" placeholder="Để trống nếu giữ nguyên mật khẩu cũ">
        </div>

        <div class="form-group">
            <label>Vai trò / Phân quyền <span style="color:red">*</span></label>
            <select name="Role" required>
                <option value="BenhNhan" <?= $user['Role'] == 'BenhNhan' ? 'selected' : '' ?>>Bệnh Nhân (BenhNhan)</option>
                <option value="BacSi" <?= $user['Role'] == 'BacSi' ? 'selected' : '' ?>>Bác Sĩ (BacSi)</option>
            </select>
        </div>

        <!-- <div class="form-group">
            <label>Trạng thái xác thực Email</label>
            <select name="TrangThaiEmail">
                <option value="DaXacThuc" <?= $user['TrangThaiEmail'] == 'DaXacThuc' ? 'selected' : '' ?>>Đã Xác Thực</option>
                <option value="ChuaXacThuc" <?= $user['TrangThaiEmail'] == 'ChuaXacThuc' ? 'selected' : '' ?>>Chưa Xác Thực</option>
            </select>
        </div> -->

        <div class="btn-action-group">
            <button type="submit" class="btn btn-save">Cập nhật</button>
            <a href="../../frontend/admin/admin.php?page=users" class="btn btn-cancel">Quay lại</a>
        </div>
    </form>
</div>

</body>
</html>