CREATE DATABASE QuanLyTruongHoc
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE QuanLyTruongHoc;

CREATE TABLE TRUONG (
    MaTruong VARCHAR(10) PRIMARY KEY,
    TenTruong VARCHAR(255) NOT NULL,
    DiaChi VARCHAR(255),
    SoDT VARCHAR(20),
    Email VARCHAR(100),
    CapTruong VARCHAR(50)
);


CREATE TABLE MONHOC (
    MaMon VARCHAR(10) PRIMARY KEY,
    TenMon VARCHAR(100) NOT NULL
);

CREATE TABLE GIAOVIEN (
    MaGV VARCHAR(10) PRIMARY KEY,
    HoTenGV VARCHAR(255) NOT NULL,
    GioiTinh VARCHAR(10),
    NgaySinh DATE,
    SoDT VARCHAR(20),
    Email VARCHAR(100),
    MaTruong VARCHAR(10),
    FOREIGN KEY (MaTruong) REFERENCES TRUONG(MaTruong)
);

CREATE TABLE LOPHOC (
    MaLop VARCHAR(10) PRIMARY KEY,
    TenLop VARCHAR(50),
    SiSoThucTe INT,
    SiSoDuKien INT,
    MaKhoi VARCHAR(10),
    NamHoc VARCHAR(20),
    MaGV_CN VARCHAR(10),
    MaTruong VARCHAR(10),
    FOREIGN KEY (MaGV_CN) REFERENCES GIAOVIEN(MaGV),
    FOREIGN KEY (MaTruong) REFERENCES TRUONG(MaTruong)
);

CREATE TABLE HOCSINH (
    MaHS VARCHAR(10) PRIMARY KEY,
    HoTenHS VARCHAR(255) NOT NULL,
    MaDinhDanh VARCHAR(20),
    GioiTinh VARCHAR(10),
    NgaySinh DATE,
    S0DT VARCHAR(20),
    Email VARCHAR(100),
    DanToc VARCHAR(50),
    TonGiao VARCHAR(50),
    HoKhau VARCHAR(255),
    QueQuan VARCHAR(255),
    HoTenBo VARCHAR(255),
    NgheNghiepBo VARCHAR(255),
    HoTenMe VARCHAR(255),
    NgheNghiepMe VARCHAR(255),
    MaTinhTrang VARCHAR(10),
    MaTruong VARCHAR(10),
    MaLop VARCHAR(10),
    FOREIGN KEY (MaTruong) REFERENCES TRUONG(MaTruong),
    FOREIGN KEY (MaLop) REFERENCES LOPHOC(MaLop)
);

CREATE TABLE PCGD (
    MaGV VARCHAR(10),
    MaLop VARCHAR(10),
    MaMon VARCHAR(10),
    PRIMARY KEY (MaGV, MaLop, MaMon),
    FOREIGN KEY (MaGV) REFERENCES GIAOVIEN(MaGV),
    FOREIGN KEY (MaLop) REFERENCES LOPHOC(MaLop),
    FOREIGN KEY (MaMon) REFERENCES MONHOC(MaMon)
);

CREATE TABLE BANGDIEM (
    MaBD VARCHAR(15) PRIMARY KEY,
    HocKy INT,
    MaHS VARCHAR(10),
    MaMon VARCHAR(10),
    FOREIGN KEY (MaHS) REFERENCES HOCSINH(MaHS),
    FOREIGN KEY (MaMon) REFERENCES MONHOC(MaMon)
);

CREATE TABLE DIEMTHANHPHAN (
    MaDTP VARCHAR(15) PRIMARY KEY,
    MaBD VARCHAR(15),
    MaMon VARCHAR(10),
    HeSo INT,
    Diem FLOAT,
    FOREIGN KEY (MaBD) REFERENCES BANGDIEM(MaBD),
    FOREIGN KEY (MaMon) REFERENCES MONHOC(MaMon)
);


CREATE TABLE HANHKIEM (
    MaHK VARCHAR(10) PRIMARY KEY,
    MaHS VARCHAR(10),
    HocKy INT,
    MaLHK VARCHAR(10),
    NhanXet TEXT,
    FOREIGN KEY (MaHS) REFERENCES HOCSINH(MaHS)
);

CREATE TABLE TAIKHOAN (
	MaTK VARCHAR(10) PRIMARY KEY,
    TenTK VARCHAR(50),
	MatKhau VARCHAR(10),
    ChucVu VARCHAR(15),
	SDT VARCHAR(15),
    Email VARCHAR(100),
    TrangThai BOOL
);

CREATE TABLE THOIKHOABIEU (
    MaTKB VARCHAR(15) PRIMARY KEY,
    MaLop VARCHAR(10),
    MaMon VARCHAR(10),
    Thu INT CHECK (Thu BETWEEN 2 AND 8),
    Tiet INT CHECK (Tiet BETWEEN 1 AND 12),
    HocKy INT,
    NamHoc VARCHAR(20),
    FOREIGN KEY (MaLop) REFERENCES LOPHOC(MaLop),
    FOREIGN KEY (MaMon) REFERENCES MONHOC(MaMon),
    UNIQUE (MaLop, Thu, Tiet, HocKy, NamHoc)
);


-- 1. Thêm dữ liệu Trường
INSERT INTO TRUONG (MaTruong, TenTruong, DiaChi, SoDT, Email, CapTruong) VALUES
('THPT01', 'THPT Chuyên Lê Quý Đôn', 'Số 1 Đại Lộ Thăng Long, Hà Nội', '0243123456', 'c3lequydon@edu.vn', 'THPT');

-- 2. Thêm dữ liệu Môn học (Các môn chính cấp 3 + Thể dục)
INSERT INTO MONHOC (MaMon, TenMon) VALUES
('MH01', 'Toán'),
('MH02', 'Vật Lý'),
('MH03', 'Hóa Học'),
('MH04', 'Sinh Học'),
('MH05', 'Tin Học'),
('MH06', 'Ngữ Văn'),
('MH07', 'Lịch Sử'),
('MH08', 'Địa Lý'),
('MH09', 'Giáo Dục Công Dân'),
('MH10', 'Tiếng Anh'),
('MH11', 'Thể Dục');
-- 3. Thêm Tài khoản cho 10 Cán bộ
INSERT INTO TAIKHOAN (MaTK, TenTK, MatKhau, ChucVu, SDT, Email, TrangThai) VALUES
('CB01', 'admin01', '123456', 'CANBO', '0901000001', 'cb01@school.edu.vn', 1),
('CB02', 'admin02', '123456', 'CANBO', '0901000002', 'cb02@school.edu.vn', 1),
('CB03', 'admin03', '123456', 'CANBO', '0901000003', 'cb03@school.edu.vn', 1),
('CB04', 'admin04', '123456', 'CANBO', '0901000004', 'cb04@school.edu.vn', 1),
('CB05', 'admin05', '123456', 'CANBO', '0901000005', 'cb05@school.edu.vn', 1),
('CB06', 'admin06', '123456', 'CANBO', '0901000006', 'cb06@school.edu.vn', 1),
('CB07', 'admin07', '123456', 'CANBO', '0901000007', 'cb07@school.edu.vn', 1),
('CB08', 'admin08', '123456', 'CANBO', '0901000008', 'cb08@school.edu.vn', 1),
('CB09', 'admin09', '123456', 'CANBO', '0901000009', 'cb09@school.edu.vn', 1),
('CB10', 'admin10', '123456', 'CANBO', '0901000010', 'cb10@school.edu.vn', 1);

