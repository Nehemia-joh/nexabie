<?php
declare(strict_types=1);

if (!defined('NEXABIE_ROUTER')) {
    http_response_code(404);
    exit('Not found');
}

require_once dirname(__DIR__) . '/includes/functions.php';
require_login();

function csrf_field(): string
{
    ensure_session();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">';
}

function csrf_verify(): void
{
    ensure_session();
    $token = (string) ($_POST['csrf'] ?? '');
    if (empty($_SESSION['csrf']) || !hash_equals((string) $_SESSION['csrf'], $token)) {
        http_response_code(403);
        exit('Invalid CSRF token. Go back and try again.');
    }
}

function admin_head(string $title, string $active): void
{
    $brand = content()['brand'] ?? [];
    $count = count(load_json('inquiries.json', []));
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title) ?> — Nexabie CMS</title>
  <link rel="icon" href="../assets/favicon.svg" type="image/svg+xml">
  <link rel="icon" href="../assets/favicon-32.png" sizes="32x32">
  <link rel="apple-touch-icon" href="../assets/apple-touch-icon.png">
  <?= palette_css() ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="admin">
  <header class="admin-bar">
    <a class="admin-brand" href="index.php">
      <span class="mark"><?= e($brand['mark'] ?? 'n') ?></span>
      <?= e($brand['name'] ?? 'Nexabie') ?> <span class="tag">CMS</span>
    </a>
    <nav>
      <a href="index.php" class="<?= $active === 'dashboard' ? 'on' : '' ?>">Dashboard</a>
      <a href="index.php?page=content" class="<?= $active === 'content' ? 'on' : '' ?>">Content</a>
      <a href="index.php?page=media" class="<?= $active === 'media' ? 'on' : '' ?>">Media</a>
      <a href="index.php?page=inbox" class="<?= $active === 'inbox' ? 'on' : '' ?>">Inbox<?= $count ? ' <span class="pill">' . $count . '</span>' : '' ?></a>
      <a href="index.php?page=settings" class="<?= $active === 'settings' ? 'on' : '' ?>">Settings</a>
      <a href="../index.php" target="_blank" rel="noopener" class="ghost">View site ↗</a>
      <a href="index.php?page=logout" class="ghost">Log out</a>
    </nav>
  </header>
  <main class="admin-main">
    <h1><?= e($title) ?></h1>
    <?php if (!empty($_GET['saved'])): ?>
      <div class="notice ok">Saved.</div>
    <?php endif; ?>
    <?php if (!empty($_GET['error'])): ?>
      <div class="notice bad"><?= e($_GET['error']) ?></div>
    <?php endif; ?>
    <?php
}

function admin_foot(): void
{
    ?>
  </main>
  <script src="../js/admin.js"></script>
</body>
</html>
    <?php
}
