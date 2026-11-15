<?php
/**
 * Nexabie site config.
 *
 * Values come from the .envnexabie file (KEY=VALUE lines), checked in order:
 *   1. one step ABOVE the site folder  (server: $HOME/.envnexabie — outside web root)
 *   2. the project root                (local development)
 * Missing keys fall back to the defaults below.
 * Admin credentials live in data/auth.json (change password from Admin → Settings).
 */

// Refuse direct HTTP access — this file is only ever required from PHP.
if (basename((string) ($_SERVER['SCRIPT_FILENAME'] ?? '')) === 'config.php') {
    http_response_code(404);
    exit('Not found');
}

$env = [];
$envCandidates = [
    dirname(__DIR__) . '/.envnexabie', // one step back (server)
    __DIR__ . '/.envnexabie',          // project root (local dev)
];
foreach ($envCandidates as $envPath) {
    if (is_readable($envPath) && is_file($envPath)) {
        foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $env[trim($key)] = trim($value, " \t\"'");
        }
        break;
    }
}

return [
    'site_name'     => 'Nexabie',
    'base_url'      => $env['SITE_URL'] ?? 'https://nexabie.co.tz',
    'admin_user'    => $env['ADMIN_USER'] ?? 'admin',
    'upload_max_mb' => (int) ($env['UPLOAD_MAX_MB'] ?? 5),

    // MySQL for contact-form messages. Leave DB_NAME empty to keep
    // storing them in data/inquiries.json instead.
    'db' => [
        'host' => $env['DB_HOST'] ?? 'localhost',
        'port' => (int) ($env['DB_PORT'] ?? 3306),
        'name' => $env['DB_NAME'] ?? '',
        'user' => $env['DB_USER'] ?? '',
        'pass' => $env['DB_PASS'] ?? '',
    ],

    // Optional SMTP relay for outgoing mail. When SMTP_HOST is set,
    // messages are sent through it; otherwise PHP mail() is used.
    'smtp' => [
        'host' => $env['SMTP_HOST'] ?? '',
        'port' => (int) ($env['SMTP_PORT'] ?? 587),
        'user' => $env['SMTP_USER'] ?? '',
        'pass' => $env['SMTP_PASS'] ?? '',
        'from' => $env['SMTP_FROM'] ?? '',
    ],
];