-- 4. Thêm Tài khoản cho 20 Giáo viên
INSERT INTO TAIKHOAN (MaTK, TenTK, MatKhau, ChucVu, SDT, Email, TrangThai) VALUES
('GV01', 'gv_toan1', '123456', 'GIAOVIEN', '0912000001', 'gv01@school.edu.vn', 1),
('GV02', 'gv_toan2', '123456', 'GIAOVIEN', '0912000002', 'gv02@school.edu.vn', 1),
('GV03', 'gv_ly1', '123456', 'GIAOVIEN', '0912000003', 'gv03@school.edu.vn', 1),
('GV04', 'gv_ly2', '123456', 'GIAOVIEN', '0912000004', 'gv04@school.edu.vn', 1),
('GV05', 'gv_hoa1', '123456', 'GIAOVIEN', '0912000005', 'gv05@school.edu.vn', 1),
('GV06', 'gv_hoa2', '123456', 'GIAOVIEN', '0912000006', 'gv06@school.edu.vn', 1),
('GV07', 'gv_sinh1', '123456', 'GIAOVIEN', '0912000007', 'gv07@school.edu.vn', 1),
('GV08', 'gv_tin1', '123456', 'GIAOVIEN', '0912000008', 'gv08@school.edu.vn', 1),
('GV09', 'gv_van1', '123456', 'GIAOVIEN', '0912000009', 'gv09@school.edu.vn', 1),
('GV10', 'gv_van2', '123456', 'GIAOVIEN', '0912000010', 'gv10@school.edu.vn', 1),
('GV11', 'gv_su1', '123456', 'GIAOVIEN', '0912000011', 'gv11@school.edu.vn', 1),
('GV12', 'gv_dia1', '123456', 'GIAOVIEN', '0912000012', 'gv12@school.edu.vn', 1),
('GV13', 'gv_gdcd1', '123456', 'GIAOVIEN', '0912000013', 'gv13@school.edu.vn', 1),
('GV14', 'gv_anh1', '123456', 'GIAOVIEN', '0912000014', 'gv14@school.edu.vn', 1),
('GV15', 'gv_anh2', '123456', 'GIAOVIEN', '0912000015', 'gv15@school.edu.vn', 1),
('GV16', 'gv_td1', '123456', 'GIAOVIEN', '0912000016', 'gv16@school.edu.vn', 1),
('GV17', 'gv_toan3', '123456', 'GIAOVIEN', '0912000017', 'gv17@school.edu.vn', 1),
('GV18', 'gv_van3', '123456', 'GIAOVIEN', '0912000018', 'gv18@school.edu.vn', 1),
('GV19', 'gv_anh3', '123456', 'GIAOVIEN', '0912000019', 'gv19@school.edu.vn', 1),
('GV20', 'gv_td2', '123456', 'GIAOVIEN', '0912000020', 'gv20@school.edu.vn', 1);

-- 5. Thêm thông tin chi tiết 20 Giáo viên
INSERT INTO GIAOVIEN (MaGV, HoTenGV, GioiTinh, NgaySinh, SoDT, Email, MaTruong) VALUES
('GV01', 'Nguyễn Văn Toán', 'Nam', '1985-01-01', '0912000001', 'gv01@school.edu.vn', 'THPT01'),
('GV02', 'Trần Thị Số', 'Nữ', '1988-02-02', '0912000002', 'gv02@school.edu.vn', 'THPT01'),
('GV03', 'Lê Văn Lý', 'Nam', '1990-03-03', '0912000003', 'gv03@school.edu.vn', 'THPT01'),
('GV04', 'Phạm Thị Điện', 'Nữ', '1986-04-04', '0912000004', 'gv04@school.edu.vn', 'THPT01'),
('GV05', 'Hoàng Văn Hóa', 'Nam', '1987-05-05', '0912000005', 'gv05@school.edu.vn', 'THPT01'),
('GV06', 'Vũ Thị Chất', 'Nữ', '1991-06-06', '0912000006', 'gv06@school.edu.vn', 'THPT01'),
('GV07', 'Đặng Văn Sinh', 'Nam', '1989-07-07', '0912000007', 'gv07@school.edu.vn', 'THPT01'),
('GV08', 'Bùi Thị Chip', 'Nữ', '1992-08-08', '0912000008', 'gv08@school.edu.vn', 'THPT01'),
('GV09', 'Đỗ Văn Văn', 'Nam', '1984-09-09', '0912000009', 'gv09@school.edu.vn', 'THPT01'),
('GV10', 'Hồ Thị Thơ', 'Nữ', '1985-10-10', '0912000010', 'gv10@school.edu.vn', 'THPT01'),
('GV11', 'Ngô Văn Sử', 'Nam', '1983-11-11', '0912000011', 'gv11@school.edu.vn', 'THPT01'),
('GV12', 'Dương Thị Đất', 'Nữ', '1986-12-12', '0912000012', 'gv12@school.edu.vn', 'THPT01'),
('GV13', 'Lý Văn Luật', 'Nam', '1988-01-13', '0912000013', 'gv13@school.edu.vn', 'THPT01'),
('GV14', 'Mai Thị Anh', 'Nữ', '1990-02-14', '0912000014', 'gv14@school.edu.vn', 'THPT01'),
('GV15', 'Trương Văn Ngữ', 'Nam', '1987-03-15', '0912000015', 'gv15@school.edu.vn', 'THPT01'),
('GV16', 'Nguyễn Văn Khỏe', 'Nam', '1985-04-16', '0912000016', 'gv16@school.edu.vn', 'THPT01'),
('GV17', 'Phan Văn Hình', 'Nam', '1993-05-17', '0912000017', 'gv17@school.edu.vn', 'THPT01'),
('GV18', 'Cao Thị Truyện', 'Nữ', '1994-06-18', '0912000018', 'gv18@school.edu.vn', 'THPT01'),
('GV19', 'Đinh Văn Grammar', 'Nam', '1995-07-19', '0912000019', 'gv19@school.edu.vn', 'THPT01'),
('GV20', 'Lâm Thị Chạy', 'Nữ', '1992-08-20', '0912000020', 'gv20@school.edu.vn', 'THPT01');
-- 6. Thêm 6 lớp học (Mỗi khối 2 lớp, GVCN lấy từ danh sách GV)
INSERT INTO LOPHOC (MaLop, TenLop, SiSoThucTe, SiSoDuKien, MaKhoi, NamHoc, MaGV_CN, MaTruong) VALUES
('10A1', 'Lớp 10A1', 30, 30, 'K10', '2023-2024', 'GV01', 'THPT01'),
('10A2', 'Lớp 10A2', 30, 30, 'K10', '2023-2024', 'GV02', 'THPT01'),
('11A1', 'Lớp 11A1', 30, 30, 'K11', '2023-2024', 'GV09', 'THPT01'),
('11A2', 'Lớp 11A2', 30, 30, 'K11', '2023-2024', 'GV10', 'THPT01'),
('12A1', 'Lớp 12A1', 30, 30, 'K12', '2023-2024', 'GV14', 'THPT01'),
('12A2', 'Lớp 12A2', 30, 30, 'K12', '2023-2024', 'GV15', 'THPT01');

