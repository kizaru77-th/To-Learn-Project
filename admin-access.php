<?php
declare(strict_types=1);

// This endpoint is the single source of truth for the developer-admin mode.
// Configure one or more comma-separated addresses in ADMIN_EMAILS, for example:
// ADMIN_EMAILS=developer@example.com,another-developer@example.com
ini_set('session.cookie_httponly', '1');
session_start();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, private');

$email = '';
if (!empty($_SESSION['user_id'])) {
    require_once 'connect.php';
    $stmt = $conn->prepare('SELECT email FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $email = strtolower(trim((string) $stmt->fetchColumn()));
}

$configuredEmails = preg_split('/\s*,\s*/', (string) getenv('ADMIN_EMAILS'), -1, PREG_SPLIT_NO_EMPTY);
$adminEmails = array_map(
    static fn(string $configuredEmail): string => strtolower(trim($configuredEmail)),
    $configuredEmails ?: []
);

echo json_encode([
    'authenticated' => !empty($_SESSION['user_id']),
    'isAdmin' => $email !== '' && in_array($email, $adminEmails, true),
], JSON_UNESCAPED_UNICODE);
