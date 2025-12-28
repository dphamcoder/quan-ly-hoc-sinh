<?php
session_start();
require_once "db.php";

// 1. Kiểm tra quyền HOCSINH
if (!isset($_SESSION['user']) || $_SESSION['user']['ChucVu'] != 'HOCSINH') {
    header("Location: dangNhap.php");
    exit();
}

$maHS = $_SESSION['user']['MaTK'];
// 2. Lấy thông tin LỚP của học sinh này từ DB
$studentInfo = null;
try {
    $conn = getConnection();
    $sql = "SELECT HoTenHS, MaLop FROM HOCSINH WHERE MaHS = :maHS";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['maHS' => $maHS]);
    $studentInfo = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Lỗi kết nối: " . $e->getMessage());
}

// Nếu không tìm thấy HS hoặc HS chưa được xếp lớp
if (!$studentInfo) {
    die("Không tìm thấy thông tin học sinh.");
}
$maLop = $studentInfo['MaLop']; // Lớp 10A1, 10A2...
$hoTen = $studentInfo['HoTenHS'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả học tập</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="test.css">
    <style>
        .score-good { color: #1cc88a; font-weight: 800; } 
        .score-bad { color: #e74a3b; font-weight: 800; }  
        .score-avg { color: #4e73df; font-weight: 800; }
        
        /* Căn giữa ô nhập điểm */
        .score-input { text-align: center; border: none; background: transparent; width: 100%; }
        .score-table th, .score-table td { text-align: center; vertical-align: middle; }
        .score-table td:first-child { text-align: left; font-weight: bold; padding-left: 15px; }
    </style>
</head>
<body>

    <button id="toggleSidebar" class="toggle-btn" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="container">
        <aside id="sidebar" class="sidebar">
            <div class="teacher-card">
                <div class="avatar"></div>
                <h3><?= htmlspecialchars($hoTen) ?></h3> 
                <p>MSHS: <?= htmlspecialchars($maHS) ?></p>
            </div>
            <nav>
                <button onclick="window.location.href='thongTinCaNhan.php'">
                    <i class="fas fa-user"></i> <span>Thông tin cá nhân</span>
                </button>
                <button onclick="window.location.href='xemThoiKhoaBieu.php'">
                    <i class="fas fa-calendar-alt"></i> <span>Thời khóa biểu</span>
                </button>
                <button class="active" onclick="window.location.href='xemDiem.php'">
                    <i class="fas fa-chart-line"></i> <span>Kết quả học tập</span>
                </button>
                <button class="logout-btn" onclick="dangXuat()">
                    <i class="fas fa-sign-out-alt"></i> <span>Đăng xuất</span>
                </button>
            </nav>
        </aside>

        <main id="mainContent" class="content">
            
            <h2 style="margin-bottom: 20px; color: var(--primary-color);">
                <i class="fas fa-chart-bar"></i> KẾT QUẢ HỌC TẬP
            </h2>

            <div style="margin-bottom: 20px;">
                <label style="font-weight: bold;">Chọn học kỳ:</label>
                <select class="semester-select" id="semesterSelect" onchange="loadData()" style="padding: 5px 10px; border-radius: 5px; margin-left: 10px;">
                    <option value="1">Học kì 1</option>
                    <option value="2">Học kì 2</option>
                </select>
            </div>

            <div class="card card-score">
                <div class="table-responsive">
                    <table class="score-table table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 25%">MÔN HỌC</th>
                                <th colspan="3">Hệ số 1 (Miệng/15p)</th>
                                <th colspan="2">Hệ số 2 (GK)</th>
                                <th>Hệ số 3 (CK)</th>
                                <th style="background-color: #f8f9fa;">ĐTB Môn</th>
                            </tr>
                        </thead>
                        <tbody id="scoreTableBody">
                            <tr><td colspan="8">Đang tải dữ liệu...</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="score-footer" style="margin-top: 20px; padding: 15px; background: #f8f9fc; border-radius: 8px; display: flex; justify-content: space-around;">
                    <div class="score-summary-item">
                        <span>Điểm trung bình HK: </span>
                        <strong id="finalAvg" style="font-size: 1.2em; color: #4e73df;">...</strong>
                    </div>
                    <div class="score-summary-item">
                        <span>Xếp loại học lực: </span>
                        <strong id="finalRank">...</strong>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="thongBao.js"></script>
    <script>
        const MA_HS = "<?= $maHS ?>";

        function loadData() {
            const hocKy = document.getElementById('semesterSelect').value;
            const tbody = document.getElementById('scoreTableBody');
            
            tbody.innerHTML = '<tr><td colspan="8" class="text-center"><i class="fas fa-spinner fa-spin"></i> Đang tải bảng điểm...</td></tr>';

            fetch(`api/getBangDiemHS.php?MaHS=${encodeURIComponent(MA_HS)}&HocKy=${hocKy}`)
                .then(res => res.json())
                .then(data => {
                    if (!data || data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="8" class="text-center">Chưa có dữ liệu điểm</td></tr>';
                        document.getElementById('finalAvg').innerText = "...";
                        document.getElementById('finalRank').innerText = "...";
                        return;
                    }
                    renderTable(data);
                })
                .catch(err => {
                    console.error(err);
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Lỗi kết nối API</td></tr>';
                });
        }

        function renderTable(data) {
            const tbody = document.getElementById('scoreTableBody');
            tbody.innerHTML = '';

            let sumAvg = 0;
            let count = 0;

            data.forEach(sub => {
                const avg = sub.avg;

                if (avg !== null && avg !== "") {
                    sumAvg += parseFloat(avg);
                    count++;
                }

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="text-start pl-3 font-weight-bold">${sub.name}</td>

                    ${createScoreCell(sub.hs1[0])}
                    ${createScoreCell(sub.hs1[1])}
                    ${createScoreCell(sub.hs1[2])}

                    ${createScoreCell(sub.hs2[0])}
                    ${createScoreCell(sub.hs2[1])}

                    ${createScoreCell(sub.hs3)}

                    <td style="background-color: #fff3cd; font-weight: bold; color: #4e73df;">
                        ${avg ?? '-'}
                    </td>
                `;
                tbody.appendChild(row);
            });

            // Tính tổng kết
            if (count > 0) {
                const final = (sumAvg / count).toFixed(1);
                document.getElementById('finalAvg').innerText = final;

                let rank = "Yếu", color = "#e74a3b";
                if (final >= 8.0) { rank = "Giỏi"; color = "#1cc88a"; }
                else if (final >= 6.5) { rank = "Khá"; color = "#4e73df"; }
                else if (final >= 5.0) { rank = "Trung Bình"; color = "#f6c23e"; }

                const rankEl = document.getElementById('finalRank');
                rankEl.innerText = rank;
                rankEl.style.color = color;
            } else {
                document.getElementById('finalAvg').innerText = "...";
                document.getElementById('finalRank').innerText = "...";
            }
        }

        function createScoreCell(score) {
            let style = "";
            if (score !== '' && score != null) {
                if (score >= 8.0) style = "color: #1cc88a; font-weight: bold;";
                else if (score < 5.0) style = "color: #e74a3b; font-weight: bold;";
            }
            return `<td>${score !== '' && score != null ? `<span style="${style}">${score}</span>` : ''}</td>`;
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleSidebar');
            if(sidebar) {
                sidebar.classList.toggle('collapsed');
                if (sidebar.classList.contains('collapsed')) {
                    if(toggleBtn) toggleBtn.classList.add('moved');
                } else {
                    if(toggleBtn) toggleBtn.classList.remove('moved');
                }
            }
        }

        function dangXuat() { if(confirm('Đăng xuất?')) window.location.href = 'dangNhap.php'; }

        window.onload = function() {
            loadData();
        };
    </script>
</body>
</html>