<?php
include '../config/connect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ho_ten = $conn->real_escape_string($_POST['HoTen']);
    $ngay_sinh = !empty($_POST['NgaySinh']) ? "'" . $conn->real_escape_string($_POST['NgaySinh']) . "'" : "NULL";
    $gioi_tinh = !empty($_POST['GioiTinh']) ? "'" . $conn->real_escape_string($_POST['GioiTinh']) . "'" : "NULL";
    $sdt = !empty($_POST['SoDienThoai']) ? "'" . $conn->real_escape_string($_POST['SoDienThoai']) . "'" : "NULL";
    $dia_chi = !empty($_POST['DiaChi']) ? "'" . $conn->real_escape_string($_POST['DiaChi']) . "'" : "NULL";
    $nhom_mau = !empty($_POST['NhomMau']) ? "'" . $conn->real_escape_string($_POST['NhomMau']) . "'" : "NULL";
    $di_ung = !empty($_POST['DiUng']) ? "'" . $conn->real_escape_string($_POST['DiUng']) . "'" : "NULL";

    // Validate dữ liệu bắt buộc
    if (empty($ho_ten)) {
        $error = "Họ tên bệnh nhân không được để trống!";
    } else {
        // Thực thi chèn dữ liệu vào bảng benhnhan
        $sql = "INSERT INTO benhnhan (HoTen, NgaySinh, GioiTinh, SoDienThoai, DiaChi, NhomMau, DiUng) 
                VALUES ('$ho_ten', $ngay_sinh, $gioi_tinh, $sdt, $dia_chi, $nhom_mau, $di_ung)";
        
        if ($conn->query($sql)) {
            $success = "Thêm bệnh nhân thành công!";
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
    <title>Thêm Bệnh Nhân - Phòng khám TH</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/patient.css">
    <style>
     
    </style>
</head>
<body>

<div class="form-card">
    <div class="form-header">
        <i class="fas fa-user-plus"></i>
        <h2>Thêm Bệnh Nhân Mới</h2>
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
                <input type="text" id="HoTen" name="HoTen" placeholder="Ví dụ: Nguyễn Văn An" required>
            </div>

            <div class="form-group">
                <label for="NgaySinh">Ngày sinh</label>
                <input type="date" id="NgaySinh" name="NgaySinh">
            </div>

            <div class="form-group">
                <label for="GioiTinh">Giới tính</label>
                <select id="GioiTinh" name="GioiTinh">
                    <option value="">-- Chọn giới tính --</option>
                    <option value="Nam">Nam</option>
                    <option value="Nu">Nữ</option>
                    <option value="Khac">Khác</option>
                </select>
            </div>

            <div class="form-group">
                <label for="SoDienThoai">Số điện thoại</label>
                <input type="text" id="SoDienThoai" name="SoDienThoai" placeholder="Ví dụ: 0912345678">
            </div>

            <div class="form-group">
                <label for="NhomMau">Nhóm máu</label>
                <select id="NhomMau" name="NhomMau">
                    <option value="">Không xác định</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                </select>
            </div>

            <div class="form-group full-width">
                <label for="DiaChi">Địa chỉ thường trú</label>
                <input type="text" id="DiaChi" name="DiaChi" placeholder="Số nhà, Tên đường, Quận/Huyện, Tỉnh/Thành phố">
            </div>

            <div class="form-group full-width">
                <label for="DiUng">Tiền sử dị ứng (Nếu có)</label>
                <textarea id="DiUng" name="DiUng" rows="2" placeholder="Ví dụ: Dị ứng penicillin, hải sản..."></textarea>
            </div>
        </div>

        <div class="btn-action-group">
            <button type="submit" class="btn btn-save">Lưu hồ sơ</button>
            <a href="../../frontend/admin/admin.php?page=patients" class="btn btn-cancel">Quay lại</a>
        </div>
    </form>
</div>

</body>
</html>