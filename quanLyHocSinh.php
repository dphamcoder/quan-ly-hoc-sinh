<?php
session_start();

// Kiểm tra quyền Cán bộ
if (!isset($_SESSION['user']) || $_SESSION['user']['ChucVu'] != 'CANBO') {
    header("Location: dangNhap.php");
    exit();
}
$currentUser = $_SESSION['user'];

require_once 'db.php';

try {
    // Lấy danh sách học sinh
    $stmt = $conn->prepare("SELECT * FROM HOCSINH ORDER BY HoTenHS");
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Lấy danh sách lớp
    $stmtClass = $conn->prepare("SELECT MaLop, TenLop FROM LOPHOC ORDER BY TenLop");
    $stmtClass->execute();
    $classes = $stmtClass->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $students = [];
    $classes = [];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản Lý Học Sinh</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="test.css" />
    <style>
        .admin-table th, .admin-table td { white-space: nowrap; padding: 10px; font-size: 13px; border: 1px solid #eee; }
        .form-row { display: flex; gap: 10px; margin-bottom: 10px; }
        .form-col { flex: 1; }
        .admin-form label { margin-bottom: 3px; display: block; color: #555; font-weight: bold; font-size: 12px;}
        .admin-table tbody tr:hover { background-color: #f9f9f9; cursor: pointer; }
        .admin-table-container { overflow-x: auto; max-height: 600px; }
        .selected { background-color: #e2e6ea !important; } /* Highlight dòng chọn */
    </style>
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
                <button onclick="window.location.href='quanLyTaiKhoan.php'"><i class="fas fa-users-cog"></i> <span>Quản lý tài khoản</span></button>
                <button class="active" onclick="window.location.href='quanLyHocSinh.php'"><i class="fas fa-user-graduate"></i> <span>Quản lý học sinh</span></button>
                <button onclick="window.location.href='phanCongGiaoVien.php'"><i class="fas fa-chalkboard-teacher"></i> <span>Phân công giảng dạy</span></button>
                <button onclick="window.location.href='xeplop.php'"><i class="fas fa-calendar-alt"></i> <span>Xếp lớp</span></button>
                <button onclick="window.location.href='danhSachLop.php'"><i class="fas fa-calendar-alt"></i> <span>Xem danh sách lớp</span></button>
                <button onclick="window.location.href='chinhSuaTKB.php'"><i class="fas fa-table"></i> <span>Xếp thời khóa biểu</span></button>
                <button class="logout-btn" onclick="dangXuat()"><i class="fas fa-sign-out-alt"></i> <span>Đăng xuất</span></button>
            </nav>
        </aside>

        <main id="mainContent" class="content">
            <div class="admin-wrapper">
                
                <div class="admin-main-col card">
                    <div class="admin-header">
                        <h2><i class="fas fa-list"></i> DANH SÁCH HỌC SINH</h2>
                        
                        <div style="display: flex; gap: 10px;">
                            <select id="filterClassSelect" class="form-control" style="width: 150px; height: 40px;" onchange="filterData()">
                                <option value="">-- Tất cả lớp --</option>
                                <?php foreach($classes as $c): ?>
                                    <option value="<?= htmlspecialchars($c['MaLop']) ?>">
                                        <?= htmlspecialchars($c['TenLop']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <div class="search-box">
                                <input type="text" id="searchInput" placeholder="Tìm tên, mã HS..." onkeyup="filterData()" />
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>

                    <div class="admin-table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Mã HS</th>
                                    <th>Họ và Tên</th>
                                    <th>Lớp</th>
                                    <th>Ngày sinh</th>
                                    <th>Giới tính</th>
                                    <th>SĐT</th>
                                    <th>Địa chỉ</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody"></tbody>
                        </table>
                    </div>
                </div>

                <div class="admin-right-col card" style="width: 450px">
                    <div class="mini-profile text-center">
                        <div class="avatar" style="width: 70px; height: 70px; margin: 0 auto 5px; font-size: 25px;"></div>
                        <h3 id="display-name" style="font-size: 16px;">Học sinh mới</h3>
                        <p id="display-id" style="font-size: 13px; color: #777;">---</p>
                    </div>

                    <div class="admin-form" style="font-size: 13px;">
                        
                        <h4 style="margin: 10px 0 5px 0; border-bottom: 1px solid #eee; color: #007bff;">1. Thông tin cá nhân</h4>
                        
                        <div class="form-row">
                            <div class="form-col">
                                <label>Mã học sinh (*)</label>
                                <input type="text" class="form-control" id="detail-code" placeholder="Nhập mã mới..." />
                            </div>
                            <div class="form-col">
                                <label>Họ và Tên (*)</label>
                                <input type="text" class="form-control" id="detail-name" />
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-col">
                                <label>CCCD / Mã định danh</label>
                                <input type="text" class="form-control" id="detail-cic" />
                            </div>
                            <div class="form-col">
                                <label>Ngày sinh (*)</label>
                                <input type="date" class="form-control" id="detail-dob" />
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-col">
                                <label>Lớp (*)</label>
                                <select class="form-control" id="detail-class">
                                    <option value="">-- Chọn lớp --</option>
                                    <?php foreach($classes as $c): ?>
                                        <option value="<?= htmlspecialchars($c['MaLop']) ?>"><?= htmlspecialchars($c['TenLop']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-col">
                                <label>Giới tính</label>
                                <select class="form-control" id="detail-gender">
                                    <option value="Nam">Nam</option>
                                    <option value="Nữ">Nữ</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-col">
                                <label>SĐT</label>
                                <input type="text" class="form-control" id="detail-phone" />
                            </div>
                            <div class="form-col">
                                <label>Email</label>
                                <input type="text" class="form-control" id="detail-email" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Địa chỉ thường trú</label>
                            <input type="text" class="form-control" id="detail-address" />
                        </div>
                        <div class="form-group">
                            <label>Quê quán</label>
                            <input type="text" class="form-control" id="detail-hometown" />
                        </div>

                        <div class="form-row">
                            <div class="form-col">
                                <label>Dân tộc</label>
                                <input type="text" class="form-control" id="detail-ethnicity" />
                            </div>
                            <div class="form-col">
                                <label>Tôn giáo</label>
                                <input type="text" class="form-control" id="detail-religion" />
                            </div>
                        </div>

                        <h4 style="margin: 15px 0 5px 0; border-bottom: 1px solid #eee; color: #007bff;">2. Thông tin gia đình</h4>
                        
                        <div class="form-row">
                            <div class="form-col">
                                <label>Họ tên Bố</label>
                                <input type="text" class="form-control" id="detail-father" />
                            </div>
                            <div class="form-col">
                                <label>Nghề nghiệp</label>
                                <input type="text" class="form-control" id="detail-father-job" />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-col">
                                <label>Họ tên Mẹ</label>
                                <input type="text" class="form-control" id="detail-mother" />
                            </div>
                            <div class="form-col">
                                <label>Nghề nghiệp</label>
                                <input type="text" class="form-control" id="detail-mother-job" />
                            </div>
                        </div>

                        <div class="action-buttons" style="margin-top: 20px;">
                            <button class="btn btn-success" onclick="resetForm()">Làm mới (Thêm)</button>
                            <button class="btn btn-primary" onclick="handleAction('add')">Lưu Mới</button>
                            <button class="btn btn-warning" onclick="handleAction('edit')">Cập nhật</button>
                            <button class="btn btn-danger" onclick="handleAction('delete')">Xóa</button>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        var mockStudents = <?php echo json_encode($students); ?>;

        // --- 1. RENDER BẢNG ---
        function renderTable(data) {
            const tbody = document.getElementById("tableBody");
            tbody.innerHTML = "";
            
            if(!data || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center">Không tìm thấy dữ liệu</td></tr>';
                return;
            }

            data.forEach((st, index) => {
                const tr = document.createElement("tr");
                tr.onclick = () => selectStudent(st, tr);
                
                // Hiển thị ngày tháng năm sinh đẹp hơn
                const dob = st.NgaySinh ? new Date(st.NgaySinh).toLocaleDateString('vi-VN') : '';

                tr.innerHTML = `
                    <td>${index + 1}</td>
                    <td><strong>${st.MaHS}</strong></td>
                    <td>${st.HoTenHS}</td>
                    <td>${st.MaLop || ''}</td>
                    <td>${dob}</td>
                    <td>${st.GioiTinh || ''}</td>
                    <td>${st.S0DT || st.SoDT || ''}</td>
                    <td>${st.HoKhau || ''}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        // --- 2. CHỌN HỌC SINH (CHẾ ĐỘ SỬA) ---
        function selectStudent(st, rowElement) {
            document.querySelectorAll("#tableBody tr").forEach(r => r.classList.remove('selected'));
            if(rowElement) rowElement.classList.add('selected');

            // Điền dữ liệu
            const codeInput = document.getElementById("detail-code");
            codeInput.value = st.MaHS;
            
            // === KHÓA Ô MÃ HS (QUAN TRỌNG) ===
            codeInput.readOnly = true; 
            codeInput.style.backgroundColor = "#e9ecef"; 
            // ==================================

            document.getElementById("detail-name").value = st.HoTenHS;
            document.getElementById("detail-cic").value = st.MaDinhDanh || '';
            document.getElementById("detail-dob").value = st.NgaySinh || '';
            document.getElementById("detail-class").value = st.MaLop || "";
            document.getElementById("detail-gender").value = st.GioiTinh || "Nam";
            document.getElementById("detail-phone").value = st.S0DT || st.SoDT || ''; 
            document.getElementById("detail-email").value = st.Email || '';
            document.getElementById("detail-address").value = st.HoKhau || '';
            document.getElementById("detail-hometown").value = st.QueQuan || '';
            document.getElementById("detail-ethnicity").value = st.DanToc || '';
            document.getElementById("detail-religion").value = st.TonGiao || '';
            
            document.getElementById("detail-father").value = st.HoTenBo || '';
            document.getElementById("detail-father-job").value = st.NgheNghiepBo || '';
            document.getElementById("detail-mother").value = st.HoTenMe || '';
            document.getElementById("detail-mother-job").value = st.NgheNghiepMe || '';

            document.getElementById("display-name").innerText = st.HoTenHS;
            document.getElementById("display-id").innerText = "Đang sửa: " + st.MaHS;
        }

        // --- 3. LÀM MỚI (CHẾ ĐỘ THÊM) ---
        function resetForm() {
            const codeInput = document.getElementById("detail-code");
            codeInput.value = "";
            
            // === MỞ KHÓA ĐỂ NHẬP MỚI ===
            codeInput.readOnly = false;
            codeInput.style.backgroundColor = "#fff"; 
            // ===========================

            // Xóa trắng input
            const ids = ["detail-name", "detail-cic", "detail-dob", "detail-phone", "detail-email", 
                        "detail-address", "detail-hometown", "detail-ethnicity", "detail-religion", 
                        "detail-father", "detail-father-job", "detail-mother", "detail-mother-job"];
            ids.forEach(id => document.getElementById(id).value = "");

            document.getElementById("detail-class").value = "";
            document.getElementById("detail-gender").value = "Nam";

            document.getElementById("display-name").innerText = "Học sinh mới";
            document.getElementById("display-id").innerText = "---";
            
            document.querySelectorAll("#tableBody tr").forEach(r => r.classList.remove('selected'));
        }

        // --- 4. XỬ LÝ API (LOGIC CHẶN) ---
        async function handleAction(type) {
            const maHS = document.getElementById("detail-code").value.trim();
            const tenHS = document.getElementById("detail-name").value.trim();
            const isReadOnly = document.getElementById("detail-code").readOnly;

            // 1. NÚT LƯU MỚI (ADD)
            if (type === 'add') {
                // Nếu đang khóa mã (đang chọn người cũ) mà bấm Thêm -> Reset form
                if (isReadOnly) { resetForm(); return; } 

                // Kiểm tra rỗng
                if (!maHS || !tenHS) { 
                    alert("Vui lòng nhập Mã học sinh và Họ tên!"); 
                    return; 
                }
            }

            // 2. NÚT CẬP NHẬT (EDIT)
            if (type === 'edit') {
                // Nếu đang mở mã (đang nhập mới) mà bấm Cập nhật -> Chặn
                if (!isReadOnly) {
                    alert("Bạn đang ở chế độ nhập mới. Vui lòng bấm nút 'Lưu Mới' để thêm, hoặc chọn một học sinh trong danh sách để sửa.");
                    return;
                }
            }

            // 3. NÚT XÓA (DELETE)
            if (type === 'delete') {
                if (!isReadOnly) { alert("Vui lòng chọn học sinh cần xóa!"); return; }
                if (!confirm('Bạn có chắc chắn muốn xóa học sinh này và toàn bộ dữ liệu liên quan?')) return;
            }

            // --- GỬI DỮ LIỆU ---
            const formData = new FormData();
            formData.append('action', type);
            formData.append('MaHS', maHS);
            formData.append('HoTenHS', tenHS);
            formData.append('MaDinhDanh', document.getElementById("detail-cic").value);
            formData.append('NgaySinh', document.getElementById("detail-dob").value);
            formData.append('MaLop', document.getElementById("detail-class").value);
            formData.append('GioiTinh', document.getElementById("detail-gender").value);
            formData.append('SoDT', document.getElementById("detail-phone").value);
            formData.append('Email', document.getElementById("detail-email").value);
            formData.append('HoKhau', document.getElementById("detail-address").value);
            formData.append('QueQuan', document.getElementById("detail-hometown").value);
            formData.append('DanToc', document.getElementById("detail-ethnicity").value);
            formData.append('TonGiao', document.getElementById("detail-religion").value);
            formData.append('HoTenBo', document.getElementById("detail-father").value);
            formData.append('NgheNghiepBo', document.getElementById("detail-father-job").value);
            formData.append('HoTenMe', document.getElementById("detail-mother").value);
            formData.append('NgheNghiepMe', document.getElementById("detail-mother-job").value);

            try {
                // Gọi API
                const response = await fetch('API/api_hocsinh.php', { method: 'POST', body: formData });
                const result = await response.text();
                
                alert(result);
                
                if (result.includes("thành công")) {
                    location.reload();
                }
            } catch (error) { 
                console.error(error); alert("Lỗi kết nối server!"); 
            }
        }

        // --- 5. TÌM KIẾM & LỌC ---
        function filterData() {
            const term = document.getElementById("searchInput").value.toLowerCase();
            const classFilter = document.getElementById("filterClassSelect").value;

            const filtered = mockStudents.filter(s => {
                const matchesSearch = (s.HoTenHS && s.HoTenHS.toLowerCase().includes(term)) || 
                                      (s.MaHS && s.MaHS.toLowerCase().includes(term));
                const matchesClass = classFilter === "" || (s.MaLop === classFilter);
                return matchesSearch && matchesClass;
            });
            renderTable(filtered);
        }

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.querySelector('.toggle-btn').classList.toggle('moved');
        }

        function dangXuat() {
            if(confirm('Đăng xuất?')) window.location.href = 'dangNhap.php';
        }

        window.onload = () => { renderTable(mockStudents); };
    </script>
</body>
</html>