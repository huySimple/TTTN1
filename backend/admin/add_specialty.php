<?php
include '../../backend/config/connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten = $_POST['TenChuyenKhoa'];
    $mota = $_POST['MoTa'];

    $sql = "INSERT INTO chuyenkhoa (TenChuyenKhoa, MoTa) VALUES ('$ten', '$mota')";
    $conn->query($sql);

    header("Location: ../../frontend/admin/admin.php?page=specialties");
}
?>

<form method="POST">
    <h2>Thêm chuyên khoa</h2>
    <input type="text" name="TenChuyenKhoa" placeholder="Tên chuyên khoa" required>
    <textarea name="MoTa" placeholder="Mô tả"></textarea>
    <button type="submit">Thêm</button>
</form>