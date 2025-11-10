document.addEventListener('DOMContentLoaded', function () {
    // Tạo bàn mới
    document.getElementById('createTableForm')?.addEventListener('submit', createTable);



    // === HÀM MỚI: BẮT ĐẦU CHƠI ===
    window.startPlaying = function(tableId) {
        if (!confirm('Bắt đầu phiên chơi cho bàn này?')) return;

        const formData = new FormData();
        formData.append('table_id', tableId);

        showLoading();
        fetch('../backend/routes.php?action=start_session', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showToast('Đã mở hóa đơn! Bàn chuyển sang ĐANG CHƠI', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'Lỗi khi mở hóa đơn!', 'error');
            }
        })
        .catch(() => {
            hideLoading();
            showToast('Lỗi kết nối!', 'error');
        });
    };

    // Cập nhật trạng thái bàn
    window.updateStatus = function(tableId, status) {
        const formData = new FormData();
        formData.append('id', tableId);
        formData.append('status', status);

        showLoading();
        fetch('../backend/routes.php?action=update_table_status', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showToast('Cập nhật trạng thái thành công!', 'success');
                setTimeout(() => location.reload(), 800);
            } else {
                showToast('Lỗi khi cập nhật!', 'error');
            }
        })
        .catch(() => {
            hideLoading();
            showToast('Lỗi kết nối server!', 'error');
        });
    };

    // Xóa bàn
    window.deleteTable = function(tableId) {
        if (!confirm('Bạn có chắc muốn xóa bàn này?')) return;

        const formData = new FormData();
        formData.append('id', tableId);

        showLoading();
        fetch('../backend/routes.php?action=delete_table', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showToast('Xóa bàn thành công!', 'success');
                setTimeout(() => location.reload(), 800);
            } else {
                showToast('Không thể xóa bàn đang sử dụng!', 'error');
            }
        });
    };
});

function createTable(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    showLoading();
    fetch('../backend/routes.php?action=create_table', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showToast('Thêm bàn mới thành công!', 'success');
            form.reset();
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('Lỗi: Tên bàn đã tồn tại!', 'error');
        }
    })
    .catch(() => {
        hideLoading();
        showToast('Lỗi kết nối!', 'error');
    });
}

// Hiệu ứng loading
function showLoading() {
    let loader = document.getElementById('global-loader');
    if (!loader) {
        loader = document.createElement('div');
        loader.id = 'global-loader';
        loader.innerHTML = `
            <div class="loader">
                <div class="spinner"></div>
                <p>Đang xử lý...</p>
            </div>
        `;
        document.body.appendChild(loader);
    }
    loader.style.display = 'flex';
}

function hideLoading() {
    const loader = document.getElementById('global-loader');
    if (loader) loader.style.display = 'none';
}

// Thông báo toast
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 100);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}