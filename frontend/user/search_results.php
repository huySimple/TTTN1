<?php
session_start();
include '../../backend/config/connect.php';

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

// Tìm kiếm theo chuyên khoa HOẶC tên bác sĩ
$sql = "SELECT bs.*, ck.TenChuyenKhoa 
        FROM bacsi bs
        LEFT JOIN chuyenkhoa ck ON bs.MaChuyenKhoa = ck.MaChuyenKhoa
        WHERE ck.TenChuyenKhoa LIKE '%$keyword%' 
           OR bs.HoTen LIKE '%$keyword%'";

$res = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả tìm kiếm - Phòng khám TH</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
      
    </style>
</head>
<body>

<div class="container">
    <a href="../../index.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Quay lại trang chủ
    </a>
    
    <div class="search-box">
        <h2><i class="fas fa-search"></i> Tìm kiếm </h2>
        <form action="search_results.php" method="GET" class="search-form">
            <input type="text" name="keyword" placeholder="Nhập chuyên khoa hoặc tên bác sĩ..." value="<?= htmlspecialchars($keyword) ?>" required>
            <button type="submit">Tìm kiếm</button>
        </form>
    </div>
    
    <?php if ($keyword): ?>
        <p class="results-info">Tìm thấy <strong><?= $res->num_rows ?></strong> bác sĩ với từ khóa "<strong><?= htmlspecialchars($keyword) ?></strong>"</p>
    <?php endif; ?>
    
    <?php if ($res && $res->num_rows > 0): ?>
        <div class="doctors-grid">
            <?php while($row = $res->fetch_assoc()): ?>
                <div class="doctor-card">
                    <div class="doctor-header">
                        <div class="doctor-avatar">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h3><?= htmlspecialchars($row['HoTen']) ?></h3>
                        <p class="specialty"><?= htmlspecialchars($row['TenChuyenKhoa'] ?? 'Chưa cập nhật') ?></p>
                    </div>
                    <div class="doctor-body">
                        <div class="doctor-info">
                            <i class="fas fa-stethoscope"></i>
                            <span><?= htmlspecialchars($row['TenChuyenKhoa'] ?? 'Chưa cập nhật') ?></span>
                        </div>
                        <div class="doctor-info">
                            <i class="fas fa-phone"></i>
                            <span><?= htmlspecialchars($row['SoDienThoai'] ?? 'Chưa cập nhật') ?></span>
                        </div>
                        <div class="doctor-info">
                            <i class="fas fa-clock"></i>
                            <span><?= htmlspecialchars($row['GioLamViec'] ?? '08:00 - 17:00') ?></span>
                        </div>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div class="doctor-actions">
                                <a href="booking.php?doctor_id=<?= $row['MaBacSi'] ?>" class="btn-booking">
                                    <i class="fas fa-calendar-plus"></i> Đặt lịch ngay
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="doctor-actions">
                                <a href="../auth/login.php" class="btn-booking">
                                    <i class="fas fa-sign-in-alt"></i> Đăng nhập để đặt lịch
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="no-results">
            <i class="fas fa-search"></i>
            <h3>Không tìm thấy kết quả</h3>
            <p>Vui lòng thử từ khóa khác hoặc liên hệ trực tiếp để được tư vấn.</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>