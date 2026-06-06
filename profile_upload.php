<?php
session_start();
require_once 'connect.php';

// ตรวจสอบว่าล็อกอินหรือยัง
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = "";
$success = false;
$new_image_path = "";

// ดึงข้อมูลผู้ใช้ปัจจุบัน
$stmt = $conn->prepare("SELECT username, profile_picture FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_FILES['profile_img']['tmp_name']) && $_FILES['profile_img']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        
        // สร้างโฟลเดอร์ uploads ถ้ายังไม่มี
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($_FILES["profile_img"]["name"], PATHINFO_EXTENSION));
        $new_filename = "profile_" . $user_id . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;

        // ตรวจสอบว่าเป็นรูปภาพจริงหรือไม่
        $check = getimagesize($_FILES["profile_img"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["profile_img"]["tmp_name"], $target_file)) {
                // อัปเดตข้อมูลในฐานข้อมูล
                $update = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
                $update->execute([$target_file, $user_id]);
                
                $new_image_path = $target_file;
                $success = true;
            } else {
                $error = "เกิดข้อผิดพลาดในการบันทึกไฟล์";
            }
        } else {
            $error = "ไฟล์ที่เลือกไม่ใช่รูปภาพ";
        }
    } else {
        $error = "กรุณาเลือกรูปภาพก่อนบันทึก";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ToLearn | เปลี่ยนรูปโปรไฟล์</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Prompt', sans-serif; 
            background: linear-gradient(135deg, #ecf7ce 0%, #1e2a8f 100%);
            min-height: 100vh; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            margin: 0; 
        }

        .container { 
            background: white; 
            padding: 30px; 
            border-radius: 20px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.2); 
            text-align: center; 
            width: 90%; 
            max-width: 400px; 
        }

        .profile-preview { 
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid black;
            margin-bottom: 20px; 
        }

        .upload-btn { 
            background: #7c86e1;
            color: white; padding: 10px 20px; 
            border-radius: 100px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
            margin-top: 10px; 
        }

        .upload-btn:hover { 
            background: #5a65c1; 
        }

        .back-link { 
            display: block;
            margin-top: 20px; color: 
            #666; text-decoration: none; 
            font-size: 14px;
        }

        .error { 
            color: red; 
            margin-bottom: 10px; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>เปลี่ยนรูปโปรไฟล์</h2>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        
        <img src="<?php echo !empty($user['profile_picture']) ? $user['profile_picture'] : 'img/Sample_User_Icon.png'; ?>" class="profile-preview" id="preview">
        
        <form action="profile_upload.php" method="post" enctype="multipart/form-data">
            <input type="file" name="profile_img" id="fileInput" style="display: none;" onchange="previewImage(this)">
            <button type="button" class="upload-btn" onclick="document.getElementById('fileInput').click()">เลือกรูปภาพใหม่</button>
            <br><br>
            <button type="submit" class="upload-btn" style="background-color: #28a745;">บันทึกการเปลี่ยนแปลง</button>
        </form>

        <a href="mainweb.html" class="back-link">← กลับหน้าหลัก</a>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        // เรียกใช้งานเมื่อโหลดหน้าเว็บ
        window.onload = checkLoginStatus;
        window.onload = checkLoginStatus;
        const INACTIVITY_TIME = 10 * 60 * 1000; 
        let timeoutId;

        // ฟังก์ชันสำหรับเตะผู้ใช้กลับหน้าหลัก
        function redirectToHome() {
            window.location.href = 'Tolearn.html'; 
        }

        // ฟังก์ชันรีเซ็ตเวลานับถอยหลังใหม่
        function resetTimer() {
        // ลบเวลาเก่าทิ้งไปก่อน
        clearTimeout(timeoutId);
    
        // เริ่มนับเวลา 10 นาทีใหม่อีกครั้ง
        timeoutId = setTimeout(redirectToHome, INACTIVITY_TIME);
        }

        // ฟังก์ชันสำหรับเริ่มจับตาดูพฤติกรรมของผู้ใช้
        function setupInactivityTimer() {
            // ดักฟังเหตุการณ์ต่างๆ (ขยับเมาส์, คลิก, กดปุ่มคีย์บอร์ด, หรือทัชหน้าจอ)
            window.addEventListener('mousemove', resetTimer);
            window.addEventListener('click', resetTimer);
            window.addEventListener('keydown', resetTimer);
            window.addEventListener('touchstart', resetTimer); // รองรับมือถือ/แท็บเล็ต

            // เริ่มต้นนับเวลาครั้งแรกทันทีที่โหลดหน้าเว็บเสร็จ
            resetTimer();
        }

        // เรียกใช้งานฟังก์ชันเมื่อหน้าเว็บโหลดเสร็จ
        document.addEventListener('DOMContentLoaded', setupInactivityTimer);
        <?php if ($success): ?>
            localStorage.setItem('profilePicture', '<?php echo $new_image_path; ?>');
            alert('อัปเดตรูปโปรไฟล์สำเร็จแล้ว!');
            window.location.href = 'mainweb.html';
        <?php endif; ?>
    </script>
</body>
</html>
