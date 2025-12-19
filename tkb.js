// Thêm biến toàn cục để lưu trạng thái sidebar
let sidebarCollapsed = false;

// Hàm toggle sidebar
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const toggleBtn = document.getElementById('toggleSidebar');
    
    sidebarCollapsed = !sidebarCollapsed;
    
    if (sidebarCollapsed) {
        sidebar.classList.add('collapsed');
        mainContent.classList.add('expanded');
        toggleBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
        toggleBtn.style.left = '80px';
    } else {
        sidebar.classList.remove('collapsed');
        mainContent.classList.remove('expanded');
        toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
        toggleBtn.style.left = '10px';
    }
    
    // Lưu trạng thái vào localStorage
    localStorage.setItem('sidebarCollapsed', sidebarCollapsed);
}

// Khởi tạo trạng thái sidebar từ localStorage
function initSidebarState() {
    const savedState = localStorage.getItem('sidebarCollapsed');
    if (savedState === 'true') {
        sidebarCollapsed = true;
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleBtn = document.getElementById('toggleSidebar');
        
        sidebar.classList.add('collapsed');
        mainContent.classList.add('expanded');
        toggleBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
        toggleBtn.style.left = '80px';
    }
}

// Dữ liệu mẫu thời khóa biểu
const thoiKhoaBieu = {
    "Thứ Hai": ["Toán", "Vật Lý", "Ngữ Văn", "Anh Văn", "Toán", "Hóa Học", "Sinh Học", "Thể Dục"],
    "Thứ Ba": ["Ngữ Văn", "Toán", "Anh Văn", "Vật Lý", "Hóa Học", "Lịch Sử", "Toán", "Sinh Học"],
    "Thứ Tư": ["Anh Văn", "Hóa Học", "Toán", "", "Vật Lý", "Thể Dục", "Sinh Học", "Lịch Sử"],
    "Thứ Năm": ["Vật Lý", "Anh Văn", "Hóa Học", "Toán", "Ngữ Văn", "Sinh Học", "Lịch Sử", "Thể Dục"],
    "Thứ Sáu": ["Toán", "Ngữ Văn", "Sinh Học", "Anh Văn", "Hóa Học", "Vật Lý", "Lịch Sử", "Tự chọn"],
    "Thứ Bảy": ["Anh Văn", "Toán", "Ngữ Văn", "Vật Lý", "Hóa Học", "Sinh Học", "SH Lớp", ""]
};

// Màu sắc cho từng môn học
const subjectColors = {
    "Toán": "#ff6b6b",
    "Ngữ Văn": "#4ecdc4",
    "Anh Văn": "#ffe66d",
    "Vật Lý": "#95e1d3",
    "Hóa Học": "#ff9a76",
    "Sinh Học": "#a8e6cf",
    "Lịch Sử": "#d4a5a5",
    "Thể Dục": "#9d65c9",
    "Tự chọn": "#b5ead7",
    "SH Lớp": "#ffdac1",
    "": "#ffffff"
};

// Tên đầy đủ cho chú thích
const subjectNames = {
    "Toán": "Toán Học",
    "Ngữ Văn": "Ngữ Văn",
    "Anh Văn": "Tiếng Anh",
    "Vật Lý": "Vật Lý",
    "Hóa Học": "Hóa Học",
    "Sinh Học": "Sinh Học",
    "Lịch Sử": "Lịch Sử",
    "Thể Dục": "Thể Dục",
    "Tự chọn": "Tự chọn",
    "SH Lớp": "Sinh hoạt lớp"
};

// Tạo thời khóa biểu
function taoThoiKhoaBieu() {
    const tbody = document.getElementById('tkbBody');
    const thu = ["Thứ Hai", "Thứ Ba", "Thứ Tư", "Thứ Năm", "Thứ Sáu", "Thứ Bảy"];
    
    // Tạo 8 tiết học
    for (let tiet = 1; tiet <= 8; tiet++) {
        const row = document.createElement('tr');
        
        // Thêm cell số tiết
        const tietCell = document.createElement('td');
        tietCell.textContent = tiet;
        row.appendChild(tietCell);
        
        // Thêm môn học cho từng thứ
        thu.forEach(thuItem => {
            const monHoc = thoiKhoaBieu[thuItem][tiet - 1];
            const cell = document.createElement('td');
            
            if (monHoc && monHoc.trim() !== "") {
                const subjectDiv = document.createElement('div');
                subjectDiv.className = 'mon-hoc';
                subjectDiv.textContent = monHoc;
                subjectDiv.style.backgroundColor = subjectColors[monHoc] || '#cccccc';
                subjectDiv.title = `${monHoc} - Tiết ${tiet} ${thuItem}`;
                cell.appendChild(subjectDiv);
            } else if (tiet === 8 && thuItem === "Thứ Bảy") {
                // Ô trống thứ 7 tiết 8
                cell.innerHTML = '<em>Nghỉ</em>';
                cell.style.color = '#999';
            }
            

            
            row.appendChild(cell);
        });
        
        tbody.appendChild(row);
    }
}

// Tạo chú thích màu sắc
function taoChuThich() {
    const legendContainer = document.querySelector('.legend-items');
    
    // Lấy danh sách môn học duy nhất
    const uniqueSubjects = [...new Set(Object.values(thoiKhoaBieu).flat())];
    
    uniqueSubjects.forEach(monHoc => {
        if (monHoc && monHoc.trim() !== "") {
            const legendItem = document.createElement('div');
            legendItem.className = 'legend-item';
            
            legendItem.innerHTML = `
                <span class="color-box" style="background-color: ${subjectColors[monHoc]}"></span>
                <span>${subjectNames[monHoc] || monHoc}</span>
            `;
            
            legendContainer.appendChild(legendItem);
        }
    });
}

// Đăng xuất
function dangXuat() {
    if (confirm("Bạn có chắc muốn đăng xuất?")) {
        localStorage.clear();
        alert("Đăng xuất thành công!");
        // window.location.href = "login.html";
    }
}

// Khởi tạo trang
document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo sidebar state
    initSidebarState();
    
    // Tạo thời khóa biểu
    taoThoiKhoaBieu();
    taoChuThich();
    
    // Thêm sự kiện cho nút toggle
    const toggleBtn = document.getElementById('toggleSidebar');
    toggleBtn.addEventListener('click', toggleSidebar);
    
    // Thêm sự kiện cho nút đăng xuất
    const logoutBtn = document.querySelector('.logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', dangXuat);
    }
    
    // Thêm sự kiện click cho các nút sidebar
    const navButtons = document.querySelectorAll('nav button');
    navButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            if (!this.classList.contains('logout-btn')) {
                navButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Có thể thêm chuyển trang ở đây
                const text = this.querySelector('span')?.textContent || '';
                if (text.includes('thông tin cá nhân')) {
                    alert('Chuyển đến trang thông tin cá nhân');
                } else if (text.includes('kết quả học tập')) {
                    alert('Chuyển đến trang kết quả học tập');
                }
            }
        });
    });
});