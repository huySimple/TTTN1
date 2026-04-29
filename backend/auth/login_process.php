<?php
session_start();
include "../config/connect.php";

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE Email='$email'";
$result = $conn->query($sql);

if($result->num_rows > 0){
    $user = $result->fetch_assoc();

    if(password_verify($password, $user['MatKhau'])){
        // LẤY TÊN HIỂN THỊ (Quan trọng để không bị kẹt)
        $maUser = $user['MaUser'];
        $hoTen = "Thành viên"; // Mặc định

        // Truy vấn lấy tên từ bảng BenhNhan nếu là bệnh nhân
        if($user['Role'] == 'BenhNhan'){
            $res_name = $conn->query("SELECT HoTen FROM benhnhan WHERE MaUser = $maUser");
            if($row = $res_name->fetch_assoc()) $hoTen = $row['HoTen'];
        }

        // GÁN SESSION (Phải khớp với file index.php)
        $_SESSION['user_id'] = $user['MaUser']; 
        $_SESSION['ho_ten'] = $hoTen;
        $_SESSION['role'] = $user['Role'];

        // Chuyển hướng
        if($user['Role'] == 'Admin'){
            header("Location: ../../frontend/admin/admin.php");
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