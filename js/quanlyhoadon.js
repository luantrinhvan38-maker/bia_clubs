window.deleteInvoice = function(invoiceId) {
    if (!confirm('Xóa hóa đơn này? Tất cả dữ liệu sẽ bị xóa!')) return;

    const formData = new FormData();
    formData.append('id', invoiceId);

    showLoading();
    fetch('../backend/routes.php?action=delete_invoice', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showToast('Xóa hóa đơn thành công!', 'success');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast('Không thể xóa!', 'error');
        }
    });
};

// Tự động làm mới danh sách mỗi 30s
setInterval(() => {
    location.reload();
}, 30000);