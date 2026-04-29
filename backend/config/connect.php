<?php
$conn = new mysqli("localhost", "root", "", "phongkhamchuyennghiep");
if ($conn->connect_error) {
    die("Lỗi kết nối DB: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>