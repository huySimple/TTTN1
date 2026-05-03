<?php
include '../config/connect.php';

$error = '';
$success = '';
$appointment = [];
$patient = [];
$exam_result = [];

// 1. Kiểm tra ID lịch hẹn truyền vào
if (isset($_GET['id'])) {
    $ma_lh = $conn->real_escape_string($_GET['id']);
    
    // Lấy thông tin lịch hẹn và bệnh nhân
    $sql = "SELECT lh.*, bn.* FROM lichhen lh
            INNER JOIN benhnhan bn ON lh.MaBenhNhan = bn.MaBenhNhan
            WHERE lh.MaLichHen = '$ma_lh'";
    $res = $conn->query($sql);
    
    if ($res && $res->num_rows > 0) {
        $appointment = $res->fetch_assoc();
        
        // Cập nhật trạng thái lịch hẹn sang "DangKham" khi bác sĩ mở trang này (nếu đang là DaXacNhan hoặc DaDen)
        if (in_array($appointment['TrangThai'], ['DaXacNhan', 'DaDen'])) {
            $conn->query("UPDATE lichhen SET TrangThai = 'DangKham', ThoiGianBatDauKham = NOW() WHERE MaLichHen = '$ma_lh'");
            $appointment['TrangThai'] = 'DangKham';
        }

        // Lấy thông tin kết quả khám cũ (nếu có)
        $res_exam = $conn->query("SELECT * FROM ketquakham WHERE MaLichHen = '$ma_lh'");
        if ($res_exam && $res_exam->num_rows > 0) {
            $exam_result = $res_exam->fetch_assoc();
        }
    } else {
        echo "Không tìm thấy thông tin lịch hẹn!";
        exit();
    }
} else {
    echo "Thiếu ID lịch hẹn!";
    exit();
}

