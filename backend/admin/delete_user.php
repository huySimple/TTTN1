<?php
include '../../backend/config/connect.php';

$id = $_GET['id'];

$conn->query("DELETE FROM users WHERE MaUser = $id");

header("Location: ../../frontend/admin/admin.php?page=users");
exit();