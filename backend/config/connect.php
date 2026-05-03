<?php
$conn = new mysqli("localhost", "root", "", "phongkhamchuyennghiep");
if ($conn->connect_error) {
    die("Lỗi kết nối DB: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
<?php
// backend/config/connect.php

$host = 'localhost';
$db   = 'phongkhamchuyennghiep';
$user = 'root';
$pass = ''; // Mặc định của Wampserver là rỗng
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Biến $pdo phải được viết chính xác như thế này
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}