let currentInvoiceId = 0;
let currentTableId = 0;

// Helper functions
function showLoading() {
    if (typeof showLoadingGlobal === 'function') {
        showLoadingGlobal();
    }
}

function hideLoading() {
    if (typeof hideLoadingGlobal === 'function') {
        hideLoadingGlobal();
    }
}

function showToast(message, type) {
    console.log(`[${type.toUpperCase()}] ${message}`);
    if (typeof showToastGlobal === 'function') {
        showToastGlobal(message, type);
    } else {
        alert(message);
    }
}

function openInvoice(invoiceId, tableId) {
    currentInvoiceId = invoiceId;
    currentTableId = tableId;
    
    document.getElementById('invoiceModal').style.display = 'block';
    document.getElementById('modalInvoiceId').textContent = '#' + invoiceId;
    
    loadInvoiceDetails();
    loadServices();
    updateRealtime();
    setInterval(updateRealtime, 30000); // cập nhật mỗi 30s
}

function closeModal() {
    document.getElementById('invoiceModal').style.display = 'none';
    currentInvoiceId = 0;
}

function loadInvoiceDetails() {
    fetch(`../backend/routes.php?action=get_invoice_details&invoice_id=${currentInvoiceId}`)
        .then(r => r.json())
        .then(data => {
            if (data) {
                document.getElementById('modalTableName').textContent = data.table_name || 'N/A';
                document.getElementById('startTime').textContent = data.start_time || 'N/A';
            }
        })
        .catch(err => {
            console.error('Lỗi tải chi tiết hóa đơn:', err);
            showToast('Lỗi tải chi tiết hóa đơn', 'error');
        });
}

function loadServices() {
    // Load danh sách món
    fetch('../backend/routes.php?action=get_services')
        .then(r => r.json())
        .then(services => {
            const select = document.getElementById('serviceSelect');
            if (select) {
                select.innerHTML = '<option value="">Chọn món</option>';
                if (services && Array.isArray(services)) {
                    services.forEach(s => {
                        select.innerHTML += `<option value="${s.ServiceID}" data-price="${s.Price_Service}">${s.ServiceName} - ${formatPrice(s.Price_Service)}₫</option>`;
                    });
                }
            }
        })
        .catch(err => console.error('Lỗi tải dịch vụ:', err));

    // Load món đã gọi
    fetch(`../backend/routes.php?action=get_invoice_services&invoice_id=${currentInvoiceId}`)
        .then(r => r.json())
        .then(items => {
            const tbody = document.querySelector('#servicesTable tbody');
            if (tbody) {
                tbody.innerHTML = '';
                let serviceTotal = 0;
                if (items && Array.isArray(items)) {
                    items.forEach(item => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${item.ServiceName}</td>
                                <td>x${item.Numbers}</td>
                                <td>${formatPrice(item.Price_Services)}₫</td>
                            </tr>
                        `;
                        serviceTotal += parseInt(item.Price_Services) || 0;
                    });
                }
                updateTotal(serviceTotal);
            }
        })
        .catch(err => console.error('Lỗi tải danh sách dịch vụ hóa đơn:', err));
}

function addService() {
    const select = document.getElementById('serviceSelect');
    const qty = document.getElementById('quantity');
    
    if (!select || !qty) {
        console.error('Không tìm thấy select hoặc input quantity');
        showToast('Lỗi: Không tìm thấy phần tử HTML', 'error');
        return;
    }
    
    const serviceId = select.value;
    if (!serviceId) {
        showToast('Chọn món trước!', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('invoiceId', currentInvoiceId);
    formData.append('tableId', currentTableId);
    formData.append('serviceId', serviceId);
    formData.append('numbers', qty.value || 1);

    console.log('Thêm dịch vụ:', { invoiceId: currentInvoiceId, tableId: currentTableId, serviceId, numbers: qty.value });

    showLoading();
    fetch('../backend/routes.php?action=add_service_to_invoice', {
        method: 'POST',
        body: formData
    })
    .then(r => {
        console.log('Response status:', r.status);
        return r.json();
    })
    .then(data => {
        hideLoading();
        console.log('Response data:', data);
        if (data && data.success) {
            showToast('Đã thêm món!', 'success');
            loadServices();
            qty.value = 1;
        } else {
            showToast('Thêm dịch vụ thất bại', 'error');
        }
    })
    .catch(err => {
        hideLoading();
        console.error('Lỗi thêm dịch vụ:', err);
        showToast('Lỗi: ' + err.message, 'error');
    });
}

function updateRealtime() {
    if (!currentInvoiceId) return;
    fetch(`../backend/routes.php?action=get_playing_time&invoice_id=${currentInvoiceId}`)
        .then(r => r.json())
        .then(data => {
            if (data) {
                const playTimeEl = document.getElementById('playTime');
                const tableFeeEl = document.getElementById('tableFee');
                if (playTimeEl) playTimeEl.textContent = (data.hours || 0) + ' giờ';
                if (tableFeeEl) tableFeeEl.textContent = formatPrice(data.table_fee || 0) + '₫';
                updateTotal(data.service_total || 0);
            }
        })
        .catch(err => console.error('Lỗi cập nhật thời gian chơi:', err));
}

function updateTotal(serviceTotal) {
    if (!currentInvoiceId) return;
    fetch(`../backend/routes.php?action=get_playing_time&invoice_id=${currentInvoiceId}`)
        .then(r => r.json())
        .then(data => {
            if (data) {
                const total = (data.table_fee || 0) + (serviceTotal || 0);
                const totalAmountEl = document.getElementById('totalAmount');
                if (totalAmountEl) totalAmountEl.textContent = formatPrice(total) + '₫';
            }
        })
        .catch(err => console.error('Lỗi cập nhật tổng tiền:', err));
}

function confirmPayment() {
    const methodEl = document.getElementById('paymentMethod');
    const totalEl = document.getElementById('totalAmount');
    
    if (!methodEl || !totalEl) {
        console.error('Không tìm thấy phần tử payment method hoặc total amount');
        showToast('Lỗi: Không tìm thấy phần tử HTML', 'error');
        return;
    }

    const method = methodEl.value;
    const total = totalEl.textContent.replace(/₫|,/g, '').trim();

    if (!method) {
        showToast('Chọn phương thức thanh toán!', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('id', currentInvoiceId);
    formData.append('paymentMethod', method);
    formData.append('total_amount', total);

    console.log('Xác nhận thanh toán:', { id: currentInvoiceId, paymentMethod: method, total_amount: total });

    showLoading();
    fetch('../backend/routes.php?action=confirm_payment', {
        method: 'POST',
        body: formData
    })
    .then(r => {
        console.log('Response status:', r.status);
        return r.json();
    })
    .then(data => {
        hideLoading();
        console.log('Response data:', data);
        if (data && data.success) {
            showToast('Thanh toán thành công!', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('Thanh toán thất bại', 'error');
        }
    })
    .catch(err => {
        hideLoading();
        console.error('Lỗi thanh toán:', err);
        showToast('Lỗi: ' + err.message, 'error');
    });
}

function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN').format(price);
}

