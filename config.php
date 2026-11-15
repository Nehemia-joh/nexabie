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
];
