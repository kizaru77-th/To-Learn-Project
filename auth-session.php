<?php
declare(strict_types=1);

const TOLEARN_AUTH_COOKIE = 'tolearn_remember';
const TOLEARN_AUTH_LIFETIME = 60 * 60 * 24 * 14;

function tolearnAuthSecret(): string
{
    $secret = trim((string) getenv('AUTH_SESSION_SECRET'));
    if ($secret !== '') {
        return $secret;
    }

    // DB_PASS is already a deployment secret. The dedicated value above is
    // preferred, while this fallback keeps existing deployments working.
    return (string) getenv('DB_PASS');
}

function tolearnCookieOptions(int $expires): array
{
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    return [
        'expires' => $expires,
        'path' => '/',
        'secure' => $isSecure,
        'httponly' => true,
        'samesite' => 'Lax',
    ];
}

function rememberAuthenticatedUser(int $userId): void
{
    $secret = tolearnAuthSecret();
    if ($userId <= 0 || $secret === '') {
        return;
    }

    $expires = time() + TOLEARN_AUTH_LIFETIME;
    $payload = $userId . '.' . $expires;
    $signature = hash_hmac('sha256', $payload, $secret);
    setcookie(TOLEARN_AUTH_COOKIE, $payload . '.' . $signature, tolearnCookieOptions($expires));
}

function restoreRememberedUser(): bool
{
    if (!empty($_SESSION['user_id'])) {
        return true;
    }

    $secret = tolearnAuthSecret();
    $cookie = (string) ($_COOKIE[TOLEARN_AUTH_COOKIE] ?? '');
    $parts = explode('.', $cookie);
    if ($secret === '' || count($parts) !== 3) {
        return false;
    }

    [$userId, $expires, $signature] = $parts;
    if (!ctype_digit($userId) || !ctype_digit($expires) || (int) $expires < time()) {
        return false;
    }

    $payload = $userId . '.' . $expires;
    $expectedSignature = hash_hmac('sha256', $payload, $secret);
    if (!hash_equals($expectedSignature, $signature)) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $userId;
    return true;
}

function forgetRememberedUser(): void
{
    setcookie(TOLEARN_AUTH_COOKIE, '', tolearnCookieOptions(time() - 3600));
}
