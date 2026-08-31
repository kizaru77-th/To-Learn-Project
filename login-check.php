<?php
session_start();
require_once 'auth-session.php';
require 'connect.php'; // ดึงไฟล์เชื่อมต่อฐานข้อมูลมาใช้

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // 1. เตรียมคำสั่ง SQL
        $stmt = $conn->prepare("SELECT id, username, email, password, profile_picture FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        // 2. ดึงข้อมูลออกมาในรูปแบบ Array (แก้ไขตรงจุดนี้ให้ปลอดภัยขึ้น)
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 3. ตรวจสอบรหัสผ่าน
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            rememberAuthenticatedUser((int) $user['id']);
            // เก็บ path รูปโปรไฟล์ หรือใช้รูปเริ่มต้นถ้าไม่มี (Sample_User_Icon)
            $profilePicture = !empty($user['profile_picture']) ? $user['profile_picture'] : 'img/Sample_User_Icon.png';
            
            // ส่งค่าไปยัง localStorage เพื่อให้ JavaScript ในหน้า HTML ทำงานต่อได้
            echo "<script>
                localStorage.setItem('isLoggedIn', 'true');
                localStorage.setItem('username', '" . addslashes($user['username']) . "');
                localStorage.setItem('profilePicture', '" . addslashes($profilePicture) . "');
                window.location.href = 'mainweb.html';
            </script>";
            exit();
        } else {
            // ถ้ารหัสไม่ตรง หรือไม่เจออีเมล
            echo "<script>alert('อีเมลหรือรหัสผ่านไม่ถูกต้อง!'); window.history.back();</script>";
        }

    } catch (PDOException $e) {
        // ถ้าเกิด Error เกี่ยวกับ PDO จริงๆ มันจะยอมพ่นข้อความบอกตรงนี้เลย
        echo "เกิดข้อผิดพลาดในการค้นหาข้อมูล: " . $e->getMessage();
    }
}
?>
