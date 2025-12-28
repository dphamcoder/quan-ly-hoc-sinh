<?php
    session_start();
    if (!isset($_SESSION['user']) || $_SESSION['user']['ChucVu'] != 'GIAOVIEN') {
        header("Location: dangNhap.php");
        exit();
    }
    $currentUser = $_SESSION['user'];

    require_once __DIR__ . '/DAL/lophoc.php';
    // Lấy lớp chủ nhiệm (Vì chỉ GVCN mới xem bảng tổng hợp)
    $dsLopChuNhiem = getLopChuNhiem($currentUser['MaTK']);
    $isChuNhiem = !empty($dsLopChuNhiem);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tổng hợp kết quả học tập</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="test.css">
    <style>
        .container { max-width: 100% !important; padding: 0 !important; margin: 0 !important; height: 100vh; }
        body { background: linear-gradient(120deg, #f0f4ff, #e8f7ff); font-family: 'Segoe UI', Tahoma, sans-serif; }
        .table-responsive { overflow-x: auto; max-height: calc(100vh - 250px); border: 1px solid #ccc; background: white;}
        .summary-table thead th { position: sticky; top: 0; z-index: 10; box-shadow: 0 2px 2px rgba(0,0,0,0.1); text-align: center; vertical-align: middle;}
        .summary-table tbody td { text-align: center; vertical-align: middle; }
        .col-name { text-align: left !important; min-width: 200px; }
        .cell-score { border: none; width: 100%; text-align: center; background: transparent; outline: none; }
        .select-custom { font-weight: 700; border: 2px solid #6c757d; cursor: pointer; }
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
                <button onclick="window.location.href='nhapDiem.php'"><i class="fas fa-marker"></i> <span>Nhập điểm bộ môn</span></button>
                <button onclick="window.location.href='danhGiaHanhKiem.php'"><i class="fas fa-user-check"></i> <span>Đánh giá hạnh kiểm</span></button>
                <button class="active" onclick="window.location.href='tongHopKetQua.php'"><i class="fas fa-chart-line"></i> <span>Tổng hợp kết quả học tập</span></button>
                <button onclick="window.location.href='xemtkbGV.php'"><i class="fas fa-calendar-alt"></i> <span>Xem thời khóa biểu</span></button>
                <button class="logout-btn" onclick="dangXuat()"><i class="fas fa-sign-out-alt"></i> <span>Đăng xuất</span></button>
            </nav>
        </aside>

        <main id="mainContent" class="content">
            
            <div class="report-controls p-3">
                <div class="d-flex align-items-center gap-2">
                    <label class="fw-bold text-secondary text-nowrap">Lớp Chủ Nhiệm:</label>
                    <select class="form-select select-custom w-auto" id="classSelect" onchange="fetchReportData()">
                         <?php if (!$isChuNhiem): ?>
                            <option value="">(Không có lớp)</option>
                        <?php else: ?>
                            <?php foreach ($dsLopChuNhiem as $lop): ?>
                                <option value="<?= htmlspecialchars($lop->MaLop) ?>">
                                    Lớp <?= htmlspecialchars($lop->TenLop) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="fw-bold text-primary text-uppercase fs-5 d-none d-md-block mx-auto">
                    <i class="fas fa-file-alt me-2"></i> Bảng Tổng Hợp Kết Quả (HK1)
                </div>
            </div>

            <div class="card" style="border: none; padding: 0; overflow: hidden;">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover summary-table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="col-stt">STT</th>
                                <th class="col-name">Họ và Tên Học Sinh</th>
                                <th style="width: 50px;">Toán</th>
                                <th style="width: 50px;">Văn</th>
                                <th style="width: 50px;">Anh</th>
                                <th style="width: 50px;">Lý</th>
                                <th style="width: 50px;">Hóa</th>
                                <th style="width: 50px;">Sinh</th>
                                <th style="width: 50px;">Sử</th>
                                <th style="width: 50px;">Địa</th>
                                <th style="width: 50px;">GDCD</th>
                                <th style="width: 50px;">Tin</th>
                                <th style="width: 50px;">TD</th>
                                <th style="width: 60px; background: #fff3cd !important;">ĐTB</th>
                                <th style="width: 80px; background: #d1e7dd !important;">Hạnh Kiểm</th>
                                <th style="width: 100px; background: #cfe2ff !important;">Danh Hiệu</th>
                            </tr>
                        </thead>
                        <tbody id="reportBody">
                            <?php if (!$isChuNhiem): ?>
                                <tr><td colspan="16" class="text-center text-danger p-4">Bạn không phải giáo viên chủ nhiệm.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="bottom-actions bg-white border-top p-3 text-center">
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print me-2"></i> In Bảng Điểm
                    </button>
                </div>
            </div>

        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="thongBao.js"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleSidebar');
            if (sidebar) {
                sidebar.classList.toggle('collapsed');
                if (sidebar.classList.contains('collapsed')) toggleBtn.classList.add('moved');
                else toggleBtn.classList.remove('moved');
            }
        }

        function dangXuat() { if(confirm('Đăng xuất?')) window.location.href = 'dangNhap.php'; }

        // Hàm gọi API
        function fetchReportData() {
            const maLop = document.getElementById('classSelect').value;
            const tbody = document.getElementById('reportBody');
            
            if(!maLop) return;
            tbody.innerHTML = '<tr><td colspan="16" class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Đang tính toán...</td></tr>';

            fetch(`api/getTongHop.php?MaLop=${maLop}`)
                .then(res => res.json())
                .then(data => {
                    renderTable(data);
                })
                .catch(err => {
                    console.error(err);
                    tbody.innerHTML = '<tr><td colspan="16" class="text-center p-3 text-danger">Lỗi tải dữ liệu</td></tr>';
                });
        }

        function renderTable(data) {
            const tbody = document.getElementById('reportBody');
            tbody.innerHTML = '';

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="16" class="text-center p-3">Chưa có dữ liệu</td></tr>';
                return;
            }

            data.forEach((st, index) => {
                const tr = document.createElement('tr');
                
                // Tô màu điểm thấp/cao
                const getScoreCell = (score) => {
                    let style = "";
                    if (score !== "" && score < 5.0) style = "color: red; font-weight: bold;";
                    return `<td style="${style}">${score}</td>`;
                };

                // Badge danh hiệu
                let badge = "";
                if(st.danhHieu === 'HS Giỏi') badge = `<span class="badge bg-danger">Giỏi</span>`;
                else if(st.danhHieu === 'HS Tiên Tiến') badge = `<span class="badge bg-primary">Tiên Tiến</span>`;

                tr.innerHTML = `
                    <td>${index + 1}</td>
                    <td class="col-name text-start ps-2 fw-bold">${st.name}</td>
                    
                    ${getScoreCell(st.scores.MH01)} ${getScoreCell(st.scores.MH06)} ${getScoreCell(st.scores.MH10)} ${getScoreCell(st.scores.MH02)} ${getScoreCell(st.scores.MH03)} ${getScoreCell(st.scores.MH04)} ${getScoreCell(st.scores.MH07)} ${getScoreCell(st.scores.MH08)} ${getScoreCell(st.scores.MH09)} ${getScoreCell(st.scores.MH05)} ${getScoreCell(st.scores.MH11)} <td style="background: #fff3cd; font-weight: bold; color: ${st.dtb >= 8 ? '#198754' : '#000'}">${st.dtb}</td>
                    <td>${st.hk || '-'}</td>
                    <td>${badge}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        window.onload = function() {
            const select = document.getElementById('classSelect');
            if (select.options.length > 0 && select.value !== "") {
                select.selectedIndex = 0;
                fetchReportData();
            }
        };
    </script>
</body>
</html>