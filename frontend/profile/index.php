<?php
session_start();
include "../../backend/config/connect.php"; 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Giả sử bạn đã session_start() và include connect.php ở đầu file
$user_id = $_SESSION['user_id'];

// 1. Lấy MaBenhNhan từ MaUser (Vì bảng lichhen dùng MaBenhNhan làm khóa ngoại)
$stmt_bn = $conn->prepare("SELECT MaBenhNhan FROM benhnhan WHERE MaUser = ?");
$stmt_bn->bind_param("i", $user_id);
$stmt_bn->execute();
$res_bn = $stmt_bn->get_result();
$benhnhan = $res_bn->fetch_assoc();

$history_res = null;
if ($benhnhan) {
    $maBN = $benhnhan['MaBenhNhan'];
    
    // 2. Truy vấn danh sách lịch hẹn
    $sql_history = "SELECT lh.*, bs.HoTen as TenBacSi, ck.TenChuyenKhoa 
                    FROM lichhen lh
                    JOIN bacsi bs ON lh.MaBacSi = bs.MaBacSi
                    LEFT JOIN chuyenkhoa ck ON bs.MaChuyenKhoa = ck.MaChuyenKhoa
                    WHERE lh.MaBenhNhan = ? 
                    ORDER BY lh.NgayHen DESC";
    $stmt_h = $conn->prepare($sql_history);
    $stmt_h->bind_param("i", $maBN);
    $stmt_h->execute();
    $history_res = $stmt_h->get_result();
}


$maUser = $_SESSION['user_id'];

// 1. Lấy thông tin bệnh nhân
$sql_user = "SELECT u.Email, b.* FROM users u 
             LEFT JOIN benhnhan b ON u.MaUser = b.MaUser 
             WHERE u.MaUser = $maUser";
$res_user = $conn->query($sql_user);
$user_data = $res_user->fetch_assoc();

if (!$user_data) { die("Không tìm thấy dữ liệu người dùng."); }
$maBN = $user_data['MaBenhNhan'];

// 2. Truy vấn Lịch sử khám chi tiết (Bổ sung thêm TrieuChung, LoiKhuyen, DonThuoc)
$sql_history = "SELECT k.*, bs.HoTen as TenBacSi, ck.TenChuyenKhoa, lh.NgayHen
                FROM ketquakham k
                JOIN lichhen lh ON k.MaLichHen = lh.MaLichHen
                JOIN bacsi bs ON lh.MaBacSi = bs.MaBacSi
                LEFT JOIN chuyenkhoa ck ON bs.MaChuyenKhoa = ck.MaChuyenKhoa
                WHERE lh.MaBenhNhan = '$maBN' AND lh.TrangThai = 'HoanThanh'
                ORDER BY lh.NgayHen DESC";
$res_history = $conn->query($sql_history);

// Lấy bản ghi mới nhất để hiển thị chỉ số sức khỏe
$latest_exam = ($res_history && $res_history->num_rows > 0) ? $res_history->fetch_assoc() : null;
if($res_history) $res_history->data_seek(0); // Trả con trỏ về đầu để lặp danh sách bên dưới

// 3. Lấy lịch hẹn đang chờ xác nhận và đã xác nhận
$sql_appointments = "SELECT lh.MaLichHen, lh.NgayHen, lh.TrangThai, 
                      bs.HoTen as TenBacSi, ck.TenChuyenKhoa
                      FROM lichhen lh
                      JOIN bacsi bs ON lh.MaBacSi = bs.MaBacSi
                      LEFT JOIN chuyenkhoa ck ON bs.MaChuyenKhoa = ck.MaChuyenKhoa
                      WHERE lh.MaBenhNhan = '$maBN' 
                      AND lh.TrangThai IN ('cho', 'xacnhan')
                      ORDER BY lh.NgayHen ASC";
