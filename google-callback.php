<?php
session_start();
require_once 'connect.php'; // เรียกใช้ไฟล์เชื่อมฐานข้อมูลเดิมของคุณ

// 1. ตรวจสอบว่า Google ส่งรหัส Code กลับมาให้หลังจากผู้ใช้กดยอมรับไหม
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    
    // ใส่ข้อมูลคีย์ของคุณตรงนี้ (ดูได้จากหน้า Credentials ในเว็บ Google Cloud)
    $client_id = '217191213791-2bnl25b2l6r5d10bi0k8jkqbdmu70gl7.apps.googleusercontent.com';
    $client_secret = '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'; // (ถ้ามี)
    $redirect_uri = 'http://localhost/To-Learn/google-callback.php';

    // 2. ส่งคำขอแลกเปลี่ยน Code ไปเป็น Access Token เพื่อดึงข้อมูลโปรไฟล์
    $token_url = "https://oauth2.googleapis.com/token";
    $post_data = [
        'code'          => $code,
        'client_id'     => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri'  => $redirect_uri,
        'grant_type'    => 'authorization_code'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $token_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $token_data = json_decode($response, true);

    if (isset($token_data['access_token'])) {
        $access_token = $token_data['access_token'];

        // 3. เปลี่ยนมาใช้ cURL ดึงข้อมูลผู้ใช้แทน file_get_contents เพื่อบังคับเอาลิงก์รูปภาพมาให้ได้ 🚨
        $userinfo_url = "https://www.googleapis.com/oauth2/v2/userinfo";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $userinfo_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $access_token
        ]);
        $userinfo_response = curl_exec($ch);
        curl_close($ch);

        $user_data = json_decode($userinfo_response, true);

        $email = $user_data['email'];
        $username = $user_data['name'];
        // ดึงลิงก์รูปภาพโปรไฟล์จาก Google Account
        $picture = isset($user_data['picture']) ? $user_data['picture'] : 'img/Sample_User_Icon.png'; 

        // 4. นำข้อมูลไปเช็กและบันทึกลงฐานข้อมูล
        $stmt = $conn->prepare("SELECT id, profile_picture FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            // ถ้าเคยล็อกอินแล้ว ให้เซ็ต session แต่จะไม่เขียนทับรูปถ้าเป็นรูปที่อัพโดยผู้ใช้ (อยู่ใน uploads/)
            $_SESSION['user_id'] = $user['id'];
            $current_pic = isset($user['profile_picture']) ? $user['profile_picture'] : '';
            // ถ้ารูปปัจจุบันเริ่มต้นด้วย 'uploads/' แสดงว่าเป็นรูปที่ผู้ใช้อัพไว้เอง -> ข้ามการอัปเดต
            if (!(strpos($current_pic, 'uploads/') === 0)) {
                $update_stmt = $conn->prepare("UPDATE users SET profile_picture = :pic WHERE id = :id");
                $update_stmt->execute(['pic' => $picture, 'id' => $user['id']]);
            }
        } else {
            // ถ้าเป็นผู้ใช้งานใหม่ บันทึกข้อมูลพร้อมลิงก์รูปภาพตรงๆ
            $stmt = $conn->prepare("INSERT INTO users (email, username, password, profile_picture) VALUES (:email, :username, 'GOOGLE_LOGIN', :pic)");
            $stmt->execute([
                'email' => $email,
                'username' => $username,
                'pic' => $picture
            ]);
            $_SESSION['user_id'] = $conn->lastInsertId();
        }

        // ดึงข้อมูลล่าสุดจากฐานข้อมูลเพื่อนำไปใส่ใน localStorage (เพื่อให้ Sidebar แสดงรูปและชื่อได้ถูกต้อง)
        $stmt = $conn->prepare("SELECT username, profile_picture FROM users WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['user_id']]);
        $login_user = $stmt->fetch(PDO::FETCH_ASSOC);

        $display_username = $login_user['username'];
        $display_picture = !empty($login_user['profile_picture']) ? $login_user['profile_picture'] : '';

        // เปลี่ยนจาก header() เป็น JavaScript เพื่อเซ็ตค่าลงในเครื่องผู้ใช้ก่อนไปหน้า mainweb.html
        echo "<script>
            localStorage.setItem('isLoggedIn', 'true');
            localStorage.setItem('username', '" . addslashes($display_username) . "');
            localStorage.setItem('profilePicture', '" . addslashes($display_picture) . "');
            window.location.href = 'mainweb.html';
        </script>";
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการแลกเปลี่ยน Token กับ Google";
    }
} else {
    echo "ไม่พบข้อมูลรหัสส่งกลับมาจาก Google";
}
?>