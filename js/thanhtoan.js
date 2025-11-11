document.getElementById('paymentForm')?.addEventListener('submit', confirmPayment);

function confirmPayment(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    showLoading();
    fetch('../backend/routes.php?action=confirm_payment', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            showToast('Thanh toán thành công!', 'success');
            setTimeout(() => {
                window.location.href = 'quanlyban_phienchoi.php'; // nếu lỗi thì đổi thành quanlyban_phienchoi.php
            }, 1500);
        } else {
            showToast('Lỗi thanh toán!', 'error');
        }
    })
    .catch(() => {
        hideLoading();
        showToast('Lỗi kết nối server!', 'error');
    });
}