-- 7. Tạo Tài khoản và Thông tin học sinh cho 10A1 (5 em mẫu)
INSERT INTO TAIKHOAN (MaTK, TenTK, MatKhau, ChucVu, SDT, Email, TrangThai) VALUES
('HS1001', 'hs10a1_01', '123456', 'HOCSINH', '0988000001', 'hs1001@school.edu.vn', 1),
('HS1002', 'hs10a1_02', '123456', 'HOCSINH', '0988000002', 'hs1002@school.edu.vn', 1),
('HS1003', 'hs10a1_03', '123456', 'HOCSINH', '0988000003', 'hs1003@school.edu.vn', 1),
('HS1004', 'hs10a1_04', '123456', 'HOCSINH', '0988000004', 'hs1004@school.edu.vn', 1),
('HS1005', 'hs10a1_05', '123456', 'HOCSINH', '0988000005', 'hs1005@school.edu.vn', 1);

INSERT INTO HOCSINH (MaHS, HoTenHS, MaDinhDanh, GioiTinh, NgaySinh, S0DT, Email, DanToc, TonGiao, HoKhau, QueQuan, HoTenBo, NgheNghiepBo, HoTenMe, NgheNghiepMe, MaTinhTrang, MaTruong, MaLop) VALUES
('HS1001', 'Nguyễn Văn A', 'DD001', 'Nam', '2008-01-01', '0988000001', 'hs1001@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Hà Nội', 'Nguyễn Văn Bố', 'Kỹ sư', 'Trần Thị Mẹ', 'Giáo viên', 'Đang học', 'THPT01', '10A1'),
('HS1002', 'Trần Thị B', 'DD002', 'Nữ', '2008-02-02', '0988000002', 'hs1002@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Nam Định', 'Trần Văn Cha', 'Bác sĩ', 'Lê Thị Má', 'Y tá', 'Đang học', 'THPT01', '10A1'),
('HS1003', 'Lê Văn C', 'DD003', 'Nam', '2008-03-03', '0988000003', 'hs1003@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Thái Bình', 'Lê Văn Phụ', 'Công nhân', 'Nguyễn Thị Mẫu', 'Nội trợ', 'Đang học', 'THPT01', '10A1'),
('HS1004', 'Phạm Thị D', 'DD004', 'Nữ', '2008-04-04', '0988000004', 'hs1004@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Hà Nam', 'Phạm Văn Tía', 'Buôn bán', 'Vũ Thị U', 'Buôn bán', 'Đang học', 'THPT01', '10A1'),
('HS1005', 'Hoàng Văn E', 'DD005', 'Nam', '2008-05-05', '0988000005', 'hs1005@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Hải Dương', 'Hoàng Văn Ba', 'Bộ đội', 'Đặng Thị Mế', 'Giáo viên', 'Đang học', 'THPT01', '10A1');

-- 8. Tạo Tài khoản và Học sinh cho 10A2 (5 em mẫu)
INSERT INTO TAIKHOAN (MaTK, TenTK, MatKhau, ChucVu, SDT, Email, TrangThai) VALUES
('HS1006', 'hs10a2_01', '123456', 'HOCSINH', '0988000006', 'hs1006@school.edu.vn', 1),
('HS1007', 'hs10a2_02', '123456', 'HOCSINH', '0988000007', 'hs1007@school.edu.vn', 1),
('HS1008', 'hs10a2_03', '123456', 'HOCSINH', '0988000008', 'hs1008@school.edu.vn', 1),
('HS1009', 'hs10a2_04', '123456', 'HOCSINH', '0988000009', 'hs1009@school.edu.vn', 1),
('HS1010', 'hs10a2_05', '123456', 'HOCSINH', '0988000010', 'hs1010@school.edu.vn', 1);

INSERT INTO HOCSINH (MaHS, HoTenHS, MaDinhDanh, GioiTinh, NgaySinh, S0DT, Email, DanToc, TonGiao, HoKhau, QueQuan, HoTenBo, NgheNghiepBo, HoTenMe, NgheNghiepMe, MaTinhTrang, MaTruong, MaLop) VALUES
('HS1006', 'Vũ Văn F', 'DD006', 'Nam', '2008-06-06', '0988000006', 'hs1006@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Hà Nội', 'Vũ Văn Cha', 'Lái xe', 'Ngô Thị Mẹ', 'Công nhân', 'Đang học', 'THPT01', '10A2'),
('HS1007', 'Đặng Thị G', 'DD007', 'Nữ', '2008-07-07', '0988000007', 'hs1007@school.edu.vn', 'Kinh', 'Phật', 'Hà Nội', 'Hà Nội', 'Đặng Văn Bố', 'Nhân viên', 'Lý Thị Mẹ', 'Kế toán', 'Đang học', 'THPT01', '10A2'),
('HS1008', 'Bùi Văn H', 'DD008', 'Nam', '2008-08-08', '0988000008', 'hs1008@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Nghệ An', 'Bùi Văn Ba', 'Xây dựng', 'Trần Thị Mẹ', 'Nội trợ', 'Đang học', 'THPT01', '10A2'),
('HS1009', 'Đỗ Thị I', 'DD009', 'Nữ', '2008-09-09', '0988000009', 'hs1009@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Thanh Hóa', 'Đỗ Văn Bố', 'Nông dân', 'Phạm Thị Mẹ', 'Nông dân', 'Đang học', 'THPT01', '10A2'),
('HS1010', 'Hồ Văn K', 'DD010', 'Nam', '2008-10-10', '0988000010', 'hs1010@school.edu.vn', 'Kinh', 'Thiên Chúa', 'Hà Nội', 'Nam Định', 'Hồ Văn Cha', 'Giáo viên', 'Nguyễn Thị Mẹ', 'Bác sĩ', 'Đang học', 'THPT01', '10A2');

-- 9. Tạo Tài khoản và Học sinh cho 11A1 (5 em mẫu)
INSERT INTO TAIKHOAN (MaTK, TenTK, MatKhau, ChucVu, SDT, Email, TrangThai) VALUES
('HS1101', 'hs11a1_01', '123456', 'HOCSINH', '0988000011', 'hs1101@school.edu.vn', 1),
('HS1102', 'hs11a1_02', '123456', 'HOCSINH', '0988000012', 'hs1102@school.edu.vn', 1),
('HS1103', 'hs11a1_03', '123456', 'HOCSINH', '0988000013', 'hs1103@school.edu.vn', 1),
('HS1104', 'hs11a1_04', '123456', 'HOCSINH', '0988000014', 'hs1104@school.edu.vn', 1),
('HS1105', 'hs11a1_05', '123456', 'HOCSINH', '0988000015', 'hs1105@school.edu.vn', 1);

