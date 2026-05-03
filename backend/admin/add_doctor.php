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

    // Mặc định ban đầu ảnh đại diện là NULL
    $anh_dai_dien_path = "NULL";

    // Xử lý Upload file ảnh từ máy tính
    if (isset($_FILES['anh_dai_dien']) && $_FILES['anh_dai_dien']['error'] == 0) {
        $file_name = $_FILES['anh_dai_dien']['name'];
        $file_size = $_FILES['anh_dai_dien']['size'];
        $file_tmp  = $_FILES['anh_dai_dien']['tmp_name'];
        
        // Lấy đuôi file (extension)
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        // Kiểm tra định dạng file ảnh hợp lệ
        if (!in_array($ext, $allowed_extensions)) {
            $error = "Chỉ cho phép tải lên các định dạng ảnh: " . implode(', ', $allowed_extensions);
        }
        // Kiểm tra kích thước file (Giới hạn tối đa 2MB)
        elseif ($file_size > 2 * 1024 * 1024) {
            $error = "Kích thước ảnh quá lớn! Vui lòng chọn ảnh dưới 2MB.";
        } else {
            // Đổi tên file để tránh bị trùng lặp
            $new_file_name = "doctor_" . time() . "_" . rand(100, 999) . "." . $ext;
            
            // Đường dẫn lưu file trên máy chủ
            $upload_dir = '../../frontend/admin/uploads/';
            
            // Tạo thư mục nếu nó chưa tồn tại
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $target_file = $upload_dir . $new_file_name;

            // Di chuyển file tạm vào thư mục lưu trữ
            if (move_uploaded_file($file_tmp, $target_file)) {
                // Đường dẫn tương đối lưu vào Database
                $db_path = "uploads/" . $new_file_name;
                $anh_dai_dien_path = "'" . $conn->real_escape_string($db_path) . "'";
            } else {
                $error = "Không thể lưu file ảnh lên máy chủ.";
            }
        }
    }

    if (empty($ho_ten) || empty($email) || empty($mat_khau)) {
        $error = "Vui lòng nhập đầy đủ Họ tên, Email và Mật khẩu!";
    } elseif (empty($error)) {
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
                               VALUES ($ma_user, '$ho_ten', $sdt, $ma_ck, '$trang_thai', $anh_dai_dien_path, $gioi_thieu)";
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

    <form method="POST" action="" enctype="multipart/form-data">
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

            <div class="form-group full-width">
                <label>Ảnh đại diện bác sĩ</label>
                <div class="img-upload-container">
                    <img id="imgPreview" src="https://via.placeholder.com/150" alt="Preview" class="preview-img">
                    
                    <div>
                        <input type="file" name="anh_dai_dien" id="fileInput" accept="image/*">
                        <small style="color: #64748b; display: block; margin-top: 5px;">Hỗ trợ: JPG, PNG, GIF, WEBP. Kích thước tối đa 2MB.</small>
                    </div>
                </div>
            </div>

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

<script>
    const fileInput = document.getElementById('fileInput');
    const imgPreview = document.getElementById('imgPreview');

    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imgPreview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>

</body>
</html>