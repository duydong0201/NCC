CREATE DATABASE IF NOT EXISTS quan_ly_nha_cung_cap CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE quan_ly_nha_cung_cap;

DROP TABLE IF EXISTS suppliers;
CREATE TABLE suppliers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    contact_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(120) DEFAULT NULL,
    address VARCHAR(255) DEFAULT NULL,
    category VARCHAR(100) NOT NULL,
    tax_code VARCHAR(30) DEFAULT NULL,
    status ENUM('active','pause','stop') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO suppliers (code,name,contact_name,phone,email,address,category,tax_code,status) VALUES
('NCC001','Công ty TNHH Thực phẩm An Phú','Nguyễn Minh Anh','0901 234 567','anh@anphu.vn','Quận 7, TP. Hồ Chí Minh','Thực phẩm','0312345678','active'),
('NCC002','Công ty Cổ phần Bao bì Việt','Trần Quốc Bảo','0902 345 678','bao@baobiviet.vn','Long Biên, Hà Nội','Bao bì','0109876543','active'),
('NCC003','Điện máy Hoàng Gia','Lê Hoàng Nam','0903 456 789','nam@hoanggia.vn','Hải Châu, Đà Nẵng','Thiết bị điện','0401234567','pause'),
('NCC004','Công ty Vận tải Thành Công','Phạm Thu Hà','0904 567 890','ha@thanhcong.vn','Thủ Đức, TP. Hồ Chí Minh','Vận chuyển','0314567890','active'),
('NCC005','Nội thất Minh Phát','Vũ Đức Long','0905 678 901','long@minhphat.vn','Ninh Kiều, Cần Thơ','Nội thất','1801234567','stop');
