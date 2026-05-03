<?php
include '../../backend/config/connect.php'; 

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Thống kê tổng quan
$total_doctors = $conn->query("SELECT COUNT(*) as total FROM bacsi")->fetch_assoc()['total'];
$total_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$total_apps_today = $conn->query("SELECT COUNT(*) as total FROM lichhen WHERE DATE(NgayHen) = CURDATE()")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - Phòng khám TH</title>
    <link rel="stylesheet" href="./css/admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="admin-wrapper">
    <aside class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-notes-medical"></i>
            <div>
                <h2>Phòng khám TH</h2>
                <span>ADMIN TERMINAL</span>
            </div>
        </div>
        
        <nav class="sidebar-menu">
            <a href="admin.php?page=dashboard" class="<?= $page == 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            <a href="admin.php?page=doctors" class="<?= $page == 'doctors' ? 'active' : '' ?>">
                <i class="fas fa-user-md"></i> Quản lý Bác sĩ
            </a>
            <a href="admin.php?page=patients" class="<?= $page == 'patients' ? 'active' : '' ?>">
                <i class="fas fa-user-injured"></i> Quản lý Bệnh nhân
            </a>
            <a href="admin.php?page=appointments" class="<?= $page == 'appointments' ? 'active' : '' ?>">
                <i class="fas fa-calendar-check"></i> Lịch hẹn khám
            </a>
            <a href="admin.php?page=users" class="<?= $page == 'users' ? 'active' : '' ?>">
                <i class="fas fa-user-shield"></i> Quản lý Tài khoản
            </a>
            <a href="admin.php?page=specialties" class="<?= $page == 'specialties' ? 'active' : '' ?>">
                <i class="fas fa-stethoscope"></i> Quản lý Chuyên khoa
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="admin-profile">
                <img src="https://ui-avatars.com/api/?name=Admin&background=006ce3&color=fff" alt="AVT">
                <div>
                    <strong>Quản trị viên</strong>
                    <small>Online</small>
                </div>
            </div>
        </div>
    </aside>

    <main class="main-content">
        <header class="top-header">
            <form method="GET" action="admin.php" class="search-box">
                <input type="hidden" name="page" value="<?= $page ?>">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Tìm kiếm nhanh..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" style="display:none"></button>
            </form>

            <div class="user-actions">
                <i class="far fa-bell"></i>
                <span class="badge-portal">Admin Portal</span>
                <button class="btn-logout" onclick="if(confirm('Bạn muốn đăng xuất?')) window.location='../../frontend/auth/logout.php'">
                     <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </button>
            </div>
        </header>

        <div class="content-body">
            
            <?php if ($page == 'dashboard'): ?>
                <section class="welcome">
                    <h1>Tổng quan</h1>
                    <p>Chào mừng trở lại! Đây là báo cáo tình hình hôm nay.</p>
                </section>
                <div class="stats-cards">
                    <div class="stat-item">
                        <div class="stat-icon blue"><i class="fas fa-user-md"></i></div>
                        <div class="stat-info"><span>BÁC SĨ</span><h2><?= $total_doctors ?></h2></div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon green"><i class="fas fa-users"></i></div>
                        <div class="stat-info"><span>TÀI KHOẢN</span><h2><?= $total_users ?></h2></div>
                    </div>
                    <div class="stat-item highlight">
                        <div class="stat-info"><span>LỊCH HÔM NAY</span><h2><?= $total_apps_today ?></h2></div>
                        <i class="far fa-calendar-alt"></i>
                    </div>
                </div>
                <div class="table-container">
                    <h3>Lịch hẹn mới nhất</h3>
                    <table class="data-table">
                        <thead>
                            <tr><th>BỆNH NHÂN</th><th>BÁC SĨ</th><th>NGÀY</th><th>TRẠNG THÁI</th></tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT lh.*, bn.HoTen as TenBN, bs.HoTen as TenBS FROM lichhen lh 
                                    JOIN benhnhan bn ON lh.MaBenhNhan = bn.MaBenhNhan 
                                    JOIN bacsi bs ON lh.MaBacSi = bs.MaBacSi ORDER BY lh.Created_at DESC LIMIT 5";
                            $res = $conn->query($sql);
                            while($row = $res->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?= $row['TenBN'] ?></strong></td>
                                    <td><?= $row['TenBS'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['NgayHen'])) ?></td>
                                    <td><span class="status-tag <?= strtolower($row['TrangThai']) ?>"><?= $row['TrangThai'] ?></span></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($page == 'doctors'): ?>
                <div class="table-container">
                    <div class="table-header">
                        <h2>Danh sách Bác sĩ <?= $search ? "- Tìm: '$search'" : "" ?></h2>
                        <button class="btn-primary-custom" onclick="window.location='../../backend/admin/add_doctor.php'">+ Thêm bác sĩ</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr><th>ID</th><th>Họ Tên</th><th>Chuyên Khoa</th><th>Trạng Thái</th><th>Thao tác</th></tr>
                        </thead>
                        <tbody>
                            <?php
                            $where = $search ? "WHERE bs.HoTen LIKE '%$search%' OR ck.TenChuyenKhoa LIKE '%$search%'" : "";
                            $sql = "SELECT bs.*, ck.TenChuyenKhoa FROM bacsi bs LEFT JOIN chuyenkhoa ck ON bs.MaChuyenKhoa = ck.MaChuyenKhoa $where";
                            $res = $conn->query($sql);
                            while($row = $res->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?= $row['MaBacSi'] ?></td>
                                    <td><strong><?= $row['HoTen'] ?></strong></td>
                                    <td><?= $row['TenChuyenKhoa'] ?></td>
                                    <td><span class="status-tag <?= strtolower($row['TrangThai']) ?>"><?= $row['TrangThai'] ?></span></td>
                                    <td>
                                        <button class="btn-icon edit" onclick="window.location='../../backend/admin/edit_doctor.php?id=<?= $row['MaBacSi'] ?>'"><i class="fas fa-edit"></i></button>
                                        <button class="btn-icon delete" onclick="if(confirm('Xóa bác sĩ?')) window.location='../../backend/admin/delete_doctor.php?id=<?= $row['MaBacSi'] ?>'"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($page == 'users'): ?>
                <div class="table-container">
                    <div class="table-header">
                        <h2>Quản lý Tài khoản <?= $search ? "- Tìm: '$search'" : "" ?></h2>
                        <button class="btn-primary-custom" onclick="window.location='../../backend/admin/add_user.php'">+ Tạo tài khoản</button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr><th>ID</th><th>Email</th><th>Quyền</th><th>Ngày tạo</th><th>Thao tác</th></tr>
                        </thead>
                        <tbody>
                            <?php
                            $where = $search ? "WHERE Email LIKE '%$search%' OR Role LIKE '%$search%'" : "";
                            $res = $conn->query("SELECT * FROM users $where ORDER BY Created_at DESC");
                            while($row = $res->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?= $row['MaUser'] ?></td>
                                    <td><strong><?= $row['Email'] ?></strong></td>
                                    <td><span class="role-badge <?= strtolower($row['Role']) ?>"><?= $row['Role'] ?></span></td>
                                    <td><?= date('d/m/Y', strtotime($row['Created_at'])) ?></td>
                                    <td class="action-cell">
                                        <button class="btn-icon edit" onclick="window.location='../../backend/admin/edit_user.php?id=<?= $row['MaUser'] ?>'"><i class="fas fa-user-edit"></i></button>
                                        <button class="btn-icon delete" onclick="if(confirm('Xóa user?')) window.location='../../backend/admin/delete_user.php?id=<?= $row['MaUser'] ?>'"><i class="fas fa-user-slash"></i></button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($page == 'patients'): ?>
                <div class="table-container">
                    <div class="table-header">
                        <h2>Danh sách Bệnh nhân <?= $search ? "- Tìm: '$search'" : "" ?></h2>
                        <button class="btn-primary-custom" onclick="window.location='../../backend/admin/add_patient.php'">
                            <i class="fas fa-plus"></i>  Thêm bệnh nhân
                        </button>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Họ Tên</th>
                                <th>Ngày Sinh</th>
                                <th>Giới Tính</th>
                                <th>Số Điện Thoại</th>
                                <th>Nhóm Máu</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $where = $search ? "WHERE HoTen LIKE '%$search%' OR SoDienThoai LIKE '%$search%'" : "";
                            $res = $conn->query("SELECT * FROM benhnhan $where ORDER BY MaBenhNhan DESC");
                            while($row = $res->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?= $row['MaBenhNhan'] ?></td>
                                    <td><strong><?= htmlspecialchars($row['HoTen']) ?></strong></td>
                                    <td><?= $row['NgaySinh'] ? date('d/m/Y', strtotime($row['NgaySinh'])) : '<em>Chưa cập nhật</em>' ?></td>
                                    <td>
                                        <span class="gender-tag <?= strtolower($row['GioiTinh']) ?>">
                                            <?= $row['GioiTinh'] == 'Nu' ? 'Nữ' : ($row['GioiTinh'] == 'Nam' ? 'Nam' : 'Khác') ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($row['SoDienThoai']) ?></td>
                                    <td><?= $row['NhomMau'] ? htmlspecialchars($row['NhomMau']) : '-' ?></td>
                                    <td>
                                        <button class="btn-icon edit" onclick="window.location='../../backend/admin/edit_patient.php?id=<?= $row['MaBenhNhan'] ?>'">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-icon delete" onclick="if(confirm('Bạn có chắc chắn muốn xóa bệnh nhân này và tất cả dữ liệu liên quan?')) window.location='../../backend/admin/delete_patient.php?id=<?= $row['MaBenhNhan'] ?>'">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>


            <?php elseif ($page == 'appointments'): ?>
                <div class="table-container">
                    <h2>Quản lý Lịch hẹn khám <?= $search ? "- Tìm: '$search'" : "" ?></h2>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Bệnh Nhân</th>
                                <th>Bác Sĩ</th>
                                <th>Ngày Khám</th>
                                <th>Trạng Thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $where = $search ? "WHERE bn.HoTen LIKE '%$search%' OR bs.HoTen LIKE '%$search%'" : "";
                            $sql = "SELECT lh.*, bn.HoTen as TenBN, bs.HoTen as TenBS FROM lichhen lh 
                                    JOIN benhnhan bn ON lh.MaBenhNhan = bn.MaBenhNhan 
                                    JOIN bacsi bs ON lh.MaBacSi = bs.MaBacSi 
                                    $where ORDER BY lh.NgayHen DESC";
                            $res = $conn->query($sql);
                            while($row = $res->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?= $row['TenBN'] ?></strong></td>
                                    <td><?= $row['TenBS'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['NgayHen'])) ?></td>
                                    <td>
                                        <span class="status-tag <?= strtolower($row['TrangThai']) ?>"><?= $row['TrangThai'] ?></span>
                                    </td>
                                    <td>
                                        <?php if ($row['TrangThai'] == 'ChoXacNhan'): ?>
                                            <button class="btn-icon edit" title="Xác nhận lịch hẹn" onclick="if(confirm('Xác nhận lịch hẹn này?')) window.location='../../backend/admin/confirm_appointment.php?id=<?= $row['MaLichHen'] ?>'">
                                                <i class="fas fa-check-circle" style="color: #28a745;"></i> Xác nhận
                                            </button>
                                        <?php else: ?>
                                            <span style="color: #888; font-size: 0.9rem;">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
            <?php elseif ($page == 'specialties'): ?>
                <div class="table-container">
                    <div class="table-header">
                        <h2>Danh sách Chuyên khoa <?= $search ? "- Tìm: '$search'" : "" ?></h2>
                        <button class="btn-primary-custom" onclick="window.location='../../backend/admin/add_specialty.php'">+ Thêm chuyên khoa</button>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên chuyên khoa</th>
                                <th>Mô tả</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $where = $search ? "WHERE TenChuyenKhoa LIKE '%$search%'" : "";
                            $res = $conn->query("SELECT * FROM chuyenkhoa $where");

                            while($row = $res->fetch_assoc()):
                            ?>
                                <tr>
                                    <td>#<?= $row['MaChuyenKhoa'] ?></td>
                                    <td><strong><?= $row['TenChuyenKhoa'] ?></strong></td>
                                    <td><?= $row['MoTa'] ?></td>
                                    <td>
                                        <button class="btn-icon edit" onclick="window.location='../../backend/admin/edit_specialty.php?id=<?= $row['MaChuyenKhoa'] ?>'">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-icon delete" onclick="if(confirm('Xóa chuyên khoa?')) window.location='../../backend/admin/delete_specialty.php?id=<?= $row['MaChuyenKhoa'] ?>'">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            

        </div>
    </main>
    
</div>
</body>
</html>