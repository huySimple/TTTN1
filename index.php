<?php
session_start(); 
include 'backend/config/connect.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phòng khám TH - Trang Chủ Đặt Lịch</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
</head>
<body>

<header>
    <div class="container">
        <nav>
            <div class="logo">Phòng khám TH</div>
            <ul class="nav-links">
                <li><a href="index.php" class="active">Trang chủ</a></li>
                <li><a href="#">Dịch vụ</a></li>
                <li><a href="#">Bác sĩ</a></li>
                <li><a href="#">Chuyên khoa</a></li>
                <li><a href="https://suckhoedoisong.vn/y-te.htm">Tin tức</a></li>
            </ul>
            <div class="nav-right">
                <i class="far fa-bell icon-btn"></i>           
                
                <div class="user-dropdown">
                    <i class="far fa-user-circle icon-btn" id="userIcon"></i>
                    
                    <div class="dropdown-content" id="userMenu">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="user-info-header">
                                <span class="welcome-text">Xin chào,</span>
                                <span class="user-name"><?php echo htmlspecialchars($_SESSION['ho_ten']); ?></span>
                            </div>
                            <a href="frontend/profile/index.php"><i class="fas fa-id-card"></i> Hồ sơ cá nhân</a>
            
                            <div class="menu-divider"></div>
                            <a href="frontend/auth/logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                        <?php else: ?>
                            <a href="frontend/auth/login.php"><i class="fas fa-sign-in-alt"></i> Đăng nhập</a>
                            <a href="frontend/auth/register.php"><i class="fas fa-user-plus"></i> Đăng ký</a>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="frontend/booking/index.php" class="btn-booking">Đặt lịch ngay</a>
                <?php else: ?>
                    <a href="frontend/auth/login.php" class="btn-booking">Đặt lịch ngay</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>

<main class="container">
    <section class="hero">
        <div class="hero-content">
            <h1>Chăm sóc sức khỏe <span>toàn diện</span> cho gia đình bạn</h1>
            <p>Đội ngũ bác sĩ chuyên khoa giàu kinh nghiệm, đặt lịch dễ dàng, chăm sóc tận tâm.</p>
            <div class="hero-btns">
                <a href="#" class="btn-main">Khám phá ngay <i class="fas fa-arrow-right"></i></a>
                <a href="#" class="btn-sub">Xem dịch vụ</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="https://media.istockphoto.com/id/1319031310/vi/anh/b%C3%A1c-s%C4%A9-vi%E1%BA%BFt-%C4%91%C6%A1n-thu%E1%BB%91c.jpg?s=612x612&w=0&k=20&c=pegfKYW4cmsyyRQQwvKyHsxgvkmc0j9s8-9xLlfLb_M=" alt="Bác sĩ">
            <div class="floating-card">
                <i class="fas fa-check-circle"></i>
                <div>
                    <small>Bác sĩ uy tín</small>
                    <strong>5+ Chuyên gia</strong>
                </div>
            </div>
        </div>
    </section>

    <div class="search-container">
        <form action="frontend/user/search_results.php" method="GET" style="display: flex; width: 100%; gap: 15px; align-items: center;">
            <div class="search-item">
                <label>Tìm kiếm</label>
                <div class="search-input">
                    <i class="fas fa-search"></i>
                    <input type="text" name="keyword" placeholder="Nhập chuyên khoa hoặc tên bác sĩ..." required>
                </div>
            </div>
            <button type="submit" class="btn-search"><i class="fas fa-search"></i> Tìm kiếm</button>
        </form>
    </div>
    
</main>

<section class="services-section">
    <div class="container">
        <p class="section-tag">Chuyên môn của chúng tôi</p>
        <h2 class="section-title">Dịch vụ của chúng tôi</h2>
        <div class="services-grid">
            <div class="service-card blue">
                <i class="fas fa-notes-medical"></i>
                <h4>Nội tổng quát</h4>
                <p>Tầm soát và điều trị các bệnh lý nội khoa đa dạng.</p>
                <a href="#">Chi tiết →</a>
            </div>
            <div class="service-card green">
                <i class="fas fa-baby"></i>
                <h4>Nhi khoa</h4>
                <p>Chăm sóc sức khỏe toàn diện cho trẻ em.</p>
                <a href="#">Chi tiết →</a>
            </div>
            <div class="service-card purple">
                <i class="fas fa-female"></i>
                <h4>Sản phụ khoa</h4>
                <p>Đồng hành cùng mẹ và bé trong suốt thai kỳ.</p>
                <a href="#">Chi tiết →</a>
            </div>
            <div class="service-card orange">
                <i class="fas fa-tooth"></i>
                <h4>Răng hàm mặt</h4>
                <p>Nha khoa thẩm mỹ và điều trị kỹ thuật cao.</p>
                <a href="#">Chi tiết →</a>
            </div>
        </div>
    </div>
</section>

<footer>
    <div class="container copyright">
        © 2024 Clinical Curator. Tất cả các quyền được bảo hộ.
    </div>
</footer>

<script>
    const userIcon = document.getElementById('userIcon');
    const userMenu = document.getElementById('userMenu');

    // Click vào icon người dùng để hiện/ẩn menu
    userIcon.addEventListener('click', function(e) {
        userMenu.classList.toggle('show');
        e.stopPropagation();
    });

    // Click ra ngoài menu để đóng menu
    window.addEventListener('click', function(e) {
        if (!userIcon.contains(e.target) && !userMenu.contains(e.target)) {
            userMenu.classList.remove('show');
        }
    });
</script>

</body>
</html>