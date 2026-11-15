<?php
require_once __DIR__ . '/functions.php';
$c = content();
$brand = $c['brand'] ?? [];
$currentPage = current_page();
$pageKey = preg_replace('/\.php$/', '', $currentPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle ?? ($brand['name'] ?? 'Nexabie')) ?></title>
  <?php if (!empty($pageDescription)): ?>
  <meta name="description" content="<?= e($pageDescription) ?>">
  <?php endif; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <!-- Live palette from Admin → Settings (must come after style.css) -->
  <?= palette_css() ?>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='8' fill='%230b0b0a'/><text x='16' y='23' font-size='18' font-family='Georgia' font-style='italic' fill='%233ea0e3' text-anchor='middle'>n</text></svg>">
</head>
<body id="top">
  <a class="skip-link" href="#main">Skip to content</a>

  <header class="site-header">
    <div class="wrap header-inner">
      <a class="brand" href="index.php" aria-label="<?= e($brand['name'] ?? 'Nexabie') ?> home">
        <span class="brand-mark" aria-hidden="true"><?= e($brand['mark'] ?? 'n') ?></span>
        <?= e($brand['name'] ?? 'Nexabie') ?>
      </a>
      <nav class="nav" id="site-nav" aria-label="Primary">
        <a href="work.php"<?= $pageKey === 'work' ? ' aria-current="page"' : '' ?>>Work</a>
        <a href="services.php"<?= $pageKey === 'services' ? ' aria-current="page"' : '' ?>>Services</a>
        <a href="about.php"<?= $pageKey === 'about' ? ' aria-current="page"' : '' ?>>About</a>
        <a href="contact.php" class="btn btn-primary"<?= $pageKey === 'contact' ? ' aria-current="page"' : '' ?>>Start a project</a>
      </nav>
      <div class="header-actions">
        <a href="contact.php" class="btn btn-primary">Start a project</a>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-nav">
          <span class="menu-label">Menu</span>
        </button>
      </div>
    </div>
  </header>

  <main id="main">
