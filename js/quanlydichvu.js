document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('createServiceForm')?.addEventListener('submit', createService);
});

// Helper functions
function showLoading() {
    console.log('Loading...');
}

function hideLoading() {
    console.log('Done loading');
}

function showToast(message, type = 'success') {
    console.log(`[${type.toUpperCase()}] ${message}`);
    alert(message);
}

window.deleteService = function(serviceId) {
    if (!confirm('Xóa dịch vụ này? Dữ liệu không thể khôi phục!')) return;

    const formData = new FormData();
    formData.append('id', serviceId);

    showLoading();
    fetch('../backend/routes.php?action=delete_service', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showToast('Xóa dịch vụ thành công!', 'success');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast('Không thể xóa dịch vụ đang được sử dụng!', 'error');
        }
    })
    .catch(err => {
        hideLoading();
        console.error('Lỗi xóa dịch vụ:', err);
        showToast('Lỗi: ' + err.message, 'error');
    });
};

function createService(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    showLoading();
    fetch('../backend/routes.php?action=create_service', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showToast('Thêm dịch vụ thành công!', 'success');
            form.reset();
            // Reload lại danh sách dịch vụ
            location.reload();
        } else {
            showToast('Lỗi: ' + (data.error || 'Không thể thêm dịch vụ!'), 'error');
        }
    })
    .catch(err => {
        hideLoading();
        console.error('Lỗi thêm dịch vụ:', err);
        showToast('Lỗi: ' + err.message, 'error');
    });
}