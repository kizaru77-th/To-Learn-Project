<?php
$host = "localhost";
$dbname = "my_login_system";
$username = "root"; // เปลี่ยนตามของคุณ
$password = "";     // เปลี่ยนตามของคุณ

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // ตั้งค่าให้แจ้งเตือนเมื่อเกิด Error
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "เชื่อมต่อฐานข้อมูลล้มเหลว: " . $e->getMessage();
}
?>