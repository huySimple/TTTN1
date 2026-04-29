<?php
include "../config/connect.php";

$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$hoten = $_POST['hoten'];

$conn->query("INSERT INTO users(Email, MatKhau, Role)
              VALUES('$email','$password','BenhNhan')");

$user_id = $conn->insert_id;

$conn->query("INSERT INTO benhnhan(MaUser, HoTen)
              VALUES('$user_id','$hoten')");

header("Location: ../../frontend/auth/login.php");