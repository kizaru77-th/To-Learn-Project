<?php
declare(strict_types=1);

ini_set('session.cookie_httponly', '1');
session_start();
require_once 'auth-session.php';

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires' => time() - 3600,
        'path' => $params['path'] ?? '/',
        'secure' => (bool) ($params['secure'] ?? false),
        'httponly' => (bool) ($params['httponly'] ?? true),
        'samesite' => 'Lax',
    ]);
}
session_destroy();
forgetRememberedUser();

http_response_code(204);
