<?php
declare(strict_types=1);

if (!defined('NEXABIE_ROUTER')) {
    http_response_code(404);
    exit('Not found');
}

require_once dirname(__DIR__) . '/includes/functions.php';
logout();
header('Location: login');
exit;
