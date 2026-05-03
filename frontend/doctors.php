<?php
session_start(); 
include '../backend/config/connect.php';

$maChuyenKhoa = isset($_GET['chuyenkhoa']) ? intval($_GET['chuyenkhoa']) : 0;

try {
    // Truy vấn danh sách bác sĩ kèm theo tên chuyên khoa của họ
    $sql = "SELECT b.*, c.TenChuyenKhoa 
            FROM `bacsi` b 
            LEFT JOIN `chuyenkhoa` c ON b.MaChuyenKhoa = c.MaChuyenKhoa";
    
    if ($maChuyenKhoa > 0) {
        $sql .= " WHERE b.MaChuyenKhoa = :maChuyenKhoa";
    }
    
    $stmt = $pdo->prepare($sql);
    if ($maChuyenKhoa > 0) {
        $stmt->bindParam(':maChuyenKhoa', $maChuyenKhoa, PDO::PARAM_INT);
    }
    $stmt->execute();
    $bacSis = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $bacSis = [];
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đội ngũ Bác sĩ - Phòng khám TH</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .doctors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 30px;
            margin-bottom: 60px;
        }
        .doctor-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            text-align: center;
            border: 1px solid #f1f5f9;
        }
        .doctor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .doctor-image {
            width: 100%;
            height: 250px;
            object-fit: contain;      
            object-position: center;  
            background: #f8fafc;
        }
        .doctor-info {
            padding: 20px;
        }
        .doctor-info h3 {
            font-size: 18px;
            color: #1e293b;
            margin-bottom: 6px;
        }
        .doctor-specialty {
            font-size: 14px;
            color: #3b82f6;
            font-weight: 600;
            margin-bottom: 10px;
            background: #ebf5ff;
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
        }
        .doctor-desc {
            font-size: 13px;
            color: #64748b;
            line-height: 1.5;
            min-height: 40px;
            margin-bottom: 15px;
        }
        .btn-booking-doc {
            display: block;
            width: 100%;
            padding: 12px 0;
            background-color: #3b82f6;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            border-radius: 0 0 12px 12px;
            transition: 0.3s;
            border: none;
        }
        .btn-booking-doc:hover {
            background-color: #1d4ed8;
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
                <li><a href="doctors.php" class="active">Bác sĩ</a></li>
                <li><a href="specialties.php">Chuyên khoa</a></li>
                <li><a href="https://suckhoedoisong.vn/y-te.htm" target="_blank">Tin tức</a></li>
            </ul>
            <div class="nav-right">
                <div class="user-dropdown">
                    <i class="far fa-user-circle icon-btn" id="userIcon"></i>
                    <div class="dropdown-content" id="userMenu">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="../profile/index.php"><i class="fas fa-id-card"></i> Hồ sơ cá nhân</a>
                            <a href="../frontend/auth/logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                        <?php else: ?>
                            <a href="../frontend/auth/login.php"><i class="fas fa-sign-in-alt"></i> Đăng nhập</a>
                            <a href="../frontend/auth/register.php"><i class="fas fa-user-plus"></i> Đăng ký</a>
                        <?php endif; ?>
                    </div>
                </div>
                <a href="../frontend/user/doctors.php" class="btn-booking">Đặt lịch ngay</a>
            </div>
        </nav>
    </div>
</header>

<main class="container" style="min-height: 70vh; margin-top: 40px;">
    <h2 style="font-size: 28px; color: #1e293b; text-align: center;">Đội ngũ Bác sĩ uy tín</h2>
    <p style="text-align: center; color: #64748b;">Hết lòng vì sức khỏe người bệnh với chuyên môn vững vàng</p>

    <div class="doctors-grid">
        <?php if (!empty($bacSis)): ?>
            <?php foreach ($bacSis as $bs): ?>
                <?php 
                    // XỬ LÝ ĐƯỜNG DẪN ẢNH TẠI ĐÂY:
                    $img_src = 'https://via.placeholder.com/300x250?text=Doctor'; // Ảnh mặc định

                    if (!empty($bs['anh_dai_dien'])) {
                        // Nếu là một liên kết URL bên ngoài (bắt đầu bằng http:// hoặc https://)
                        if (strpos($bs['anh_dai_dien'], 'http://') === 0 || strpos($bs['anh_dai_dien'], 'https://') === 0) {
                            $img_src = $bs['anh_dai_dien'];
                        } else {
                            // Nếu là ảnh được tải lên từ Admin (lưu dạng uploads/ten_file.jpg)
                            // Cần trỏ đúng vào thư mục admin chứa ảnh
                            $img_src = '../frontend/admin/' . $bs['anh_dai_dien'];
                        }
                    }
                ?>
                <div class="doctor-card">
                    <img src="<?php echo htmlspecialchars($img_src); ?>" 
                         alt="<?php echo htmlspecialchars($bs['HoTen']); ?>" class="doctor-image">
                    <div class="doctor-info">
                        <h3><?php echo htmlspecialchars($bs['HoTen']); ?></h3>
                        <span class="doctor-specialty"><?php echo htmlspecialchars($bs['TenChuyenKhoa'] ?? 'Đa khoa'); ?></span>
                        <p class="doctor-desc">
                            <?php echo !empty($bs['gioithieu_banthan']) ? htmlspecialchars($bs['gioithieu_banthan']) : 'Nhiều năm kinh nghiệm trong lĩnh vực khám và chữa bệnh.'; ?>
                        </p>
                    </div>
                    <a href="../frontend/user/booking.php?doctor_id=<?php echo $bs['MaBacSi']; ?>" class="btn-booking-doc">Đặt lịch khám</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; grid-column: 1/-1;">Hiện tại chưa có dữ liệu bác sĩ phù hợp.</p>
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