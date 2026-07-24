<?php
// ตั้งค่าความปลอดภัยของ Session สำหรับ Server Deployment
ini_set('session.cookie_httponly', 1);
if (PHP_VERSION_ID >= 70300) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}
session_start();

// ป้องกัน Browser Prefetch (Chrome / Edge / Safari แอบยิง URL ล่วงหน้าจน Code ถูกใช้ก่อนที่ยูสเซอร์จะเปิดจริง)
if (
    (isset($_SERVER['HTTP_PURPOSE']) && strtolower($_SERVER['HTTP_PURPOSE']) === 'prefetch') ||
    (isset($_SERVER['HTTP_X_PURPOSE']) && strtolower($_SERVER['HTTP_X_PURPOSE']) === 'preview') ||
    (isset($_SERVER['HTTP_SEC_PURPOSE']) && strpos(strtolower($_SERVER['HTTP_SEC_PURPOSE']), 'prefetch') !== false)
) {
    http_response_code(204);
    exit();
}   

require_once 'connect.php';

if (isset($_GET['code'])) {
    $code = trim($_GET['code']);

    // ถ้าพบว่าเคยแลก code นี้สำเร็จไปแล้ว หรือล็อกอินอยู่แล้ว ให้ไปหน้าหลักทันที
    if ((isset($_SESSION['last_success_code']) && $_SESSION['last_success_code'] === $code) || isset($_SESSION['user_id'])) {
        echo "<script>
            localStorage.setItem('isLoggedIn', 'true');
            window.location.href = 'mainweb.html';
        </script>";
        exit();
    }

    $app_id = '992584539930554'; 
    $app_secret = '18e0bbf0605e6c4d2f8ea0ba695e877c';

    // ตรวจสอบโดเมนปัจจุบันแบบ Dynamic (รองรับทั้ง localhost และ render.com)
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    
    if (strpos($host, 'onrender.com') !== false) {
        $redirect_uri = 'https://to-learn-project.onrender.com/facebook-callback.php';
    } else {
        $redirect_uri = $protocol . '://' . $host . '/To-Learn/facebook-callback.php';
    }

    // ยิงแลก Token ด้วย cURL (Facebook Graph API ใช้ GET)
    $token_url = "https://graph.facebook.com/v19.0/oauth/access_token?"
        . "client_id=" . $app_id
        . "&redirect_uri=" . urlencode($redirect_uri)
        . "&client_secret=" . $app_secret
        . "&code=" . urlencode($code);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $token_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);

    $token_data = json_decode($response, true);

    // กรณีแลก Token สำเร็จ
    if (isset($token_data['access_token'])) {
        $_SESSION['last_success_code'] = $code; // จำไว้ว่า code นี้แลกผ่านแล้ว
        $access_token = $token_data['access_token'];

        // 1. ดึงข้อมูลโปรไฟล์ Facebook
        $user_url = "https://graph.facebook.com/me?fields=id,name,picture.type(large)&access_token=" . $access_token;
        $user_response = @file_get_contents($user_url);
        $user_data = json_decode($user_response, true);

        $fb_id = isset($user_data['id']) ? $user_data['id'] : time();
        $username = isset($user_data['name']) ? $user_data['name'] : 'Facebook User';
        $email = $fb_id . '@facebook.com'; 
        $picture = isset($user_data['picture']['data']['url']) ? $user_data['picture']['data']['url'] : '';

        // 2. ค้นหาหรือบันทึกลง Database
        $stmt = $conn->prepare("SELECT id, profile_picture FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            if (!empty($picture)) {
                $current_pic = isset($user['profile_picture']) ? $user['profile_picture'] : '';
                if (!(strpos($current_pic, 'uploads/') === 0)) {
                    $update_stmt = $conn->prepare("UPDATE users SET profile_picture = :pic WHERE id = :id");
                    $update_stmt->execute(['pic' => $picture, 'id' => $user['id']]);
                }
            }
        } else {
            $stmt = $conn->prepare("INSERT INTO users (email, username, password, profile_picture) VALUES (:email, :username, 'FACEBOOK_LOGIN', :pic)");
            $stmt->execute([
                'email' => $email,
                'username' => $username,
                'pic' => $picture
            ]);
            $_SESSION['user_id'] = $conn->lastInsertId();
        }

        // 3. ดึงข้อมูลผู้ใช้เพื่อส่งไปเก็บใน LocalStorage แล้วเปลี่ยนหน้าทันที
        $stmt = $conn->prepare("SELECT username, profile_picture FROM users WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['user_id']]);
        $login_user = $stmt->fetch(PDO::FETCH_ASSOC);

        echo "<script>
            localStorage.setItem('isLoggedIn', 'true');
            localStorage.setItem('username', " . json_encode($login_user['username']) . ");
            localStorage.setItem('profilePicture', " . json_encode($login_user['profile_picture']) . ");
            window.location.href = 'mainweb.html';
        </script>";
        exit();

    } else {
        // หากแลกไม่ผ่าน แต่คนนี้เข้าสู่ระบบไว้แล้วใน Session ให้พาไปหน้าหลักเลย
        if (isset($_SESSION['user_id'])) {
            echo "<script>
                localStorage.setItem('isLoggedIn', 'true');
                window.location.href = 'mainweb.html';
            </script>";
            exit();
        }

        // แสดงผล Error ค้างไว้ เพื่อหยุดวงรอบการเด้งกลับอัตโนมัติ
        echo "<!DOCTYPE html><html lang='th'><head><meta charset='UTF-8'><title>Facebook Login Error</title></head><body style='font-family: sans-serif; padding: 30px; text-align: center;'>";
        echo "<div style='max-width: 500px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>";
        echo "<h3 style='color: #e53e3e;'>เกิดข้อผิดพลาดในการแลกเปลี่ยน Token:</h3>";
        echo "<pre style='text-align: left; background: #f7fafc; padding: 15px; border-radius: 5px; overflow-x: auto;'>";
        print_r($token_data);
        echo "</pre>";
        echo "<br><a href='login.html' style='display:inline-block; padding: 10px 20px; background: #3182ce; color: white; text-decoration: none; border-radius: 5px;'>กลับไปหน้าล็อกอิน</a>";
        echo "</div></body></html>";
        exit();
    }
} else {
    echo "ไม่พบข้อมูลยืนยันตัวตนส่งกลับมาจาก Facebook";
}
?>