<?php
session_start(); 
include '../backend/config/connect.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dịch vụ Y tế - Phòng khám TH</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .services-full-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
            margin-bottom: 60px;
        }
        .service-full-card {
            background: #fff;
            padding: 35px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            text-align: left;
            border-left: 5px solid #3b82f6;
        }
        .service-full-card:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .service-full-card.blue { border-left-color: #3b82f6; }
        .service-full-card.green { border-left-color: #10b981; }
        .service-full-card.purple { border-left-color: #8b5cf6; }
        .service-full-card.orange { border-left-color: #f59e0b; }
        
        .service-full-card i {
            font-size: 35px;
            margin-bottom: 15px;
            display: block;
        }
        .service-full-card.blue i { color: #3b82f6; }
        .service-full-card.green i { color: #10b981; }
        .service-full-card.purple i { color: #8b5cf6; }
        .service-full-card.orange i { color: #f59e0b; }

        .service-full-card h4 {
            font-size: 20px;
            color: #1e293b;
            margin-bottom: 12px;
        }
        .service-full-card p {
            font-size: 14px;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .service-full-card a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
        }
        .service-full-card a:hover {
            text-decoration: underline;
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
                <li><a href="services.php" class="active">Dịch vụ</a></li>
                <li><a href="doctors.php">Bác sĩ</a></li>
                <li><a href="specialties.php">Chuyên khoa</a></li>
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
    <h2 style="font-size: 28px; color: #1e293b; text-align: center;">Dịch vụ của chúng tôi</h2>
    <p style="text-align: center; color: #64748b;">Đa dạng dịch vụ y tế kỹ thuật cao phục vụ người bệnh</p>

    <div class="services-full-grid">
        <div class="service-full-card blue">
            <i class="fas fa-notes-medical"></i>
            <h4>Nội tổng quát</h4>
            <p>Khám sức khỏe tổng quát, tầm soát bệnh lý mạn tính như tiểu đường, huyết áp, các vấn đề tim mạch và nội tiết.</p>
            <a href="frontend/booking/index.php">Đăng ký khám →</a>
        </div>
        <div class="service-full-card green">
            <i class="fas fa-baby"></i>
            <h4>Nhi khoa</h4>
            <p>Khám tổng quát cho trẻ em, tư vấn dinh dưỡng và điều trị các bệnh lý về hô hấp, tiêu hóa, tai mũi họng cho trẻ nhỏ.</p>
            <a href="frontend/booking/index.php">Đăng ký khám →</a>
        </div>
        <div class="service-full-card purple">
            <i class="fas fa-female"></i>
            <h4>Sản phụ khoa</h4>
            <p>Khám và tư vấn chăm sóc sức khỏe thai sản, siêu âm thai định kỳ và điều trị các bệnh lý phụ khoa thường gặp.</p>
            <a href="frontend/booking/index.php">Đăng ký khám →</a>
        </div>
        <div class="service-full-card orange">
            <i class="fas fa-tooth"></i>
            <h4>Răng hàm mặt</h4>
            <p>Chăm sóc răng miệng, lấy cao răng, nhổ răng, phục hình thẩm mỹ và điều trị chuyên sâu kỹ thuật cao.</p>
            <a href="frontend/booking/index.php">Đăng ký khám →</a>
        </div>
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