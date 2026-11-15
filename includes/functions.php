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
        'stone' => '#f1f1f1', 'stone_l' => '#f7f7f7',
        'line' => '#e2e2e2', 'line_soft' => '#eeeeee',
        'ink' => '#000000', 'ink_2' => '#3a3a3a', 'ink_3' => '#6f6f6f',
        'accent' => '#1775bb', 'accent_dark' => '#0e5a91',
        'accent_bright' => '#3ea0e3', 'accent_tint' => '#e8f4fd',
        'red' => '#f53030',
    ];
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Root-absolute URL for a stored asset path (uploads/…) so it resolves
 *  from any page, including /admin/* where relative paths break. */
function asset(string $rel): string
{
    $rel = str_replace(['..', "\0"], '', $rel);
    $rel = ltrim($rel, '/');
    return $rel === '' ? '' : '/' . $rel;
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
        '--shadow' => '0 1px 2px rgba(0, 0, 0, 0.05), 0 16px 32px -24px rgba(0, 0, 0, 0.18)',
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
        header('Location: login');
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

/* ---------- Inquiries (MySQL when DB_NAME is set, data/inquiries.json otherwise) ---------- */

function db(): ?PDO
{
    static $pdo = false; // false = not tried yet, null = unavailable
    if ($pdo !== false) {
        return $pdo;
    }
    $pdo = null;
    $cfg = site_config()['db'] ?? [];
    if (($cfg['name'] ?? '') === '') {
        return null;
    }
    try {
        $dsn = 'mysql:host=' . $cfg['host'] . ';port=' . $cfg['port']
            . ';dbname=' . $cfg['name'] . ';charset=utf8mb4';
        $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS inquiries (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `date` VARCHAR(32) NOT NULL DEFAULT "",
                name VARCHAR(190) NOT NULL DEFAULT "",
                email VARCHAR(190) NOT NULL DEFAULT "",
                service VARCHAR(120) NOT NULL DEFAULT "",
                message TEXT NOT NULL,
                ip VARCHAR(45) NOT NULL DEFAULT "",
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );
        // Flush any messages that were spilled into the JSON fallback
        // (e.g. while MySQL was unreachable) into the database.
        $old = load_json('inquiries.json', []);
        if ($old) {
            $ins = $pdo->prepare('INSERT INTO inquiries (`date`, name, email, service, message, ip) VALUES (?,?,?,?,?,?)');
            foreach (array_reverse($old) as $row) { // JSON is newest-first; insert oldest first
                $ins->execute([
                    $row['date'] ?? '',
                    $row['name'] ?? '',
                    $row['email'] ?? '',
                    $row['service'] ?? '',
                    $row['message'] ?? '',
                    $row['ip'] ?? '',
                ]);
            }
            save_json('inquiries.json', []);
        }
    } catch (Throwable $e) {
        error_log('Nexabie: MySQL unavailable, using JSON inbox — ' . $e->getMessage());
        $pdo = null;
    }
    return $pdo;
}

function load_inquiries(): array
{
    $pdo = db();
    if ($pdo !== null) {
        return $pdo->query('SELECT id, `date`, name, email, service, message, ip FROM inquiries ORDER BY id DESC')->fetchAll();
    }
    return load_json('inquiries.json', []);
}

function save_inquiry(array $row): void
{
    $pdo = db();
    if ($pdo !== null) {
        $stmt = $pdo->prepare('INSERT INTO inquiries (`date`, name, email, service, message, ip) VALUES (?,?,?,?,?,?)');
        $stmt->execute([
            $row['date'] ?? date('c'),
            $row['name'] ?? '',
            $row['email'] ?? '',
            $row['service'] ?? '',
            $row['message'] ?? '',
            $row['ip'] ?? '',
        ]);
        return;
    }
    $all = load_json('inquiries.json', []);
    array_unshift($all, $row);
    save_json('inquiries.json', array_slice($all, 0, 500));
}

function delete_inquiry(?int $id, int $index = -1): void
{
    $pdo = db();
    if ($pdo !== null) {
        if ($id !== null && $id > 0) {
            $stmt = $pdo->prepare('DELETE FROM inquiries WHERE id = ?');
            $stmt->execute([$id]);
        }
        return;
    }
    $all = load_json('inquiries.json', []);
    if (isset($all[$index])) {
        unset($all[$index]);
        save_json('inquiries.json', array_values($all));
    }
}

function clear_inquiries(): void
{
    $pdo = db();
    if ($pdo !== null) {
        $pdo->exec('DELETE FROM inquiries');
        save_json('inquiries.json', []); // drop any not-yet-flushed spillover too
        return;
    }
    save_json('inquiries.json', []);
}

/* ---------- Outgoing email (SMTP when configured, PHP mail() otherwise) ---------- */

function smtp_read($fp): array
{
    $text = '';
    $code = 0;
    while (($line = fgets($fp, 515)) !== false) {
        $text .= $line;
        if (strlen($line) < 4 || $line[3] !== '-') {
            $code = (int) substr($line, 0, 3);
            break;
        }
    }
    return ['code' => $code, 'text' => $text];
}

function smtp_cmd($fp, string $cmd): array
{
    fwrite($fp, $cmd . "\r\n");
    return smtp_read($fp);
}

function smtp_mime_word(string $s): string
{
    return preg_match('/[^\x20-\x7E]/', $s) ? '=?UTF-8?B?' . base64_encode($s) . '?=' : $s;
}

function smtp_send(array $cfg, string $to, string $subject, string $body, string $fromName, string $fromAddr, string $replyTo, string $html = ''): bool
{
    $host = (string) $cfg['host'];
    $port = (int) ($cfg['port'] ?? 587);
    $user = (string) ($cfg['user'] ?? '');
    $pass = (string) ($cfg['pass'] ?? '');
    $from = ($cfg['from'] ?? '') !== '' ? (string) $cfg['from'] : ($user !== '' ? $user : $fromAddr);

    $errno = 0;
    $errstr = '';
    if ($port === 465) {
        $fp = @stream_socket_client('tls://' . $host . ':' . $port, $errno, $errstr, 8);
    } else {
        $fp = @fsockopen($host, $port, $errno, $errstr, 8);
    }
    if (!$fp) {
        error_log("Nexabie SMTP: cannot connect to $host:$port — $errstr");
        return false;
    }
    stream_set_timeout($fp, 8);

    $fail = function ($fp, string $why) {
        @fwrite($fp, "QUIT\r\n");
        fclose($fp);
        error_log('Nexabie SMTP: ' . $why);
        return false;
    };

    if (smtp_read($fp)['code'] !== 220) {
        return $fail($fp, 'no greeting');
    }
    $ehlo = smtp_cmd($fp, 'EHLO ' . (gethostname() ?: 'localhost'));
    if ($ehlo['code'] !== 250) {
        return $fail($fp, 'EHLO rejected');
    }
    if ($port !== 465 && stripos($ehlo['text'], 'STARTTLS') !== false) {
        if (smtp_cmd($fp, 'STARTTLS')['code'] !== 220) {
            return $fail($fp, 'STARTTLS rejected');
        }
        if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            return $fail($fp, 'TLS handshake failed');
        }
        $ehlo = smtp_cmd($fp, 'EHLO ' . (gethostname() ?: 'localhost'));
        if ($ehlo['code'] !== 250) {
            return $fail($fp, 'EHLO after STARTTLS rejected');
        }
    }
    if ($user !== '') {
        if (smtp_cmd($fp, 'AUTH LOGIN')['code'] !== 334) {
            return $fail($fp, 'AUTH LOGIN refused');
        }
        if (smtp_cmd($fp, base64_encode($user))['code'] !== 334) {
            return $fail($fp, 'username rejected');
        }
        if (smtp_cmd($fp, base64_encode($pass))['code'] !== 235) {
            return $fail($fp, 'password rejected');
        }
    }
    if (smtp_cmd($fp, "MAIL FROM:<$from>")['code'] !== 250) {
        return $fail($fp, 'MAIL FROM rejected');
    }
    $rcpt = smtp_cmd($fp, "RCPT TO:<$to>");
    if (!in_array($rcpt['code'], [250, 251], true)) {
        return $fail($fp, 'recipient rejected: ' . trim($rcpt['text']));
    }
    if (smtp_cmd($fp, 'DATA')['code'] !== 354) {
        return $fail($fp, 'DATA refused');
    }

    if ($html !== '') {
        $boundary = 'b_' . bin2hex(random_bytes(12));
        $msg = 'From: ' . smtp_mime_word($fromName) . " <$from>\r\n"
            . "To: <$to>\r\n"
            . 'Subject: ' . smtp_mime_word($subject) . "\r\n"
            . ($replyTo !== '' ? "Reply-To: <$replyTo>\r\n" : '')
            . 'Date: ' . date('r') . "\r\n"
            . 'Message-ID: <' . bin2hex(random_bytes(8)) . '@' . (gethostname() ?: 'localhost') . ">\r\n"
            . "MIME-Version: 1.0\r\n"
            . "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n\r\n"
            . "--$boundary\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n"
            . chunk_split(base64_encode($body), 76, "\r\n")
            . "--$boundary\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n"
            . chunk_split(base64_encode($html), 76, "\r\n")
            . "--$boundary--\r\n";
    } else {
        $msg = 'From: ' . smtp_mime_word($fromName) . " <$from>\r\n"
            . "To: <$to>\r\n"
            . 'Subject: ' . smtp_mime_word($subject) . "\r\n"
            . ($replyTo !== '' ? "Reply-To: <$replyTo>\r\n" : '')
            . 'Date: ' . date('r') . "\r\n"
            . 'Message-ID: <' . bin2hex(random_bytes(8)) . '@' . (gethostname() ?: 'localhost') . ">\r\n"
            . "MIME-Version: 1.0\r\n"
            . "Content-Type: text/plain; charset=UTF-8\r\n"
            . "Content-Transfer-Encoding: base64\r\n"
            . "\r\n"
            . chunk_split(base64_encode($body), 76, "\r\n");
    }
    fwrite($fp, $msg . "\r\n.\r\n");
    if (smtp_read($fp)['code'] !== 250) {
        return $fail($fp, 'message rejected');
    }
    @fwrite($fp, "QUIT\r\n");
    fclose($fp);
    return true;
}

