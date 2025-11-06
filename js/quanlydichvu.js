document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('createServiceForm')?.addEventListener('submit', createService);
});

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
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('Lỗi: Tên dịch vụ đã tồn tại!', 'error');
        }
    });
}

// Re-use loading & toast từ file trước
function showLoading() { /* same as above */ }
function hideLoading() { /* same as above */ }
function showToast(message, type = 'success') { /* same as above */ }