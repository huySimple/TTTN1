<?php
include '../../backend/config/connect.php';

$id = $_GET['id'];
$conn->query("DELETE FROM chuyenkhoa WHERE MaChuyenKhoa = $id");

header("Location: ../../frontend/admin/admin.php?page=specialties");