<?php
declare(strict_types=1);

if (!defined('NEXABIE_ROUTER')) {
    http_response_code(404);
    exit('Not found');
}

require_once dirname(__DIR__) . '/includes/functions.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ensure_session();
    $user = trim((string) ($_POST['username'] ?? ''));
    $pass = (string) ($_POST['password'] ?? '');
    if (attempt_login($user, $pass)) {
        header('Location: ./');
        exit;
    }
    $error = 'Wrong username or password.';
}

$brand = content()['brand'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Log in — Nexabie CMS</title>
  <link rel="icon" href="../assets/favicon.svg" type="image/svg+xml">
  <link rel="icon" href="../assets/favicon-32.png" sizes="32x32">
  <link rel="apple-touch-icon" href="../assets/apple-touch-icon.png">
  <?= palette_css() ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="admin login-page">
  <form class="login-card" method="post" action="login">
    <div class="admin-brand" style="justify-content:center;margin-bottom:6px">
      <?php if (!empty($brand['logo']) && is_file(SITE_ROOT . '/' . $brand['logo'])): ?>
        <img class="mark-img" src="<?= e(asset($brand['logo'])) ?>" alt="">
      <?php else: ?>
        <span class="mark"><?= e($brand['mark'] ?? 'n') ?></span>
      <?php endif; ?>
      <?= e($brand['name'] ?? 'Nexabie') ?> <span class="tag">CMS</span>
    </div>
    <p class="muted" style="text-align:center;margin-top:0">Sign in to edit the website.</p>
    <?php if ($error): ?><div class="notice bad"><?= e($error) ?></div><?php endif; ?>
    <label for="username">Username</label>
    <input id="username" name="username" type="text" autocomplete="username" required autofocus>
    <label for="password">Password</label>
    <input id="password" name="password" type="password" autocomplete="current-password" required>
    <button class="btn-a primary" type="submit">Log in</button>
    <p class="muted" style="font-size:13px;text-align:center;margin-bottom:0">
      Default: <code>admin</code> / <code>nexabie2026</code> — change it in Settings.
    </p>
  </form>
</body>
</html>
