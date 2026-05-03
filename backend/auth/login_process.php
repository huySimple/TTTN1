<?php
session_start();
include "../config/connect.php";

$email = $_POST['email'];
$password = $_POST['password'];

// Tránh SQL Injection
$email = $conn->real_escape_string($email);

$sql = "SELECT * FROM users WHERE Email='$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['MatKhau'])) {
        $maUser = $user['MaUser'];
        $hoTen = "Thành viên"; // Mặc định
        $maBacSi = null;
        $maBenhNhan = null;

        // 1. Nếu là Admin
        if ($user['Role'] == 'Admin') {
            $res_admin = $conn->query("SELECT HoTen FROM admin WHERE MaUser = $maUser");
            if ($res_admin && $row = $res_admin->fetch_assoc()) {
                $hoTen = $row['HoTen'];
            }
        } 
        // 2. Nếu là Bác sĩ -> Lấy thêm MaBacSi và HoTen từ bảng bacsi
        elseif ($user['Role'] == 'BacSi') {
            $res_doctor = $conn->query("SELECT MaBacSi, HoTen FROM bacsi WHERE MaUser = $maUser");
            if ($res_doctor && $row = $res_doctor->fetch_assoc()) {
                $hoTen = $row['HoTen'];
                $maBacSi = $row['MaBacSi'];
            }
        } 
        // 3. Nếu là Bệnh nhân
        elseif ($user['Role'] == 'BenhNhan') {
            $res_patient = $conn->query("SELECT MaBenhNhan, HoTen FROM benhnhan WHERE MaUser = $maUser");
            if ($res_patient && $row = $res_patient->fetch_assoc()) {
                $hoTen = $row['HoTen'];
                $maBenhNhan = $row['MaBenhNhan'];
            }
        }

        // GÁN SESSION
        $_SESSION['user_id'] = $user['MaUser']; 
        $_SESSION['ho_ten'] = $hoTen;
        $_SESSION['role'] = $user['Role'];
        
        // Lưu ID cụ thể để sử dụng truy vấn ở các trang chức năng
        if ($maBacSi) $_SESSION['doctor_id'] = $maBacSi;
        if ($maBenhNhan) $_SESSION['patient_id'] = $maBenhNhan;

        // CHUYỂN HƯỚNG THEO VAI TRÒ (ROLE)
        if ($user['Role'] == 'Admin') {
            header("Location: ../../frontend/admin/admin.php");
        } elseif ($user['Role'] == 'BacSi') {
            // Chuyển hướng bác sĩ đến trang dashboard của bác sĩ
            header("Location: ../../backend/doctor/doctor_dashboard.php");
        } else {
            header("Location: ../../index.php");
        }
        exit();
    } else {
        echo "Sai mật khẩu";
    }
} else {
    echo "Không tồn tại email";
}
?>