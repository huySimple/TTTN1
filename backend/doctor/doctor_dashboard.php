<?php
session_start();
include '../config/connect.php';

// 1. Kiểm tra xem người dùng có phải là Bác sĩ không
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'BacSi' || !isset($_SESSION['user_id'])) {
    header("Location: ../../frontend/auth/login.php");
    exit();
}

// Xử lý logic Đăng xuất
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_unset();
    session_destroy();
    header("Location: ../../frontend/auth/login.php");
    exit();
}

$ma_user = $_SESSION['user_id'];

// 2. Lấy MaBacSi và HoTen từ bảng bacsi thông qua MaUser
$sql_bs = "SELECT MaBacSi, HoTen FROM bacsi WHERE MaUser = '$ma_user'";
$res_bs = $conn->query($sql_bs);

if ($res_bs && $res_bs->num_rows > 0) {
    $bacsi = $res_bs->fetch_assoc();
    $ma_bs = $bacsi['MaBacSi'];
    $_SESSION['doctor_id'] = $ma_bs;
} else {
    echo "Không tìm thấy thông tin bác sĩ!";
    exit();
}

// 3. Lấy danh sách lịch hẹn của bác sĩ này
$sql_lh = "SELECT lh.*, bn.HoTen AS TenBenhNhan, bn.SoDienThoai 
           FROM lichhen lh
           INNER JOIN benhnhan bn ON lh.MaBenhNhan = bn.MaBenhNhan
           WHERE lh.MaBacSi = '$ma_bs'
           ORDER BY lh.NgayHen DESC";
$res_lh = $conn->query($sql_lh);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách lịch hẹn của Bác sĩ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<div class="container">
    <div class="dashboard-header">
        <div class="welcome-text">
            <h2><i class="fas fa-user-md"></i> Bác sĩ <?= htmlspecialchars($bacsi['HoTen']) ?></h2>
            <p>Quản lý danh sách lịch hẹn và tiến trình khám bệnh</p>
        </div>

        <div class="user-menu-container" id="userMenu">
            <div class="user-trigger" onclick="toggleDropdown()">
                <i class="fas fa-user-md avatar"></i>
                <span class="user-name"><?= htmlspecialchars($bacsi['HoTen']) ?></span>
                <i class="fas fa-chevron-down" style="font-size: 12px; color: #64748b;"></i>
            </div>
            <div class="dropdown-menu">
                <a href="doctor_profile.php">
                    <i class="fas fa-user-circle"></i> Hồ sơ của tôi
                </a>
                <a href="doctor_dashboard.php?action=logout" class="logout-item" onclick="return confirm('Bạn có chắc chắn muốn đăng xuất?')">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Mã LH</th>
                <th>Bệnh nhân</th>
                <th>Số điện thoại</th>
                <th>Ngày hẹn</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($res_lh && $res_lh->num_rows > 0): ?>
                <?php while($row = $res_lh->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $row['MaLichHen'] ?></td>
                        <td><strong><?= htmlspecialchars($row['TenBenhNhan']) ?></strong></td>
                        <td><?= htmlspecialchars($row['SoDienThoai'] ?? '-') ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['NgayHen'])) ?></td>
                        <td>
                            <span class="status <?= $row['TrangThai'] ?>">
                                <?= $row['TrangThai'] == 'DaXacNhan' ? 'Đã xác nhận' : ($row['TrangThai'] == 'DangKham' ? 'Đang khám' : ($row['TrangThai'] == 'HoanThanh' ? 'Hoàn thành' : ($row['TrangThai'] == 'KhongDen' ? 'Không đến' : ($row['TrangThai'] == 'DaHuy' ? 'Đã hủy' : ($row['TrangThai'] == 'DangCho' ? 'Đang chờ' : $row['TrangThai']))))) ?>
                            </span>
                        </td>   
                        <td>
                            <a href="doctor_exam.php?id=<?= $row['MaLichHen'] ?>" class="btn-exam">
                                <i class="fas fa-stethoscope"></i> Khám bệnh
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #64748b; padding: 30px;">
                        <i class="fas fa-calendar-times" style="font-size: 24px; margin-bottom: 10px; display:block;"></i>
                        Bạn chưa có lịch hẹn khám nào.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    // Hàm bật tắt dropdown
    function toggleDropdown() {
        document.getElementById('userMenu').classList.toggle('active');
    }

    // Đóng dropdown nếu bấm ra ngoài vùng menu
    window.addEventListener('click', function(e) {
        const menuContainer = document.getElementById('userMenu');
        if (!menuContainer.contains(e.target)) {
            menuContainer.classList.remove('active');
        }
    });
</script>

</body>
</html>