function send_email(string $to, string $subject, string $body, string $fromName, string $fromAddr, string $replyTo = '', string $html = ''): bool
{
    $smtp = site_config()['smtp'] ?? [];
    if (($smtp['host'] ?? '') !== '') {
        if (smtp_send($smtp, $to, $subject, $body, $fromName, $fromAddr, $replyTo, $html)) {
            return true;
        }
        error_log('Nexabie: SMTP failed for ' . $to . ' — trying PHP mail()');
    }
    if ($html !== '') {
        $boundary = 'b_' . bin2hex(random_bytes(12));
        $headers = 'From: ' . smtp_mime_word($fromName) . " <$fromAddr>\r\n"
            . ($replyTo !== '' ? "Reply-To: $replyTo\r\n" : '')
            . "MIME-Version: 1.0\r\n"
            . "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n";
        $message = "--$boundary\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n"
            . chunk_split(base64_encode($body), 76, "\r\n")
            . "--$boundary\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n"
            . chunk_split(base64_encode($html), 76, "\r\n")
            . "--$boundary--\r\n";
        return (bool) @mail($to, $subject, $message, $headers);
    }
    $headers = 'From: ' . smtp_mime_word($fromName) . " <$fromAddr>\r\n"
        . ($replyTo !== '' ? "Reply-To: $replyTo\r\n" : '')
        . "MIME-Version: 1.0\r\n"
        . "Content-Type: text/plain; charset=UTF-8\r\n";
    return (bool) @mail($to, $subject, $body, $headers);
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
