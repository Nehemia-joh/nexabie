<?php
declare(strict_types=1);

/* Nexabie CMS core — JSON file storage, auth, palette, uploads. */

define('SITE_ROOT', dirname(__DIR__));

function site_config(): array
{
    static $c = null;
    if ($c === null) {
        $c = require SITE_ROOT . '/config.php';
    }
    return $c;
}

function data_path(string $file): string
{
    return SITE_ROOT . '/data/' . $file;
}

function load_json(string $file, array $default = []): array
{
    $path = data_path($file);
    if (!is_file($path)) {
        return $default;
    }
    $data = json_decode((string) file_get_contents($path), true);
    return is_array($data) ? $data : $default;
}

function save_json(string $file, array $data): void
{
    file_put_contents(
        data_path($file),
        json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        LOCK_EX
    );
}

function settings(): array
{
    static $s = null;
    if ($s === null) {
        $defaults = ['palette' => default_palette()];
        $s = array_replace_recursive($defaults, load_json('settings.json', $defaults));
    }
    return $s;
}

function content(): array
{
    static $c = null;
    if ($c === null) {
        $c = load_json('content.json', []);
    }
    return $c;
}

function default_palette(): array
{
    return [
        'paper' => '#fafafa', 'paper_l' => '#ffffff',
        'stone' => '#eef2f6', 'stone_l' => '#f5f7fa',
        'line' => '#dfe4ea', 'line_soft' => '#ebeff3',
        'ink' => '#0b0b0a', 'ink_2' => '#33383e', 'ink_3' => '#6d7680',
        'accent' => '#1775bb', 'accent_dark' => '#0e5a91',
        'accent_bright' => '#3ea0e3', 'accent_tint' => '#e8f4fd',
        'red' => '#f53030',
    ];
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function hex_to_rgb(string $hex): array
{
    $hex = ltrim(trim($hex), '#');
    if (strlen($hex) === 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    if (!preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
        return [0, 0, 0];
    }
    return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
}

function rgba(string $hex, float $alpha): string
{
    [$r, $g, $b] = hex_to_rgb($hex);
    return sprintf('rgba(%d, %d, %d, %s)', $r, $g, $b, rtrim(rtrim(number_format($alpha, 2, '.', ''), '0'), '.'));
}

/** Renders the live palette as CSS custom properties. */
function palette_css(?array $settings = null): string
{
    $settings = $settings ?? settings();
    $p = array_merge(default_palette(), $settings['palette'] ?? []);
    $map = [
        '--paper' => $p['paper'],
        '--paper-l' => $p['paper_l'],
        '--stone' => $p['stone'],
        '--stone-l' => $p['stone_l'],
        '--line' => $p['line'],
        '--line-soft' => $p['line_soft'],
        '--ink' => $p['ink'],
        '--ink-2' => $p['ink_2'],
        '--ink-3' => $p['ink_3'],
        '--accent' => $p['accent'],
        '--accent-dark' => $p['accent_dark'],
        '--accent-bright' => $p['accent_bright'],
        '--accent-tint' => $p['accent_tint'],
        '--red' => $p['red'],
        '--red-10' => rgba($p['red'], 0.10),
        '--white' => '#ffffff',
        '--shadow' => '0 1px 2px rgba(11, 11, 10, 0.06), 0 12px 32px -16px ' . rgba($p['accent_bright'], 0.28),
    ];
    $out = '';
    foreach ($map as $var => $val) {
        $out .= $var . ':' . $val . ';';
    }
    return '<style id="live-palette">:root{' . $out . '}</style>';
}

/* ---------- Auth ---------- */

function ensure_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
        session_start();
    }
}

function is_logged_in(): bool
{
    ensure_session();
    return !empty($_SESSION['admin_logged_in']);
}

function attempt_login(string $user, string $pass): bool
{
    ensure_session();
    $cfg = site_config();
    $auth = load_json('auth.json', []);
    if (
        hash_equals((string) ($cfg['admin_user'] ?? 'admin'), $user)
        && !empty($auth['password_hash'])
        && password_verify($pass, (string) $auth['password_hash'])
    ) {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        return true;
    }
    return false;
}

function logout(): void
{
    ensure_session();
    $_SESSION = [];
    session_destroy();
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function set_password(string $new): bool
{
    $hash = password_hash($new, PASSWORD_DEFAULT);
    if ($hash === false) {
        return false;
    }
    save_json('auth.json', ['password_hash' => $hash]);
    return true;
}

/* ---------- Uploads ---------- */

function upload_max_bytes(): int
{
    return (int) (site_config()['upload_max_mb'] ?? 5) * 1024 * 1024;
}

/** Saves an uploaded image; returns relative path (uploads/…) or null. */
function handle_upload(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }
    if (($file['size'] ?? 0) > upload_max_bytes()) {
        return null;
    }
    $ext = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
    if (!in_array($ext, $allowed, true)) {
        return null;
    }
    $name = bin2hex(random_bytes(10)) . '.' . $ext;
    $dest = SITE_ROOT . '/uploads/' . $name;
    if (!move_uploaded_file((string) $file['tmp_name'], $dest)) {
        return null;
    }
    return 'uploads/' . $name;
}

function delete_upload(string $rel): bool
{
    $rel = str_replace(['..', "\0"], '', $rel);
    if (!str_starts_with($rel, 'uploads/')) {
        return false;
    }
    $path = SITE_ROOT . '/' . $rel;
    return is_file($path) && unlink($path);
}

function list_uploads(): array
{
    $files = glob(SITE_ROOT . '/uploads/*') ?: [];
    $out = [];
    foreach ($files as $f) {
        if (is_file($f) && strtolower(pathinfo($f, PATHINFO_EXTENSION)) !== 'htaccess') {
            $out[] = 'uploads/' . basename($f);
        }
    }
    rsort($out);
    return $out;
}

/* ---------- Inquiries ---------- */

function save_inquiry(array $row): void
{
    $all = load_json('inquiries.json', []);
    array_unshift($all, $row);
    save_json('inquiries.json', array_slice($all, 0, 500));
}

/* ---------- Small helpers for templates ---------- */

function current_page(): string
{
    return basename($_SERVER['SCRIPT_NAME'] ?? '');
}

function project_by_id(array $content, string $id): ?array
{
    foreach ($content['work']['projects'] ?? [] as $p) {
        if (($p['id'] ?? '') === $id) {
            return $p;
        }
    }
    return null;
}