// 2. API xử lý AJAX chuyển trạng thái nhanh (DaDen, DangKham)
if (isset($_POST['action']) && $_POST['action'] === 'update_status') {
    header('Content-Type: application/json; charset=utf-8');
    $new_status = $conn->real_escape_string($_POST['status']);
    
    if (in_array($new_status, ['DaXacNhan', 'DaDen', 'DangKham'])) {
        $update_sql = "UPDATE lichhen SET TrangThai = '$new_status' WHERE MaLichHen = '$ma_lh'";
        if ($new_status === 'DangKham') {
            $update_sql = "UPDATE lichhen SET TrangThai = 'DangKham', ThoiGianBatDauKham = NOW() WHERE MaLichHen = '$ma_lh'";
        }
        if ($conn->query($update_sql)) {
            echo json_encode(['success' => true, 'message' => 'Cập nhật trạng thái thành công!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể cập nhật trạng thái.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Trạng thái không hợp lệ.']);
    }
    exit();
}

// 3. Xử lý khi Bác sĩ bấm "Lưu kết quả khám"
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['action'])) {
    // Chỉ số sinh tồn
    $can_nang = !empty($_POST['CanNang']) ? "'" . $conn->real_escape_string($_POST['CanNang']) . "'" : "NULL";
    $chieu_cao = !empty($_POST['ChieuCao']) ? "'" . $conn->real_escape_string($_POST['ChieuCao']) . "'" : "NULL";
    $huyet_ap = !empty($_POST['HuyetAp']) ? "'" . $conn->real_escape_string($_POST['HuyetAp']) . "'" : "NULL";
    $nhiet_do = !empty($_POST['NhietDo']) ? "'" . $conn->real_escape_string($_POST['NhietDo']) . "'" : "NULL";
    $mach = !empty($_POST['Mach']) ? "'" . $conn->real_escape_string($_POST['Mach']) . "'" : "NULL";
    $spo2 = !empty($_POST['SpO2']) ? "'" . $conn->real_escape_string($_POST['SpO2']) . "'" : "NULL";
    $nhip_tho = !empty($_POST['NhipTho']) ? "'" . $conn->real_escape_string($_POST['NhipTho']) . "'" : "NULL";

    // Khám và chẩn đoán
    $trieu_chung = !empty($_POST['TrieuChung']) ? "'" . $conn->real_escape_string($_POST['TrieuChung']) . "'" : "NULL";
    $chuan_doan = !empty($_POST['ChuanDoan']) ? "'" . $conn->real_escape_string($_POST['ChuanDoan']) . "'" : "NULL";
    $chuan_doan_icd = !empty($_POST['ChuanDoanICD']) ? "'" . $conn->real_escape_string($_POST['ChuanDoanICD']) . "'" : "NULL";
    $loi_khuyen = !empty($_POST['LoiKhuyen']) ? "'" . $conn->real_escape_string($_POST['LoiKhuyen']) . "'" : "NULL";
    $don_thuoc = !empty($_POST['DonThuoc']) ? "'" . $conn->real_escape_string($_POST['DonThuoc']) . "'" : "NULL";
    
    // Ngày tái khám và ký
    $ngay_tai_kham = !empty($_POST['NgayTaiKham']) ? "'" . $conn->real_escape_string($_POST['NgayTaiKham']) . "'" : "NULL";
    $da_ky = isset($_POST['DaKy']) ? 1 : 0;
    $thoi_gian_ky = $da_ky ? "NOW()" : "NULL";

    $conn->begin_transaction();
    try {
        // Kiểm tra xem đã có bản ghi kết quả khám cho lịch hẹn này chưa
        $check_exam = $conn->query("SELECT MaKetQua FROM ketquakham WHERE MaLichHen = '$ma_lh'");
        
        if ($check_exam && $check_exam->num_rows > 0) {
            // Đã có -> Cập nhật
            $sql_exam = "UPDATE ketquakham SET 
                            TrieuChung = $trieu_chung, ChuanDoan = $chuan_doan, ChuanDoanICD = $chuan_doan_icd, 
                            LoiKhuyen = $loi_khuyen, DonThuoc = $don_thuoc, NgayTaiKham = $ngay_tai_kham, 
                            CanNang = $can_nang, ChieuCao = $chieu_cao, HuyetAp = $huyet_ap, 
                            NhietDo = $nhiet_do, Mach = $mach, SpO2 = $spo2, NhipTho = $nhip_tho, 
                            DaKy = $da_ky, ThoiGianKy = $thoi_gian_ky
                         WHERE MaLichHen = '$ma_lh'";
        } else {
            // Chưa có -> Thêm mới
            $sql_exam = "INSERT INTO ketquakham (MaLichHen, TrieuChung, ChuanDoan, ChuanDoanICD, LoiKhuyen, DonThuoc, NgayTaiKham, CanNang, ChieuCao, HuyetAp, NhietDo, Mach, SpO2, NhipTho, DaKy, ThoiGianKy)
                         VALUES ('$ma_lh', $trieu_chung, $chuan_doan, $chuan_doan_icd, $loi_khuyen, $don_thuoc, $ngay_tai_kham, $can_nang, $chieu_cao, $huyet_ap, $nhiet_do, $mach, $spo2, $nhip_tho, $da_ky, $thoi_gian_ky)";
        }
        $conn->query($sql_exam);

        // Nếu bấm Lưu và Hoàn thành hoặc đã ký -> Đổi trạng thái lịch hẹn thành Hoàn thành
        if ($da_ky || isset($_POST['HoanThanhKham'])) {
            $conn->query("UPDATE lichhen SET TrangThai = 'HoanThanh', ThoiGianKetThuc = NOW() WHERE MaLichHen = '$ma_lh'");
        }

        $conn->commit();
        $success = "Lưu hồ sơ khám bệnh thành công!";
        
        // Load lại dữ liệu mới
        header("refresh:1;url=doctor_exam.php?id=" . $ma_lh);
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Lỗi hệ thống: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phòng khám TH - Bác sĩ khám bệnh</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/exam.css">
</head>
<body>

<div class="exam-container">
    <div class="card">
        <div class="card-title">
            <i class="fas fa-id-card"></i>
            Thông tin Bệnh nhân
        </div>
        <div class="info-row">
            <span class="info-label">Họ tên:</span>
            <span class="info-value" style="font-size:16px; color:var(--primary);"><strong><?= htmlspecialchars($appointment['HoTen']) ?></strong></span>
        </div>
        <div class="info-row">
            <span class="info-label">Ngày sinh:</span>
            <span class="info-value"><?= $appointment['NgaySinh'] ? date('d/m/Y', strtotime($appointment['NgaySinh'])) : 'Chưa cập nhật' ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Giới tính:</span>
            <span class="info-value"><?= $appointment['GioiTinh'] == 'Nam' ? 'Nam' : ($appointment['GioiTinh'] == 'Nu' ? 'Nữ' : 'Khác') ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Số điện thoại:</span>
            <span class="info-value"><?= htmlspecialchars($appointment['SoDienThoai'] ?? '-') ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Nhóm máu:</span>
            <span class="info-value"><?= htmlspecialchars($appointment['NhomMau'] ?? 'Không rõ') ?></span>
        </div>
        <div class="info-row" style="flex-direction: column; text-align: left; margin-top: 10px;">
            <span class="info-label">Dị ứng:</span>
            <span class="info-value" style="text-align:left; color:#dc2626; margin-top:4px;">
                <?= $appointment['DiUng'] ? htmlspecialchars($appointment['DiUng']) : '<em>Không có dữ liệu dị ứng</em>' ?>
            </span>
        </div>
        
        <div class="card-title" style="margin-top: 25px;">
            <i class="fas fa-calendar-check"></i>
            Thông tin Lịch hẹn
        </div>
        <div class="info-row">
            <span class="info-label">Mã lịch hẹn:</span>
            <span class="info-value">#<?= $appointment['MaLichHen'] ?></span>
        </div>
        
        <div class="info-row">
            <span class="info-label">Trạng thái:</span>
            <span class="info-value">
                <?php if (in_array($appointment['TrangThai'], ['DaXacNhan', 'DaDen', 'DangKham'])): ?>
                    <select class="status-dropdown" id="updateStatusSelect">
                        <option value="DaXacNhan" <?= $appointment['TrangThai'] == 'DaXacNhan' ? 'selected' : '' ?>>Đã xác nhận</option>
                        <option value="DaDen" <?= $appointment['TrangThai'] == 'DaDen' ? 'selected' : '' ?>>Đã đến</option>
                        <option value="DangKham" <?= $appointment['TrangThai'] == 'DangKham' ? 'selected' : '' ?>>Đang khám</option>
                    </select>
                    <span id="statusToast" class="status-success-toast"><i class="fas fa-check"></i> Đã cập nhật</span>
                <?php else: ?>
                    <span class="status-tag <?= $appointment['TrangThai'] ?>"><?= $appointment['TrangThai'] ?></span>
                <?php endif; ?>
            </span>
        </div>

        <div class="info-row">
            <span class="info-label">Ngày hẹn:</span>
            <span class="info-value"><?= date('d/m/Y H:i', strtotime($appointment['NgayHen'])) ?></span>
        </div>
        <div class="info-row" style="flex-direction: column; text-align: left; margin-top: 10px;">
            <span class="info-label">Ghi chú từ bệnh nhân:</span>
            <span class="info-value" style="text-align:left; margin-top: 4px;">
                <?= $appointment['GhiChuBenhNhan'] ? htmlspecialchars($appointment['GhiChuBenhNhan']) : '<em>Không có</em>' ?>
            </span>
        </div>
    </div>

    <div class="card">
        <div class="card-title">
            <i class="fas fa-user-md"></i>
            Nội dung khám & Kê đơn thuốc
        </div>

        <?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

        <form method="POST" action="">
            <div class="vitals-grid">
                <div class="form-group">
                    <label><i class="fas fa-weight"></i> Cân nặng (kg)</label>
                    <input type="number" step="0.1" name="CanNang" value="<?= htmlspecialchars($exam_result['CanNang'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-ruler-vertical"></i> Chiều cao (cm)</label>
                    <input type="number" name="ChieuCao" value="<?= htmlspecialchars($exam_result['ChieuCao'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-heartbeat"></i> Huyết áp</label>
                    <input type="text" name="HuyetAp" placeholder="Ví dụ: 120/80" value="<?= htmlspecialchars($exam_result['HuyetAp'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-thermometer-half"></i> Nhiệt độ (°C)</label>
                    <input type="number" step="0.1" name="NhietDo" value="<?= htmlspecialchars($exam_result['NhietDo'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-pulse"></i> Mạch (lần/phút)</label>
                    <input type="number" name="Mach" value="<?= htmlspecialchars($exam_result['Mach'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-lungs"></i> SpO2 (%)</label>
                    <input type="number" name="SpO2" value="<?= htmlspecialchars($exam_result['SpO2'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-wind"></i> Nhịp thở</label>
                    <input type="number" name="NhipTho" value="<?= htmlspecialchars($exam_result['NhipTho'] ?? '') ?>">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group full-width">
                    <label><i class="fas fa-notes-medical"></i> Triệu chứng lâm sàng</label>
                    <textarea name="TrieuChung" rows="2" placeholder="Nhập triệu chứng của bệnh nhân..."><?= htmlspecialchars($exam_result['TrieuChung'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-stethoscope"></i> Chẩn đoán bệnh</label>
                    <textarea name="ChuanDoan" rows="2" placeholder="Chẩn đoán xác định bệnh lý..."><?= htmlspecialchars($exam_result['ChuanDoan'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-barcode"></i> Mã chẩn đoán ICD (Nếu có)</label>
                    <input type="text" name="ChuanDoanICD" placeholder="Ví dụ: J00, E11..." value="<?= htmlspecialchars($exam_result['ChuanDoanICD'] ?? '') ?>">
                </div>

                <div class="form-group full-width">
                    <label><i class="fas fa-pills"></i> Đơn thuốc</label>
                    <textarea name="DonThuoc" rows="3" placeholder="Ghi chi tiết đơn thuốc: Tên thuốc - Liều lượng - Cách dùng..."><?= htmlspecialchars($exam_result['DonThuoc'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-calendar-alt"></i> Ngày tái khám (Nếu có)</label>
                    <input type="date" name="NgayTaiKham" value="<?= htmlspecialchars($exam_result['NgayTaiKham'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-comment-medical"></i> Lời khuyên của Bác sĩ</label>
                    <textarea name="LoiKhuyen" rows="1" placeholder="Chế độ ăn uống, nghỉ ngơi..."><?= htmlspecialchars($exam_result['LoiKhuyen'] ?? '') ?></textarea>
                </div>

                <div class="form-group full-width" style="background: #fffbeb; padding: 12px; border-radius: 8px; border: 1px solid #fef3c7; display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" name="DaKy" value="1" id="DaKy" style="width:auto;" <?= (!empty($exam_result['DaKy']) && $exam_result['DaKy'] == 1) ? 'checked' : '' ?>>
                    <label for="DaKy" style="margin: 0; font-weight: bold; cursor: pointer; color: #b45309;">
                        Bác sĩ ký xác nhận (Lịch hẹn sẽ hoàn thành và không thể sửa sau khi ký)
                    </label>
                </div>
            </div>

            <div class="action-footer">
                <a href="doctor_dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
                
                <?php if ($appointment['TrangThai'] != 'HoanThanh'): ?>
                    <button type="submit" name="LuuTam" class="btn btn-submit">
                        <i class="fas fa-save"></i> Lưu tạm kết quả
                    </button>
                    <button type="submit" name="HoanThanhKham" class="btn btn-complete">
                        <i class="fas fa-check-double"></i> Lưu & Hoàn thành khám
                    </button>
                <?php else: ?>
                    <span class="status-tag HoanThanh" style="font-size: 15px; padding: 8px 16px;">
                        <i class="fas fa-check-circle"></i> Hồ sơ này đã hoàn thành
                    </span>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const statusSelect = document.getElementById('updateStatusSelect');
    const statusToast = document.getElementById('statusToast');

    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            const newStatus = this.value;

            // Dùng FormData để gửi POST dữ liệu qua fetch (AJAX)
            const formData = new FormData();
            formData.append('action', 'update_status');
            formData.append('status', newStatus);

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hiển thị chữ "Đã cập nhật" màu xanh lá
                    statusToast.style.display = 'inline';
                    setTimeout(() => {
                        statusToast.style.display = 'none';
                    }, 2000);
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã có lỗi xảy ra khi kết nối tới máy chủ.');
            });
        });
    }
});
</script>

</body>
</html>