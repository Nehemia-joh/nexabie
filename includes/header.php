<?php
require_once __DIR__ . '/functions.php';
$c = content();
$brand = $c['brand'] ?? [];
$currentPage = current_page();
$pageKey = preg_replace('/\.php$/', '', $currentPage);

/* Absolute canonical URL for the current page (apex host, no trailing slash). */
$siteHost = 'nexabie.co.tz';
$isNotFound = (http_response_code() === 404) || $pageKey === '404'
    || ($pageKey === 'service' && empty($_GET['slug']));
$pathMap = [
    'index'   => '/',
    'work'    => '/work',
    'services'=> '/services',
    'about'   => '/about',
    'contact' => '/contact',
    'privacy' => '/privacy',
];
if ($pageKey === 'service') {
    $slug = (string) ($_GET['slug'] ?? '');
    $canonicalPath = $slug !== '' ? '/services/' . $slug : '/services';
} else {
    $canonicalPath = $pathMap[$pageKey] ?? '/';
}
$canonicalUrl = 'https://' . $siteHost . $canonicalPath;
$ogTitle = (string) ($pageTitle ?? ($brand['name'] ?? 'Nexabie'));
$ogDescription = (string) ($pageDescription ?? '');
$ogImage = 'https://' . $siteHost . '/assets/og-image.png';
$isHome = $pageKey === 'index' || $canonicalPath === '/';

/* JSON-LD: Organization + WebSite on home; Service on service detail pages. */
$jsonLd = [];
$orgId = 'https://' . $siteHost . '/#organization';
$jsonLd[] = [
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    '@id' => $orgId,
    'name' => $brand['name'] ?? 'Nexabie',
    'url' => 'https://' . $siteHost . '/',
    'email' => $brand['email'] ?? 'hello@nexabie.co.tz',
    'logo' => 'https://' . $siteHost . '/assets/favicon-64.png',
    'description' => 'Software design and development studio in Dar es Salaam, Tanzania.',
    'address' => [
        '@type' => 'PostalAddress',
        'addressLocality' => 'Dar es Salaam',
        'addressCountry' => 'TZ',
    ],
    'sameAs' => array_values(array_filter([
        $brand['linkedin'] ?? '',
        $brand['github'] ?? '',
        $brand['twitter'] ?? '',
    ], static fn ($u) => $u !== '' && $u !== '#')),
];
$jsonLd[] = [
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    '@id' => 'https://' . $siteHost . '/#website',
    'url' => 'https://' . $siteHost . '/',
    'name' => $brand['name'] ?? 'Nexabie',
    'publisher' => ['@id' => $orgId],
    'inLanguage' => 'en',
];
if ($pageKey === 'service' && isset($eng) && is_array($eng)) {
    $jsonLd[] = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => $eng['name'] ?? '',
        'description' => $eng['seo_desc'] ?? ($eng['desc'] ?? ''),
        'url' => $canonicalUrl,
        'provider' => ['@id' => $orgId],
        'serviceType' => $eng['name'] ?? '',
        'areaServed' => 'Worldwide',
    ];
}
if (!$isNotFound && !$isHome && $canonicalPath !== '/') {
    $breadcrumb = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => 'https://' . $siteHost . '/'],
        ],
    ];
    if ($pageKey === 'service') {
        $breadcrumb['itemListElement'][] = ['@type' => 'ListItem', 'position' => 2, 'name' => 'Services', 'item' => 'https://' . $siteHost . '/services'];
        $breadcrumb['itemListElement'][] = ['@type' => 'ListItem', 'position' => 3, 'name' => $eng['name'] ?? 'Service', 'item' => $canonicalUrl];
    } else {
        $crumbName = ucfirst($pageKey === 'index' ? 'Home' : $pageKey);
        $breadcrumb['itemListElement'][] = ['@type' => 'ListItem', 'position' => 2, 'name' => $crumbName, 'item' => $canonicalUrl];
    }
    $jsonLd[] = $breadcrumb;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($ogTitle) ?></title>
  <?php if ($isNotFound): ?>
  <meta name="robots" content="noindex, follow">
  <?php else: ?>
  <link rel="canonical" href="<?= e($canonicalUrl) ?>">
  <?php endif; ?>
  <?php if ($ogDescription !== ''): ?>
  <meta name="description" content="<?= e($ogDescription) ?>">
  <?php endif; ?>
  <meta property="og:type" content="<?= $isHome ? 'website' : 'article' ?>">
  <meta property="og:site_name" content="<?= e($brand['name'] ?? 'Nexabie') ?>">
  <meta property="og:title" content="<?= e($ogTitle) ?>">
  <?php if ($ogDescription !== ''): ?>
  <meta property="og:description" content="<?= e($ogDescription) ?>">
  <?php endif; ?>
  <meta property="og:url" content="<?= e($canonicalUrl) ?>">
  <meta property="og:image" content="<?= e($ogImage) ?>">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:locale" content="en_US">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= e($ogTitle) ?>">
  <?php if ($ogDescription !== ''): ?>
  <meta name="twitter:description" content="<?= e($ogDescription) ?>">
  <?php endif; ?>
  <meta name="twitter:image" content="<?= e($ogImage) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/style.css">
  <!-- Live palette from Admin → Settings (must come after style.css) -->
  <?= palette_css() ?>
  <link rel="icon" href="/assets/favicon.svg" type="image/svg+xml">
  <link rel="icon" href="/assets/favicon-32.png" sizes="32x32">
  <link rel="icon" href="/assets/favicon-64.png" sizes="64x64">
  <link rel="apple-touch-icon" href="/assets/apple-touch-icon.png">
  <meta name="theme-color" content="#000000">
  <script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>
</head>
<body id="top">
  <a class="skip-link" href="#main">Skip to content</a>

  <header class="site-header">
    <div class="wrap header-inner">
      <a class="brand" href="/" aria-label="<?= e($brand['name'] ?? 'Nexabie') ?> home">
        <?php if (!empty($brand['logo']) && is_file(SITE_ROOT . '/' . $brand['logo'])): ?>
          <img class="brand-logo" src="<?= e(asset($brand['logo'])) ?>" alt="">
        <?php else: ?>
          <span class="brand-mark" aria-hidden="true"><?= e($brand['mark'] ?? 'n') ?></span>
        <?php endif; ?>
        <?= e($brand['name'] ?? 'Nexabie') ?>
      </a>
      <?php $servicesActive = in_array($pageKey, ['services', 'service'], true); ?>
      <nav class="nav" id="site-nav" aria-label="Primary">
        <a href="/work"<?= $pageKey === 'work' ? ' aria-current="page"' : '' ?>>Work</a>
        <a href="/services"<?= $servicesActive ? ' aria-current="page"' : '' ?>>Services</a>
        <a href="/about"<?= $pageKey === 'about' ? ' aria-current="page"' : '' ?>>About</a>
        <a href="/contact" class="btn btn-primary"<?= $pageKey === 'contact' ? ' aria-current="page"' : '' ?>>Start a project</a>
      </nav>
      <div class="header-actions">
        <a href="/contact" class="btn btn-primary">Start a project</a>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="site-nav">
          <span class="menu-label">Menu</span>
        </button>
      </div>
    </div>
  </header>

  <main id="main">
