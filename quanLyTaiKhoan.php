<?php
session_start();
// KIỂM TRA QUYỀN: Chỉ Cán bộ mới được vào
if (!isset($_SESSION['user']) || $_SESSION['user']['ChucVu'] != 'CANBO') {
    header("Location: dangNhap.php");
    exit();
}
$currentUser = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Tài khoản</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="test.css">
</head>
<body class="admin-page">
    
    <button id="toggleSidebar" class="toggle-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="container">
        <aside id="sidebar" class="sidebar">
            <div class="teacher-card">
                <div class="avatar"></div>
                <h3><?= htmlspecialchars($currentUser['TenHienThi'] ?? $currentUser['TenTK']) ?></h3> 
                <p>MSCB: <?= htmlspecialchars($currentUser['MaTK']) ?></p>
            </div>
            <nav>
                <button class="active" onclick="window.location.href='quanLyTaiKhoan.php'">
                    <i class="fas fa-users-cog"></i> <span>Quản lý tài khoản</span>
                </button>
                <button onclick="window.location.href='quanLyHocSinh.php'">
                    <i class="fas fa-user-graduate"></i> <span>Quản lý học sinh</span>
                </button>
                <button onclick="window.location.href='phanCongGiaoVien.php'">
                    <i class="fas fa-chalkboard-teacher"></i> <span>Phân công giảng dạy</span>
                </button>
                <button onclick="window.location.href='xeplop.php'">
                    <i class="fas fa-calendar-alt"></i> <span>Xếp lớp</span>
                </button>
                <button onclick="window.location.href='danhSachLop.php'">
                    <i class="fas fa-calendar-alt"></i> <span>Xem danh sách lớp</span>
                </button>
                <button onclick="window.location.href='chinhSuaTKB.php'">
                    <i class="fas fa-table"></i> <span>Xếp thời khóa biểu</span>
                </button>
                 <button class="logout-btn" onclick="dangXuat()">
                    <i class="fas fa-sign-out-alt"></i> <span>Đăng xuất</span>
                </button>
            </nav>
        </aside>

        <main id="mainContent" class="content">
            <div class="admin-wrapper">
                
                <div class="admin-main-col card">
                    <div class="admin-header">
                        <h2>QUẢN LÝ TÀI KHOẢN NGƯỜI DÙNG</h2>
                        <div class="search-box">
                            <input type="text" id="searchInput" placeholder="Tìm tên, ID, SĐT..." onkeyup="searchAccounts()">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>

                    <div class="admin-table-container">
                        <table class="custom-table admin-table" id="userTable">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">STT</th>
                                    <th>ID</th>
                                    <th>Họ và Tên</th>
                                    <th>Vai trò</th>
                                    <th>Email</th>
                                    <th>SĐT</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody"></tbody>
                        </table>
                    </div>
                </div>

                <div class="admin-right-col card">
                    <div class="teacher-card mini-profile">
                        <div class="avatar"></div>
                        <h3 id="display-name">Chọn người dùng</h3>
                        <p id="display-id">---</p>
                    </div>

                    <form class="admin-form">
                        <div class="form-group">
                            <label>Tài khoản (ID) :</label>
                            <input type="text" class="form-control" id="detail-id">
                        </div>

                        <div class="form-group">
                            <label>Họ và Tên :</label>
                            <input type="text" class="form-control" id="detail-name">
                        </div>

                        <div class="form-group">
                            <label>Mật khẩu :</label>
                            <input type="password" class="form-control" id="detail-pass" placeholder="Nhập để đổi mật khẩu..." style="background: #fff;">
                        </div>

                        <div class="form-group">
                            <label>Vai trò :</label>
                            <select class="form-control" id="detail-role">
                                <option value="CANBO">Cán bộ</option>
                                <option value="GIAOVIEN">Giáo viên</option>
                                <option value="HOCSINH">Học sinh</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Email :</label>
                            <input type="text" class="form-control" id="detail-email">
                        </div>

                        <div class="form-group">
                            <label>SĐT :</label>
                            <input type="text" class="form-control" id="detail-SDT">
                        </div>

                        <div class="form-group">
                            <label>Trạng thái :</label>
                            <select class="form-control" id= "detail-active" onchange="changeColor(this)">
                                <option value="Đã kích hoạt" style="color: green; font-weight: bold;">Kích hoạt</option>
                                <option value="Không kích hoạt" style="color: red; font-weight: bold;">Không kích hoạt</option>
                            </select>
                        </div>

                        <div class="action-buttons">
                            <div class="btn-row">
                                <button type="button" class="btn btn-outline" onclick="xoaTrangForm()">Mới</button>
                                <button type="button" class="btn btn-outline" onclick="xoaTaiKhoan()">Xóa</button>
                            </div>
                            <button type="button" class="btn btn-primary full-width" onclick="luuTaiKhoan()">Lưu / Cập nhật</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script src="thongBao.js"></script>

    <script>
        let allAccounts = [];

        window.onload = function() {
            loadData();
            changeColor(document.getElementById('detail-active'));
        };

        // 1. TẢI DỮ LIỆU
        function loadData() {
            // Gọi vào thư mục api
            fetch('api/xuLyTaiKhoan.php?action=fetch')
                .then(res => res.json())
                .then(data => {
                    if(data.error) { alert(data.error); return; }
                    allAccounts = data;
                    renderTable(allAccounts);
                })
                .catch(err => console.error("Lỗi tải data:", err));
        }

        // 2. VẼ BẢNG
        function renderTable(data) {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = ''; 

            if (!data || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;">Không có dữ liệu</td></tr>';
                return;
            }

            data.forEach((acc, index) => {
                const tr = document.createElement('tr');
                tr.onclick = function() { selectRow(this, acc); }; // Truyền thẳng object data vào
                
                const statusStr = (acc.TrangThai == 1) ? 'Đã kích hoạt' : 'Không kích hoạt';
                const statusColor = (acc.TrangThai == 1) ? 'green' : 'red';

                tr.innerHTML = `
                    <td style="text-align: center;">${index + 1}</td>
                    <td>${acc.MaTK}</td>
                    <td>${acc.TenTK}</td>
                    <td>${acc.ChucVu}</td>
                    <td>${acc.Email}</td>
                    <td>${acc.SDT}</td>
                    <td style="color: ${statusColor}; font-weight: bold;">${statusStr}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        // 3. CHỌN DÒNG ĐỂ SỬA
        function selectRow(row, acc) {
            // Highlight
            document.querySelectorAll('#userTable tbody tr').forEach(r => r.classList.remove('selected'));
            row.classList.add('selected');

            // Đổ dữ liệu vào form
            const idInput = document.getElementById('detail-id');
            idInput.value = acc.MaTK;
            
            // === KHÓA Ô ID KHI SỬA ===
            idInput.readOnly = true; 
            idInput.style.backgroundColor = "#e9ecef";
            // ==========================

            document.getElementById('detail-name').value = acc.TenTK;
            document.getElementById('detail-role').value = acc.ChucVu;
            document.getElementById('detail-email').value = acc.Email;
            document.getElementById('detail-SDT').value = acc.SDT;
            document.getElementById('detail-pass').value = ""; // Reset ô pass để tránh hiểu lầm
            
            const statusSelect = document.getElementById('detail-active');
            statusSelect.value = (acc.TrangThai == 1) ? "Đã kích hoạt" : "Không kích hoạt";
            changeColor(statusSelect);

            document.getElementById('display-name').innerText = acc.TenTK;
            document.getElementById('display-id').innerText = acc.MaTK;
        }

        // 4. XÓA TRẮNG FORM (ĐỂ THÊM MỚI)
        function xoaTrangForm() {
            document.querySelector('.admin-form').reset();
            
            // === MỞ KHÓA Ô ID ===
            const idInput = document.getElementById('detail-id');
            idInput.readOnly = false;
            idInput.style.backgroundColor = "white";
            // ====================

            document.getElementById('display-name').innerText = 'Người dùng mới';
            document.getElementById('display-id').innerText = '---';
            document.querySelectorAll('#userTable tbody tr').forEach(r => r.classList.remove('selected'));
            changeColor(document.getElementById('detail-active'));
        }

        // 5. LƯU (TỰ ĐỘNG PHÂN BIỆT THÊM/SỬA)
        function luuTaiKhoan() {
            const ma = document.getElementById('detail-id').value.trim();
            const ten = document.getElementById('detail-name').value.trim();
            
            if (ma === "" || ten === "") {
                alert("Vui lòng nhập Mã và Tên tài khoản!");
                return;
            }

            // Kiểm tra xem đang Thêm hay Sửa dựa vào trạng thái ReadOnly của ô ID
            const isEdit = document.getElementById('detail-id').readOnly;
            const actionType = isEdit ? 'update' : 'insert';
            
            if(!confirm(isEdit ? 'Lưu thay đổi?' : 'Thêm tài khoản mới?')) return;

            const fd = new FormData();
            fd.append('action_type', actionType);
            fd.append('ma', ma);
            fd.append('ten', ten);
            fd.append('pass', document.getElementById('detail-pass').value);
            fd.append('role', document.getElementById('detail-role').value);
            fd.append('email', document.getElementById('detail-email').value);
            fd.append('sdt', document.getElementById('detail-SDT').value);
            fd.append('status', document.getElementById('detail-active').value === 'Đã kích hoạt' ? 1 : 0);

            fetch('api/xuLyTaiKhoan.php?action=save', { method: 'POST', body: fd })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Thành công!');
                        loadData(); 
                        if (!isEdit) xoaTrangForm(); // Nếu là thêm mới thì reset form
                    } else {
                        alert('Lỗi: ' + (data.error || 'Thao tác thất bại'));
                    }
                })
                .catch(err => { console.error(err); alert('Lỗi kết nối server'); });
        }

        // 6. XÓA TÀI KHOẢN
        function xoaTaiKhoan() {
            const ma = document.getElementById('detail-id').value;
            // Chỉ xóa được khi đang chọn 1 dòng (ID đang bị khóa)
            if (!document.getElementById('detail-id').readOnly || !ma) {
                alert('Vui lòng chọn tài khoản từ danh sách để xóa!'); 
                return;
            }

            if (!confirm('Xóa tài khoản ' + ma + '? Hành động này không thể hoàn tác!')) return;

            const fd = new FormData();
            fd.append('ma', ma);

            fetch('api/xuLyTaiKhoan.php?action=delete', { method: 'POST', body: fd })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Đã xóa thành công!');
                        loadData();      
                        xoaTrangForm();  
                    } else {
                        alert(data.error || 'Lỗi xóa tài khoản');
                    }
                });
        }

        // --- Các hàm phụ trợ ---
        function searchAccounts() {
            const filter = document.getElementById('searchInput').value.toUpperCase();
            const filtered = allAccounts.filter(acc =>
                acc.TenTK.toUpperCase().includes(filter) ||
                acc.MaTK.toUpperCase().includes(filter) ||
                (acc.SDT && acc.SDT.includes(filter))
            );
            renderTable(filtered);
        }

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.querySelector('.toggle-btn').classList.toggle('moved');
        }

        function dangXuat() {
            if(confirm('Bạn có chắc chắn muốn đăng xuất?')) window.location.href = 'dangNhap.php';
        }

        function changeColor(element) {
            if (element.value == "Đã kích hoạt") {
                element.style.color = "green";
            } else {
                element.style.color = "red";
            }
            element.style.fontWeight = "bold";
        }
    </script>
</body>
</html>