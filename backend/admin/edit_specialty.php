<?php
include '../../backend/config/connect.php';

$id = $_GET['id'];
$res = $conn->query("SELECT * FROM chuyenkhoa WHERE MaChuyenKhoa = $id");
$row = $res->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten = $_POST['TenChuyenKhoa'];
    $mota = $_POST['MoTa'];

    $conn->query("UPDATE chuyenkhoa SET TenChuyenKhoa='$ten', MoTa='$mota' WHERE MaChuyenKhoa=$id");

    header("Location: ../../frontend/admin/admin.php?page=specialties");
}
?>

<form method="POST">
    <h2>Sửa chuyên khoa</h2>
    <input type="text" name="TenChuyenKhoa" value="<?= $row['TenChuyenKhoa'] ?>" required>
    <textarea name="MoTa"><?= $row['MoTa'] ?></textarea>
    <button type="submit">Cập nhật</button>
</form>