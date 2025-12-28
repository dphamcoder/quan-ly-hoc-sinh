<?php
session_start();
if(!isset($_SESSION['user']) || $_SESSION['user']['ChucVu'] != 'CANBO'){
    header("Location: dangNhap.php");
    exit();
}
$currentUser = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xếp Lớp - Hệ Thống Quản Lý</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="test.css">
    <style>
        /* CSS bổ sung cho trang Xếp lớp */
        .gvcn-selector { margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px dashed #ccc; }
        .gvcn-selector label { font-weight: bold; display: block; margin-bottom: 5px; color: #4e73df; }
        .form-select-custom { width: 100%; padding: 8px; border: 1px solid #d1d3e2; border-radius: 4px; font-weight: 600; color: #333; }
        .header-select { border: none; background: transparent; font-size: 16px; font-weight: 800; color: #4e73df; cursor: pointer; outline: none; padding: 5px; border-bottom: 2px solid transparent; transition: all 0.3s; }
        .header-select:hover { border-bottom-color: #4e73df; }
        .full-class { border-color: #e74a3b !important; color: #e74a3b !important; }
    </style>
</head>
<body class="admin-page">

    <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

    <button id="toggleSidebar" class="toggle-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="container">
        <aside id="sidebar" class="sidebar">
            <div class="teacher-card">
                <div class="avatar"></div>
                <h3><?= htmlspecialchars('admin01') ?></h3> 
                <p>MSCB: CB01 </p>
            </div>

            <nav>
                <button onclick="window.location.href='quanLyTaiKhoan.html'">
                    <i class="fas fa-users-cog"></i> <span>Quản lý tài khoản</span>
                </button>
                <button onclick="window.location.href='quanLyHocSinh.html'">
                    <i class="fas fa-user-graduate"></i> <span>Quản lý học sinh</span>
                </button>
                <button onclick="window.location.href='phanCongGiaoVien.html'">
                    <i class="fas fa-chalkboard-teacher"></i> <span>Phân công giảng dạy</span>
                </button>
                <button class="active" onclick="window.location.href='xeplop.html'">
                    <i class="fas fa-calendar-alt"></i> <span>Xếp lớp</span>
                </button>
                <button onclick="window.location.href='danhSachLop.html'">
                    <i class="fas fa-calendar-alt"></i> <span>Xem danh sách lớp</span>
                </button>
                <button onclick="window.location.href='chinhSuaTKB.html'">
                    <i class="fas fa-table"></i> <span>Xếp thời khóa biểu</span>
                </button>
                 <button class="logout-btn" onclick="dangXuat()">
                    <i class="fas fa-sign-out-alt"></i> <span>Đăng xuất</span>
                </button>
            </nav>
        </aside>

        <main id="mainContent" class="content">
            
            <div class="placement-layout">
                
                <div class="left-section">
                    <div class="table-box">
                        <div class="box-header">Danh sách học sinh khả dụng (<span id="countAvailable">0</span>)</div>
                        <div class="table-scroll">
                            <table class="placement-table" id="availableTable">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">STT</th>
                                        <th>Họ và Tên Học Sinh</th>
                                        <th style="width: 100px;">Lớp cũ</th>
                                        <th style="width: 100px;">Học lực</th>
                                        <th style="width: 90px;">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="availableBody"></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="table-box">
                        <div class="box-header">Danh sách học sinh <span id="currentClassNameDisplay">...</span> (<span id="countAdded">0</span>)</div>
                        <div class="table-scroll">
                            <table class="placement-table" id="addedTable">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">STT</th>
                                        <th>Họ và Tên Học Sinh</th>
                                        <th style="width: 100px;">Lớp cũ</th>
                                        <th style="width: 100px;">Học lực</th>
                                        <th style="width: 90px;">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody id="addedBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="right-section">
                    <div class="class-info">
                        <i class="fas fa-layer-group"></i> 
                        <span>
                            <select class="header-select" id="targetClassSelect" onchange="changeClass()">
                                <option value="12A1">Lớp 12A1</option>
                                <option value="12A2">Lớp 12A2</option>
                                <option value="12A3">Lớp 12A3</option>
                            </select>
                        </span>
                    </div>

                    <div class="gvcn-selector">
                        <label><i class="fas fa-user-tie"></i> GV Chủ Nhiệm:</label>
                        <select class="form-select-custom" id="gvcnSelect">
                            <option value="">-- Chọn GVCN --</option>
                        </select>
                    </div>

                    <div class="stat-group">
                        <div class="stat-row"><span>Sĩ số dự kiến :</span><input type="text" class="stat-input" value="40" readonly id="maxStudents"></div>
                        <div class="stat-row"><span>Hiện tại:</span><input type="text" class="stat-input" value="0" id="currentCount" readonly></div>
                        <div class="stat-row"><span style="color: #e74a3b;">Còn thiếu :</span><input type="text" class="stat-input" value="40" id="missingCount" readonly style="color: #e74a3b; border-color: #e74a3b;"></div>
                    </div>

                    <div style="margin-top: auto;"></div>
                    <div class="control-buttons">
                        <button class="btn-control btn-cancel" onclick="resetData()">Hủy</button>
                        <button class="btn-control btn-save" onclick="saveClass()">Lưu</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="thongBao.js"></script>

    <script>
        // DATA
        const db = {
            "12A1": {
                gvcn: "Nguyễn Đình Bình Minh",
                added: [
                    { id: 101, name: "Nguyễn Văn Hoàng", oldClass: "11A1", rank: "Giỏi" },
                    { id: 102, name: "Nguyễn Đình Bình Minh", oldClass: "11A1", rank: "Giỏi" }
                ],
                available: [
                    { id: 1, name: "Phạm Văn Thái Dương", oldClass: "11A1", rank: "Giỏi" },
                    { id: 2, name: "Nguyễn Doãn Hiếu", oldClass: "11A1", rank: "Khá" },
                    { id: 3, name: "Hoàng Văn Hải", oldClass: "11A2", rank: "TB" },
                    { id: 4, name: "Trần Nguyễn Anh Tài", oldClass: "11A1", rank: "Giỏi" }
                ]
            },
            "12A2": {
                gvcn: "Trần Thị C",
                added: [{ id: 201, name: "Lê Văn Luyện", oldClass: "11B2", rank: "TB" }],
                available: [
                    { id: 5, name: "Lê Ngọc Phúc", oldClass: "11B1", rank: "Khá" },
                    { id: 6, name: "Học sinh A", oldClass: "11C1", rank: "TB" },
                    { id: 7, name: "Học sinh B", oldClass: "11A2", rank: "Khá" }
                ]
            },
            "12A3": {
                gvcn: "",
                added: [],
                available: [
                    { id: 8, name: "Học sinh C", oldClass: "11B1", rank: "Giỏi" },
                    { id: 9, name: "Học sinh D", oldClass: "11C2", rank: "Yếu" },
                    { id: 10, name: "Học sinh E", oldClass: "11A1", rank: "Khá" }
                ]
            }
        };

        const listTeachers = ["Nguyễn Đình Bình Minh", "Trần Thị C", "Trần Văn D", "Hoàng Thị H", "Phạm Văn Thái Dương", "Nguyễn Doãn Hiếu"];
        let currentClassId = "12A1";
        let currentData = db["12A1"]; 
        const MAX_STUDENTS = 40;

        function init() {
            renderTeachers();
            changeClass();
            // Gọi hàm từ thongBao.js để load thông tin người dùng
            if(typeof loadUserProfile === 'function') loadUserProfile();
        }

        function changeClass() {
            const select = document.getElementById('targetClassSelect');
            currentClassId = select.value;
            currentData = db[currentClassId];
            document.getElementById('currentClassNameDisplay').innerText = select.options[select.selectedIndex].text;
            document.getElementById('gvcnSelect').value = currentData.gvcn;
            renderTables();
        }

        function renderTables() {
            const availBody = document.getElementById('availableBody');
            const addedBody = document.getElementById('addedBody');
            availBody.innerHTML = '';
            addedBody.innerHTML = '';

            currentData.available.forEach((st, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td class="text-center">${index + 1}</td><td>${st.name}</td><td><div class="fake-input">${st.oldClass}</div></td><td><div class="fake-input">${st.rank}</div></td><td><button class="btn-action-small btn-select" onclick="moveToAdded(${index})">Chọn</button></td>`;
                availBody.appendChild(tr);
            });

            currentData.added.forEach((st, index) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td class="text-center">${index + 1}</td><td>${st.name}</td><td><div class="fake-input">${st.oldClass}</div></td><td><div class="fake-input">${st.rank}</div></td><td><button class="btn-action-small btn-deselect" onclick="moveToAvailable(${index})">Bỏ</button></td>`;
                addedBody.appendChild(tr);
            });
            updateStats();
        }

        function renderTeachers() {
            const select = document.getElementById('gvcnSelect');
            listTeachers.forEach(t => {
                const opt = document.createElement('option');
                opt.value = t; opt.innerText = t; select.appendChild(opt);
            });
        }

        function moveToAdded(index) {
            if (currentData.added.length >= MAX_STUDENTS) {
                // Sử dụng alert nếu showToast chưa sẵn sàng, hoặc showToast từ thongBao.js
                alert("Lớp đã đủ sĩ số!"); return;
            }
            currentData.added.push(currentData.available.splice(index, 1)[0]);
            renderTables();
        }

        function moveToAvailable(index) {
            currentData.available.push(currentData.added.splice(index, 1)[0]);
            renderTables();
        }

        function updateStats() {
            const current = currentData.added.length;
            document.getElementById('countAvailable').innerText = currentData.available.length;
            document.getElementById('countAdded').innerText = current;
            document.getElementById('currentCount').value = current;
            const missingInput = document.getElementById('missingCount');
            if (current >= MAX_STUDENTS) {
                missingInput.value = "ĐỦ"; missingInput.style.color = "green";
            } else {
                missingInput.value = MAX_STUDENTS - current; missingInput.style.color = "#e74a3b";
            }
        }

        function saveClass() {
            const gvcn = document.getElementById('gvcnSelect').value;
            currentData.gvcn = gvcn;
            if(!gvcn) { alert("Vui lòng chọn GV Chủ nhiệm!"); return; }
            alert(`Đã lưu dữ liệu cho lớp ${currentClassId} thành công!`);
        }

        function resetData() {
            if(confirm("Hủy bỏ mọi thay đổi của lớp này?")) location.reload();
        }

        // Hàm toggleSidebar (nếu thongBao.js chưa load kịp hoặc bị lỗi)
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleSidebar');
            sidebar.classList.toggle('collapsed');
            if (sidebar.classList.contains('collapsed')) {
                toggleBtn.classList.add('moved');
            } else {
                toggleBtn.classList.remove('moved');
            }
        }

        window.onload = init;
    </script>
</body>
</html>