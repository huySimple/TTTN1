<?php
include '../../backend/config/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['Email']);
    $password = password_hash($_POST['Password'], PASSWORD_DEFAULT);
    $role = $_POST['Role'];
    $trangthai = 'ChuaXacThuc'; 


    $sql = "INSERT INTO users (Email, MatKhau, Role) VALUES (?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $email, $password, $role);

    if ($stmt->execute()) {
        echo "<script>alert('Thêm tài khoản thành công!'); window.location='../../frontend/admin/admin.php?page=users';</script>";
        exit();
    } else {
        echo "Lỗi: " . $stmt->error;
    }
    $stmt->close();
}
?>

<link rel="stylesheet" href="./css/admin_style.css">
<div class="form-container">
    <form method="POST" class="add-form">
        <h2> Thêm tài khoản mới</h2>

        <label>Email đăng nhập:</label>
        <input type="email" name="Email" placeholder="ví dụ: admin@gmail.com" required>

        <label>Mật khẩu:</label>
        <input type="password" name="Password" placeholder="Nhập mật khẩu" required>

        <label>Quyền hạn:</label>
        <select name="Role">
            <option value="BacSi">Bác sĩ</option>
            <option value="BenhNhan">Bệnh nhân</option>
        </select>

        <button type="submit" class="btn-add">Tạo tài khoản</button>
        <a href="../../frontend/admin/admin.php?page=users" class="btn-cancel">Hủy bỏ</a>
    </form>
</div>