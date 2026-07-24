<?php
// ดึงค่า Config จาก Environment Variables บน Render
$host     = getenv('DB_HOST');
$port     = getenv('DB_PORT') ?: "3306";
$dbname   = getenv('DB_NAME') ?: "my_login_system";
$username = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASS') ?: "";

// ถ้าหา DB_HOST ไม่เจอ (ไม่ได้ตั้งใน Render) ให้ fallback กลับไปที่ 127.0.0.1 แทน localhost
if (!$host) {
    $host = "127.0.0.1"; 
}

try {
    // บังคับการเชื่อมต่อแบบ TCP/IP
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