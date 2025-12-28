<?php
    session_start();

    if (!isset($_SESSION['user']) || $_SESSION['user']['ChucVu'] != 'GIAOVIEN') {
        header("Location: dangNhap.php");
        exit();
    }
    $currentUser = $_SESSION['user'];

    require_once __DIR__ . '/DAL/lophoc.php'; 
    $dsLopChuNhiem = getLopChuNhiem($currentUser['MaTK']);
    
    $isChuNhiem = !empty($dsLopChuNhiem);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đánh Giá Hạnh Kiểm</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="test.css">
    <style>
        .conduct-wrapper { display: flex; gap: 20px; height: 100%; padding: 10px; box-sizing: border-box; }
        .conduct-list-col { flex: 7; display: flex; flex-direction: column; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05); overflow: hidden; }
        .conduct-detail-col { flex: 3; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.05); padding: 20px; display: flex; flex-direction: column; align-items: center; }
        .conduct-toolbar { padding: 15px; border-bottom: 1px solid #eee; display: flex; align-items: center; gap: 20px; background: #f8f9fc; }
        .conduct-filter-select { padding: 8px 12px; border: 1px solid #d1d3e2; border-radius: 5px; outline: none; font-weight: 600; color: #444; }
        .badge-rating { padding: 5px 12px; border-radius: 15px; font-weight: bold; font-size: 0.85em; display: inline-block; min-width: 80px; text-align: center; }
        .rating-tot { background-color: #d4edda; color: #155724; }
        .rating-kha { background-color: #cce5ff; color: #004085; }
        .rating-tb { background-color: #fff3cd; color: #856404; }
        .rating-yeu { background-color: #f8d7da; color: #721c24; }
        .rating-none { background-color: #e2e3e5; color: #383d41; }
        .conduct-table tbody tr { transition: 0.2s; cursor: pointer; }
        .conduct-table tbody tr:hover { background-color: #f1f1f1; }
        .conduct-table tbody tr.selected { background-color: #e8f0fe; border-left: 4px solid #4e73df; }
        .student-profile-mini { text-align: center; margin-bottom: 30px; width: 100%; }
        .avatar-xl { width: 80px; height: 80px; background-color: #4e73df; border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 30px;}
        .rating-buttons-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; width: 100%; margin-bottom: 30px; }
        .btn-rate { padding: 15px; border-radius: 8px; border: 2px solid #eee; cursor: pointer; text-align: center; transition: all 0.2s; position: relative; font-weight: bold; color: #555; }
        .btn-rate:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .btn-rate.active { border-color: currentColor; background-color: rgba(0,0,0,0.03); }
        .val-tot { color: #28a745; border-color: #d4edda; }
        .val-tot.active { background-color: #d4edda; border-color: #28a745; }
        .val-kha { color: #007bff; border-color: #cce5ff; }
        .val-kha.active { background-color: #cce5ff; border-color: #007bff; }
        .val-tb { color: #ffc107; border-color: #fff3cd; }
        .val-tb.active { background-color: #fff3cd; border-color: #ffc107; }
        .val-yeu { color: #dc3545; border-color: #f8d7da; }
        .val-yeu.active { background-color: #f8d7da; border-color: #dc3545; }
        .btn-save-conduct { width: 100%; padding: 12px; background-color: #4e73df; color: white; border: none; border-radius: 5px; font-weight: bold; font-size: 16px; cursor: pointer; transition: 0.2s; }
        .btn-save-conduct:hover { background-color: #2e59d9; }
        .btn-save-conduct:disabled { background-color: #ccc; cursor: not-allowed; }
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
                <button onclick="window.location.href='danhSachLopGV.php'">
                    <i class="fas fa-list-ol"></i> <span>Xem danh sách lớp</span>
                </button>
                <button onclick="window.location.href='nhapDiem.php'">
                    <i class="fas fa-marker"></i> <span>Nhập điểm bộ môn</span>
                </button>
                <button class="active" onclick="window.location.href='danhGiaHanhKiem.php'">
                    <i class="fas fa-user-check"></i> <span>Đánh giá hạnh kiểm</span>
                </button>
                <button onclick="window.location.href='tongHopKetQua.php'">
                    <i class="fas fa-chart-line"></i> <span>Tổng hợp kết quả học tập</span>
                </button>
                <button onclick="window.location.href='xemtkbGV.php'">
                    <i class="fas fa-calendar-alt"></i> <span>Xem thời khóa biểu</span>
                </button>
                <button class="logout-btn" onclick="dangXuat()">
                    <i class="fas fa-sign-out-alt"></i> <span>Đăng xuất</span>
                </button>
            </nav>
        </aside>

        <main id="mainContent" class="content">
            <div class="conduct-wrapper">
                
                <div class="conduct-list-col">
                    <div class="conduct-toolbar">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-calendar-check text-primary"></i>
                            <select class="conduct-filter-select">
                                <option>Học kỳ 1 (2023-2024)</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-users text-primary"></i>
                            
                            <select class="conduct-filter-select" id="classSelect" onchange="changeClass()">
                                <?php if (!$isChuNhiem): ?>
                                    <option value="">Không có lớp chủ nhiệm</option>
                                <?php else: ?>
                                    <?php foreach ($dsLopChuNhiem as $lop): ?>
                                        <option value="<?= htmlspecialchars($lop->MaLop) ?>">
                                            Lớp <?= htmlspecialchars($lop->TenLop) ?> (Chủ nhiệm)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div style="margin-left: auto; color: #4e73df; font-weight: bold; font-size: 18px;">
                            QUẢN LÝ HẠNH KIỂM
                        </div>
                    </div>

                    <div class="table-scroll">
                        <table class="placement-table conduct-table" style="width: 100%;">
                            <thead style="position: sticky; top: 0; z-index: 10;">
                                <tr>
                                    <th style="width: 50px;">STT</th>
                                    <th style="text-align: left;">Họ và Tên Học Sinh</th>
                                    <th style="width: 150px;">Hạnh Kiểm</th>
                                </tr>
                            </thead>
                            <tbody id="studentTableBody">
                                <?php if (!$isChuNhiem): ?>
                                    <tr><td colspan="3" class="text-center p-4 text-danger">
                                        <i class="fas fa-exclamation-circle"></i> Bạn không phải giáo viên chủ nhiệm nên không thể sử dụng chức năng này.
                                    </td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="conduct-detail-col">
                    <div class="student-profile-mini">
                        <div class="avatar-xl"><i class="fas fa-user-graduate"></i></div>
                        <h3 id="displayName" style="margin:0; color:#333;">Vui lòng chọn lớp</h3>
                        <p id="displayId" style="color:#888; font-weight:600; margin-top:5px;">...</p>
                    </div>

                    <div class="rating-buttons-grid" style="<?= !$isChuNhiem ? 'opacity: 0.5; pointer-events: none;' : '' ?>">
                        <div class="btn-rate val-tot" onclick="rateStudent('Tốt')">
                            <span><i class="fas fa-star me-2"></i> Tốt</span>
                        </div>
                        <div class="btn-rate val-kha" onclick="rateStudent('Khá')">
                            <span><i class="fas fa-thumbs-up me-2"></i> Khá</span>
                        </div>
                        <div class="btn-rate val-tb" onclick="rateStudent('Trung Bình')">
                            <span><i class="fas fa-minus me-2"></i> Trung Bình</span>
                        </div>
                        <div class="btn-rate val-yeu" onclick="rateStudent('Yếu')">
                            <span><i class="fas fa-exclamation-triangle me-2"></i> Yếu</span>
                        </div>
                    </div>

                    <div class="action-footer">
                        <button class="btn-save-conduct" onclick="saveData()" <?= !$isChuNhiem ? 'disabled' : '' ?>>
                            <i class="fas fa-save me-2"></i> Lưu đánh giá
                        </button>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script src="thongBao.js"></script>
    <script>
        let students = [];
        let selectedIndex = null;
        const isChuNhiem = <?= json_encode($isChuNhiem) ?>;

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

        function dangXuat() { if(confirm('Đăng xuất?')) window.location.href = 'dangNhap.php'; }

        function changeClass() {
            if (!isChuNhiem) return; 

            const maLop = document.getElementById('classSelect').value;
            if(!maLop) return;

            const tbody = document.getElementById('studentTableBody');
            tbody.innerHTML = '<tr><td colspan="3" class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Đang tải dữ liệu...</td></tr>';
            
            document.getElementById('displayName').innerText = "Vui lòng chọn học sinh";
            document.getElementById('displayId').innerText = "...";
            selectedIndex = null;
            updateRatingButtons(null);

            fetch(`api/getHanhKiem.php?MaLop=${maLop}`)
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`Lỗi HTTP! Status: ${res.status}`);
                    }
                    return res.json();
                })
                .then(data => {
                    console.log("Dữ liệu nhận được:", data); 
                    students = data;
                    renderTable();
                    if (students.length > 0) selectStudent(0);
                })
                .catch(err => {
                    console.error("Lỗi tải API:", err);
                    tbody.innerHTML = `<tr><td colspan="3" class="text-center p-3 text-danger">
                        Lỗi tải dữ liệu (Xem chi tiết trong F12 Console)<br>
                        Kiểm tra file: api/getHanhKiem.php
                    </td></tr>`;
                });
        }

        function renderTable() {
            const tbody = document.getElementById('studentTableBody');
            tbody.innerHTML = '';

            if(students.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center p-3">Lớp chưa có học sinh</td></tr>';
                return;
            }

            students.forEach((st, index) => {
                const tr = document.createElement('tr');
                if (index === selectedIndex) tr.classList.add('selected');
                tr.onclick = () => selectStudent(index);

                let badgeClass = "rating-none";
                let badgeText = "Chưa đánh giá";
                
                if(st.rank === "Tốt") { badgeClass = "rating-tot"; badgeText = "Tốt"; }
                else if(st.rank === "Khá") { badgeClass = "rating-kha"; badgeText = "Khá"; }
                else if(st.rank === "Trung Bình") { badgeClass = "rating-tb"; badgeText = "TB"; }
                else if(st.rank === "Yếu") { badgeClass = "rating-yeu"; badgeText = "Yếu"; }

                tr.innerHTML = `
                    <td class="text-center">${index + 1}</td>
                    <td style="font-weight: 500;">${st.name}</td>
                    <td class="text-center"><span class="badge-rating ${badgeClass}">${badgeText}</span></td>
                `;
                tbody.appendChild(tr);
            });
        }

        function selectStudent(index) {
            selectedIndex = index;
            renderTable();
            const st = students[index];
            document.getElementById('displayName').innerText = st.name;
            document.getElementById('displayId').innerText = "Mã HS: " + st.MaHS;
            updateRatingButtons(st.rank);
        }

        function updateRatingButtons(rank) {
            document.querySelectorAll('.btn-rate').forEach(btn => btn.classList.remove('active'));
            if(rank === "Tốt") document.querySelector('.val-tot').classList.add('active');
            else if(rank === "Khá") document.querySelector('.val-kha').classList.add('active');
            else if(rank === "Trung Bình") document.querySelector('.val-tb').classList.add('active');
            else if(rank === "Yếu") document.querySelector('.val-yeu').classList.add('active');
        }

        function rateStudent(rank) {
            if (selectedIndex === null || !isChuNhiem) return;
            students[selectedIndex].rank = rank;
            renderTable();
            updateRatingButtons(rank);
            if (selectedIndex < students.length - 1) {
                setTimeout(() => { selectStudent(selectedIndex + 1); }, 200);
            }
        }

        function saveData() {
            if (!isChuNhiem) return;
            
            const btn = document.querySelector('.btn-save-conduct');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
            btn.disabled = true;

            const payload = students.map(st => ({
                id: st.MaHS,
                rank: st.rank
            }));

            fetch('api/saveHanhKiem.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ Data: payload })
            })
            .then(res => res.json())
            .then(res => {
                if(res.success) alert("Đã lưu đánh giá hạnh kiểm!");
                else alert("Lỗi: " + (res.message || "Không lưu được"));
            })
            .catch(err => {
                console.error(err);
                alert("Lỗi kết nối Server!");
            })
            .finally(() => {
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            });
        }

        window.onload = function() {
            if (isChuNhiem) {
                const select = document.getElementById('classSelect');
                if (select.options.length > 0 && select.value !== "") {
                    select.selectedIndex = 0;
                    changeClass();
                }
            }
        };
    </script>
</body>
</html>