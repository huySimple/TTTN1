<?php
include '../config/connect.php';

$error = '';
$success = '';
$row = [];

// 1. Lấy thông tin bệnh nhân hiện tại để đổ vào Form
if (isset($_GET['id'])) {
    $ma_bn = $conn->real_escape_string($_GET['id']);
    $res = $conn->query("SELECT * FROM benhnhan WHERE MaBenhNhan = '$ma_bn'");
    
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
    } else {
        echo "Không tìm thấy bệnh nhân!";
        exit();
    }
} else {
    header("Location: ../../frontend/admin/admin.php?page=patients");
    exit();
}

// 2. Xử lý cập nhật thông tin khi Admin bấm nút Lưu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ho_ten = $conn->real_escape_string($_POST['HoTen']);
    $ngay_sinh = !empty($_POST['NgaySinh']) ? "'" . $conn->real_escape_string($_POST['NgaySinh']) . "'" : "NULL";
    $gioi_tinh = !empty($_POST['GioiTinh']) ? "'" . $conn->real_escape_string($_POST['GioiTinh']) . "'" : "NULL";
    $sdt = !empty($_POST['SoDienThoai']) ? "'" . $conn->real_escape_string($_POST['SoDienThoai']) . "'" : "NULL";
    $dia_chi = !empty($_POST['DiaChi']) ? "'" . $conn->real_escape_string($_POST['DiaChi']) . "'" : "NULL";
    $nhom_mau = !empty($_POST['NhomMau']) ? "'" . $conn->real_escape_string($_POST['NhomMau']) . "'" : "NULL";
    $di_ung = !empty($_POST['DiUng']) ? "'" . $conn->real_escape_string($_POST['DiUng']) . "'" : "NULL";

    if (empty($ho_ten)) {
        $error = "Họ tên bệnh nhân không được để trống!";
    } else {
        // Câu lệnh UPDATE dữ liệu bệnh nhân
        $sql = "UPDATE benhnhan SET 
                HoTen = '$ho_ten', 
                NgaySinh = $ngay_sinh, 
                GioiTinh = $gioi_tinh, 
                SoDienThoai = $sdt, 
                DiaChi = $dia_chi, 
                NhomMau = $nhom_mau, 
                DiUng = $di_ung 
                WHERE MaBenhNhan = '$ma_bn'";
        
        if ($conn->query($sql)) {
            $success = "Cập nhật thông tin bệnh nhân thành công!";
            // Tải lại dữ liệu mới nhất để hiển thị lại trên form
            $res = $conn->query("SELECT * FROM benhnhan WHERE MaBenhNhan = '$ma_bn'");
            $row = $res->fetch_assoc();
            
            // Chuyển hướng về trang danh sách sau 1.5 giây
            header("refresh:1.5;url=../../frontend/admin/admin.php?page=patients");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Bệnh Nhân - Phòng khám TH</title>
    <link rel="stylesheet" href="css/patient.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   
</head>
<body>

<div class="form-card">
    <div class="form-header">
        <i class="fas fa-user-edit"></i>
        <h2>Chỉnh Sửa Hồ Sơ Bệnh Nhân #<?= $row['MaBenhNhan'] ?></h2>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-grid">
            <div class="form-group full-width">
                <label for="HoTen">Họ và Tên <span style="color:red">*</span></label>
                <input type="text" id="HoTen" name="HoTen" value="<?= htmlspecialchars($row['HoTen']) ?>" required>
            </div>

            <div class="form-group">
                <label for="NgaySinh">Ngày sinh</label>
                <input type="date" id="NgaySinh" name="NgaySinh" value="<?= $row['NgaySinh'] ?>">
            </div>

            <div class="form-group">
                <label for="GioiTinh">Giới tính</label>
                <select id="GioiTinh" name="GioiTinh">
                    <option value="">-- Chọn giới tính --</option>
                    <option value="Nam" <?= $row['GioiTinh'] == 'Nam' ? 'selected' : '' ?>>Nam</option>
                    <option value="Nu" <?= $row['GioiTinh'] == 'Nu' ? 'selected' : '' ?>>Nữ</option>
                    <option value="Khac" <?= $row['GioiTinh'] == 'Khac' ? 'selected' : '' ?>>Khác</option>
                </select>
            </div>

            <div class="form-group">
                <label for="SoDienThoai">Số điện thoại</label>
                <input type="text" id="SoDienThoai" name="SoDienThoai" value="<?= htmlspecialchars($row['SoDienThoai'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="NhomMau">Nhóm máu</label>
                <select id="NhomMau" name="NhomMau">
                    <option value="">Không xác định</option>
                    <?php 
                    $nhom_mau_array = ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'];
                    foreach($nhom_mau_array as $mau):
                    ?>
                        <option value="<?= $mau ?>" <?= $row['NhomMau'] == $mau ? 'selected' : '' ?>><?= $mau ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group full-width">
                <label for="DiaChi">Địa chỉ thường trú</label>
                <input type="text" id="DiaChi" name="DiaChi" value="<?= htmlspecialchars($row['DiaChi'] ?? '') ?>">
            </div>

            <div class="form-group full-width">
                <label for="DiUng">Tiền sử dị ứng (Nếu có)</label>
                <textarea id="DiUng" name="DiUng" rows="2"><?= htmlspecialchars($row['DiUng'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="btn-action-group">
            <button type="submit" class="btn btn-save">Cập nhật</button>
            <a href="../../frontend/admin/admin.php?page=patients" class="btn btn-cancel">Quay lại</a>
        </div>
    </form>
</div>

</body>
</html>