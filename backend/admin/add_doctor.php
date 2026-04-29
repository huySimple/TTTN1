<?php
include '../../backend/config/connect.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hoten = $_POST['HoTen'];
    $chuyenkhoa = $_POST['MaChuyenKhoa'];
    $trangthai = $_POST['TrangThai'];
    $email = $_POST['Email'];
    // Mã hóa mật khẩu để bảo mật
    $password = password_hash($_POST['Password'], PASSWORD_DEFAULT);

    // Bắt đầu Transaction để đảm bảo nếu 1 trong 2 bảng lỗi thì sẽ không lưu bảng kia
    $conn->begin_transaction();

    try {
        // 1. Thêm vào bảng users trước
        $sql_user = "INSERT INTO users (Email, Password, Role, Created_at) VALUES (?, ?, 'BacSi', NOW())";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("ss", $email, $password);
        $stmt_user->execute();

        // Lấy MaUser vừa mới tạo tự động
        $new_user_id = $conn->insert_id;

        // 2. Thêm vào bảng bacsi với MaUser vừa lấy được
        $sql_bacsi = "INSERT INTO bacsi (HoTen, MaChuyenKhoa, TrangThai, MaUser) VALUES (?, ?, ?, ?)";
        $stmt_bacsi = $conn->prepare($sql_bacsi);
        $stmt_bacsi->bind_param("sisi", $hoten, $chuyenkhoa, $trangthai, $new_user_id);
        $stmt_bacsi->execute();

        // Nếu mọi thứ ổn thì xác nhận lưu vào DB
        $conn->commit();

        header("Location: ../../frontend/admin/admin.php?page=doctors");
        exit();

    } catch (Exception $e) {
        // Nếu có lỗi (ví dụ trùng Email), hoàn tác lại toàn bộ
        $conn->rollback();
        echo "Lỗi: " . $e->getMessage();
    }
}
?>

<form method="POST" class="add-form">
    <h2>Thêm bác sĩ & Tài khoản</h2>

    <div style="margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 10px;">
        <label>Thông tin tài khoản:</label>
        <input type="email" name="Email" placeholder="Email đăng nhập" required>
        <input type="password" name="Password" placeholder="Mật khẩu" required>
    </div>

    <div style="margin-top: 10px;">
        <label>Thông tin bác sĩ:</label>
        <input type="text" name="HoTen" placeholder="Họ tên bác sĩ" required>

        <select name="MaChuyenKhoa" required>
            <option value="">-- Chọn chuyên khoa --</option>
            <?php
            $res = $conn->query("SELECT * FROM chuyenkhoa");
            while($row = $res->fetch_assoc()):
            ?>
                <option value="<?= $row['MaChuyenKhoa'] ?>">
                    <?= $row['TenChuyenKhoa'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <select name="TrangThai">
            <option value="NhanLich">NHẬN LỊCH</option>
            <option value="KhongNhanLich">KHÔNG NHẬN LỊCH</option>
            <option value="NghiPhep">NGHỈ PHÉP</option>
        </select>
    </div>

    <button class="btn-add" type="submit" style="margin-top: 20px;">Thêm</button>
</form>