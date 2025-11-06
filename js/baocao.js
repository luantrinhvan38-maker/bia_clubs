document.addEventListener('DOMContentLoaded', function () {
    loadReportData();
    setInterval(loadReportData, 60000); // Cập nhật mỗi phút
});

function loadReportData() {
    const today = new Date().toISOString().split('T')[0];

    Promise.all([
        fetch(`../backend/routes.php?action=get_revenue&date=${today}`),
        fetch('../backend/routes.php?action=get_popular_services'),
        fetch('../backend/routes.php?action=get_active_tables')
    ])
    .then(responses => Promise.all(responses.map(r => r.json())))
    .then(([revenueData, servicesData, activeData]) => {
        document.querySelector('.stat-card:nth-child(1) .value').textContent = 
            formatCurrency(revenueData.revenue || 0);
        document.querySelector('.stat-card:nth-child(2) .value').textContent = 
            activeData.active || 0;
        document.querySelector('.stat-card:nth-child(3) .value').textContent = 
            servicesData.length > 0 ? servicesData[0].count : 0;
    });
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}