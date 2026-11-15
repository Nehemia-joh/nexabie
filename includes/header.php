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
  <link rel="icon" href="assets/favicon.svg" type="image/svg+xml">
  <link rel="icon" href="assets/favicon-32.png" sizes="32x32">
  <link rel="icon" href="assets/favicon-64.png" sizes="64x64">
  <link rel="apple-touch-icon" href="assets/apple-touch-icon.png">
  <meta name="theme-color" content="#000000">
</head>
<body id="top">
  <a class="skip-link" href="#main">Skip to content</a>

  <header class="site-header">
    <div class="wrap header-inner">
      <a class="brand" href="index" aria-label="<?= e($brand['name'] ?? 'Nexabie') ?> home">
        <?php if (!empty($brand['logo']) && is_file(SITE_ROOT . '/' . $brand['logo'])): ?>
          <img class="brand-logo" src="<?= e(asset($brand['logo'])) ?>" alt="">
        <?php else: ?>
          <span class="brand-mark" aria-hidden="true"><?= e($brand['mark'] ?? 'n') ?></span>
        <?php endif; ?>
        <?= e($brand['name'] ?? 'Nexabie') ?>
      </a>
      <nav class="nav" id="site-nav" aria-label="Primary">
        <a href="work"<?= $pageKey === 'work' ? ' aria-current="page"' : '' ?>>Work</a>
        <a href="services"<?= $pageKey === 'services' ? ' aria-current="page"' : '' ?>>Services</a>
        <a href="about"<?= $pageKey === 'about' ? ' aria-current="page"' : '' ?>>About</a>
        <a href="contact" class="btn btn-primary"<?= $pageKey === 'contact' ? ' aria-current="page"' : '' ?>>Start a project</a>
      </nav>
      <div class="header-actions">
        <a href="contact" class="btn btn-primary">Start a project</a>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-nav">
          <span class="menu-label">Menu</span>
        </button>
      </div>
    </div>
  </header>

  <main id="main">
