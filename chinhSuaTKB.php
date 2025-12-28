<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['ChucVu'] != 'CANBO') {
    header("Location: dangNhap.php");
    exit();
}
$currentUser = $_SESSION['user'];

require_once 'db.php';

$stmtLop = $conn->query("SELECT MaLop, TenLop FROM LOPHOC ORDER BY TenLop");
$dsLop = $stmtLop->fetchAll(PDO::FETCH_ASSOC);

$stmtMon = $conn->query("SELECT MaMon, TenMon FROM MONHOC ORDER BY TenMon");
$dsMon = $stmtMon->fetchAll(PDO::FETCH_ASSOC);

function getSubjectColorClass($tenMon) {

    if (function_exists('mb_strtolower')) {
        $lower = mb_strtolower($tenMon, 'UTF-8');
    } else {
        $lower = strtolower($tenMon);
    }

    if (strpos($lower, 'toán') !== false) return 'math';
    if (strpos($lower, 'văn') !== false) return 'lit';
    if (strpos($lower, 'anh') !== false) return 'eng';
    if (strpos($lower, 'lý') !== false || strpos($lower, 'vật lí') !== false) return 'phys';
    if (strpos($lower, 'hóa') !== false) return 'chem';
    if (strpos($lower, 'sinh') !== false) return 'bio';
    if (strpos($lower, 'sử') !== false) return 'hist';
    if (strpos($lower, 'địa') !== false) return 'geo';
    if (strpos($lower, 'gdcd') !== false) return 'gdcd';
    if (strpos($lower, 'tin') !== false) return 'tech';
    if (strpos($lower, 'thể dục') !== false) return 'pe';
    if (strpos($lower, 'shcn') !== false || strpos($lower, 'chào cờ') !== false) return 'shcn';
    
    return 'math';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xếp Thời Khóa Biểu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="test.css">
    <style>
        .drop-zone { position: relative; height: 60px; padding: 0 !important; vertical-align: middle; background-color: #fff; }
        .drop-zone.drag-over { background-color: #e2e6ea; border: 2px dashed #4e73df; }
        
        .cell-content {
            width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
            font-weight: bold; font-size: 13px; color: white; position: relative; cursor: grab;
            border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .remove-btn {
            position: absolute; top: 2px; right: 2px; font-size: 10px; 
            background: rgba(0,0,0,0.3); color: white; border-radius: 50%; 
            width: 16px; height: 16px; display: none; align-items: center; justify-content: center;
            cursor: pointer; z-index: 10;
        }
        .cell-content:hover .remove-btn { display: flex; }
        .remove-btn:hover { background: red; }

        .subjects-palette { max-height: calc(100vh - 150px); overflow-y: auto; }
        .subject-list { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .draggable-subject {
            padding: 10px; border-radius: 5px; color: white; font-weight: bold; 
            text-align: center; cursor: grab; font-size: 13px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); user-select: none;
        }
        .draggable-subject:active { cursor: grabbing; }
        .draggable-subject.empty { background: #fff; color: #e74a3b; border: 1px dashed #e74a3b; }

        .math { background: linear-gradient(135deg, #4e73df, #224abe); }
        .lit { background: linear-gradient(135deg, #e74a3b, #c0392b); }
        .eng { background: linear-gradient(135deg, #f6c23e, #dda20a); color: #333 !important; }
        .phys { background: linear-gradient(135deg, #36b9cc, #258391); }
        .chem { background: linear-gradient(135deg, #1cc88a, #13855c); }
        .bio { background: linear-gradient(135deg, #20c9a6, #168f76); }
        .hist { background: linear-gradient(135deg, #858796, #60616f); }
        .geo { background: linear-gradient(135deg, #fd7e14, #d35400); }
        .gdcd { background: linear-gradient(135deg, #6610f2, #520dc2); }
        .tech { background: linear-gradient(135deg, #6f42c1, #59359a); }
        .pe { background: linear-gradient(135deg, #28a745, #1e7e34); }
        .shcn { background: #5a5c69; }
    </style>
</head>
<body class="admin-page"> 
    <button id="toggleSidebar" class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>

    <div class="container">
        <aside id="sidebar" class="sidebar">
            <div class="teacher-card">
                <div class="avatar"></div>
                <h3><?= htmlspecialchars($currentUser['TenHienThi'] ?? $currentUser['TenTK']) ?></h3> 
                <p>MSCB: <?= htmlspecialchars($currentUser['MaTK']) ?></p>
            </div>
            <nav>
                <button onclick="window.location.href='quanLyTaiKhoan.php'">
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
                <button class="active" onclick="window.location.href='chinhSuaTKB.php'">
                    <i class="fas fa-table"></i> <span>Xếp thời khóa biểu</span>
                </button>
                 <button class="logout-btn" onclick="dangXuat()">
                    <i class="fas fa-sign-out-alt"></i> <span>Đăng xuất</span>
                </button>
            </nav>
        </aside>

        <main id="mainContent" class="content">
            
            <div class="editor-header">
                <h2><i class="fas fa-edit"></i> Xếp Thời Khóa Biểu</h2>
                <div class="editor-controls">
                    <select id="namHocSelect" class="form-control" style="width: 140px;">
                        <option value="2023-2024">2023-2024</option>
                        <option value="2024-2025">2024-2025</option>
                        <option value="2025-2026">2025-2026</option>
                    </select>

                    <select id="classSelect" class="form-control" style="width: 150px; font-weight: bold; color: var(--primary-color);" onchange="loadTKB()">
                        <option value="">-- Chọn Lớp --</option>
                        <?php foreach ($dsLop as $lop): ?>
                            <option value="<?= htmlspecialchars($lop['MaLop']) ?>">Lớp <?= htmlspecialchars($lop['TenLop']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <button class="btn btn-primary" onclick="saveTKB()"><i class="fas fa-save"></i> Lưu TKB</button>
                </div>
            </div>

            <div class="editor-layout">
                <div class="timetable-wrapper card">
                    <div class="table-responsive">
                        <table class="custom-table timetable editor-table" id="tkbTable">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">Tiết</th>
                                    <th>Thứ 2</th><th>Thứ 3</th><th>Thứ 4</th><th>Thứ 5</th><th>Thứ 6</th><th>Thứ 7</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="session-label"><td colspan="7">Buổi Sáng</td></tr>
                                <?php for($i=1; $i<=4; $i++): ?>
                                <tr>
                                    <td><?= $i ?></td>
                                    <?php for($thu=2; $thu<=7; $thu++): ?>
                                        <td class="drop-zone" 
                                            data-thu="<?= $thu ?>" 
                                            data-tiet="<?= $i ?>"
                                            ondrop="drop(event)" 
                                            ondragover="allowDrop(event)"
                                            ondragenter="dragEnter(event)"
                                            ondragleave="dragLeave(event)">
                                        </td>
                                    <?php endfor; ?>
                                </tr>
                                <?php endfor; ?>

                                <tr class="session-label"><td colspan="7">Buổi Chiều</td></tr>
                                <?php for($i=5; $i<=8; $i++): ?>
                                <tr>
                                    <td><?= $i ?></td>
                                    <?php for($thu=2; $thu<=7; $thu++): ?>
                                        <td class="drop-zone" 
                                            data-thu="<?= $thu ?>" 
                                            data-tiet="<?= $i ?>"
                                            ondrop="drop(event)" 
                                            ondragover="allowDrop(event)"
                                            ondragenter="dragEnter(event)"
                                            ondragleave="dragLeave(event)">
                                        </td>
                                    <?php endfor; ?>
                                </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="subjects-palette card">
                    <h4><i class="fas fa-cubes"></i> Kho môn học</h4>
                    <p class="hint-text">Kéo môn học thả vào bảng</p>
                    
                    <div class="subject-list">
                        <?php foreach ($dsMon as $mon): 

                            $colorClass = getSubjectColorClass($mon['TenMon']);
                        ?>
                            <div class="draggable-subject <?= $colorClass ?>" 
                                 draggable="true" 
                                 ondragstart="drag(event)" 
                                 data-mamon="<?= $mon['MaMon'] ?>" 
                                 data-name="<?= htmlspecialchars($mon['TenMon']) ?>">
                                 <?= htmlspecialchars($mon['TenMon']) ?>
                            </div>
                        <?php endforeach; ?>
                        
                        <div class="draggable-subject empty" draggable="true" ondragstart="drag(event)" data-mamon="" data-name="">
                            <i class="fas fa-eraser"></i> (Xóa tiết)
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="thongBao.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.querySelector('.toggle-btn').classList.toggle('moved');
        }

        function drag(ev) {
            ev.dataTransfer.setData("maMon", ev.target.getAttribute('data-mamon'));
            ev.dataTransfer.setData("text", ev.target.getAttribute('data-name'));
            ev.dataTransfer.setData("class", ev.target.className);
        }

        function allowDrop(ev) {
            ev.preventDefault();
        }

        function dragEnter(ev) {
            if(ev.target.classList.contains('drop-zone')) {
                ev.target.classList.add('drag-over');
            }
        }

        function dragLeave(ev) {
            if(ev.target.classList.contains('drop-zone')) {
                ev.target.classList.remove('drag-over');
            }
        }

        function drop(ev) {
            ev.preventDefault();
            document.querySelectorAll('.drop-zone').forEach(el => el.classList.remove('drag-over'));

            var maMon = ev.dataTransfer.getData("maMon");
            var name = ev.dataTransfer.getData("text");
            var originalClass = ev.dataTransfer.getData("class");
            
            var targetCell = ev.target.closest('.drop-zone');
            if (!targetCell) return;

            if (maMon === "") {
                clearCellLogic(targetCell);
            } else {
                var colorClass = originalClass.replace("draggable-subject", "").trim();
                renderCellContent(targetCell, maMon, name, colorClass);
            }
        }

        function clearCell(icon) {
            var cell = icon.closest('.drop-zone');
            clearCellLogic(cell);
        }

        function clearCellLogic(cell) {
            cell.innerHTML = "";
            cell.removeAttribute('data-mamon'); 
            cell.classList.remove('filled');
        }

        function renderCellContent(cell, maMon, name, colorClass) {
            cell.innerHTML = `
                <div class="cell-content ${colorClass}">
                    ${name} 
                    <i class="fas fa-times remove-btn" onclick="clearCell(this)"></i>
                </div>`;
            cell.setAttribute('data-mamon', maMon); 
            cell.classList.add('filled');
        }

        function loadTKB() {
            const maLop = document.getElementById('classSelect').value;
            const namHoc = document.getElementById('namHocSelect').value;
            const hocKy = 1;

            document.querySelectorAll('.drop-zone').forEach(cell => clearCellLogic(cell));

            if(!maLop) return;

            fetch(`api/getTKBByMaLop.php?MaLop=${maLop}&NamHoc=${namHoc}&HocKy=${hocKy}`)
                .then(res => res.json())
                .then(data => {
                    if(data && data.length > 0) {
                        data.forEach(item => {
                            const cell = document.querySelector(`.drop-zone[data-thu="${item.Thu}"][data-tiet="${item.Tiet}"]`);
                            if(cell) {
                                const colorClass = getSubjectColorClassJS(item.TenMon); 
                                renderCellContent(cell, item.MaMon || '', item.TenMon, colorClass);
                            }
                        });
                    }
                })
                .catch(err => console.error("Lỗi load TKB:", err));
        }

        function saveTKB() {
            const maLop = document.getElementById('classSelect').value;
            const namHoc = document.getElementById('namHocSelect').value;
            const hocKy = 1;

            if(!maLop) { alert("Vui lòng chọn lớp!"); return; }

            const dataToSave = [];
            const cells = document.querySelectorAll('.drop-zone[data-mamon]'); 

            cells.forEach(cell => {
                dataToSave.push({
                    thu: cell.getAttribute('data-thu'),
                    tiet: cell.getAttribute('data-tiet'),
                    maMon: cell.getAttribute('data-mamon')
                });
            });


            fetch('api/saveTKB.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    maLop: maLop,
                    hocKy: hocKy,
                    namHoc: namHoc,
                    data: dataToSave
                })
            })
            .then(res => res.json())
            .then(resp => {
                if(resp.success) {
                    alert("Lưu thời khóa biểu thành công!");
                } else {
                    alert("Lỗi: " + resp.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert("Lỗi kết nối server!");
            });
        }


        function getSubjectColorClassJS(name) {
            if(!name) return 'math';
            const lower = name.toLowerCase();
            if(lower.includes('toán')) return 'math';
            if(lower.includes('văn')) return 'lit';
            if(lower.includes('anh')) return 'eng';
            if(lower.includes('lý')) return 'phys';
            if(lower.includes('hóa')) return 'chem';
            if(lower.includes('sinh')) return 'bio';
            if(lower.includes('sử')) return 'hist';
            if(lower.includes('địa')) return 'geo';
            if(lower.includes('gdcd')) return 'gdcd';
            if(lower.includes('tin')) return 'tech';
            if(lower.includes('thể dục')) return 'pe';
            if(lower.includes('shcn')) return 'shcn';
            return 'math';
        }

        function dangXuat() {
            if(confirm('Bạn có chắc chắn muốn đăng xuất?')) window.location.href = 'dangNhap.php';
        }


        window.onload = function() {
            document.getElementById('namHocSelect').value = "2023-2024";
        }
    </script>
</body>
</html>