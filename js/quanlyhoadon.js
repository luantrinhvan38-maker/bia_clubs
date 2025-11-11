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

window.deleteInvoice = function(invoiceId) {
    console.log('deleteInvoice called with invoiceId:', invoiceId);
    if (!confirm('Xóa hóa đơn này? Tất cả dữ liệu sẽ bị xóa!')) {
        console.log('User cancelled delete');
        return;
    }

    const formData = new FormData();
    formData.append('id', invoiceId);

    console.log('Sending delete request for invoice:', invoiceId);
    showLoading();
    
    fetch('../backend/routes.php?action=delete_invoice', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        return response.text(); // Đọc như text trước để debug
    })
    .then(text => {
        console.log('Raw response text:', text);
        hideLoading();
        
        // Parse JSON
        try {
            const data = JSON.parse(text);
            console.log('Parsed data:', data);
            
            if (data && data.success) {
                showToast('Xóa hóa đơn thành công!', 'success');
                setTimeout(() => location.reload(), 800);
            } else {
                showToast('Không thể xóa: ' + (data?.error || 'Lỗi không xác định'), 'error');
            }
        } catch (e) {
            console.error('JSON parse error:', e);
            showToast('Lỗi phân tích phản hồi: ' + e.message, 'error');
        }
    })
    .catch(err => {
        hideLoading();
        console.error('Fetch error:', err);
        showToast('Lỗi: ' + err.message, 'error');
    });
};

// Tự động làm mới danh sách mỗi 30s
setInterval(() => {
    location.reload();
}, 30000);