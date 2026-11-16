<?php
require_once __DIR__ . '/includes/functions.php';
$c = content();
$brand = $c['brand'] ?? [];

http_response_code(404);
$pageTitle = 'Page not found — ' . ($brand['name'] ?? 'Nexabie');
$pageDescription = 'That page does not exist. See the work, services, or get in touch with Nexabie.';
require __DIR__ . '/includes/header.php';
?>

    <section class="page-hero wrap">
      <p class="breadcrumb"><a href="/"><?= e($brand['name']) ?></a> — 404</p>
      <h1 class="display">Page not<br>found.</h1>
      <p class="lead">The page you’re looking for moved or never existed. Everything we offer is a click away.</p>
      <div class="cta-actions" style="margin-top:26px">
        <a class="btn btn-primary" href="/work">See the work</a>
        <a class="btn btn-ghost" href="/services">All services</a>
        <a class="btn btn-ghost" href="/contact">Start a project</a>
      </div>
    </section>

    <section class="section wrap">
      <div class="cta-band">
        <div>
          <h2 class="h2">Can’t find it?<br>Ask us.</h2>
          <p class="lead">Or email <a href="mailto:<?= e($brand['email'] ?? 'hello@nexabie.co.tz') ?>" style="color:inherit"><?= e($brand['email'] ?? 'hello@nexabie.co.tz') ?></a></p>
        </div>
        <div class="cta-actions">
          <a class="btn btn-primary" href="/contact">Book a call</a>
          <a class="btn btn-ghost" href="/">Back home</a>
        </div>
      </div>
    </section>

<?php require __DIR__ . '/includes/footer.php'; ?>
