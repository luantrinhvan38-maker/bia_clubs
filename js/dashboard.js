// Code JS để lấy dữ liệu dashboard qua AJAX
fetch('../backend/routes.php?action=dashboard_data')
    .then(response => response.json())
    .then(data => console.log(data));