<?php
include '../../backend/config/connect.php';

$id = $_GET['id'];

$conn->query("DELETE FROM bacsi WHERE MaBacSi = $id");

header("Location: ../../frontend/admin/admin.php?page=doctors");
exit();