INSERT INTO HOCSINH (MaHS, HoTenHS, MaDinhDanh, GioiTinh, NgaySinh, S0DT, Email, DanToc, TonGiao, HoKhau, QueQuan, HoTenBo, NgheNghiepBo, HoTenMe, NgheNghiepMe, MaTinhTrang, MaTruong, MaLop) VALUES
('HS1101', 'Ngô Văn L', 'DD011', 'Nam', '2007-01-01', '0988000011', 'hs1101@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Hà Nội', 'Ngô Văn Bố', 'Kinh doanh', 'Trần Thị Mẹ', 'Kinh doanh', 'Đang học', 'THPT01', '11A1'),
('HS1102', 'Dương Thị M', 'DD012', 'Nữ', '2007-02-02', '0988000012', 'hs1102@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Hưng Yên', 'Dương Văn Cha', 'Công an', 'Lê Thị Mẹ', 'Giáo viên', 'Đang học', 'THPT01', '11A1'),
('HS1103', 'Lý Văn N', 'DD013', 'Nam', '2007-03-03', '0988000013', 'hs1103@school.edu.vn', 'Tày', 'Không', 'Hà Nội', 'Lạng Sơn', 'Lý Văn Bố', 'Nông dân', 'Hoàng Thị Mẹ', 'Nông dân', 'Đang học', 'THPT01', '11A1'),
('HS1104', 'Mai Thị O', 'DD014', 'Nữ', '2007-04-04', '0988000014', 'hs1104@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Hà Nội', 'Mai Văn Tía', 'Nhân viên', 'Phạm Thị U', 'Kế toán', 'Đang học', 'THPT01', '11A1'),
('HS1105', 'Trương Văn P', 'DD015', 'Nam', '2007-05-05', '0988000015', 'hs1105@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Bắc Ninh', 'Trương Văn Ba', 'Kỹ sư', 'Vũ Thị Mế', 'Công nhân', 'Đang học', 'THPT01', '11A1');

-- 10. Tạo Tài khoản và Học sinh cho 11A2 (5 em mẫu)
INSERT INTO TAIKHOAN (MaTK, TenTK, MatKhau, ChucVu, SDT, Email, TrangThai) VALUES
('HS1106', 'hs11a2_01', '123456', 'HOCSINH', '0988000016', 'hs1106@school.edu.vn', 1),
('HS1107', 'hs11a2_02', '123456', 'HOCSINH', '0988000017', 'hs1107@school.edu.vn', 1),
('HS1108', 'hs11a2_03', '123456', 'HOCSINH', '0988000018', 'hs1108@school.edu.vn', 1),
('HS1109', 'hs11a2_04', '123456', 'HOCSINH', '0988000019', 'hs1109@school.edu.vn', 1),
('HS1110', 'hs11a2_05', '123456', 'HOCSINH', '0988000020', 'hs1110@school.edu.vn', 1);

INSERT INTO HOCSINH (MaHS, HoTenHS, MaDinhDanh, GioiTinh, NgaySinh, S0DT, Email, DanToc, TonGiao, HoKhau, QueQuan, HoTenBo, NgheNghiepBo, HoTenMe, NgheNghiepMe, MaTinhTrang, MaTruong, MaLop) VALUES
('HS1106', 'Nguyễn Văn Q', 'DD016', 'Nam', '2007-06-06', '0988000016', 'hs1106@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Hà Nội', 'Nguyễn Văn Cha', 'Tự do', 'Lê Thị Mẹ', 'Tự do', 'Đang học', 'THPT01', '11A2'),
('HS1107', 'Phan Thị R', 'DD017', 'Nữ', '2007-07-07', '0988000017', 'hs1107@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Vĩnh Phúc', 'Phan Văn Bố', 'Bác sĩ', 'Trần Thị Mẹ', 'Dược sĩ', 'Đang học', 'THPT01', '11A2'),
('HS1108', 'Cao Văn S', 'DD018', 'Nam', '2007-08-08', '0988000018', 'hs1108@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Phú Thọ', 'Cao Văn Ba', 'Công an', 'Nguyễn Thị Mẹ', 'Giáo viên', 'Đang học', 'THPT01', '11A2'),
('HS1109', 'Đinh Thị T', 'DD019', 'Nữ', '2007-09-09', '0988000019', 'hs1109@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Hà Nội', 'Đinh Văn Tía', 'Nhân viên', 'Hoàng Thị U', 'Nhân viên', 'Đang học', 'THPT01', '11A2'),
('HS1110', 'Lâm Văn U', 'DD020', 'Nam', '2007-10-10', '0988000020', 'hs1110@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Bắc Giang', 'Lâm Văn Bố', 'Kinh doanh', 'Phạm Thị Mế', 'Kế toán', 'Đang học', 'THPT01', '11A2');

-- 11. Tạo Tài khoản và Học sinh cho 12A1 (5 em mẫu)
INSERT INTO TAIKHOAN (MaTK, TenTK, MatKhau, ChucVu, SDT, Email, TrangThai) VALUES
('HS1201', 'hs12a1_01', '123456', 'HOCSINH', '0988000021', 'hs1201@school.edu.vn', 1),
('HS1202', 'hs12a1_02', '123456', 'HOCSINH', '0988000022', 'hs1202@school.edu.vn', 1),
('HS1203', 'hs12a1_03', '123456', 'HOCSINH', '0988000023', 'hs1203@school.edu.vn', 1),
('HS1204', 'hs12a1_04', '123456', 'HOCSINH', '0988000024', 'hs1204@school.edu.vn', 1),
('HS1205', 'hs12a1_05', '123456', 'HOCSINH', '0988000025', 'hs1205@school.edu.vn', 1);

INSERT INTO HOCSINH (MaHS, HoTenHS, MaDinhDanh, GioiTinh, NgaySinh, S0DT, Email, DanToc, TonGiao, HoKhau, QueQuan, HoTenBo, NgheNghiepBo, HoTenMe, NgheNghiepMe, MaTinhTrang, MaTruong, MaLop) VALUES
('HS1201', 'Phạm Văn V', 'DD021', 'Nam', '2006-01-01', '0988000021', 'hs1201@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Hà Nội', 'Phạm Văn Cha', 'Kỹ sư', 'Nguyễn Thị Mẹ', 'Giáo viên', 'Đang học', 'THPT01', '12A1'),
('HS1202', 'Hoàng Thị W', 'DD022', 'Nữ', '2006-02-02', '0988000022', 'hs1202@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Quảng Ninh', 'Hoàng Văn Bố', 'Kinh doanh', 'Trần Thị Mẹ', 'Kinh doanh', 'Đang học', 'THPT01', '12A1'),
('HS1203', 'Vũ Văn X', 'DD023', 'Nam', '2006-03-03', '0988000023', 'hs1203@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Hải Phòng', 'Vũ Văn Ba', 'Công nhân', 'Lê Thị Mẹ', 'Nội trợ', 'Đang học', 'THPT01', '12A1'),
('HS1204', 'Đặng Thị Y', 'DD024', 'Nữ', '2006-04-04', '0988000024', 'hs1204@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Hà Nội', 'Đặng Văn Tía', 'Nhân viên', 'Phạm Thị U', 'Nhân viên', 'Đang học', 'THPT01', '12A1'),
('HS1205', 'Bùi Văn Z', 'DD025', 'Nam', '2006-05-05', '0988000025', 'hs1205@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Hà Nội', 'Bùi Văn Bố', 'Luật sư', 'Nguyễn Thị Mế', 'Thẩm phán', 'Đang học', 'THPT01', '12A1');

