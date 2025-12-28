<?php
session_start();

// 1. KIỂM TRA QUYỀN: Chỉ Cán bộ (CANBO) mới được vào
if (!isset($_SESSION['user']) || $_SESSION['user']['ChucVu'] != 'CANBO') {
    header("Location: dangNhap.php");
    exit();
}
$currentUser = $_SESSION['user'];

require_once 'db.php';

try {
    // 2. Lấy danh sách lớp
    $stmtLop = $conn->prepare("SELECT MaLop, TenLop FROM lophoc ORDER BY TenLop");
    $stmtLop->execute();
    $lops = $stmtLop->fetchAll(PDO::FETCH_ASSOC);

    // 3. Lấy danh sách giáo viên
    $stmtGV = $conn->prepare("SELECT MaGV, HoTenGV FROM giaovien ORDER BY HoTenGV");
    $stmtGV->execute();
    $allTeachers = $stmtGV->fetchAll(PDO::FETCH_ASSOC);

    // 4. Lấy danh sách môn học
    $stmtMon = $conn->prepare("SELECT MaMon, TenMon FROM monhoc ORDER BY TenMon");
    $stmtMon->execute();
    $subjects = $stmtMon->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Phân công giảng dạy</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
    <link rel="stylesheet" href="test.css" />

    <style>
      /* CSS giữ nguyên từ file gốc của bạn */
      .assign-layout { display: flex; gap: 20px; height: calc(100vh - 100px); overflow: hidden; }
      .assign-col-left { flex: 4; display: flex; flex-direction: column; overflow-y: auto; padding-right: 5px; }
      .assign-col-right { flex: 6; display: flex; flex-direction: column; border-left: 2px solid #e3e6f0; padding-left: 20px; }
      .top-filters { display: flex; gap: 15px; margin-bottom: 20px; }
      .filter-box { background: var(--white); border: 2px solid #d1d3e2; padding: 8px 15px; border-radius: 5px; font-weight: 700; color: var(--text-dark); display: flex; align-items: center; gap: 10px; cursor: pointer; }
      .subject-row { display: flex; align-items: center; margin-bottom: 12px; }
      .subject-label { width: 90px; font-weight: 700; color: #555; text-align: right; padding-right: 15px; font-size: 14px; }
      .subject-input { flex: 1; border: 1px solid #b7b9cc; background: var(--white); padding: 8px 15px; border-radius: 4px; font-weight: 600; text-align: center; cursor: pointer; transition: all 0.2s; min-height: 40px; display: flex; align-items: center; justify-content: center; }
      .subject-input.active, .teacher-row.active { background-color: #66ff66; border-color: #4cd14c; color: black; }
      .teacher-list-wrapper { flex: 1; border: 2px solid #b7b9cc; background: var(--white); overflow-y: auto; margin-bottom: 20px; }
      .teacher-row { display: flex; border-bottom: 1px solid #d1d3e2; cursor: pointer; font-size: 14px; }
      .teacher-row:hover { background-color: #f1f3f9; }
      .t-stt { width: 50px; text-align: center; padding: 10px; border-right: 1px solid #d1d3e2; font-weight: bold; display: flex; align-items: center; justify-content: center; }
      .t-name { flex: 1; padding: 10px; display: flex; align-items: center; }
      .action-area { display: flex; flex-direction: column; gap: 10px; width: 80%; margin: 0 auto; }
      .btn-large-action { width: 100%; padding: 12px; background: var(--white); border: 1px solid var(--text-dark); color: var(--text-dark); font-weight: 700; border-radius: 4px; text-transform: uppercase; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); transition: all 0.2s; cursor: pointer; }
      .btn-large-action:hover { background: var(--text-dark); color: var(--white); }
      ::-webkit-scrollbar { width: 12px; }
      ::-webkit-scrollbar-track { background: #f1f1f1; }
      ::-webkit-scrollbar-thumb { background: black; border: 2px solid #f1f1f1; }
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
          <button onclick="window.location.href='quanLyTaiKhoan.php'">
            <i class="fas fa-users-cog"></i> <span>Quản lý tài khoản</span>
          </button>
          <button onclick="window.location.href='quanLyHocSinh.php'">
            <i class="fas fa-user-graduate"></i> <span>Quản lý học sinh</span>
          </button>
          <button class="active" onclick="window.location.href='phanCongGiaoVien.php'">
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
        <div class="top-filters">
          <div class="filter-box">
            <select id="classSelect" onchange="loadAssigned()">
                <?php foreach($lops as $lop): ?>
                    <option value="<?= htmlspecialchars(trim($lop['MaLop'])) ?>">
                        <?= htmlspecialchars($lop['TenLop']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
          </div>
          <div class="filter-box">
            <select>
              <option>Học kì 1 - 2025/2026</option>
            </select>
          </div>
        </div>

        <div class="assign-layout">
          <div class="assign-col-left" id="subjectListContainer"></div>

          <div class="assign-col-right">
            <div class="filter-box mb-3" style="width: fit-content; margin: 0 auto 15px auto">
              <i class="fas fa-list"></i>
              <span id="teacherListTitle">DS Giáo viên</span>
            </div>

            <div class="teacher-list-wrapper">
              <div class="teacher-row" style="background: #ccc; font-weight: bold; position: sticky; top: 0; z-index: 10;">
                <div class="t-stt" style="border-right: 1px solid #999">STT</div>
                <div class="t-name" style="justify-content: center">Giáo viên</div>
              </div>

              <div id="teacherListContainer"></div>
            </div>

            <div class="action-area">
              <button class="btn-large-action" onclick="assignTeacher()">Chọn giáo viên</button>
              <button class="btn-large-action" onclick="removeTeacher()">Xóa giáo viên</button>
              <button class="btn-large-action">Chọn làm GV chủ nhiệm</button>
            </div>
          </div>
        </div>
      </main>
    </div>

    <script src="thongBao.js"></script>

    <script>
    // --- 1. DỮ LIỆU TỪ PHP ---
    const allTeachers = <?php echo json_encode($allTeachers); ?>;
    const allSubjects = <?php echo json_encode($subjects); ?>;
    let assignedData = []; 

    let selectedSubjectIndex = null;
    let selectedTeacherCode = null;

    // --- 2. CÁC HÀM TẢI DỮ LIỆU TỪ BACKEND ---

    async function loadAssigned() {
        const maLop = document.getElementById("classSelect").value;
        const formData = new FormData();
        formData.append('action', 'fetch_assigned');
        formData.append('MaLop', maLop);

        try {
            const res = await fetch('api_phancong.php', { method: 'POST', body: formData });
            assignedData = await res.json();
            renderSubjects();
        } catch (error) {
            console.error("Lỗi tải dữ liệu:", error);
        }
    }

    // --- 3. CÁC HÀM VẼ GIAO DIỆN (RENDER) ---

    function renderSubjects() {
        const container = document.getElementById("subjectListContainer");
        container.innerHTML = "";

        allSubjects.forEach((sub, index) => {
            const assigned = assignedData.find(a => a.MaMon === sub.MaMon);
            const teacherName = assigned ? assigned.HoTenGV : "Chưa phân công";
            
            const row = document.createElement("div");
            row.className = "subject-row";

            const activeClass = (selectedSubjectIndex === index) ? "active" : "";
            const displayStyle = assigned ? "" : "color: #888; font-style: italic;";

            row.innerHTML = `
                <div class="subject-label">${sub.TenMon}</div>
                <div class="subject-input ${activeClass}" style="${displayStyle}" 
                     onclick="selectSubject(${index}, '${sub.MaMon}')">
                    ${teacherName}
                </div>
            `;
            container.appendChild(row);
        });
    }

    function renderTeachers() {
        const container = document.getElementById("teacherListContainer");
        container.innerHTML = "";
        
        allTeachers.forEach((gv, idx) => {
            const row = document.createElement("div");
            row.className = `teacher-row ${selectedTeacherCode === gv.MaGV ? 'active' : ''}`;
            row.onclick = function () {
                selectedTeacherCode = gv.MaGV;
                renderTeachers(); // Vẽ lại để cập nhật trạng thái xanh (active)
            };

            row.innerHTML = `
                <div class="t-stt">${idx + 1}</div>
                <div class="t-name">${gv.HoTenGV} <small style="margin-left:5px; color:#666">(${gv.MaGV})</small></div>
            `;
            container.appendChild(row);
        });
    }

    // --- 4. XỬ LÝ SỰ KIỆN ---

    function selectSubject(index, maMon) {
        selectedSubjectIndex = index;
        renderSubjects();
        renderTeachers(); // Hiển thị toàn bộ giáo viên để chọn
    }

    async function assignTeacher() {
        if (selectedSubjectIndex === null || !selectedTeacherCode) {
            alert("Vui lòng chọn Môn học (bên trái) và Giáo viên (bên phải)!");
            return;
        }

        const maLop = document.getElementById("classSelect").value;
        const maMon = allSubjects[selectedSubjectIndex].MaMon;

        const formData = new FormData();
        formData.append('action', 'assign');
        formData.append('MaGV', selectedTeacherCode);
        formData.append('MaLop', maLop);
        formData.append('MaMon', maMon);

        const res = await fetch('api_phancong.php', { method: 'POST', body: formData });
        const msg = await res.text();
        alert(msg);
        loadAssigned(); 
    }

    async function removeTeacher() {
        if (selectedSubjectIndex === null) {
            alert("Vui lòng chọn môn học cần xóa!");
            return;
        }
        
        if (!confirm("Bạn có chắc chắn muốn xóa phân công cho môn này không?")) return;

        const maLop = document.getElementById("classSelect").value;
        const maMon = allSubjects[selectedSubjectIndex].MaMon;

        const formData = new FormData();
        formData.append('action', 'remove');
        formData.append('MaLop', maLop);
        formData.append('MaMon', maMon);

        const res = await fetch('api_phancong.php', { method: 'POST', body: formData });
        alert(await res.text());
        loadAssigned();
    }

    function toggleSidebar() {
        document.getElementById("sidebar").classList.toggle("collapsed");
    }

    function dangXuat() {
        if(confirm('Bạn có chắc chắn muốn đăng xuất?')) window.location.href = 'dangNhap.php';
    }

    // Chỉ gọi các hàm load dữ liệu, không gọi loadUserProfile nữa
    window.onload = function () {
        loadAssigned();
        renderTeachers();
    };
    </script>
  </body>
</html>