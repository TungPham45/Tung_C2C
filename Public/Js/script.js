// Hàm mở Modal và lấy dữ liệu
function openModal(userId) {
    const modal = document.getElementById('accountModal');
    const modalBody = document.getElementById('modalBody');

    modal.style.display = 'flex';
    modalBody.innerHTML = '<p style="text-align:center;">Đang tải dữ liệu...</p>';

    // Đảm bảo đường dẫn này đúng với cấu trúc thư mục của bạn
    fetch(`/quanlyc2c/Public/admin/getDetail/${userId}`) 
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            renderDetail(data); // Hàm này hiển thị dữ liệu lên modal
        })
        .catch(err => {
            console.error(err);
            // Đây là dòng tạo ra lỗi bạn đang thấy
            modalBody.innerHTML = '<div class="alert-error">Không thể kết nối đến máy chủ.</div>';
        });
}

// Hàm render nội dung và các nút chức năng dựa trên trạng thái
function renderDetail(user) {
    const modalBody = document.getElementById('modalBody');
    
    // Logic nút bấm linh hoạt theo trạng thái
    let statusActionBtn = '';
    if (user.trangthai === 'Chờ duyệt') {
        statusActionBtn = `<button class="btn btn-success" onclick="updateStatus(${user.id_nguoidung}, 'Hoạt động')">Phê duyệt</button>`;
    } else if (user.trangthai === 'Hoạt động') {
        statusActionBtn = `<button class="btn btn-warning" onclick="updateStatus(${user.id_nguoidung}, 'Bị khóa')">Khóa tài khoản</button>`;
    } else if (user.trangthai === 'Bị khóa') {
        statusActionBtn = `<button class="btn btn-primary" onclick="updateStatus(${user.id_nguoidung}, 'Hoạt động')">Mở lại tài khoản</button>`;
    }

    modalBody.innerHTML = `
        <div class="detail-container">
            <div class="detail-left">
                <img src="/quanlyc2c/Public/Uploads/Avatars/${user.anhdaidien || 'default.png'}" class="avatar-detail">
            </div>
            <div class="detail-right">
                <p><strong>Tên đăng nhập:</strong> ${user.tentaikhoan}</p> <p><strong>Họ tên:</strong> ${user.hoten}</p>
                <p><strong>Email:</strong> ${user.email}</p>
                <p><strong>Loại:</strong> ${user.loaitaikhoan}</p>
                <p><strong>Số ĐT:</strong> ${user.sdt || 'N/A'}</p>
                <p><strong>Địa chỉ:</strong> ${user.diachi || 'N/A'}</p>
                <p><strong>Trạng thái:</strong> <span class="badge">${user.trangthai}</span></p>
                <p><strong>Ngày tham gia:</strong> ${user.ngaytao}</p>
            </div>
        </div>
        <div class="modal-footer">
            ${statusActionBtn}
            <button class="btn btn-danger" onclick="deleteAccount(${user.id_nguoidung})">Xóa tài khoản</button>
        </div>
    `;
}

// Hàm đóng Modal
function closeModal() {
    document.getElementById('accountModal').style.display = 'none';
}

// Đóng modal khi click ra ngoài khung trắng
window.onclick = function(event) {
    const modal = document.getElementById('accountModal');
    if (event.target == modal) {
        closeModal();
    }
}

function updateStatus(userId, newStatus) {
    if (!confirm(`Bạn có chắc chắn muốn chuyển trạng thái tài khoản này sang "${newStatus}"?`)) return;

    fetch('/quanlyc2c/Public/admin/updateStatus', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${userId}&status=${newStatus}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Cập nhật trạng thái thành công!');
            location.reload(); // Tạm thời reload để cập nhật bảng nhanh nhất
            // Hoặc tối ưu hơn: closeModal(); và cập nhật dòng <tr> tương ứng bằng JS
        } else {
            alert('Lỗi: ' + data.message);
        }
    });
}

// Hàm xóa tài khoản vĩnh viễn
function deleteAccount(userId) {
    if (!confirm('CẢNH BÁO: Hành động này sẽ xóa vĩnh viễn tài khoản và mọi dữ liệu liên quan. Bạn có chắc chắn không?')) return;

    fetch(`/quanlyc2c/Public/admin/deleteAccount/${userId}`, { method: 'POST' })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Đã xóa tài khoản thành công!');
            closeModal();
            location.reload(); 
        } else {
            alert('Lỗi: ' + data.message);
        }
    });
}