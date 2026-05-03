<?php
session_start(); 
include '../backend/config/connect.php';

// Truy vấn lấy danh sách chuyên khoa
try {
    $sql = "SELECT * FROM `chuyenkhoa`";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $chuyenKhoas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $chuyenKhoas = [];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chuyên khoa - Phòng khám TH</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .specialties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-top: 30px;
            margin-bottom: 50px;
        }
        .specialty-card {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            text-align: center;
            transition: all 0.3s ease;
            border-top: 4px solid #3b82f6;
        }
        .specialty-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .specialty-icon {
            font-size: 45px;
            color: #3b82f6;
            margin-bottom: 20px;
        }
        .specialty-card h3 {
            font-size: 20px;
            color: #1e293b;
            margin-bottom: 12px;
        }
        .specialty-card p {
            font-size: 14px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 20px;
            min-height: 75px;
        }
        .btn-view-doctor {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ebf5ff;
            color: #3b82f6;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-view-doctor:hover {
            background-color: #3b82f6;
            color: #fff;
        }
    </style>
</head>
<body>

<header>
    <div class="container">
        <nav>
            <div class="logo">Phòng khám TH</div>
            <ul class="nav-links">
                <li><a href="../index.php">Trang chủ</a></li>
                <li><a href="services.php">Dịch vụ</a></li>
                <li><a href="doctors.php">Bác sĩ</a></li>
                <li><a href="specialties.php" class="active">Chuyên khoa</a></li>
                <li><a href="https://suckhoedoisong.vn/y-te.htm" target="_blank">Tin tức</a></li>
            </ul>
            <div class="nav-right">
                <div class="user-dropdown">
                    <i class="far fa-user-circle icon-btn" id="userIcon"></i>
                    <div class="dropdown-content" id="userMenu">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="../frontend/profile/index.php"><i class="fas fa-id-card"></i> Hồ sơ cá nhân</a>
                            <a href="../frontend/auth/logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                        <?php else: ?>
                            <a href="../frontend/auth/login.php"><i class="fas fa-sign-in-alt"></i> Đăng nhập</a>
                            <a href="../frontend/auth/register.php"><i class="fas fa-user-plus"></i> Đăng ký</a>
                        <?php endif; ?>
                    </div>
                </div>
                <a href="../frontend/doctors.php" class="btn-booking">Đặt lịch ngay</a>
            </div>
        </nav>
    </div>
</header>

<main class="container" style="min-height: 70vh; margin-top: 40px;">
    <h2 style="font-size: 28px; color: #1e293b; text-align: center;">Danh sách Chuyên khoa</h2>
    <p style="text-align: center; color: #64748b;">Khám phá các chuyên khoa mũi nhọn tại phòng khám chúng tôi</p>

    <div class="specialties-grid">
        <?php if (!empty($chuyenKhoas)): ?>
            <?php foreach ($chuyenKhoas as $ck): ?>
                <div class="specialty-card">
                    <div class="specialty-icon">
                        <i class="fas fa-notes-medical"></i>
                    </div>
                    <h3><?php echo htmlspecialchars($ck['TenChuyenKhoa']); ?></h3>
                    <p>
                        <?php 
                        echo !empty($ck['MoTa']) 
                            ? htmlspecialchars($ck['MoTa']) 
                            : 'Cung cấp các dịch vụ khám và điều trị chất lượng cao dành cho mọi đối tượng khách hàng.';
                        ?>
                    </p>
                    <a href="doctors.php?chuyenkhoa=<?php echo $ck['MaChuyenKhoa']; ?>" class="btn-view-doctor">
                        Xem bác sĩ <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; grid-column: 1/-1;">Hiện tại chưa có dữ liệu chuyên khoa.</p>
        <?php endif; ?>
    </div>
</main>

<footer>
    <div class="container copyright">
        © 2024 Clinical Curator. Tất cả các quyền được bảo hộ.
    </div>
</footer>

<script>
    const userIcon = document.getElementById('userIcon');
    const userMenu = document.getElementById('userMenu');
    userIcon.addEventListener('click', function(e) {
        userMenu.classList.toggle('show');
        e.stopPropagation();
    });
    window.addEventListener('click', function(e) {
        if (!userIcon.contains(e.target) && !userMenu.contains(e.target)) {
            userMenu.classList.remove('show');
        }
    });
</script>
</body>
</html>