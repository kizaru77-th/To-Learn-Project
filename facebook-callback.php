<?php
session_start();
require_once 'connect.php';

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // 🔒 ป้องกันการรัน code เดิมซ้ำซ้อน
    if (isset($_SESSION['last_used_code']) && $_SESSION['last_used_code'] === $code) {
        // ถ้าเป็น code เดิมที่เคยแลกไปแล้ว ให้ข้ามไปหน้า mainweb ทันที
        header("Location: mainweb.html");
        exit();
    }
    
    // บันทึก code ล่าสุดไว้ใน Session
    $_SESSION['last_used_code'] = $code;

    $app_id = '992584539930554'; 
    $app_secret = '18e0bbf0605e6c4d2f8ea0ba695e877c';
    $redirect_uri = 'https://to-learn-project.onrender.com/facebook-callback.php';

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

    if (isset($token_data['access_token'])) {
        $access_token = $token_data['access_token'];

        // ดึงข้อมูลผู้ใช้
        $user_url = "https://graph.facebook.com/me?fields=id,name,picture.type(large)&access_token=" . $access_token;
        $user_response = file_get_contents($user_url);
        $user_data = json_decode($user_response, true);

        $fb_id = $user_data['id'];
        $username = isset($user_data['name']) ? $user_data['name'] : 'Facebook User';
        $email = $fb_id . '@facebook.com'; 
        $picture = isset($user_data['picture']['data']['url']) ? $user_data['picture']['data']['url'] : '';

        // บันทึกลง Database
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

        $stmt = $conn->prepare("SELECT username, profile_picture FROM users WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['user_id']]);
        $login_user = $stmt->fetch(PDO::FETCH_ASSOC);

        echo "<script>
            localStorage.setItem('isLoggedIn', 'true');
            localStorage.setItem('username', '" . addslashes($login_user['username']) . "');
            localStorage.setItem('profilePicture', '" . addslashes($login_user['profile_picture']) . "');
            window.location.href = 'mainweb.html';
        </script>";
        exit();

    } else {
        echo "<h3>เกิดข้อผิดพลาดในการแลกเปลี่ยน Token:</h3>";
        echo "<pre>";
        print_r($token_data);
        echo "</pre>";
    }
} else {
    echo "ไม่พบข้อมูลยืนยันตัวตนส่งกลับมาจาก Facebook";
}
?>