-- 12. Tạo Tài khoản và Học sinh cho 12A2 (5 em mẫu)
INSERT INTO TAIKHOAN (MaTK, TenTK, MatKhau, ChucVu, SDT, Email, TrangThai) VALUES
('HS1206', 'hs12a2_01', '123456', 'HOCSINH', '0988000026', 'hs1206@school.edu.vn', 1),
('HS1207', 'hs12a2_02', '123456', 'HOCSINH', '0988000027', 'hs1207@school.edu.vn', 1),
('HS1208', 'hs12a2_03', '123456', 'HOCSINH', '0988000028', 'hs1208@school.edu.vn', 1),
('HS1209', 'hs12a2_04', '123456', 'HOCSINH', '0988000029', 'hs1209@school.edu.vn', 1),
('HS1210', 'hs12a2_05', '123456', 'HOCSINH', '0988000030', 'hs1210@school.edu.vn', 1);

INSERT INTO HOCSINH (MaHS, HoTenHS, MaDinhDanh, GioiTinh, NgaySinh, S0DT, Email, DanToc, TonGiao, HoKhau, QueQuan, HoTenBo, NgheNghiepBo, HoTenMe, NgheNghiepMe, MaTinhTrang, MaTruong, MaLop) VALUES
('HS1206', 'Đỗ Văn AA', 'DD026', 'Nam', '2006-06-06', '0988000026', 'hs1206@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Hà Nội', 'Đỗ Văn Cha', 'Xe ôm', 'Ngô Thị Mẹ', 'Bán hàng', 'Đang học', 'THPT01', '12A2'),
('HS1207', 'Hồ Thị BB', 'DD027', 'Nữ', '2006-07-07', '0988000027', 'hs1207@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Hà Nội', 'Hồ Văn Bố', 'Giám đốc', 'Trần Thị Mẹ', 'Kế toán', 'Đang học', 'THPT01', '12A2'),
('HS1208', 'Ngô Văn CC', 'DD028', 'Nam', '2006-08-08', '0988000028', 'hs1208@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Nam Định', 'Ngô Văn Ba', 'Công nhân', 'Lê Thị Mẹ', 'May mặc', 'Đang học', 'THPT01', '12A2'),
('HS1209', 'Dương Thị DD', 'DD029', 'Nữ', '2006-09-09', '0988000029', 'hs1209@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Thái Bình', 'Dương Văn Tía', 'Nông dân', 'Phạm Thị U', 'Nông dân', 'Đang học', 'THPT01', '12A2'),
('HS1210', 'Lý Văn EE', 'DD030', 'Nam', '2006-10-10', '0988000030', 'hs1210@school.edu.vn', 'Kinh', 'Không', 'Hà Nội', 'Hà Nội', 'Lý Văn Bố', 'Bảo vệ', 'Hoàng Thị Mế', 'Lao công', 'Đang học', 'THPT01', '12A2');

-- 13. Phân công giảng dạy (Mẫu cho lớp 10A1)
-- Các môn: Toán, Lý, Hóa, Sinh, Tin, Văn, Sử, Địa, GDCD, Anh, Thể dục
INSERT INTO PCGD (MaGV, MaLop, MaMon) VALUES
('GV01', '10A1', 'MH01'), -- Toán
('GV03', '10A1', 'MH02'), -- Lý
('GV05', '10A1', 'MH03'), -- Hóa
('GV07', '10A1', 'MH04'), -- Sinh
('GV08', '10A1', 'MH05'), -- Tin
('GV09', '10A1', 'MH06'), -- Văn
('GV11', '10A1', 'MH07'), -- Sử
('GV12', '10A1', 'MH08'), -- Địa
('GV13', '10A1', 'MH09'), -- GDCD
('GV14', '10A1', 'MH10'), -- Anh
('GV16', '10A1', 'MH11'); -- Thể dục
INSERT INTO PCGD (MaGV, MaLop, MaMon) VALUES
('GV01', '10A2', 'MH01'); -- Toán,

INSERT INTO PCGD (MaGV, MaLop, MaMon) VALUES
('GV01', '10A2', 'MH02'); -- Lý

-- Phân công cho 10A2 (Đổi giáo viên khác)
INSERT INTO PCGD (MaGV, MaLop, MaMon) VALUES
('GV02', '10A2', 'MH01'), -- Toán (GV khác)

('GV06', '10A2', 'MH03'), -- Hóa
('GV07', '10A2', 'MH04'),
('GV08', '10A2', 'MH05'),
('GV10', '10A2', 'MH06'), -- Văn
('GV11', '10A2', 'MH07'),
('GV12', '10A2', 'MH08'),
('GV13', '10A2', 'MH09'),
('GV15', '10A2', 'MH10'), -- Anh
('GV20', '10A2', 'MH11');


-- 14. Tạo Bảng điểm cho học sinh HS1001 (Lớp 10A1) môn Toán (MH01) và Văn (MH06) học kỳ 1
INSERT INTO BANGDIEM (MaBD, HocKy, MaHS, MaMon) VALUES
('BD_1001_T', 1, 'HS1001', 'MH01'),
('BD_1001_V', 1, 'HS1001', 'MH06'),
('BD_1002_T', 1, 'HS1002', 'MH01'),
('BD_1002_V', 1, 'HS1002', 'MH06');

-- 15. Nhập điểm thành phần
INSERT INTO DIEMTHANHPHAN (MaDTP, MaBD, MaMon, HeSo, Diem) VALUES
-- Điểm Toán HS1001
('DTP01', 'BD_1001_T', 'MH01', 1, 8.5), -- Miệng/15p
('DTP02', 'BD_1001_T', 'MH01', 1, 9.0),
('DTP03', 'BD_1001_T', 'MH01', 2, 8.0), -- Giữa kỳ
('DTP04', 'BD_1001_T', 'MH01', 3, 9.0), -- Cuối kỳ

-- Điểm Văn HS1001
('DTP05', 'BD_1001_V', 'MH06', 1, 7.5),
('DTP06', 'BD_1001_V', 'MH06', 2, 7.0),
('DTP07', 'BD_1001_V', 'MH06', 3, 8.0),

-- Điểm Toán HS1002
('DTP08', 'BD_1002_T', 'MH01', 1, 6.5),
('DTP09', 'BD_1002_T', 'MH01', 2, 7.0),
('DTP10', 'BD_1002_T', 'MH01', 3, 7.5);


-- 16. Đánh giá hạnh kiểm học kỳ 1
INSERT INTO HANHKIEM (MaHK, MaHS, HocKy, MaLHK, NhanXet) VALUES
('HK01', 'HS1001', 1, 'Tốt', 'Chăm ngoan, học giỏi'),
('HK02', 'HS1002', 1, 'Khá', 'Còn nói chuyện riêng trong giờ'),
('HK03', 'HS1003', 1, 'Tốt', 'Có tiến bộ vượt bậc'),
('HK04', 'HS1004', 1, 'Trung Bình', 'Thường xuyên đi học muộn'),
('HK05', 'HS1005', 1, 'Tốt', 'Năng nổ tham gia phong trào');

