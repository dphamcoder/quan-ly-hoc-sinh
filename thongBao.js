/* ==========================================
   FILE: thongBao.js
========================================== */

// 1. DỮ LIỆU CÁN BỘ (MOCK DATA)
const currentUser = {
    name: "Nguyễn Văn B",
    code: "MSCB: 456789123JKQ"
};

// 2. HÀM HIỂN THỊ TÊN CÁN BỘ (Hàm này trước đây bị rỗng)
function loadUserProfile() {
    const nameEl = document.getElementById('userName');
    const codeEl = document.getElementById('userCode');
    
    // Kiểm tra xem trên trang có chỗ để điền tên không
    if (nameEl) nameEl.innerText = currentUser.name;
    if (codeEl) codeEl.innerText = currentUser.code;
}

// 3. HÀM ĐĂNG XUẤT
function dangXuat() {
    if (confirm("Bạn có muốn đăng xuất không ?")) {
        window.location.href = "dangNhap.html";
    }
}

// 4. HÀM NẠP SIDEBAR (Dùng cho cách kế thừa, nếu bạn dùng sidebar cứng thì hàm này không ảnh hưởng)
async function loadSidebar() {
    try {
        // Lưu ý: Tên file HTML sidebar của bạn là 'slideBar.html' hay 'sidebar.html'?
        // Hãy sửa lại tên file ở dòng dưới cho đúng với file bạn đã tạo.
        const response = await fetch('slideBar.html'); 
        if (!response.ok) return; // Nếu không tìm thấy file thì thôi
        
        const html = await response.text();
        const container = document.getElementById('sidebar-container');
        if (container) {
            container.innerHTML = html;
            loadUserProfile(); // Nạp xong thì điền tên luôn
            setActiveLink();   
        }
    } catch (error) {
        console.log("Không dùng sidebar chung hoặc lỗi file.");
    }
}

// 5. HÀM TÔ MÀU MENU
function setActiveLink() {
    const currentPath = window.location.pathname.split("/").pop();
    const links = document.querySelectorAll('nav button');
    
    links.forEach(btn => {
        // Reset style cũ
        btn.classList.remove('active'); 
        
        // Nếu nút này dẫn tới trang hiện tại
        const onclickAttr = btn.getAttribute('onclick');
        if (onclickAttr && onclickAttr.includes(currentPath)) {
            btn.classList.add('active');
        }
    });
}

// 6. HÀM TOGGLE SIDEBAR
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');
    if(sidebar) {
        sidebar.classList.toggle('collapsed');
        if(toggleBtn) {
            sidebar.classList.contains('collapsed') ? toggleBtn.classList.add('moved') : toggleBtn.classList.remove('moved');
        }
    }
}

// 7. HÀM HIỆN THÔNG BÁO (TOAST)
function showToast(message, type = 'success') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = "position: fixed; top: 20px; right: 20px; z-index: 9999;";
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    // Style cho toast
    let bgColor = type === 'error' ? '#e74a3b' : '#1cc88a';
    let iconClass = type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle';
    
    toast.style.cssText = `
        background: white; color: #333; padding: 15px 25px; margin-bottom: 10px;
        border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        display: flex; align-items: center; gap: 10px; min-width: 300px;
        border-left: 5px solid ${bgColor}; animation: slideIn 0.3s ease forwards;
    `;
    
    toast.innerHTML = `<i class="fas ${iconClass}" style="color: ${bgColor}; font-size: 20px;"></i><span style="font-weight: bold;">${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => { toast.remove(); }, 3000);
}

// Style Animation
const styleSheet = document.createElement("style");
styleSheet.innerText = `@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }`;
document.head.appendChild(styleSheet);

// --- TỰ ĐỘNG CHẠY ---
document.addEventListener('DOMContentLoaded', function() {
    // Nếu trang có div #sidebar-container thì nạp sidebar chung
    if(document.getElementById('sidebar-container')) {
        loadSidebar();
    } else {
        // Nếu trang dùng sidebar cứng (như file xepLop.html bạn vừa gửi), thì gọi hàm điền tên luôn
        loadUserProfile();
    }
});