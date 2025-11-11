<?php
$host = 'localhost';
$dbname = 'billiards_clubs';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Đặt timezone MySQL = UTC+7 (Việt Nam) để khớp với PHP
    $pdo->exec("SET time_zone = '+07:00'");
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Đặt timezone PHP = UTC+7
date_default_timezone_set('Asia/Ho_Chi_Minh');
?>