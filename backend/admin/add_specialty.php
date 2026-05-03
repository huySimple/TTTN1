<?php
include '../config/connect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten_ck = $conn->real_escape_string($_POST['TenChuyenKhoa']);
    $mo_ta = !empty($_POST['MoTaChuyenKhoa']) ? "'" . $conn->real_escape_string($_POST['MoTaChuyenKhoa']) . "'" : "NULL";

    if (empty($ten_ck)) {
        $error = "Tên chuyên khoa không được để trống!";
    } else {
        // Kiểm tra chuyên khoa đã tồn tại chưa
        $check = $conn->query("SELECT MaChuyenKhoa FROM chuyenkhoa WHERE TenChuyenKhoa = '$ten_ck'");
        if ($check->num_rows > 0) {
            $error = "Tên chuyên khoa này đã tồn tại!";
        } else {
            $sql = "INSERT INTO chuyenkhoa (TenChuyenKhoa, MoTaChuyenKhoa) VALUES ('$ten_ck', $mo_ta)";
            if ($conn->query($sql)) {
                $success = "Thêm chuyên khoa mới thành công!";
                header("refresh:1.5;url=../../frontend/admin/admin.php?page=specialties");
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
    <title>Thêm Chuyên Khoa - Phòng khám TH</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/specialty.css">
</head>
<body>

<div class="form-card">
    <div class="form-header">
        <i class="fas fa-stethoscope" style="font-size: 24px; color: var(--primary);"></i>
        <h2>Thêm Chuyên Khoa Mới</h2>
    </div>

    <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Tên Chuyên Khoa <span style="color:red">*</span></label>
            <input type="text" name="TenChuyenKhoa" placeholder="Ví dụ: Nội tổng quát, Nhi khoa..." required>
        </div>

        <div class="form-group">
            <label>Mô tả chuyên khoa</label>
            <textarea name="MoTaChuyenKhoa" rows="4" placeholder="Mô tả sơ lược về chuyên khoa này..."></textarea>
        </div>

        <div class="btn-action-group">
            <button type="submit" class="btn btn-save">Lưu chuyên khoa</button>
            <a href="../../frontend/admin/admin.php?page=specialties" class="btn btn-cancel">Quay lại</a>
        </div>
    </form>
</div>

</body>
</html>