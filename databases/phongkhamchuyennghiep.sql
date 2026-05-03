-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3306
-- Thời gian đã tạo: Th5 03, 2026 lúc 05:28 PM
-- Phiên bản máy phục vụ: 9.1.0
-- Phiên bản PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `phongkhamchuyennghiep`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `MaAdmin` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `MaUser` int UNSIGNED DEFAULT NULL,
  `HoTen` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`MaAdmin`),
  KEY `admin_mauser_foreign` (`MaUser`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `admin`
--

INSERT INTO `admin` (`MaAdmin`, `MaUser`, `HoTen`) VALUES
(1, 1, 'Quản Trị Viên Hệ Thống');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bacsi`
--

DROP TABLE IF EXISTS `bacsi`;
CREATE TABLE IF NOT EXISTS `bacsi` (
  `MaBacSi` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `MaUser` int UNSIGNED DEFAULT NULL,
  `HoTen` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `SoDienThoai` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MaChuyenKhoa` int UNSIGNED DEFAULT NULL,
  `TrangThai` enum('NhanLich','KhongNhanLich','NghiPhep') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'NhanLich',
  `anh_dai_dien` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gioithieu_banthan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`MaBacSi`),
  KEY `bacsi_mauser_foreign` (`MaUser`),
  KEY `bacsi_machuyenkhoa_foreign` (`MaChuyenKhoa`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `bacsi`
--

INSERT INTO `bacsi` (`MaBacSi`, `MaUser`, `HoTen`, `SoDienThoai`, `MaChuyenKhoa`, `TrangThai`, `anh_dai_dien`, `gioithieu_banthan`) VALUES
(1, 2, 'BS. Trần Văn Khiêm', '0901234567', 6, 'NhanLich', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQspkzCwNb0_9vsgJ4u-6XyJZlrwy319JFSoA&s', NULL),
(2, 3, 'BS. Nguyễn Phương Thảo', '0912345678', 2, 'NhanLich', 'uploads/doctor_2_1777828664.jpg', NULL),
(3, 4, 'BS. Lê Bảo Long', '0923456789', 3, 'NhanLich', 'uploads/doctor_3_1777828672.jpg', NULL),
(4, 5, 'BS. Phạm Thanh Ngà', '0934567890', 4, 'NhanLich', 'uploads/doctor_4_1777828735.jpg', NULL),
(5, 12, 'Huỳnh Bảo Minh Phát', NULL, 5, 'NhanLich', 'uploads/doctor_5_1777828743.jpg', NULL),
(6, 13, 'Nguyễn Tuấn Huy', NULL, 4, 'KhongNhanLich', 'uploads/doctor_6_1777828752.jpg', NULL),
(7, 14, 'Trọng Duy', NULL, 4, 'NghiPhep', 'uploads/doctor_7_1777828762.jpg', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `benhnhan`
--

DROP TABLE IF EXISTS `benhnhan`;
CREATE TABLE IF NOT EXISTS `benhnhan` (
  `MaBenhNhan` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `MaUser` int UNSIGNED DEFAULT NULL,
  `HoTen` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `NgaySinh` date DEFAULT NULL,
  `GioiTinh` enum('Nam','Nu','Khac') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SoDienThoai` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DiaChi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NhomMau` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DiUng` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`MaBenhNhan`),
  KEY `benhnhan_mauser_foreign` (`MaUser`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `benhnhan`
--

INSERT INTO `benhnhan` (`MaBenhNhan`, `MaUser`, `HoTen`, `NgaySinh`, `GioiTinh`, `SoDienThoai`, `DiaChi`, `NhomMau`, `DiUng`) VALUES
(1, 6, 'Nguyễn Văn An', '1985-02-07', 'Nam', '0981112233', 'Quận 1, TP. Hồ Chí Minh', NULL, NULL),
(2, 7, 'Trần Thị Bảo Ngọc', '1992-08-25', 'Nu', '0982223344', 'Quận Cầu Giấy, Hà Nội', NULL, NULL),
(3, 8, 'Lê Hoàng Cường', '1978-11-03', 'Nam', '0983334455', 'Quận 7, TP. Hồ Chí Minh', NULL, NULL),
(5, 10, 'Vũ Thanh Em', '1995-09-30', 'Nu', '0985556677', 'Quận Gò Vấp, TP. Hồ Chí Minh', NULL, NULL),
(6, 11, 'kiet chan', '2003-02-20', 'Nam', '1121212', '171 khánh hội', 'AB+', NULL),
(7, 15, 'Huy nguyễn', '2004-10-26', 'Nam', '0912345678', 'binh chanh, tp.hcm', 'A+', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chuyenkhoa`
--

DROP TABLE IF EXISTS `chuyenkhoa`;
CREATE TABLE IF NOT EXISTS `chuyenkhoa` (
  `MaChuyenKhoa` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `TenChuyenKhoa` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `MoTa` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`MaChuyenKhoa`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `chuyenkhoa`
--

INSERT INTO `chuyenkhoa` (`MaChuyenKhoa`, `TenChuyenKhoa`, `MoTa`) VALUES
(2, 'Nhi Khoa', 'Khám tổng quát, tư vấn dinh dưỡng và điều trị bệnh lây nhiễm cho trẻ nhỏ dưới 16 tuổi.'),
(3, 'Da Liễu', 'Điều trị các bệnh lý về da, dị ứng, mụn trứng cá và tư vấn thẩm mỹ da.'),
(4, 'Răng Hàm Mặt', 'Nhổ răng, tẩy trắng răng, lấy cao răng, điều trị tủy và nha chu.'),
(5, 'Tai Mũi Họng', 'Chẩn đoán và điều trị viêm tai giữa, viêm xoang, viêm họng mãn tính, nội soi tai mũi họng.'),
(6, 'Khoa Nội', 'kham ben trong co the'),
(7, 'Khoa ngoại', '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ketquakham`
--

DROP TABLE IF EXISTS `ketquakham`;
CREATE TABLE IF NOT EXISTS `ketquakham` (
  `MaKetQua` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `MaLichHen` int UNSIGNED DEFAULT NULL,
  `TrieuChung` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ChuanDoan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ChuanDoanICD` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `LoiKhuyen` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `DonThuoc` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `DonThuocChiTiet` json DEFAULT NULL,
  `DaKy` tinyint(1) NOT NULL DEFAULT '0',
  `ThoiGianKy` datetime DEFAULT NULL,
  `NgayTaiKham` date DEFAULT NULL,
  `CanNang` decimal(5,2) DEFAULT NULL,
  `ChieuCao` int DEFAULT NULL,
  `HuyetAp` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `NhietDo` decimal(4,1) DEFAULT NULL,
  `Mach` int DEFAULT NULL,
  `SpO2` int DEFAULT NULL,
  `NhipTho` int DEFAULT NULL,
  PRIMARY KEY (`MaKetQua`),
  UNIQUE KEY `ketquakham_malichhen_unique` (`MaLichHen`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `ketquakham`
--

INSERT INTO `ketquakham` (`MaKetQua`, `MaLichHen`, `TrieuChung`, `ChuanDoan`, `ChuanDoanICD`, `LoiKhuyen`, `DonThuoc`, `DonThuocChiTiet`, `DaKy`, `ThoiGianKy`, `NgayTaiKham`, `CanNang`, `ChieuCao`, `HuyetAp`, `NhietDo`, `Mach`, `SpO2`, `NhipTho`) VALUES
(1, 1, 'Họng sưng đỏ, amidan có mủ, nêm mạc phù nề, ho đàm nhầy.', 'Viêm họng cấp có mủ.', NULL, 'Uống nhiều nước ấm, súc miệng đều đặn, không ăn đồ chua cay nóng cứng.', '1. Kháng sinh Amoxicillin 500mg - 20 viên (Chia sáng/tối)\n2. Alphachoay - 20 viên (Ngậm tan dưới lưỡi)\n3. Tiêu đờm Halixol - 01 chai', NULL, 0, NULL, '2026-03-27', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 6, 'Phổi nghe có ran rít nổ, ho đàm xanh đục, nhịp thở nhanh, sốt 38.5 độ.', 'Viêm tiểu phế quản ở trẻ em.', NULL, 'Cần theo dõi sát nhịp thở, chườm ấm liên tục, cho bú mẹ hoặc uống thêm nước sữa đều đặn.', '1. Kháng sinh Klamentin 250mg - 15 gói (Ngày 3 lần)\n2. Cảm cúm Baby - 01 Hộp', NULL, 0, NULL, '2026-03-25', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 8, 'Da quanh miêng ban đỏ, sẩn li ti, rỉ dịch mỏng.', 'Viêm da cơ địa tiếp xúc (Chàm sữa).', NULL, 'Thường xuyên thoa kem dưỡng ẩm, lau khô nước dãi cho trẻ, không được tắm nước bẩn.', '1. Eumovate Cream 15g - Bôi lớp mỏng sáng tối\n2. Hồ nước - 01 lọ bôi giữ ẩm', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 13, 'Nói nhiều', 'Bệnh Hoạt Ngôn', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 12, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 21, NULL, NULL, 'đấ', 'đấ', 'dấdsadsadasdasadasd', '\"[]\"', 1, '2026-04-05 02:21:21', NULL, 5.00, 11, NULL, 3.0, 55, -1, 6),
(11, 26, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-05-03 15:42:20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 25, NULL, NULL, NULL, NULL, NULL, '\"[]\"', 0, NULL, NULL, 1.00, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lichhen`
--

DROP TABLE IF EXISTS `lichhen`;
CREATE TABLE IF NOT EXISTS `lichhen` (
  `MaLichHen` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `MaBenhNhan` int UNSIGNED DEFAULT NULL,
  `MaBacSi` int UNSIGNED DEFAULT NULL,
  `NgayHen` datetime NOT NULL,
  `ThoiGianDen` datetime DEFAULT NULL,
  `ThoiGianBatDauKham` datetime DEFAULT NULL,
  `ThoiGianKetThuc` datetime DEFAULT NULL,
  `TrangThai` enum('ChoXacNhan','DaXacNhan','DaDen','DangKham','DaHuy','HoanThanh','KhongDen') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'ChoXacNhan',
  `GhiChuBenhNhan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `LyDoKham` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `KieuLich` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DatTruoc',
  `Created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `MaTimeSlot` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`MaLichHen`),
  KEY `lichhen_mabenhnhan_foreign` (`MaBenhNhan`),
  KEY `lichhen_mabacsi_foreign` (`MaBacSi`),
  KEY `lichhen_matimeslot_foreign` (`MaTimeSlot`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `lichhen`
--

INSERT INTO `lichhen` (`MaLichHen`, `MaBenhNhan`, `MaBacSi`, `NgayHen`, `ThoiGianDen`, `ThoiGianBatDauKham`, `ThoiGianKetThuc`, `TrangThai`, `GhiChuBenhNhan`, `LyDoKham`, `KieuLich`, `Created_at`, `MaTimeSlot`) VALUES
(1, 1, 1, '2026-03-20 00:00:00', NULL, NULL, NULL, 'HoanThanh', 'Bị đau rát hầu họng kéo dài, ho khan nhiều về đêm.', NULL, 'DatTruoc', '2026-03-14 06:42:33', 1),
(2, 2, 1, '2026-03-20 00:00:00', NULL, NULL, NULL, 'KhongDen', 'Khó thở nhẹ, tức ngực.', NULL, 'DatTruoc', '2026-03-14 06:42:33', 2),
(3, 3, 1, '2026-03-20 00:00:00', NULL, NULL, NULL, 'KhongDen', 'Sốt nhẹ 38 độ và ớn lạnh từ tối qua.', NULL, 'DatTruoc', '2026-03-14 06:42:33', 3),
(5, 5, 1, '2026-03-21 00:00:00', NULL, NULL, NULL, 'DaXacNhan', 'Tiêu chảy nhiều ngày, mất nước.', NULL, 'DatTruoc', '2026-03-14 06:42:33', 6),
(6, 1, 2, '2026-03-21 00:00:00', NULL, NULL, NULL, 'HoanThanh', 'Bé 5 tuổi bị ho có đờm, khò khè, sổ mũi.', NULL, 'DatTruoc', '2026-03-14 06:42:33', 1),
(7, 2, 2, '2026-03-22 00:00:00', NULL, NULL, NULL, 'DaXacNhan', 'Con hay bỏ bú, quấy khóc ban đêm suốt 3 hôm nay.', NULL, 'DatTruoc', '2026-03-14 06:42:33', 2),
(8, 3, 2, '2026-03-21 00:00:00', NULL, NULL, NULL, 'HoanThanh', 'Bé bị nổi mẩn ngứa quanh mép miệng và cổ.', NULL, 'DatTruoc', '2026-03-14 06:42:33', 3),
(39, 7, 2, '2026-05-08 09:00:00', NULL, NULL, NULL, 'ChoXacNhan', NULL, NULL, 'DatTruoc', '2026-05-03 17:21:23', 3),
(10, 5, 1, '2026-03-22 00:00:00', NULL, NULL, NULL, 'DaXacNhan', 'Xin kiểm tra tổng quát đường tiêu hoá định kỳ.', NULL, 'DatTruoc', '2026-03-14 06:42:33', 8),
(11, 6, 1, '2026-03-15 13:00:00', NULL, NULL, NULL, 'DaHuy', NULL, NULL, 'DatTruoc', '2026-03-14 08:01:02', 7),
(12, 3, 1, '2026-03-15 13:30:00', NULL, NULL, NULL, 'HoanThanh', NULL, NULL, 'DatTruoc', '2026-03-14 08:02:01', 8),
(13, 6, 1, '2026-03-15 13:00:00', NULL, NULL, NULL, 'HoanThanh', NULL, NULL, 'DatTruoc', '2026-03-14 08:09:06', 7),
(14, 6, 1, '2026-03-20 09:30:00', NULL, NULL, NULL, 'DaXacNhan', NULL, NULL, 'DatTruoc', '2026-03-15 04:04:27', 4),
(15, 6, 2, '2026-03-21 08:30:00', NULL, NULL, NULL, 'DaHuy', NULL, NULL, 'DatTruoc', '2026-03-15 04:07:24', 2),
(16, 6, 4, '2026-03-16 14:30:00', NULL, NULL, NULL, 'DaHuy', NULL, NULL, 'DatTruoc', '2026-03-16 06:26:06', 10),
(17, 6, 3, '2026-03-24 08:30:00', NULL, NULL, NULL, 'DaHuy', 'mụn', NULL, 'DatTruoc', '2026-03-24 03:34:33', 2),
(18, 6, 3, '2026-03-24 08:30:00', NULL, NULL, NULL, 'DaHuy', NULL, NULL, 'DatTruoc', '2026-03-24 03:36:30', 2),
(19, 6, 3, '2026-03-24 08:30:00', NULL, NULL, NULL, 'DaHuy', NULL, NULL, 'DatTruoc', '2026-03-24 03:38:12', 2),
(20, 6, 3, '2026-03-24 08:00:00', NULL, NULL, NULL, 'DaXacNhan', NULL, NULL, 'DatTruoc', '2026-03-24 03:40:54', 1),
(21, 6, 1, '2026-04-11 08:30:00', '2026-04-04 17:09:00', '2026-04-04 17:09:05', '2026-04-05 02:21:21', 'HoanThanh', NULL, NULL, 'DatTruoc', '2026-04-04 17:06:33', 2),
(22, 6, 1, '2026-04-05 03:38:18', '2026-04-05 03:38:18', NULL, NULL, 'KhongDen', NULL, NULL, 'KhamNgoai', '2026-04-05 03:38:18', NULL),
(38, 7, 1, '2026-05-04 09:00:00', NULL, NULL, NULL, 'ChoXacNhan', NULL, NULL, 'DatTruoc', '2026-05-03 17:20:45', 3),
(24, 1, 1, '2026-04-11 10:30:00', '2026-04-05 15:17:47', '2026-04-05 15:17:53', NULL, 'DangKham', NULL, NULL, 'DatTruoc', '2026-04-05 15:17:17', 6),
(25, 6, 1, '2026-04-26 08:30:00', '2026-04-24 08:38:35', '2026-05-03 15:35:07', '2026-05-03 15:41:55', 'HoanThanh', NULL, NULL, 'DatTruoc', '2026-04-24 05:45:15', 2),
(26, 7, 1, '2026-04-26 09:00:00', NULL, NULL, '2026-05-03 15:42:20', 'HoanThanh', NULL, NULL, 'DatTruoc', '2026-04-24 08:40:34', 3),
(27, 5, 1, '2026-04-25 01:59:26', '2026-04-25 01:59:26', '2026-05-04 00:07:12', NULL, 'DaDen', NULL, NULL, 'KhamNgoai', '2026-04-25 01:59:26', NULL),
(28, 6, 2, '2026-04-30 00:00:00', NULL, NULL, NULL, '', NULL, NULL, 'DatTruoc', '2026-04-28 17:43:56', NULL),
(29, 6, 2, '2026-05-01 00:00:00', NULL, NULL, NULL, '', NULL, NULL, 'DatTruoc', '2026-04-28 17:50:27', NULL),
(30, 6, 2, '2026-05-02 00:00:00', NULL, NULL, NULL, '', NULL, NULL, 'DatTruoc', '2026-04-28 17:52:40', NULL),
(31, 6, 2, '2026-05-03 00:00:00', NULL, NULL, NULL, '', NULL, NULL, 'DatTruoc', '2026-04-28 17:54:10', NULL),
(32, 1, 6, '2026-04-30 00:00:00', NULL, NULL, NULL, '', NULL, NULL, 'DatTruoc', '2026-04-28 17:56:44', NULL),
(33, 1, 2, '2026-04-28 00:00:00', NULL, NULL, NULL, '', NULL, NULL, 'DatTruoc', '2026-04-28 18:04:00', NULL),
(34, 1, 2, '2026-09-25 08:00:00', NULL, NULL, NULL, 'DaXacNhan', NULL, NULL, 'DatTruoc', '2026-04-28 18:12:12', 1),
(35, 7, 2, '2026-05-05 09:30:00', NULL, NULL, NULL, 'DaXacNhan', NULL, NULL, 'DatTruoc', '2026-04-29 15:17:30', 4),
(36, 7, 6, '2026-05-06 09:30:00', NULL, NULL, NULL, 'DaHuy', NULL, NULL, 'DatTruoc', '2026-04-29 15:20:11', 4),
(37, 7, 2, '2026-04-30 10:30:00', NULL, NULL, NULL, 'DaXacNhan', NULL, NULL, 'DatTruoc', '2026-04-29 15:20:30', 6);

--
-- Bẫy `lichhen`
--
DROP TRIGGER IF EXISTS `Check_LichHen_1Ngay_1Lan`;
DELIMITER $$
CREATE TRIGGER `Check_LichHen_1Ngay_1Lan` BEFORE INSERT ON `lichhen` FOR EACH ROW BEGIN
                IF EXISTS (
                    SELECT 1
                    FROM lichhen
                    WHERE MaBenhNhan = NEW.MaBenhNhan
                    AND DATE(NgayHen) = DATE(NEW.NgayHen)
                    AND TrangThai != 'DaHuy'
                ) THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Ban chi duoc dat 1 lich trong 1 ngay';
                END IF;
            END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `Check_Update_LichHen`;
DELIMITER $$
CREATE TRIGGER `Check_Update_LichHen` BEFORE UPDATE ON `lichhen` FOR EACH ROW BEGIN
                IF EXISTS (
                    SELECT 1
                    FROM lichhen
                    WHERE MaBenhNhan = NEW.MaBenhNhan
                    AND DATE(NgayHen) = DATE(NEW.NgayHen)
                    AND TrangThai != 'DaHuy'
                    AND MaLichHen != OLD.MaLichHen
                ) THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Ban da co lich trong ngay nay';
                END IF;
            END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `trg_after_insert_lichhen`;
DELIMITER $$
CREATE TRIGGER `trg_after_insert_lichhen` AFTER INSERT ON `lichhen` FOR EACH ROW BEGIN
                UPDATE lichlamviec
                SET SoLuongDaDat = SoLuongDaDat + 1
                WHERE MaBacSi = NEW.MaBacSi
                AND NgayLamViec = DATE(NEW.NgayHen);
            END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lichlamviec`
--

DROP TABLE IF EXISTS `lichlamviec`;
CREATE TABLE IF NOT EXISTS `lichlamviec` (
  `MaLich` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `MaBacSi` int UNSIGNED DEFAULT NULL,
  `NgayLamViec` date NOT NULL,
  `CaLamViec` enum('Sang','Chieu','Toi') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SoLuongToiDa` int DEFAULT '10',
  `SoLuongDaDat` int DEFAULT '0',
  PRIMARY KEY (`MaLich`),
  KEY `lichlamviec_mabacsi_foreign` (`MaBacSi`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `lichlamviec`
--

INSERT INTO `lichlamviec` (`MaLich`, `MaBacSi`, `NgayLamViec`, `CaLamViec`, `SoLuongToiDa`, `SoLuongDaDat`) VALUES
(1, 1, '2026-03-20', 'Sang', 10, 7),
(2, 1, '2026-03-21', 'Chieu', 10, 4),
(3, 1, '2026-03-22', 'Toi', 10, 2),
(4, 2, '2026-03-20', 'Sang', 10, 2),
(5, 2, '2026-03-21', 'Sang', 10, 4),
(6, 2, '2026-03-22', 'Chieu', 10, 1),
(7, 3, '2026-03-23', 'Toi', 10, 2),
(8, 3, '2026-03-24', 'Sang', 10, 4),
(9, 3, '2026-03-25', 'Chieu', 10, 0),
(10, 4, '2026-03-24', 'Sang', 10, 0),
(11, 4, '2026-03-25', 'Sang', 10, 0),
(12, 4, '2026-03-26', 'Toi', 10, 0),
(13, 1, '2026-03-15', 'Chieu', 10, 3),
(15, 3, '2026-04-09', 'Sang', 10, 0),
(16, 1, '2026-04-11', 'Sang', 10, 2),
(17, 1, '2026-04-26', 'Sang', 10, 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(3, '2026_03_14_035150_create_clinic_tables', 1),
(4, '2026_03_14_165812_add_vitals_to_ketquakham_table', 2),
(5, '2026_03_14_165818_add_fixed_info_to_benhnhan_table', 3),
(6, '2026_04_03_020921_add_auth_fields_to_users_table', 4),
(7, '2026_03_14_035852_create_personal_access_tokens_table', 5),
(8, '2026_04_04_010000_update_exam_flow_fields', 6);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `timeslot`
--

DROP TABLE IF EXISTS `timeslot`;
CREATE TABLE IF NOT EXISTS `timeslot` (
  `MaTimeSlot` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `GioBatDau` time NOT NULL,
  `GioKetThuc` time NOT NULL,
  PRIMARY KEY (`MaTimeSlot`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `timeslot`
--

INSERT INTO `timeslot` (`MaTimeSlot`, `GioBatDau`, `GioKetThuc`) VALUES
(1, '08:00:00', '08:30:00'),
(2, '08:30:00', '09:00:00'),
(3, '09:00:00', '09:30:00'),
(4, '09:30:00', '10:00:00'),
(5, '10:00:00', '10:30:00'),
(6, '10:30:00', '11:00:00'),
(7, '13:00:00', '13:30:00'),
(8, '13:30:00', '14:00:00'),
(9, '14:00:00', '14:30:00'),
(10, '14:30:00', '15:00:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `MaUser` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `Email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `MatKhau` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Role` enum('Admin','BacSi','BenhNhan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `verification_token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `reset_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_token_expires_at` timestamp NULL DEFAULT NULL,
  `TrangThaiEmail` enum('ChuaXacThuc','DaXacThuc') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'ChuaXacThuc',
  `MaXacThucEmail` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ThoiGianHetHanMaXacThuc` datetime DEFAULT NULL,
  `ResetToken` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ResetTokenExpire` datetime DEFAULT NULL,
  `TrangThaiToken` enum('ChuaSuDung','DaSuDung') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'ChuaSuDung',
  `Created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`MaUser`),
  UNIQUE KEY `users_email_unique` (`Email`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`MaUser`, `Email`, `MatKhau`, `Role`, `verification_token`, `email_verified_at`, `reset_token`, `reset_token_expires_at`, `TrangThaiEmail`, `MaXacThucEmail`, `ThoiGianHetHanMaXacThuc`, `ResetToken`, `ResetTokenExpire`, `TrangThaiToken`, `Created_at`) VALUES
(1, 'admin@phongkham.com', '$2y$12$LS1UEwvG5335DXOoQ0kcLOLF98X4rX1qzxbM..ubODZ1g0yIxHLe2', 'Admin', NULL, NULL, NULL, NULL, 'DaXacThuc', NULL, NULL, NULL, NULL, 'ChuaSuDung', '2026-03-14 06:42:33'),
(2, 'bs.khiem@phongkham.com', '$2y$10$ShyelLFbZj/CCFbSEs6oXuv106tQvWF18NCzNucXbual/NrToJWwm', 'BacSi', NULL, NULL, NULL, NULL, 'DaXacThuc', NULL, NULL, NULL, NULL, 'ChuaSuDung', '2026-03-14 06:42:33'),
(3, 'bs.thao@phongkham.com', '$2y$12$LS1UEwvG5335DXOoQ0kcLOLF98X4rX1qzxbM..ubODZ1g0yIxHLe2', 'BacSi', NULL, NULL, NULL, NULL, 'DaXacThuc', NULL, NULL, NULL, NULL, 'ChuaSuDung', '2026-03-14 06:42:33'),
(4, 'bs.long@phongkham.com', '$2y$12$LS1UEwvG5335DXOoQ0kcLOLF98X4rX1qzxbM..ubODZ1g0yIxHLe2', 'BacSi', NULL, NULL, NULL, NULL, 'DaXacThuc', NULL, NULL, NULL, NULL, 'ChuaSuDung', '2026-03-14 06:42:33'),
(5, 'bs.nga@phongkham.com', '$2y$12$LS1UEwvG5335DXOoQ0kcLOLF98X4rX1qzxbM..ubODZ1g0yIxHLe2', 'BacSi', NULL, NULL, NULL, NULL, 'DaXacThuc', NULL, NULL, NULL, NULL, 'ChuaSuDung', '2026-03-14 06:42:33'),
(6, 'nguyen.van.a@gmail.com', '$2y$10$Af6pT6ZwrEfAkGNeSxLf1.raCzCj9rFrYmgd5kzdxfLL8IsJiTXc2', 'BenhNhan', NULL, NULL, NULL, NULL, 'DaXacThuc', NULL, NULL, NULL, NULL, 'ChuaSuDung', '2026-03-14 06:42:33'),
(7, 'tran.thi.b@gmail.com', '$2y$12$LS1UEwvG5335DXOoQ0kcLOLF98X4rX1qzxbM..ubODZ1g0yIxHLe2', 'BenhNhan', NULL, NULL, NULL, NULL, 'DaXacThuc', NULL, NULL, NULL, NULL, 'ChuaSuDung', '2026-03-14 06:42:33'),
(8, 'le.hoang.c@gmail.com', '$2y$12$LS1UEwvG5335DXOoQ0kcLOLF98X4rX1qzxbM..ubODZ1g0yIxHLe2', 'BenhNhan', NULL, NULL, NULL, NULL, 'DaXacThuc', NULL, NULL, NULL, NULL, 'ChuaSuDung', '2026-03-14 06:42:33'),
(11, 'ckiet2102@gmail.com', '$2y$12$CLGWj124BMzdakM012sDQ.IuWpMk1wx1j7nb5OB6huKiCpxu0.R3.', 'BenhNhan', NULL, NULL, '$2y$12$w2ToUUzRC0D8cLUf6AXEieG9TZ.QtvnnBMavRDigtvTJD7slY8wbK', '2026-04-05 11:04:30', 'ChuaXacThuc', NULL, NULL, NULL, NULL, 'ChuaSuDung', '2026-03-14 07:56:59'),
(12, 'minhphat@gmail.com', '$2y$12$3eFijaZDce2wl8R8UHr19uLNmdyVLMMEYeTDDRVK.WIOMyyANbufm', 'BacSi', NULL, NULL, NULL, NULL, 'ChuaXacThuc', NULL, NULL, NULL, NULL, 'ChuaSuDung', '2026-03-14 16:14:33'),
(13, 'tuanhuy@gmail.com', '$2y$12$Kn6feDDOZ1a0Qy4FvXJpzeT1pgjUL9BtHs0oQS7dYYq07d/CxKI8m', 'BacSi', NULL, NULL, NULL, NULL, 'ChuaXacThuc', NULL, NULL, NULL, NULL, 'ChuaSuDung', '2026-03-14 16:14:58'),
(14, 'duy@gmail.com', '$2y$12$phrvhmhkUM6ljex0/pp8IeUzTa5.3joeNELjN2AiQLmw.0Ht2JH2u', 'BacSi', NULL, NULL, NULL, NULL, 'ChuaXacThuc', NULL, NULL, NULL, NULL, 'ChuaSuDung', '2026-03-14 16:15:48'),
(15, 'huy@gmail.com', '$2y$12$5gL9FUuRFGDfCbh3h9rVIuAHBQmgXVUdWx3dS4f4p4E6oTR3VkKae', 'BenhNhan', '3TR2jqC2MJlIhvg5Aly8YKCopFwisweGvWjPmwYBsnAJep0Wx4aJuqVmSSHlsrFS', NULL, NULL, NULL, 'ChuaXacThuc', NULL, NULL, NULL, NULL, 'ChuaSuDung', '2026-04-24 08:40:11');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
