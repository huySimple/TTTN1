<?php
include '../config/connect.php';

$error = '';
$success = '';
$specialty = [];

if (isset($_GET['id'])) {
    $ma_ck = $conn->real_escape_string($_GET['id']);
    // Truy vấn dữ liệu từ bảng chuyenkhoa
    $res = $conn->query("SELECT * FROM chuyenkhoa WHERE MaChuyenKhoa = '$ma_ck'");
    if ($res && $res->num_rows > 0) {
        $specialty = $res->fetch_assoc();
    } else {
        echo "Không tìm thấy chuyên khoa!";
        exit();
    }
} else {
    header("Location: ../../frontend/admin/admin.php?page=specialties");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten_ck = $conn->real_escape_string($_POST['TenChuyenKhoa']);
    // Sử dụng đúng tên cột MoTa
    $mo_ta = !empty($_POST['MoTa']) ? "'" . $conn->real_escape_string($_POST['MoTa']) . "'" : "NULL";

    if (empty($ten_ck)) {
        $error = "Tên chuyên khoa không được để trống!";
    } else {
        // Câu lệnh UPDATE chính xác theo bảng của bạn
        $sql = "UPDATE chuyenkhoa SET TenChuyenKhoa = '$ten_ck', MoTa = $mo_ta WHERE MaChuyenKhoa = '$ma_ck'";
        
        if ($conn->query($sql)) {
            $success = "Cập nhật chuyên khoa thành công!";
            // Load lại dữ liệu mới sau khi cập nhật
            $res = $conn->query("SELECT * FROM chuyenkhoa WHERE MaChuyenKhoa = '$ma_ck'");
            $specialty = $res->fetch_assoc();
            header("refresh:1.5;url=../../frontend/admin/admin.php?page=specialties");
        } else {
            $error = "Lỗi hệ thống: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Chuyên Khoa - Phòng khám TH</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #006ce3; --bg: #f4f6f9; --text: #333; }
        body { font-family: 'Segoe UI', sans-serif; background-color: var(--bg); margin: 0; padding: 40px 20px; display: flex; justify-content: center; }
        .form-card { background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); max-width: 500px; width: 100%; }
        .form-header { display: flex; align-items: center; gap: 12px; margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px; }
        .form-header h2 { margin: 0; font-size: 20px; color: var(--text); }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 600; font-size: 14px; }
        .form-group input, .form-group textarea { width: 100%; padding: 11px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 14px; }
        .btn-action-group { display: flex; gap: 10px; margin-top: 25px; }
        .btn { flex: 1; padding: 12px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; text-align: center; text-decoration: none; font-size: 15px; }
        .btn-save { background: var(--primary); color: white; }
        .btn-cancel { background: #6c757d; color: white; }
        .alert { padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>

<div class="form-card">
    <div class="form-header">
        <i class="fas fa-edit" style="font-size: 24px; color: var(--primary);"></i>
        <h2>Sửa Chuyên Khoa #<?= $specialty['MaChuyenKhoa'] ?></h2>
    </div>

    <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Tên Chuyên Khoa <span style="color:red">*</span></label>
            <input type="text" name="TenChuyenKhoa" value="<?= htmlspecialchars($specialty['TenChuyenKhoa']) ?>" required>
        </div>

        <div class="form-group">
            <label>Mô tả chuyên khoa</label>
            <textarea name="MoTa" rows="4"><?= htmlspecialchars($specialty['MoTa'] ?? '') ?></textarea>
        </div>

        <div class="btn-action-group">
            <button type="submit" class="btn btn-save">Cập nhật</button>
            <a href="../../frontend/admin/admin.php?page=specialties" class="btn btn-cancel">Quay lại</a>
        </div>
    </form>
</div>

</body>
</html>