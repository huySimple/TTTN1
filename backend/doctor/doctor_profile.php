<?php
session_start();
include '../config/connect.php';

// Kiểm tra quyền truy cập của bác sĩ
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'BacSi' || !isset($_SESSION['user_id'])) {
    header("Location: ../../frontend/auth/login.php");
    exit();
}

$ma_user = $_SESSION['user_id'];
$error = '';
$success = '';

// 1. Lấy thông tin hiện tại của bác sĩ từ CSDL
$sql_bs = "SELECT bs.*, u.Email, u.MatKhau 
           FROM bacsi bs 
           INNER JOIN users u ON bs.MaUser = u.MaUser 
           WHERE bs.MaUser = '$ma_user'";
$res_bs = $conn->query($sql_bs);
$doctor = $res_bs->fetch_assoc();

if (!$doctor) {
    echo "Không tìm thấy hồ sơ bác sĩ!";
    exit();
}

// 2. Xử lý Cập nhật thông tin cá nhân
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action_profile'])) {
    $ho_ten = $conn->real_escape_string(trim($_POST['HoTen']));
    $sdt = $conn->real_escape_string(trim($_POST['SoDienThoai']));
    
    if (empty($ho_ten)) {
        $error = "Họ tên không được để trống!";
    } else {
        $sql_update = "UPDATE bacsi SET HoTen = '$ho_ten', SoDienThoai = '$sdt' WHERE MaUser = '$ma_user'";
        if ($conn->query($sql_update)) {
            $success = "Cập nhật hồ sơ thành công!";
            $doctor['HoTen'] = $ho_ten;
            $doctor['SoDienThoai'] = $sdt;
        } else {
            $error = "Có lỗi xảy ra: " . $conn->error;
        }
    }
}

// 3. Xử lý Đổi mật khẩu
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action_password'])) {
    $old_pass = trim($_POST['old_password']);
    $new_pass = trim($_POST['new_password']);
    $confirm_pass = trim($_POST['confirm_password']);

    if (empty($old_pass) || empty($new_pass) || empty($confirm_pass)) {
        $error = "Vui lòng nhập đầy đủ tất cả các trường mật khẩu!";
    } elseif ($new_pass !== $confirm_pass) {
        $error = "Mật khẩu mới và xác nhận mật khẩu không khớp nhau!";
    } elseif (strlen($new_pass) < 6) {
        $error = "Mật khẩu mới phải có độ dài từ 6 ký tự trở lên!";
    } else {
        // Kiểm tra xem mật khẩu cũ có chính xác không
        if (password_verify($old_pass, $doctor['MatKhau'])) {
            // Mã hóa mật khẩu mới trước khi lưu vào CSDL
            $hashed_pass = password_hash($new_pass, PASSWORD_BCRYPT);
            $sql_update_pass = "UPDATE users SET MatKhau = '$hashed_pass' WHERE MaUser = '$ma_user'";
            
            if ($conn->query($sql_update_pass)) {
                $success = "Đổi mật khẩu thành công!";
                // Cập nhật lại mật khẩu trong biến doctor để tránh dùng lại mật khẩu cũ
                $doctor['MatKhau'] = $hashed_pass;
            } else {
                $error = "Có lỗi xảy ra: " . $conn->error;
            }
        } else {
            $error = "Mật khẩu hiện tại không chính xác!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hồ sơ cá nhân và Đổi mật khẩu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="css/profile.css">
</head>
<body>

<div class="profile-wrapper">
    <div class="card">
        <div class="header-title">
            <i class="fas fa-id-card"></i> Thông tin cá nhân
        </div>

        <?php if ($success && isset($_POST['action_profile'])): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        <?php if ($error && isset($_POST['action_profile'])): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="action_profile" value="1">
            <div class="form-group">
                <label>Email tài khoản</label>
                <input type="text" value="<?= htmlspecialchars($doctor['Email']) ?>" disabled>
            </div>
            <div class="form-group">
                <label>Họ và Tên bác sĩ <span style="color:red">*</span></label>
                <input type="text" name="HoTen" value="<?= htmlspecialchars($doctor['HoTen']) ?>" required>
            </div>
            <div class="form-group">
                <label>Số điện thoại</label>
                <input type="text" name="SoDienThoai" value="<?= htmlspecialchars($doctor['SoDienThoai'] ?? '') ?>">
            </div>

            <button type="submit" class="btn btn-save">
                <i class="fas fa-save"></i> Lưu thông tin
            </button>
        </form>
    </div>

    <div class="card">
        <div class="header-title">
            <i class="fas fa-key"></i> Đổi mật khẩu
        </div>

        <?php if ($success && isset($_POST['action_password'])): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        <?php if ($error && isset($_POST['action_password'])): ?>
            <div class="alert alert-error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="action_password" value="1">
            <div class="form-group">
                <label>Mật khẩu hiện tại <span style="color:red">*</span></label>
                <input type="password" name="old_password" required placeholder="Nhập mật khẩu cũ">
            </div>
            <div class="form-group">
                <label>Mật khẩu mới <span style="color:red">*</span></label>
                <input type="password" name="new_password" required placeholder="Tối thiểu 6 ký tự">
            </div>
            <div class="form-group">
                <label>Xác nhận mật khẩu mới <span style="color:red">*</span></label>
                <input type="password" name="confirm_password" required placeholder="Nhập lại mật khẩu mới">
            </div>

            <button type="submit" class="btn btn-save" style="background-color: #10b981;">
                <i class="fas fa-check-circle"></i> Cập nhật mật khẩu
            </button>
        </form>
    </div>
</div>

<a href="doctor_dashboard.php" class="btn btn-back">
    <i class="fas fa-arrow-left"></i> Quay lại
</a>

</body>
</html>