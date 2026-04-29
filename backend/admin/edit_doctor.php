<?php
include '../../backend/config/connect.php';

$id = $_GET['id'];

$res = $conn->query("SELECT * FROM bacsi WHERE MaBacSi = $id");
$data = $res->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hoten = $_POST['HoTen'];
    $chuyenkhoa = $_POST['MaChuyenKhoa'];
    $trangthai = $_POST['TrangThai'];

    $sql = "UPDATE bacsi SET HoTen=?, MaChuyenKhoa=?, TrangThai=? WHERE MaBacSi=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisi", $hoten, $chuyenkhoa, $trangthai, $id);

    if ($stmt->execute()) {
        header("Location: ../../frontend/admin/admin.php?page=doctors");
        exit();
    }
}
?>

<form method="POST" class="add-form">
    <h2>Sửa bác sĩ</h2>

    <input type="text" name="HoTen" value="<?= $data['HoTen'] ?>" required>

    <select name="MaChuyenKhoa">
        <?php
        $res = $conn->query("SELECT * FROM chuyenkhoa");
        while($row = $res->fetch_assoc()):
        ?>
            <option value="<?= $row['MaChuyenKhoa'] ?>"
                <?= $row['MaChuyenKhoa'] == $data['MaChuyenKhoa'] ? 'selected' : '' ?>>
                <?= $row['TenChuyenKhoa'] ?>
            </option>
        <?php endwhile; ?>
    </select>

    <select name="TrangThai">
    <option value="NhanLich" <?= $data['TrangThai'] == 'NhanLich' ? 'selected' : '' ?>>
        Nhận lịch
    </option>
    <option value="KhongNhanLich" <?= $data['TrangThai'] == 'KhongNhanLich' ? 'selected' : '' ?>>
        Không nhận lịch
    </option>
    <option value="NghiPhep" <?= $data['TrangThai'] == 'NghiPhep' ? 'selected' : '' ?>>
        Nghỉ phép
    </option>
</select>

    <button class="btn-add">Cập nhật</button>
</form>