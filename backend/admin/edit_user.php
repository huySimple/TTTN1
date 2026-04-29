<?php
include '../../backend/config/connect.php';

$id = $_GET['id'];

$res = $conn->query("SELECT * FROM users WHERE MaUser = $id");
$user = $res->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['Email'];
    $role = $_POST['Role'];

    $sql = "UPDATE users SET Email=?, Role=? WHERE MaUser=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $email, $role, $id);

    if ($stmt->execute()) {
        header("Location: ../../frontend/admin/admin.php?page=users");
        exit();
    }
}
?>

<form method="POST" class="add-form">
    <h2>Sửa tài khoản</h2>

    <label>Email:</label>
    <input type="email" name="Email" value="<?= htmlspecialchars($user['Email']) ?>" required>

    <label>Quyền hạn:</label>
    <select name="Role">
        <option value="Admin" <?= $user['Role'] == 'Admin' ? 'selected' : '' ?>>
            Quản trị viên (Admin)
        </option>
        
        <option value="BacSi" <?= $user['Role'] == 'BacSi' ? 'selected' : '' ?>>
            Bác sĩ
        </option>
        
        <option value="BenhNhan" <?= $user['Role'] == 'BenhNhan' ? 'selected' : '' ?>>
            Bệnh nhân
        </option>
    </select>

    <button type="submit" class="btn-add">Cập nhật</button>
</form>