<?php
declare(strict_types=1);

/**
 * Local development router — mirrors the .htaccess for PHP's built-in server:
 *
 *     php -S 127.0.0.1:8080 dev-router.php
 *
 * On cPanel/Apache this file does nothing: .htaccess does the routing there.
 * Safe to deploy — only whitelisted paths are mapped, everything else 404s.
 */

$path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';

// Never serve data files or dotfiles (mirrors the .htaccess protections).
if (str_starts_with($path, '/data/') || preg_match('#/\.#', $path)) {
    http_response_code(403);
    exit('Forbidden');
}

// Real files (assets, css, js, direct .php) → let the built-in server handle
// them. Direct .php hits still hit the in-PHP guards (login.php etc. → 404).
if ($path !== '/' && preg_match('/\.(php|css|js|svg|png|jpe?g|webp|gif|ico|json|txt|xml|webmanifest|map)$/', $path)) {
    return false;
}

$rootPages = [
    '/'         => 'index.php',
    '/index'    => 'index.php',
    '/work'     => 'work.php',
    '/services' => 'services.php',
    '/about'    => 'about.php',
    '/contact'  => 'contact.php',
    '/privacy'  => 'privacy.php',
];

if (isset($rootPages[$path])) {
    $_SERVER['SCRIPT_NAME'] = $rootPages[$path];
    require __DIR__ . '/' . $rootPages[$path];
    exit;
}

if (preg_match('#^/admin/?$#', $path)) {
    $_SERVER['SCRIPT_NAME'] = '/admin/index.php';
    require __DIR__ . '/admin/index.php';
    exit;
}

if (preg_match('#^/admin/([a-z]+)/?$#', $path, $m)) {
    $_GET['page'] = $m[1];
    $_SERVER['SCRIPT_NAME'] = '/admin/index.php';
    require __DIR__ . '/admin/index.php';
    exit;
}

http_response_code(404);
exit('Not found');
