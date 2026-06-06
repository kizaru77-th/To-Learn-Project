<?php
session_start();
require_once 'connect.php'; // เชื่อมฐานข้อมูลเดิมของคุณ

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    
    // 🚨 เดี๋ยวเราจะเอาคีย์ 2 ตัวนี้มาจากเว็บ Facebook Developers กันครับ
    $app_id = '992584539930554'; 
    $app_secret = '18e0bbf0605e6c4d2f8ea0ba695e877c';
    $redirect_uri = 'http://localhost/To-Learn/facebook-callback.php';

    // 1. แลกเปลี่ยน Code เป็น Access Token
    $token_url = "https://graph.facebook.com/v19.0/oauth/access_token?" . http_build_query([
        'client_id'     => $app_id,
        'redirect_uri'  => $redirect_uri,
        'client_secret' => $app_secret,
        'code'          => $code
    ]);

    $response = file_get_contents($token_url);
    $token_data = json_decode($response, true);

    if (isset($token_data['access_token'])) {
        $access_token = $token_data['access_token'];

        // 2. ดึงข้อมูลเฉพาะ ID, ชื่อ และรูปภาพ (ตัดการเรียก fields email ออก)
        $user_url = "https://graph.facebook.com/me?fields=id,name,picture.type(large)&access_token=" . $access_token;
        $user_response = file_get_contents($user_url);
        $user_data = json_decode($user_response, true);

        $fb_id = $user_data['id'];
        $username = isset($user_data['name']) ? $user_data['name'] : 'Facebook User';
        
        // เนื่องจากไม่ได้ขอสิทธิ์ email เราจะใช้ Facebook ID ผสมคำเพื่อตั้งเป็นอีเมลจำลองในระบบแทน 🚨
        $email = $fb_id . '@facebook.com'; 
        $picture = isset($user_data['picture']['data']['url']) ? $user_data['picture']['data']['url'] : '';
        // 3. นำข้อมูลไปตรวจสอบในฐานข้อมูล
        $stmt = $conn->prepare("SELECT id, profile_picture FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            // ถ้ามีรูปจาก Facebook ให้อัปเดต แต่ข้ามการอัปเดตถ้าผู้ใช้เคยอัพรูปเอง (อยู่ใน uploads/)
            if (!empty($picture)) {
                $current_pic = isset($user['profile_picture']) ? $user['profile_picture'] : '';
                if (!(strpos($current_pic, 'uploads/') === 0)) {
                    $update_stmt = $conn->prepare("UPDATE users SET profile_picture = :pic WHERE id = :id");
                    $update_stmt->execute(['pic' => $picture, 'id' => $user['id']]);
                }
            }
        } else {
            // สมัครสมาชิกใหม่อัตโนมัติ
            $stmt = $conn->prepare("INSERT INTO users (email, username, password, profile_picture) VALUES (:email, :username, 'FACEBOOK_LOGIN', :pic)");
            $stmt->execute([
                'email' => $email,
                'username' => $username,
                'pic' => $picture
            ]);
            $_SESSION['user_id'] = $conn->lastInsertId();
        }

        // ดึงข้อมูลล่าสุดไปเซ็ตลง localStorage เหมือนเดิม
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
        echo "เกิดข้อผิดพลาดในการแลกเปลี่ยน Token กับ Facebook";
    }
} else {
    echo "ไม่พบข้อมูลยืนยันตัวตนส่งกลับมาจาก Facebook";
}
?>