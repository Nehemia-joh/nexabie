<?php
declare(strict_types=1);

/**
 * Admin front controller — the ONLY admin file that can be opened directly.
 *
 * Every admin URL is routed through this file:
 *   /admin                    → dashboard
 *   /admin/login              → login form
 *   /admin/content|media|inbox|settings|logout
 *
 * The other admin/*.php files refuse direct access (404) and are only
 * ever reached from here, so login.php and friends are never readable
 * as standalone URLs.
 */

define('NEXABIE_ROUTER', true);

$pages = [
    'dashboard' => 'dashboard.php',
    'login'     => 'login.php',
    'logout'    => 'logout.php',
    'content'   => 'content.php',
    'media'     => 'media.php',
    'inbox'     => 'inbox.php',
    'settings'  => 'settings.php',
];

$page = (string) ($_GET['page'] ?? 'dashboard');
if (!isset($pages[$page])) {
    $page = 'dashboard';
}

require __DIR__ . '/' . $pages[$page];
