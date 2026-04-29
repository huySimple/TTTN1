<?php
include '../../backend/config/connect.php';

header('Content-Type: application/json');

$doctor_id = $_GET['doctor_id'] ?? 0;
$date = $_GET['date'] ?? '';

if (!$doctor_id || !$date) {
    echo json_encode(['timeslots' => [], 'booked' => []]);
    exit();
}

// 1. Lấy tất cả khung giờ hiện có trong hệ thống
$all_slots = [];
$res_slots = $conn->query("SELECT MaTimeSlot, GioBatDau FROM timeslot ORDER BY GioBatDau");
while ($row = $res_slots->fetch_assoc()) {
    $all_slots[] = $row;
}

// 2. Lấy các khung giờ đã bị đặt của bác sĩ này trong ngày này
$booked_slots = [];
$res_booked = $conn->query("SELECT MaTimeSlot FROM lichhen 
                            WHERE MaBacSi = '$doctor_id' 
                            AND DATE(NgayHen) = '$date' 
                            AND TrangThai != 'DaHuy'");
while ($row = $res_booked->fetch_assoc()) {
    $booked_slots[] = (int)$row['MaTimeSlot'];
}

// Trả về định dạng JSON mà JavaScript đang chờ
echo json_encode([
    'timeslots' => $all_slots,
    'booked' => $booked_slots
]);