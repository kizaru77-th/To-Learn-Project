<?php
// ตั้งค่าความปลอดภัยของ Session สำหรับ Server Deployment
ini_set('session.cookie_httponly', 1);
session_start();

require_once 'connect.php';

if (isset($_GET['code'])) {
    $code = trim($_GET['code']);

    // ถ้าพบว่าเคยแลก code นี้สำเร็จไปแล้ว ให้เด้งไปหน้าหลักทันที
    if (isset($_SESSION['last_success_code']) && $_SESSION['last_success_code'] === $code) {
        echo "<script>
            window.location.href = 'mainweb.html';
        </script>";
        exit();
    }

    $app_id = '992584539930554'; 
    $app_secret = '18e0bbf0605e6c4d2f8ea0ba695e877c';
    $redirect_uri = 'https://to-learn-project.onrender.com/facebook-callback.php';

    // ยิงแลก Token ด้วย cURL
    $token_url = "https://graph.facebook.com/v19.0/oauth/access_token?"
        . "client_id=" . $app_id
        . "&redirect_uri=" . urlencode($redirect_uri)
        . "&client_secret=" . $app_secret
        . "&code=" . $code;

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

        // กรณี Authorization Code ถูกใช้ไปแล้ว (เกิดจากการ Refresh หน้าเว็บ หรือกด Back/Forward ซ้ำ)
        if (isset($token_data['error']['code']) && $token_data['error']['code'] == 100) {
            echo "<script>
                alert('Authorization Code ถูกใช้งานไปแล้ว หรือมีการกด Refresh หน้าเว็บ กรุณาล็อกอินใหม่อีกครั้ง');
                window.location.href = 'login.html';
            </script>";
            exit();
        }

        // แสดงผล Error กรณีอื่นๆ
        echo "<h3>เกิดข้อผิดพลาดในการแลกเปลี่ยน Token:</h3>";
        echo "<pre>";
        print_r($token_data);
        echo "</pre>";
    }
} else {
    echo "ไม่พบข้อมูลยืนยันตัวตนส่งกลับมาจาก Facebook";
}
?>