-- Thứ 2
INSERT INTO THOIKHOABIEU (MaTKB, MaLop, MaMon, Thu, Tiet, HocKy, NamHoc) VALUES
('TKB_10A1_01','10A1','MH01',2,1,1,'2025-2026'),
('TKB_10A1_02','10A1','MH01',2,2,1,'2025-2026'),
('TKB_10A1_03','10A1','MH02',2,3,1,'2025-2026'),
('TKB_10A1_04','10A1','MH06',2,5,1,'2025-2026'),
('TKB_10A1_05','10A1','MH10',2,6,1,'2025-2026');

-- Thứ 3
INSERT INTO THOIKHOABIEU (MaTKB, MaLop, MaMon, Thu, Tiet, HocKy, NamHoc) VALUES
('TKB_10A1_06','10A1','MH03',3,1,1,'2025-2026'),
('TKB_10A1_07','10A1','MH04',3,2,1,'2025-2026'),
('TKB_10A1_08','10A1','MH11',3,5,1,'2025-2026'),
('TKB_10A1_09','10A1','MH11',3,6,1,'2025-2026'),
('TKB_10A1_10','10A1','MH05',3,7,1,'2025-2026');

-- Thứ 4
INSERT INTO THOIKHOABIEU (MaTKB, MaLop, MaMon, Thu, Tiet, HocKy, NamHoc) VALUES
('TKB_10A1_11','10A1','MH01',4,1,1,'2025-2026'),
('TKB_10A1_12','10A1','MH02',4,2,1,'2025-2026'),
('TKB_10A1_13','10A1','MH06',4,3,1,'2025-2026'),
('TKB_10A1_14','10A1','MH10',4,4,1,'2025-2026'),
('TKB_10A1_15','10A1','MH05',4,5,1,'2025-2026');

-- Thứ 5
INSERT INTO THOIKHOABIEU (MaTKB, MaLop, MaMon, Thu, Tiet, HocKy, NamHoc) VALUES
('TKB_10A1_16','10A1','MH03',5,1,1,'2025-2026'),
('TKB_10A1_17','10A1','MH04',5,2,1,'2025-2026'),
('TKB_10A1_18','10A1','MH05',5,3,1,'2025-2026'),
('TKB_10A1_19','10A1','MH06',5,4,1,'2025-2026'),
('TKB_10A1_20','10A1','MH10',5,5,1,'2025-2026');

-- Thứ 6
INSERT INTO THOIKHOABIEU (MaTKB, MaLop, MaMon, Thu, Tiet, HocKy, NamHoc) VALUES
('TKB_10A1_21','10A1','MH01',6,1,1,'2025-2026'),
('TKB_10A1_22','10A1','MH02',6,2,1,'2025-2026'),
('TKB_10A1_23','10A1','MH03',6,3,1,'2025-2026'),
('TKB_10A1_24','10A1','MH11',6,4,1,'2025-2026'),
('TKB_10A1_25','10A1','MH11',6,5,1,'2025-2026');

-- Thứ 2
INSERT INTO THOIKHOABIEU (MaTKB, MaLop, MaMon, Thu, Tiet, HocKy, NamHoc) VALUES
('TKB_10A2_01','10A2','MH01',2,1,1,'2025-2026'),
('TKB_10A2_02','10A2','MH02',2,2,1,'2025-2026'),
('TKB_10A2_03','10A2','MH03',2,3,1,'2025-2026'),
('TKB_10A2_04','10A2','MH06',2,4,1,'2025-2026'),
('TKB_10A2_05','10A2','MH10',2,5,1,'2025-2026');

-- Thứ 3
INSERT INTO THOIKHOABIEU VALUES
('TKB_10A2_06','10A2','MH04',3,1,1,'2025-2026'),
('TKB_10A2_07','10A2','MH05',3,2,1,'2025-2026'),
('TKB_10A2_08','10A2','MH11',3,3,1,'2025-2026'),
('TKB_10A2_09','10A2','MH11',3,4,1,'2025-2026'),
('TKB_10A2_10','10A2','MH01',3,5,1,'2025-2026');
-- Thứ 4
INSERT INTO THOIKHOABIEU VALUES
('TKB_10A2_11','10A2','MH02',4,1,1,'2025-2026'),
('TKB_10A2_12','10A2','MH03',4,2,1,'2025-2026'),
('TKB_10A2_13','10A2','MH06',4,3,1,'2025-2026'),
('TKB_10A2_14','10A2','MH10',4,4,1,'2025-2026'),
('TKB_10A2_15','10A2','MH05',4,5,1,'2025-2026');

-- Thứ 5
INSERT INTO THOIKHOABIEU VALUES
('TKB_10A2_16','10A2','MH03',5,1,1,'2025-2026'),
('TKB_10A2_17','10A2','MH04',5,2,1,'2025-2026'),
('TKB_10A2_18','10A2','MH05',5,3,1,'2025-2026'),
('TKB_10A2_19','10A2','MH06',5,4,1,'2025-2026'),
('TKB_10A2_20','10A2','MH10',5,5,1,'2025-2026');

-- Thứ 6
INSERT INTO THOIKHOABIEU VALUES
('TKB_10A2_21','10A2','MH01',6,1,1,'2025-2026'),
('TKB_10A2_22','10A2','MH02',6,2,1,'2025-2026'),
('TKB_10A2_23','10A2','MH03',6,3,1,'2025-2026'),
('TKB_10A2_24','10A2','MH11',6,4,1,'2025-2026'),
('TKB_10A2_25','10A2','MH11',6,5,1,'2025-2026');

-- Thứ 2
INSERT INTO THOIKHOABIEU VALUES
('TKB_11A1_01','11A1','MH01',2,1,1,'2025-2026'),
('TKB_11A1_02','11A1','MH02',2,2,1,'2025-2026'),
('TKB_11A1_03','11A1','MH03',2,3,1,'2025-2026'),
('TKB_11A1_04','11A1','MH06',2,4,1,'2025-2026'),
('TKB_11A1_05','11A1','MH10',2,5,1,'2025-2026');

-- Thứ 3
INSERT INTO THOIKHOABIEU VALUES
('TKB_11A1_06','11A1','MH04',3,1,1,'2025-2026'),
('TKB_11A1_07','11A1','MH05',3,2,1,'2025-2026'),
('TKB_11A1_08','11A1','MH11',3,3,1,'2025-2026'),
('TKB_11A1_09','11A1','MH11',3,4,1,'2025-2026'),
('TKB_11A1_10','11A1','MH01',3,5,1,'2025-2026');

-- Thứ 4
INSERT INTO THOIKHOABIEU VALUES
('TKB_11A1_11','11A1','MH02',4,1,1,'2025-2026'),
('TKB_11A1_12','11A1','MH03',4,2,1,'2025-2026'),
('TKB_11A1_13','11A1','MH06',4,3,1,'2025-2026'),
('TKB_11A1_14','11A1','MH10',4,4,1,'2025-2026'),
('TKB_11A1_15','11A1','MH05',4,5,1,'2025-2026');

