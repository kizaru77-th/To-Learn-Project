<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once 'connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'is_logged_in' => false, 'message' => 'ยังไม่ได้เข้าสู่ระบบ']);
    exit();
}

$user_id = $_SESSION['user_id'];
$course_id = isset($_GET['course_id']) ? trim($_GET['course_id']) : '';

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

    if (!empty($course_id)) {
        // ดึงความคืบหน้าของวิชาที่ระบุ
        $stmt = $conn->prepare("SELECT course_id, last_lesson_id, last_lesson_title, updated_at FROM user_progress WHERE user_id = :user_id AND course_id = :course_id LIMIT 1");
        $stmt->execute([':user_id' => $user_id, ':course_id' => $course_id]);
        $progress = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'is_logged_in' => true,
            'progress' => $progress ?: null
        ]);
    } else {
        // ดึงความคืบหน้าทั้งหมด เรียงตามอันล่าสุดที่มีการเรียน
        $stmt = $conn->prepare("SELECT course_id, last_lesson_id, last_lesson_title, updated_at FROM user_progress WHERE user_id = :user_id ORDER BY updated_at DESC");
        $stmt->execute([':user_id' => $user_id]);
        $all_progress = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'is_logged_in' => true,
            'progress_list' => $all_progress
        ]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