$res_appointments = $conn->query($sql_appointments);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hồ sơ cá nhân - <?php echo htmlspecialchars($user_data['HoTen']); ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <h2>Phòng khám TH</h2>
            <nav>
                <ul>
                    <li><a href="../../index.php"><i class="fas fa-home"></i> Trang chủ</a></li>
                    <li class="active"><a href="#"><i class="fas fa-id-card"></i> Hồ sơ</a></li>
                </ul>
            </nav>
            
            <!-- Sidebar: Lịch hẹn -->
            <style>

</style>

<div class="appointment-history">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h3><i class="fas fa-history"></i> Lịch sử đặt lịch</h3>
    </div>

    <?php if ($history_res && $history_res->num_rows > 0): ?>
        <?php while($row = $history_res->fetch_assoc()): 
            // Xử lý thời gian
            $date_obj = strtotime($row['NgayHen']);
            $ngay = date('d', $date_obj);
            $thangNam = "Th" . date('m, Y', $date_obj);
            $gio = date('H:i', $date_obj);
            
            // Xử lý màu sắc trạng thái
            $status_class = 'status-default';
            $status_text = $row['TrangThai'];
            
            switch($row['TrangThai']) {
                case 'HoanThanh': $status_class = 'status-hoanthanh'; $status_text = 'Hoàn thành'; break;
                case 'ChoXacNhan': $status_class = 'status-choxacnhan'; $status_text = 'Chờ xác nhận'; break;
                case 'DaHuy': $status_class = 'status-dahuy'; $status_text = 'Đã hủy'; break;
                case 'DaXacNhan': $status_class = 'status-choxacnhan'; $status_text = 'Đã xác nhận'; break;
            }
        ?>
            <div class="appointment-card">
                <div style="width: 60px; text-align: center; background: #f7fafc; padding: 10px; border-radius: 10px; margin-right: 20px;">
                    <div style="font-weight: bold; font-size: 1.2em; color: #2d3748;"><?= $ngay ?></div>
                    <div style="font-size: 0.7em; color: #718096; text-transform: uppercase;"><?= $thangNam ?></div>
                </div>

                <div style="flex: 1;">
                    <h4 style="margin: 0; color: #2d3748;"><?= htmlspecialchars($row['TenChuyenKhoa'] ?? 'Khám Nội') ?></h4>
                    <p style="margin: 5px 0 0; color: #718096; font-size: 0.9em;">
                        <i class="fas fa-user-md"></i> BS. <?= htmlspecialchars($row['TenBacSi']) ?>
                    </p>
                    <p style="margin: 2px 0 0; color: #a0aec0; font-size: 0.85em;">
                        <i class="far fa-clock"></i> Giờ hẹn: <?= $gio ?>
                    </p>
                </div>

                <div style="text-align: right;">
                    <span class="status-badge <?= $status_class ?>">
                        <?= $status_text ?>
                    </span>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div style="text-align: center; padding: 40px; background: #f8fafc; border-radius: 12px; border: 2px dashed #e2e8f0;">
            <i class="fas fa-calendar-times" style="font-size: 3em; color: #cbd5e0; margin-bottom: 15px;"></i>
            <p style="color: #718096; margin: 0;">Bạn chưa có lịch hẹn nào được ghi nhận.</p>
            <a href="booking.php" style="display: inline-block; margin-top: 15px; color: #4a90e2; font-weight: bold; text-decoration: none;">Đặt lịch ngay -></a>
        </div>
    <?php endif; ?>
