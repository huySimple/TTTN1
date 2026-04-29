<?php
    session_start();
    include '../../backend/config/connect.php';

    if (!isset($_SESSION['user_id'])) {
        header("Location: ../../frontend/auth/login.php");
        exit();
    }


    $doctor_id = $_GET['doctor_id'];

    // Lấy thông tin bác sĩ
    $res = $conn->query("SELECT bs.*, ck.TenChuyenKhoa 
        FROM bacsi bs
        LEFT JOIN chuyenkhoa ck ON bs.MaChuyenKhoa = ck.MaChuyenKhoa
        WHERE bs.MaBacSi = $doctor_id");
    $doctor = $res->fetch_assoc();

    // Lấy danh sách timeslot từ DB
    $timeslots = $conn->query("SELECT * FROM timeslot ORDER BY GioBatDau");

    // Xử lý đặt lịch
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $date = $_POST['NgayHen'];
        $timeslot_id = $_POST['MaTimeSlot'];
        $user_id = $_SESSION['user_id'];

        // Kiểm tra timeslot đã được đặt chưa (theo ngày + bác sĩ)
        $check = $conn->query("SELECT lh.MaLichHen FROM lichhen lh
            WHERE lh.MaBacSi = '$doctor_id' AND DATE(lh.NgayHen) = '$date' 
            AND lh.MaTimeSlot = '$timeslot_id' AND lh.TrangThai != 'DaHuy'");
        
        if ($check->num_rows > 0) {
            echo "<script>alert('Giờ này đã có người đặt! Vui lòng chọn giờ khác.'); history.back();</script>";
            exit();
        }

        // Lấy thông tin timeslot để tạo NgayHen đầy đủ
        $ts = $conn->query("SELECT GioBatDau FROM timeslot WHERE MaTimeSlot = $timeslot_id")->fetch_assoc();
        $gioBatDau = $ts['GioBatDau'];
        $ngayHenFull = $date . ' ' . $gioBatDau;

        // Lấy MaBenhNhan thực từ bảng benhnhan
        $bn = $conn->query("SELECT MaBenhNhan FROM benhnhan WHERE MaUser = $user_id")->fetch_assoc();
        $maBN = $bn['MaBenhNhan'];

        $conn->query("INSERT INTO lichhen (MaBenhNhan, MaBacSi, NgayHen, MaTimeSlot, TrangThai)
                    VALUES ('$maBN', '$doctor_id', '$ngayHenFull', '$timeslot_id', 'ChoXacNhan')");

        echo "<script>alert('Đặt lịch thành công!'); window.location='../../frontend/profile/index.php';</script>";
    }

    // Lấy danh sách timeslot đã đặt hôm nay (mặc định)
    $today = date('Y-m-d');
    $booked_slots = $conn->query("SELECT MaTimeSlot FROM lichhen 
        WHERE MaBacSi = '$doctor_id' AND DATE(NgayHen) = '$today' AND TrangThai != 'DaHuy'");
    $booked_ids = [];
    while($b = $booked_slots->fetch_assoc()) {
        $booked_ids[] = $b['MaTimeSlot'];
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

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lịch khám - Phòng khám TH</title>
    <link rel="stylesheet" href="css/book_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  
</head>
<body>

<div class="container">
    <a href="../../index.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Quay lại trang chủ
    </a>

    <div class="booking-card">
        <div class="booking-header">
            <i class="fas fa-calendar-check"></i>
            <h2>Đặt lịch khám</h2>
            <p>Vui lòng điền thông tin để xác nhận đặt lịch</p>
        </div>

        <div class="booking-body">
            <div class="doctor-info">
                <div class="doctor-avatar">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="doctor-details">
                    <h3><?= htmlspecialchars($doctor['HoTen']) ?></h3>
                    <p><i class="fas fa-stethoscope"></i> <?= htmlspecialchars($doctor['TenChuyenKhoa'] ?? 'Chưa cập nhật') ?></p>
                    <span class="specialty-badge"><?= htmlspecialchars($doctor['TenChuyenKhoa'] ?? 'Chưa cập nhật') ?></span>
                </div>
            </div>

            <div class="info-box">
                <i class="fas fa-info-circle"></i>
                <span>Vui lòng chọn ngày và giờ khám phù hợp.</span>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label><i class="fas fa-calendar-day"></i> Chọn ngày khám</label>
                    <input type="date" name="NgayHen" id="NgayHen" class="form-control" required min="<?= date('Y-m-d') ?>">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-clock"></i> Chọn giờ khám</label>
                    <select name="MaTimeSlot" id="MaTimeSlot" class="form-control" required>
                        <option value="">-- Chọn ngày trước --</option>
                        <?php 
                        // Reset pointer for display
                        $timeslots->data_seek(0);
                        while($ts = $timeslots->fetch_assoc()): 
                            $gio = date('H:i', strtotime($ts['GioBatDau']));
                            $is_booked = in_array($ts['MaTimeSlot'], $booked_ids);
                        ?>
                            <option value="<?= $ts['MaTimeSlot'] ?>" <?= $is_booked ? 'disabled' : '' ?> <?= $is_booked ? 'style="color:red"' : '' ?>>
                                <?= $gio ?> <?= $is_booked ? '(Đã đặt)' : '' ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <p class="form-hint"><i class="fas fa-info-circle"></i> Giờ bị đỏ = đã có người đặt</p>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-check-circle"></i> Xác nhận đặt lịch
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('NgayHen').addEventListener('change', function() {
    const date = this.value;
    const select = document.getElementById('MaTimeSlot');
    const doctorId = <?= $doctor_id ?>;
    
    if (!date) return;
    
    // Show loading
    select.innerHTML = '<option value="">Đang tải...</option>';
    
    // Fetch booked timeslots via AJAX
    fetch('get_timeslot.php?doctor_id=' + doctorId + '&date=' + date)
        .then(res => res.json())
        .then(data => {
            // Load all timeslots
            let html = '<option value="">-- Chọn giờ --</option>';
            
            data.timeslots.forEach(function(ts) {
                const isBooked = data.booked.includes(ts.MaTimeSlot);
                const gio = ts.GioBatDau.substring(0, 5);
                const disabled = isBooked ? 'disabled style="color:red"' : '';
                const label = isBooked ? ' (Đã đặt)' : '';
                
                html += `<option value="${ts.MaTimeSlot}" ${disabled}>${gio}${label}</option>`;
            });
            
            select.innerHTML = html;
        })
        .catch(err => {
            select.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
            console.error(err);
        });
});
</script>