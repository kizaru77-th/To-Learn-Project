<?php
// บรรทัดเปิดโหมดแจ้งเตือนเผื่อมี Error ซ่อนอยู่
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // เข้ารหัสลับรหัสผ่าน
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    try {
        // ตรวจสอบก่อนว่าอีเมลนี้ถูกใช้แล้วหรือยัง (ป้องกันข้อผิดพลาด duplicate)
        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $checkStmt->bindParam(':email', $email);
        $checkStmt->execute();
        $exists = $checkStmt->fetchColumn();
        if ($exists) {
            echo "<script>alert('อีเมลนี้ถูกใช้งานแล้ว กรุณาใช้บัญชีอีเมลอื่นหรือเข้าสู่ระบบ'); window.history.back();</script>";
            exit();
        }

        // กำหนดรูปโปรไฟล์เริ่มต้นสำหรับผู้ใช้ใหม่
        $defaultProfile = 'img/Sample_User_Icon.png';

        $stmt = $conn->prepare("INSERT INTO users (email, username, password, profile_picture) VALUES (:email, :username, :password, :profile)");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $passwordHash);
        $stmt->bindParam(':profile', $defaultProfile);
        $stmt->execute();
        
        // ใช้คำสั่งนี้แทน JavaScript ครับ มันจะย้ายหน้าไป login.html ทันทีแบบชัวร์ 100%
        header("Location: login.html");
        exit(); 
        
    } catch(PDOException $e) {
        // หากอีเมลซ้ำหรือติดปัญหาฐานข้อมูล ให้พ่น Error ออกมาดูตรงๆ เลย
        die("ระบบฐานข้อมูลแจ้งเตือนข้อผิดพลาด: " . $e->getMessage());
    }
} else {
    echo "กรุณาส่งข้อมูลมาจากฟอร์มสมัครสมาชิก";
}
?>