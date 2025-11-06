<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billiards Club Management</title>
    <link rel="stylesheet" href="../css/header.css">
    <link rel="stylesheet" href="../css/global.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <i class="fas fa-billiards"></i>
                <span>Billiards Club</span>
            </div>
            <nav class="main-nav">
                <a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="quanlyban_phienchoi.php" class="<?= basename($_SERVER['PHP_SELF']) == 'quanlyban_phienchoi.php' ? 'active' : '' ?>">
                    <i class="fas fa-table"></i> Quản lý Bàn
                </a>
                <a href="quanlydichvu.php" class="<?= basename($_SERVER['PHP_SELF']) == 'quanlydichvu.php' ? 'active' : '' ?>">
                    <i class="fas fa-utensils"></i> Dịch Vụ
                </a>
                <a href="thanhtoan.php" class="<?= basename($_SERVER['PHP_SELF']) == 'thanhtoan.php' ? 'active' : '' ?>">
                    <i class="fas fa-cash-register"></i> Thanh Toán
                </a>
                <a href="quanlyhoadon.php" class="<?= basename($_SERVER['PHP_SELF']) == 'quanlyhoadon.php' ? 'active' : '' ?>">
                    <i class="fas fa-file-invoice"></i> Hóa Đơn
                </a>
                <a href="baocao.php" class="<?= basename($_SERVER['PHP_SELF']) == 'baocao.php' ? 'active' : '' ?>">
                    <i class="fas fa-chart-bar"></i> Báo Cáo
                </a>
            </nav>
            <div class="mobile-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </header>

    <div class="content-wrapper">
        <!-- Nội dung trang sẽ được chèn vào đây -->