<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once 'connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'ยังไม่ได้เข้าสู่ระบบ']);
    exit();
}

$user_id = $_SESSION['user_id'];

// อ่านข้อมูล JSON จาก Body หรือ POST Parameters
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if (!$data) {
    $data = $_POST;
}

$course_id = isset($data['course_id']) ? trim($data['course_id']) : '';
$last_lesson_id = isset($data['last_lesson_id']) ? trim($data['last_lesson_id']) : '';
$last_lesson_title = isset($data['last_lesson_title']) ? trim($data['last_lesson_title']) : '';

if (empty($course_id) || empty($last_lesson_id)) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit();
}

try {
    // สร้างตาราง user_progress อัตโนมัติหากยังไม่มีในฐานข้อมูล
    $tableSql = "CREATE TABLE IF NOT EXISTS `user_progress` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `course_id` VARCHAR(50) NOT NULL,
        `last_lesson_id` VARCHAR(100) NOT NULL,
        `last_lesson_title` VARCHAR(255) NOT NULL,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY `user_course_unique` (`user_id`, `course_id`),
        CONSTRAINT `fk_user_progress_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $conn->exec($tableSql);

    $stmt = $conn->prepare("INSERT INTO user_progress (user_id, course_id, last_lesson_id, last_lesson_title, updated_at) 
        VALUES (:user_id, :course_id, :last_lesson_id, :last_lesson_title, NOW())
        ON DUPLICATE KEY UPDATE 
        last_lesson_id = VALUES(last_lesson_id),
        last_lesson_title = VALUES(last_lesson_title),
        updated_at = NOW()");
    
    $stmt->execute([
        ':user_id' => $user_id,
        ':course_id' => $course_id,
        ':last_lesson_id' => $last_lesson_id,
        ':last_lesson_title' => $last_lesson_title
    ]);

    echo json_encode(['success' => true, 'message' => 'บันทึกความคืบหน้าเรียบร้อยแล้ว']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
