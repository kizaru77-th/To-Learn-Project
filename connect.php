<?php
// ดึงค่า Config จาก Environment Variables บน Render
$host     = getenv('DB_HOST') ?: "localhost";
$port     = getenv('DB_PORT') ?: "3306";
$dbname   = getenv('DB_NAME') ?: "my_login_system";
$username = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASS') ?: "";

try {
    // เพิ่มเครื่องหมาย ; หลัง $host
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_SSL_CA => true,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false
    ];

    $conn = new PDO($dsn, $username, $password, $options);
} catch(PDOException $e) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $e->getMessage());
}
?>