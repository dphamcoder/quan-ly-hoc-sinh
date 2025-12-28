<?php
    session_start();
    // 1. Kiểm tra quyền Giáo viên
    if (!isset($_SESSION['user']) || $_SESSION['user']['ChucVu'] != 'GIAOVIEN') {
        header("Location: dangNhap.php");
        exit();
    }
    $currentUser = $_SESSION['user'];

    // 2. Lấy danh sách lớp phân công
    require_once __DIR__ . '/DAL/lophoc.php'; 
    $dsLopPhanCong = getLopByGiaoVien($currentUser['MaTK']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nhập Điểm Bộ Môn</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="test.css">
    <style>
        .header-hs1 { border-top: 5px solid #28a745 !important; }
        .header-hs2 { border-top: 5px solid #dc3545 !important; }
        .header-hs3 { border-top: 5px solid #0d6efd !important; }
        .header-dtb { border-top: 5px solid #ffc107 !important; }
        .grade-input { width: 100%; height: 35px; text-align: center; border: 1px solid #ced4da; border-radius: 4px; font-weight: 600; }
        .grade-input:focus { border-color: #4e73df; outline: none; box-shadow: 0 0 0 3px rgba(78,115,223,0.1); }
        .avg-input { background-color: #e9ecef; color: #4e73df; font-weight: 800; border: none; }
        .header-select { border: none; background: transparent; font-size: 20px; font-weight: 800; color: #333; outline: none; cursor: pointer; max-width: 100%;}
        .bottom-action-bar { margin-top: 20px; padding: 15px; background: #e9ecef; border-radius: 5px; text-align: center; }
        .btn-save-grades { padding: 10px 30px; background-color: #e2e6ea; border: 1px solid #333; font-weight: bold; cursor: pointer; transition: 0.2s;}
        .btn-save-grades:hover { background-color: #333; color: white; }
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
                <button onclick="window.location.href='danhSachLopGV.php'"><i class="fas fa-list-ol"></i> <span>Xem danh sách lớp</span></button>
                <button class="active" onclick="window.location.href='nhapDiem.php'"><i class="fas fa-marker"></i> <span>Nhập điểm bộ môn</span></button>
                <button onclick="window.location.href='danhGiaHanhKiem.php'"><i class="fas fa-user-check"></i> <span>Đánh giá hạnh kiểm</span></button>
                <button onclick="window.location.href='tongHopKetQua.php'"><i class="fas fa-chart-line"></i> <span>Tổng hợp kết quả học tập</span></button>
                <button onclick="window.location.href='xemtkbGV.php'"><i class="fas fa-calendar-alt"></i> <span>Xem thời khóa biểu</span></button>
                <button class="logout-btn" onclick="dangXuat()"><i class="fas fa-sign-out-alt"></i> <span>Đăng xuất</span></button>
            </nav>
        </aside>

        <main id="mainContent" class="content">
            <div class="card" style="height: 100%; display: flex; flex-direction: column;">
                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                    <div style="flex: 1;">
                        <i class="fas fa-list-ul" style="margin-right: 10px; color: #555;"></i>
                        <select class="header-select" id="classSelect" onchange="changeClass()">
                            <?php if (empty($dsLopPhanCong)): ?>
                                <option value="">Chưa được phân công</option>
                            <?php else: ?>
                                <?php foreach ($dsLopPhanCong as $lop): ?>
                                    <option value="<?= htmlspecialchars($lop->MaLop) ?>" 
                                            data-mamon="<?= htmlspecialchars($lop->MaMon) ?>"
                                            data-subject="<?= htmlspecialchars($lop->TenMon) ?>">
                                        Lớp <?= htmlspecialchars($lop->TenLop) ?> - Môn: <?= htmlspecialchars($lop->TenMon) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="table-responsive" style="flex: 1; overflow-y: auto;">
                    <table class="placement-table grade-table" style="width: 100%;">
                        <thead style="position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th rowspan="2" style="width: 50px;">STT</th>
                                <th rowspan="2" style="min-width: 200px;">Họ tên học sinh</th>
                                <th colspan="3" class="header-hs1">Hệ số 1</th>
                                <th colspan="2" class="header-hs2">Hệ số 2</th>
                                <th colspan="1" class="header-hs3">Hệ số 3</th>
                                <th rowspan="2" class="header-dtb" style="width: 80px;">ĐTB</th>
                            </tr>
                            <tr>
                                <td>1</td><td>2</td><td>3</td><td>1</td><td>2</td><td>Thi</td>
                            </tr>
                        </thead>
                        <tbody id="gradeTableBody"></tbody>
                    </table>
                </div>
                <div class="bottom-action-bar">
                    <button class="btn-save-grades" onclick="saveGrades()"><i class="fas fa-save me-2"></i> Lưu thay đổi</button>
                </div>
            </div>
        </main>
    </div>

    <script src="thongBao.js"></script>
    <script>

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

        function changeClass() {
            const select = document.getElementById('classSelect');
            const maLop = select.value;
            const maMon = select.options[select.selectedIndex].getAttribute('data-mamon');
            loadBangDiem(maLop, maMon);
        }

        function loadBangDiem(maLop, maMon) {
            const tbody = document.getElementById('gradeTableBody');
            if (!maLop) { tbody.innerHTML = ''; return; }
            tbody.innerHTML = '<tr><td colspan="9" class="text-center p-3">Đang tải dữ liệu...</td></tr>';

            fetch(`api/getDiem.php?MaLop=${maLop}&MaMon=${maMon}`)
                .then(res => res.json())
                .then(data => {
                    if (!data || data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="9" class="text-center p-3">Lớp này chưa có học sinh</td></tr>';
                        return;
                    }
                    tbody.innerHTML = data.map((st, index) => `
                        <tr data-mahs="${st.MaHS}">
                            <td class="text-center"><b>${index + 1}</b></td>
                            <td class="text-start pl-2 font-weight-bold">${st.HoTenHS}</td>
                            <td><input type="number" class="grade-input" value="${st.DiemM1_1 || ''}" oninput="calcRowAvg(this)"></td>
                            <td><input type="number" class="grade-input" value="${st.DiemM1_2 || ''}" oninput="calcRowAvg(this)"></td>
                            <td><input type="number" class="grade-input" value="${st.DiemM1_3 || ''}" oninput="calcRowAvg(this)"></td>
                            <td><input type="number" class="grade-input" value="${st.DiemM2_1 || ''}" oninput="calcRowAvg(this)"></td>
                            <td><input type="number" class="grade-input" value="${st.DiemM2_2 || ''}" oninput="calcRowAvg(this)"></td>
                            <td><input type="number" class="grade-input" value="${st.DiemThi || ''}" oninput="calcRowAvg(this)"></td>
                            <td><input type="text" class="grade-input avg-input" value="${st.DTB || ''}" readonly></td>
                        </tr>`).join('');
                })
                .catch(err => {
                    console.error(err);
                    tbody.innerHTML = '<tr><td colspan="9" class="text-center p-3 text-danger">Lỗi kết nối API</td></tr>';
                });
        }

        function calcRowAvg(input) {
            const row = input.closest('tr');
            const inputs = row.querySelectorAll('input.grade-input');
            let total = 0; let count = 0;

            for(let i=0; i<3; i++) {
                let val = parseFloat(inputs[i].value);
                if(!isNaN(val)) { total += val * 1; count += 1; }
            }
            for(let i=3; i<5; i++) {
                let val = parseFloat(inputs[i].value);
                if(!isNaN(val)) { total += val * 2; count += 2; }
            }
            let valThi = parseFloat(inputs[5].value);
            if(!isNaN(valThi)) { total += valThi * 3; count += 3; }

            const dtbInput = row.querySelector('.avg-input');
            if(count > 0) dtbInput.value = (total / count).toFixed(1);
            else dtbInput.value = '';
        }

        function saveGrades() {
            const btn = document.querySelector('.btn-save-grades');
            const originalText = btn.innerHTML;
            const select = document.getElementById('classSelect');
            const maLop = select.value;
            const maMon = select.options[select.selectedIndex].getAttribute('data-mamon');

            if (!maLop || !maMon) { alert("Vui lòng chọn lớp!"); return; }

            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
            btn.disabled = true;

            const rows = document.querySelectorAll('#gradeTableBody tr');
            const payload = [];

            rows.forEach(row => {
                const maHS = row.getAttribute('data-mahs'); 
                const inputs = row.querySelectorAll('input.grade-input');
                payload.push({
                    MaHS: maHS,
                    Diem: {
                        "1": [inputs[0].value, inputs[1].value, inputs[2].value],
                        "2": [inputs[3].value, inputs[4].value],
                        "3": [inputs[5].value]
                    }
                });
            });

            fetch('api/saveDiem.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ MaLop: maLop, MaMon: maMon, Data: payload })
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) alert(res.message);
                else alert('Lỗi: ' + res.message);
            })
            .catch(err => { console.error(err); alert('Lỗi kết nối Server!'); })
            .finally(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        }

        function dangXuat() { if(confirm('Đăng xuất?')) window.location.href = 'dangNhap.php'; }

        window.onload = function() {
            const select = document.getElementById('classSelect');
            if (select.options.length > 0 && select.value !== "") {
                select.selectedIndex = 0;
                changeClass();
            }
        };
    </script>
</body>
</html>