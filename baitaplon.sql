CREATE DATABASE IF NOT EXISTS QuanLyBanHang;
USE QuanLyBanHang;

-- Bảng KhachHang
CREATE TABLE KhachHang (
    SoDienThoai VARCHAR(15) PRIMARY KEY,
    TenKhachHang VARCHAR(100) NOT NULL
);

-- Bảng SanPham
CREATE TABLE SanPham (
    MaSanPham VARCHAR(10) PRIMARY KEY,
    TenSanPham VARCHAR(100) NOT NULL,
    SoLuong INT DEFAULT 0
);

-- Bảng HoaDon
CREATE TABLE HoaDon (
    MaHoaDon INT PRIMARY KEY AUTO_INCREMENT,
    SoDienThoai VARCHAR(15),
    NgayMua DATE NOT NULL,
    LoaiHoaDon ENUM('Nhap','Ban') NOT NULL,
    ThanhTien DECIMAL(15,2),
    FOREIGN KEY (SoDienThoai) REFERENCES KhachHang(SoDienThoai)
);

-- Bảng ChiTietHoaDon
CREATE TABLE ChiTietHoaDon (
    MaHoaDon INT,
    MaSanPham VARCHAR(10),
    SoLuong INT NOT NULL,
    DonGia DECIMAL(15,2) NOT NULL,
    TongTien DECIMAL(15,2) NOT NULL,
    PRIMARY KEY (MaHoaDon, MaSanPham),
    FOREIGN KEY (MaHoaDon) REFERENCES HoaDon(MaHoaDon),
    FOREIGN KEY (MaSanPham) REFERENCES SanPham(MaSanPham)
);

-- Thêm dữ liệu mẫu Khách hàng
INSERT INTO KhachHang (SoDienThoai, TenKhachHang) VALUES
('0901111222', 'Nguyen Van A'),
('0903333444', 'Tran Thi B');

-- Thêm dữ liệu mẫu Sản phẩm
INSERT INTO SanPham (MaSanPham, TenSanPham, SoLuong) VALUES
('SP01', 'Ao Thun', 0),
('SP02', 'Quan Jean', 0),
('SP03', 'Ao So Mi', 0);

-- =============================================
-- SQL mở rộng: View, Trigger, Index để nhấn mạnh CSDL
-- Có thể chạy lại file nhiều lần nhờ các lệnh DROP IF EXISTS
-- Lưu ý: Ứng dụng PHP đã tính tổng tiền và cập nhật tồn kho.
-- Các trigger dưới đây chỉ minh họa và không chỉnh tồn kho để tránh double-update.

-- View doanh thu theo tháng
DROP VIEW IF EXISTS v_doanhthu_thang;
CREATE VIEW v_doanhthu_thang AS
SELECT DATE_FORMAT(NgayMua, '%Y-%m') AS Thang,
       SUM(CASE WHEN LoaiHoaDon='Ban'  THEN ThanhTien ELSE 0 END) AS TongBan,
       SUM(CASE WHEN LoaiHoaDon='Nhap' THEN ThanhTien ELSE 0 END) AS TongNhap,
       SUM(CASE WHEN LoaiHoaDon='Ban'  THEN ThanhTien ELSE -ThanhTien END) AS LoiNhuanGop
FROM HoaDon
GROUP BY DATE_FORMAT(NgayMua, '%Y-%m');

-- Index hỗ trợ JOIN/WHERE phổ biến
CREATE INDEX IF NOT EXISTS idx_cthd_mahd ON ChiTietHoaDon(MaHoaDon);
CREATE INDEX IF NOT EXISTS idx_cthd_masp ON ChiTietHoaDon(MaSanPham);
CREATE INDEX IF NOT EXISTS idx_kh_ten ON KhachHang(TenKhachHang);
CREATE INDEX IF NOT EXISTS idx_sp_ten ON SanPham(TenSanPham);

-- Trigger tính TongTien chi tiết hóa đơn khi INSERT/UPDATE
DROP TRIGGER IF EXISTS trg_cthd_bi_tinhtien;
DELIMITER $$
CREATE TRIGGER trg_cthd_bi_tinhtien BEFORE INSERT ON ChiTietHoaDon
FOR EACH ROW BEGIN
  SET NEW.TongTien = NEW.SoLuong * NEW.DonGia;
END $$
DELIMITER ;

DROP TRIGGER IF EXISTS trg_cthd_bu_tinhtien;
DELIMITER $$
CREATE TRIGGER trg_cthd_bu_tinhtien BEFORE UPDATE ON ChiTietHoaDon
FOR EACH ROW BEGIN
  SET NEW.TongTien = NEW.SoLuong * NEW.DonGia;
END $$
DELIMITER ;

-- Trigger cập nhật ThanhTien của HoaDon sau khi thay đổi ChiTietHoaDon
DROP TRIGGER IF EXISTS trg_cthd_ai_capnhat_thanhtien;
DELIMITER $$
CREATE TRIGGER trg_cthd_ai_capnhat_thanhtien AFTER INSERT ON ChiTietHoaDon
FOR EACH ROW BEGIN
  UPDATE HoaDon h SET h.ThanhTien = (
    SELECT IFNULL(SUM(TongTien),0) FROM ChiTietHoaDon WHERE MaHoaDon = NEW.MaHoaDon
  ) WHERE h.MaHoaDon = NEW.MaHoaDon;
END $$
DELIMITER ;

DROP TRIGGER IF EXISTS trg_cthd_au_capnhat_thanhtien;
DELIMITER $$
CREATE TRIGGER trg_cthd_au_capnhat_thanhtien AFTER UPDATE ON ChiTietHoaDon
FOR EACH ROW BEGIN
  UPDATE HoaDon h SET h.ThanhTien = (
    SELECT IFNULL(SUM(TongTien),0) FROM ChiTietHoaDon WHERE MaHoaDon = NEW.MaHoaDon
  ) WHERE h.MaHoaDon = NEW.MaHoaDon;
END $$
DELIMITER ;

DROP TRIGGER IF EXISTS trg_cthd_ad_capnhat_thanhtien;
DELIMITER $$
CREATE TRIGGER trg_cthd_ad_capnhat_thanhtien AFTER DELETE ON ChiTietHoaDon
FOR EACH ROW BEGIN
  UPDATE HoaDon h SET h.ThanhTien = (
    SELECT IFNULL(SUM(TongTien),0) FROM ChiTietHoaDon WHERE MaHoaDon = OLD.MaHoaDon
  ) WHERE h.MaHoaDon = OLD.MaHoaDon;
END $$
DELIMITER ;