-- Thứ 5
INSERT INTO THOIKHOABIEU VALUES
('TKB_11A1_16','11A1','MH03',5,1,1,'2023-2024'),
('TKB_11A1_17','11A1','MH04',5,2,1,'2023-2024'),
('TKB_11A1_18','11A1','MH05',5,3,1,'2023-2024'),
('TKB_11A1_19','11A1','MH06',5,4,1,'2023-2024'),
('TKB_11A1_20','11A1','MH10',5,5,1,'2023-2024');

-- Thứ 6
INSERT INTO THOIKHOABIEU VALUES
('TKB_11A1_21','11A1','MH01',6,1,1,'2025-2026'),
('TKB_11A1_22','11A1','MH02',6,2,1,'2025-2026'),
('TKB_11A1_23','11A1','MH03',6,3,1,'2025-2026'),
('TKB_11A1_24','11A1','MH11',6,4,1,'2025-2026'),
('TKB_11A1_25','11A1','MH11',6,5,1,'2025-2026');

-- Thứ 2
INSERT INTO THOIKHOABIEU VALUES
('TKB_11A2_01','11A2','MH01',2,1,1,'2025-2026'),
('TKB_11A2_02','11A2','MH02',2,2,1,'2025-2026'),
('TKB_11A2_03','11A2','MH03',2,3,1,'2025-2026'),
('TKB_11A2_04','11A2','MH06',2,4,1,'2025-2026'),
('TKB_11A2_05','11A2','MH10',2,5,1,'2025-2026');

-- Thứ 3
INSERT INTO THOIKHOABIEU VALUES
('TKB_11A2_06','11A2','MH04',3,1,1,'2025-2026'),
('TKB_11A2_07','11A2','MH05',3,2,1,'2025-2026'),
('TKB_11A2_08','11A2','MH11',3,3,1,'2025-2026'),
('TKB_11A2_09','11A2','MH11',3,4,1,'2025-2026'),
('TKB_11A2_10','11A2','MH01',3,5,1,'2025-2026');

-- Thứ 4
INSERT INTO THOIKHOABIEU VALUES
('TKB_11A2_11','11A2','MH02',4,1,1,'2025-2026'),
('TKB_11A2_12','11A2','MH03',4,2,1,'2025-2026'),
('TKB_11A2_13','11A2','MH06',4,3,1,'2025-2026'),
('TKB_11A2_14','11A2','MH10',4,4,1,'2025-2026'),
('TKB_11A2_15','11A2','MH05',4,5,1,'2025-2026');

-- Thứ 5
INSERT INTO THOIKHOABIEU VALUES
('TKB_11A2_16','11A2','MH03',5,1,1,'2025-2026'),
('TKB_11A2_17','11A2','MH04',5,2,1,'2025-2026'),
('TKB_11A2_18','11A2','MH05',5,3,1,'2025-2026'),
('TKB_11A2_19','11A2','MH06',5,4,1,'2025-2026'),
('TKB_11A2_20','11A2','MH10',5,5,1,'2025-2026');

-- Thứ 6
INSERT INTO THOIKHOABIEU VALUES
('TKB_11A2_21','11A2','MH01',6,1,1,'2025-2026'),
('TKB_11A2_22','11A2','MH02',6,2,1,'2025-2026'),
('TKB_11A2_23','11A2','MH03',6,3,1,'2025-2026'),
('TKB_11A2_24','11A2','MH11',6,4,1,'2025-2026'),
('TKB_11A2_25','11A2','MH11',6,5,1,'2025-2026');

-- Thứ 2
INSERT INTO THOIKHOABIEU VALUES
('TKB_12A1_01','12A1','MH01',2,1,1,'2025-2026'),
('TKB_12A1_02','12A1','MH02',2,2,1,'2025-2026'),
('TKB_12A1_03','12A1','MH03',2,3,1,'2025-2026'),
('TKB_12A1_04','12A1','MH06',2,4,1,'2025-2026'),
('TKB_12A1_05','12A1','MH10',2,5,1,'2025-2026');

-- Thứ 3
INSERT INTO THOIKHOABIEU VALUES
('TKB_12A1_06','12A1','MH04',3,1,1,'2023-2024'),
('TKB_12A1_07','12A1','MH05',3,2,1,'2023-2024'),
('TKB_12A1_08','12A1','MH11',3,3,1,'2023-2024'),
('TKB_12A1_09','12A1','MH11',3,5,1,'2023-2024'),
('TKB_12A1_10','12A1','MH01',3,6,1,'2023-2024');

-- Thứ 4
INSERT INTO THOIKHOABIEU VALUES
('TKB_12A1_11','12A1','MH02',4,1,1,'2025-2026'),
('TKB_12A1_12','12A1','MH03',4,2,1,'2025-2026'),
('TKB_12A1_13','12A1','MH06',4,3,1,'2025-2026'),
('TKB_12A1_14','12A1','MH10',4,4,1,'2025-2026'),
('TKB_12A1_15','12A1','MH05',4,5,1,'2025-2026');

-- Thứ 5
INSERT INTO THOIKHOABIEU VALUES
('TKB_12A1_16','12A1','MH03',5,1,1,'2025-2026'),
('TKB_12A1_17','12A1','MH04',5,2,1,'2025-2026'),
('TKB_12A1_18','12A1','MH05',5,3,1,'2025-2026'),
('TKB_12A1_19','12A1','MH06',5,4,1,'2025-2026'),
('TKB_12A1_20','12A1','MH10',5,5,1,'2025-2026');

-- Thứ 6
INSERT INTO THOIKHOABIEU VALUES
('TKB_12A1_21','12A1','MH01',6,1,1,'2025-2026'),
('TKB_12A1_22','12A1','MH02',6,2,1,'2025-2026'),
('TKB_12A1_23','12A1','MH03',6,3,1,'2025-2026'),
('TKB_12A1_24','12A1','MH11',6,4,1,'2025-2026'),
('TKB_12A1_25','12A1','MH11',6,5,1,'2025-2026');

-- Thứ 2
INSERT INTO THOIKHOABIEU VALUES
('TKB_12A2_01','12A2','MH01',2,1,1,'2025-2026'),
('TKB_12A2_02','12A2','MH02',2,2,1,'2025-2026'),
('TKB_12A2_03','12A2','MH03',2,3,1,'2025-2026'),
('TKB_12A2_04','12A2','MH06',2,4,1,'2025-2026'),
('TKB_12A2_05','12A2','MH10',2,5,1,'2025-2026');

-- Thứ 3
INSERT INTO THOIKHOABIEU VALUES
('TKB_12A2_06','12A2','MH04',3,1,1,'2025-2026'),
('TKB_12A2_07','12A2','MH05',3,2,1,'2025-2026'),
('TKB_12A2_08','12A2','MH11',3,3,1,'2025-2026'),
('TKB_12A2_09','12A2','MH11',3,4,1,'2025-2026'),
('TKB_12A2_10','12A2','MH01',3,5,1,'2025-2026');