</div>
        </aside>

        <main class="main-content">
            <header style="display:flex; justify-content: space-between; margin-bottom: 20px;">
                <h2>Hồ sơ cá nhân</h2>
                <div id="status-msg"></div>
            </header>
            

            <div class="profile-grid">
                <div class="left-col">
                    <section class="card">
                        <h3>Thông tin cơ bản</h3>
                        <form id="profileForm">
                            <div class="details">
                                <div class="detail-item">
                                    <label>HỌ VÀ TÊN</label>
                                    <input type="text" name="HoTen" value="<?php echo htmlspecialchars($user_data['HoTen']); ?>" class="edit-input">
                                </div>
                                <div class="detail-item">
                                    <label>SỐ ĐIỆN THOẠI</label>
                                    <input type="text" name="SoDienThoai" value="<?php echo htmlspecialchars($user_data['SoDienThoai']); ?>" class="edit-input">
                                </div>
                                <div class="detail-item">
                                    <label>NGÀY SINH</label>
                                    <input type="date" name="NgaySinh" value="<?php echo $user_data['NgaySinh']; ?>" class="edit-input">
                                </div>
                                <div class="detail-item">
                                    <label>GIỚI TÍNH</label>
                                    <select name="GioiTinh" class="edit-input">
                                        <option value="Nam" <?php if($user_data['GioiTinh']=='Nam') echo 'selected'; ?>>Nam</option>
                                        <option value="Nu" <?php if($user_data['GioiTinh']=='Nu') echo 'selected'; ?>>Nữ</option>
                                    </select>
                                </div>
                                <div class="detail-item full">
                                    <label>ĐỊA CHỈ</label>
                                    <input type="text" name="DiaChi" value="<?php echo htmlspecialchars($user_data['DiaChi']); ?>" class="edit-input">
                                </div>
                            </div>
                            <div style="text-align:right; margin-top:15px;">
                                <button type="submit" class="btn-save" id="btnSave">Lưu thông tin</button>
                            </div>
                        </form>
                    </section>

                    <section class="card">
                        <h3><i class="fas fa-history"></i> Lịch sử khám bệnh</h3>
                        <?php if ($res_history->num_rows > 0): ?>
                            <?php while($row = $res_history->fetch_assoc()): ?>
                                <div class="history-card">
                                    <div class="history-header">
                                        <div>
                                            <strong style="color:var(--primary); font-size: 1.1em;">
                                                <?php echo htmlspecialchars($row['ChuanDoan'] ?? 'Chưa có chuẩn đoán'); ?>
                                            </strong><br>
                                            <small><i class="fas fa-user-md"></i> <?php echo $row['TenBacSi']; ?> | <?php echo $row['TenChuyenKhoa']; ?></small>
                                        </div>
                                        <div style="text-align:right">
                                            <span style="background:#e2e8f0; padding:2px 8px; border-radius:10px; font-size:0.8em;">
                                                <?php echo date('d/m/Y', strtotime($row['NgayHen'])); ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="medical-content">
                                        <?php if(!empty($row['TrieuChung'])): ?>
                                            <div class="symptom-text">
                                                <i class="fas fa-notes-medical"></i> Triệu chứng: <?php echo htmlspecialchars($row['TrieuChung']); ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if(!empty($row['LoiKhuyen'])): ?>
                                            <div class="advice-box">
                                                <span class="medical-label"><i class="fas fa-comment-medical"></i> Lời khuyên:</span><br>
                                                <?php echo nl2br(htmlspecialchars($row['LoiKhuyen'])); ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if(!empty($row['DonThuoc'])): ?>
                                            <div class="prescription-box">
                                                <span class="medical-label"><i class="fas fa-pills"></i> Đơn thuốc:</span><br>
                                                <?php echo nl2br(htmlspecialchars($row['DonThuoc'])); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p style="text-align:center; color:#a0aec0; padding:20px;">Chưa có dữ liệu lịch sử khám.</p>
                        <?php endif; ?>
                    </section>
                </div>

 

                <div class="right-col">
                    <section class="card">
                        <h3><i class="fas fa-shield-alt"></i> Bảo mật</h3>
                        <div style="display: flex; justify-content: space-between; align-items: center; background: #f8fafc; padding: 15px; border-radius: 8px;">
                            <div>
                                <strong>Mật khẩu</strong><br>
                                <small style="color: #718096;">Cập nhật định kỳ để bảo vệ tài khoản</small>
                            </div>
                            <button onclick="openPwdModal()" style="color: #4a90e2; border: none; background: none; font-weight: bold; cursor: pointer;">THAY ĐỔI</button>
                        </div>
                    </section>

                    <section class="card" style="margin-top: 20px;">
                        <h3>Chỉ số gần nhất</h3>
                        <?php if($latest_exam): ?>
                            <div class="vital-item"><i class="fas fa-heartbeat"></i> Mạch: <b><?php echo $latest_exam['Mach']; ?> bpm</b></div>
                            <div class="vital-item"><i class="fas fa-tint"></i> Huyết áp: <b><?php echo $latest_exam['HuyetAp']; ?></b></div>
                            <div class="vital-item"><i class="fas fa-weight"></i> Cân nặng: <b><?php echo $latest_exam['CanNang']; ?> kg</b></div>
                            <div class="vital-item"><i class="fas fa-ruler-vertical"></i> Chiều cao: <b><?php echo $latest_exam['ChieuCao']; ?> cm</b></div>
                        <?php endif; ?>
                    </section>
                </div>

                <div id="pwdModal" class="modal">
                    <div class="modal-content">
                        <span class="close-modal" onclick="closePwdModal()">&times;</span>
                        <h3 style="margin-top: 0;">Đổi mật khẩu</h3>
                        <form id="changePwdForm">
                            <label>Mật khẩu hiện tại</label>
                            <input type="password" name="current_pwd" class="pwd-input" required>
                            
                            <label>Mật khẩu mới</label>
                            <input type="password" name="new_pwd" id="new_pwd" class="pwd-input" required>
                            
                            <label>Xác nhận mật khẩu mới</label>
                            <input type="password" name="confirm_pwd" id="confirm_pwd" class="pwd-input" required>
                            
                            <div id="pwd-msg" style="margin-bottom: 10px; font-size: 0.9em;"></div>
                            <button type="submit" class="btn-pwd" id="btnUpdatePwd">Cập nhật mật khẩu</button>
                        </form>
                    </div>
                </div>

                <script>
                function openPwdModal() { document.getElementById('pwdModal').style.display = 'block'; }
                function closePwdModal() { document.getElementById('pwdModal').style.display = 'none'; }

                document.getElementById('changePwdForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    const btn = document.getElementById('btnUpdatePwd');
                    const msg = document.getElementById('pwd-msg');
                    const newPwd = document.getElementById('new_pwd').value;
                    const confirmPwd = document.getElementById('confirm_pwd').value;

                    if(newPwd !== confirmPwd) {
                        msg.innerHTML = '<span style="color:red">Mật khẩu xác nhận không khớp!</span>';
                        return;
                    }

                    btn.disabled = true;
                    btn.innerText = 'Đang xử lý...';

                    fetch('change_password.php', {
                        method: 'POST',
                        body: new FormData(this)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            msg.innerHTML = '<span style="color:green">Đổi mật khẩu thành công!</span>';
                            setTimeout(() => { closePwdModal(); location.reload(); }, 1500);
                        } else {
                            msg.innerHTML = '<span style="color:red">' + data.message + '</span>';
                        }
                    })
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerText = 'Cập nhật mật khẩu';
                    });
                });
                </script>
            </div>
        </main>
    </div>

    <script>
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSave');
        const msg = document.getElementById('status-msg');
        
        btn.disabled = true;
        btn.innerText = 'Đang lưu...';

        fetch('update_profile.php', {
            method: 'POST',
            body: new FormData(this)
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                msg.innerHTML = '<b style="color:green; background:#e6fffa; padding:5px 10px; border-radius:5px;">Đã lưu thành công!</b>';
            } else {
                alert('Lỗi: ' + data.message);
            }
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerText = 'Lưu thông tin';
            setTimeout(() => msg.innerHTML = '', 3000);
        });
    });
    </script>
    
</body>
</html>