<?php
session_start();
include "../config/connect.php";

$user = $_SESSION['user'];

$MaUser = $user['MaUser'];

// lấy MaBenhNhan
$bn = $conn->query("SELECT * FROM benhnhan WHERE MaUser='$MaUser'")
          ->fetch_assoc();

$MaBenhNhan = $bn['MaBenhNhan'];

$MaBacSi = $_POST['MaBacSi'];
$NgayHen = $_POST['NgayHen'];
$MaTimeSlot = $_POST['MaTimeSlot'];
$GhiChu = $_POST['GhiChu'];

// lấy giờ từ timeslot
$ts = $conn->query("SELECT GioBatDau FROM timeslot WHERE MaTimeSlot='$MaTimeSlot'")
           ->fetch_assoc();

$NgayHenFull = $NgayHen . " " . $ts['GioBatDau'];

$sql = "INSERT INTO lichhen(MaBenhNhan, MaBacSi, NgayHen, GhiChuBenhNhan, MaTimeSlot)
        VALUES('$MaBenhNhan','$MaBacSi','$NgayHenFull','$GhiChu','$MaTimeSlot')";

if($conn->query($sql)){
    echo "<script>alert('Đặt lịch thành công');window.location='../../frontend/user/index.php';</script>";
}else{
    echo "Lỗi: " . $conn->error;
}