-- Thứ 4
INSERT INTO THOIKHOABIEU VALUES
('TKB_12A2_11','12A2','MH02',4,1,1,'2025-2026'),
('TKB_12A2_12','12A2','MH03',4,2,1,'2025-2026'),
('TKB_12A2_13','12A2','MH06',4,3,1,'2025-2026'),
('TKB_12A2_14','12A2','MH10',4,4,1,'2025-2026'),
('TKB_12A2_15','12A2','MH05',4,5,1,'2025-2026');

-- Thứ 5
INSERT INTO THOIKHOABIEU VALUES
('TKB_12A2_16','12A2','MH03',5,1,1,'2025-2026'),
('TKB_12A2_17','12A2','MH04',5,2,1,'2025-2026'),
('TKB_12A2_18','12A2','MH05',5,3,1,'2025-2026'),
('TKB_12A2_19','12A2','MH06',5,4,1,'2025-2026'),
('TKB_12A2_20','12A2','MH06',5,4,1,'2025-2026');

-- DDFSDFSDFSDFSF
-- Vật Lý (MH02)
INSERT INTO BANGDIEM (MaBD, HocKy, MaHS, MaMon) VALUES
('BD_1001_L', 1, 'HS1001', 'MH02');

INSERT INTO DIEMTHANHPHAN (MaDTP, MaBD, MaMon, HeSo, Diem) VALUES
-- Hệ số 1: 3 cột
('DTP11','BD_1001_L','MH02',1,8.0),
('DTP12','BD_1001_L','MH02',1,8.5),
('DTP13','BD_1001_L','MH02',1,7.5),
-- Hệ số 2: 2 cột
('DTP14','BD_1001_L','MH02',2,8.0),
('DTP15','BD_1001_L','MH02',2,7.5),
-- Hệ số 3: 1 cột
('DTP16','BD_1001_L','MH02',3,8.5);

-- Hóa Học (MH03)
INSERT INTO BANGDIEM VALUES
('BD_1001_H', 1, 'HS1001', 'MH03');

INSERT INTO DIEMTHANHPHAN VALUES
('DTP17','BD_1001_H','MH03',1,7.0),
('DTP18','BD_1001_H','MH03',1,7.5),
('DTP19','BD_1001_H','MH03',1,8.0),
('DTP20','BD_1001_H','MH03',2,8.0),
('DTP21','BD_1001_H','MH03',2,7.5),
('DTP22','BD_1001_H','MH03',3,8.0);

-- Sinh Học (MH04)
INSERT INTO BANGDIEM VALUES
('BD_1001_S',1,'HS1001','MH04');

INSERT INTO DIEMTHANHPHAN VALUES
('DTP23','BD_1001_S','MH04',1,8.0),
('DTP24','BD_1001_S','MH04',1,8.5),
('DTP25','BD_1001_S','MH04',1,7.5),
('DTP26','BD_1001_S','MH04',2,8.0),
('DTP27','BD_1001_S','MH04',2,7.5),
('DTP28','BD_1001_S','MH04',3,8.5);

-- Tin Học (MH05)
INSERT INTO BANGDIEM VALUES
('BD_1001_TN',1,'HS1001','MH05');

INSERT INTO DIEMTHANHPHAN VALUES
('DTP29','BD_1001_TN','MH05',1,9.0),
('DTP30','BD_1001_TN','MH05',1,8.5),
('DTP31','BD_1001_TN','MH05',1,9.0),
('DTP32','BD_1001_TN','MH05',2,9.0),
('DTP33','BD_1001_TN','MH05',2,8.5),
('DTP34','BD_1001_TN','MH05',3,9.0);

-- Tiếng Anh (MH10)
INSERT INTO BANGDIEM VALUES
('BD_1001_A',1,'HS1001','MH10');

INSERT INTO DIEMTHANHPHAN VALUES
('DTP35','BD_1001_A','MH10',1,8.5),
('DTP36','BD_1001_A','MH10',1,9.0),
('DTP37','BD_1001_A','MH10',1,8.0),
('DTP38','BD_1001_A','MH10',2,8.5),
('DTP39','BD_1001_A','MH10',2,9.0),
('DTP40','BD_1001_A','MH10',3,8.5);

-- Thể Dục (MH11)
INSERT INTO BANGDIEM VALUES
('BD_1001_TD',1,'HS1001','MH11');

INSERT INTO DIEMTHANHPHAN VALUES
('DTP41','BD_1001_TD','MH11',1,10.0),
('DTP42','BD_1001_TD','MH11',1,9.5),
('DTP43','BD_1001_TD','MH11',1,9.0),
('DTP44','BD_1001_TD','MH11',2,9.5),
('DTP45','BD_1001_TD','MH11',2,10.0),
('DTP46','BD_1001_TD','MH11',3,10.0);

-- Ngữ Văn (MH06) - bổ sung đầy đủ hệ số 1 → 3, hệ số 2 → 2, hệ số 3 →1
INSERT INTO DIEMTHANHPHAN VALUES
('DTP47','BD_1001_V','MH06',1,7.5),
('DTP48','BD_1001_V','MH06',1,8.0),
('DTP49','BD_1001_V','MH06',1,7.0),
('DTP50','BD_1001_V','MH06',2,7.0),
('DTP51','BD_1001_V','MH06',2,7.5),
('DTP52','BD_1001_V','MH06',3,8.0);

-- Lịch Sử (MH07)
INSERT INTO BANGDIEM (MaBD,HocKy,MaHS,MaMon) VALUES
('BD_1001_LS',1,'HS1001','MH07');

INSERT INTO DIEMTHANHPHAN VALUES
('DTP53','BD_1001_LS','MH07',1,8.0),
('DTP54','BD_1001_LS','MH07',1,8.5),
('DTP55','BD_1001_LS','MH07',1,7.5),
('DTP56','BD_1001_LS','MH07',2,8.0),
('DTP57','BD_1001_LS','MH07',2,7.5),
('DTP58','BD_1001_LS','MH07',3,8.5);

-- Địa Lý (MH08)
INSERT INTO BANGDIEM VALUES
('BD_1001_DL',1,'HS1001','MH08');

INSERT INTO DIEMTHANHPHAN VALUES
('DTP59','BD_1001_DL','MH08',1,7.5),
('DTP60','BD_1001_DL','MH08',1,8.0),
('DTP61','BD_1001_DL','MH08',1,7.0),
('DTP62','BD_1001_DL','MH08',2,7.5),
('DTP63','BD_1001_DL','MH08',2,8.0),
('DTP64','BD_1001_DL','MH08',3,8.0);

-- Giáo Dục Công Dân (MH09)
INSERT INTO BANGDIEM VALUES
('BD_1001_GDC',1,'HS1001','MH09');

INSERT INTO DIEMTHANHPHAN VALUES
('DTP65','BD_1001_GDC','MH09',1,8.5),
('DTP66','BD_1001_GDC','MH09',1,8.0),
('DTP67','BD_1001_GDC','MH09',1,7.5),
('DTP68','BD_1001_GDC','MH09',2,8.0),
('DTP69','BD_1001_GDC','MH09',2,8.5),
('DTP70','BD_1001_GDC','MH09',3,8.5);






