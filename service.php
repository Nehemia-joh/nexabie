<?php
require_once __DIR__ . '/includes/functions.php';
$c = content();
$brand = $c['brand'];
$s = $c['services'];

$slug = (string) ($_GET['slug'] ?? '');
$eng = null;
foreach ($s['engagements'] as $candidate) {
    if (($candidate['slug'] ?? '') === $slug) {
        $eng = $candidate;
        break;
    }
}

if ($eng === null) {
    http_response_code(404);
    $pageTitle = 'Page not found | ' . ($brand['name'] ?? 'Nexabie');
    $pageDescription = 'This page does not exist.';
    require __DIR__ . '/includes/header.php';
    ?>
    <section class="page-hero wrap">
      <p class="breadcrumb"><a href="/"><?= e($brand['name']) ?></a> — 404</p>
      <h1 class="display">Page not<br>found.</h1>
      <p class="lead">That page does not exist — but everything we offer is on the services page.</p>
      <div class="cta-actions" style="margin-top:26px">
        <a class="btn btn-primary" href="/services">See all services</a>
        <a class="btn btn-ghost" href="/contact">Start a project</a>
      </div>
    </section>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = (string) ($eng['seo_title'] ?? '');
if ($pageTitle === '') {
    $pageTitle = $eng['name'] . ' — ' . ($brand['name'] ?? 'Nexabie');
}
$pageDescription = (string) ($eng['seo_desc'] ?? '');

$examples = [];
foreach ((array) ($eng['examples'] ?? []) as $exId) {
    $p = project_by_id($c, (string) $exId);
    if ($p !== null) {
        $examples[] = $p;
    }
}

require __DIR__ . '/includes/header.php';
?>

    <section class="page-hero wrap">
      <p class="breadcrumb"><a href="/"><?= e($brand['name']) ?></a> — <a href="/services">Services</a> — <?= e($eng['name']) ?></p>
      <h1 class="display"><?= e($eng['h1_a']) ?><br><?= e($eng['h1_b']) ?></h1>
      <p class="lead"><?= e($eng['desc']) ?></p>
      <div class="hero-note">
        <span><?= e(str_replace('Typical length: ', '', (string) $eng['duration'])) ?></span>
        <span>Free intro call</span>
      </div>
      <div class="cta-actions" style="margin-top:26px">
        <a class="btn btn-primary" href="/contact?service=<?= e($eng['slug']) ?>"><?= e($eng['cta']) ?></a>
        <a class="btn btn-ghost" href="#examples">See examples of this work</a>
      </div>
    </section>

    <?php if (!empty($eng['body'])): ?>
    <section class="section wrap" style="padding-top:0" aria-label="About this service">
      <div class="rich-text" style="max-width:760px">
        <p class="lead" style="margin-bottom:0"><?= e($eng['body']) ?></p>
      </div>
    </section>
    <?php endif; ?>

    <!-- Problems this solves -->
    <section class="section section-rule wrap" aria-labelledby="problems-title">
      <div class="section-head">
        <h2 class="h2" id="problems-title">Does this sound<br>like you?</h2>
      </div>
      <ul class="svc-problems">
        <?php foreach ((array) ($eng['problems'] ?? []) as $prob): ?>
        <li><?= e($prob) ?></li>
        <?php endforeach; ?>
      </ul>
    </section>

    <!-- What you get + pricing -->
    <section class="section section-rule wrap" aria-labelledby="included-title">
      <div class="services-list">
        <article class="service">
          <div>
            <h3 id="included-title"><?= e($s['deliverables_label']) ?></h3>
            <p class="lead"><?= e($eng['price_note']) ?></p>
          </div>
          <div class="service-details">
            <p style="margin-top:0;font-weight:600;font-size:15px"><?= e($eng['name']) ?> includes</p>
            <ul>
              <?php foreach ($eng['points'] as $pt): ?><li><?= e($pt) ?></li><?php endforeach; ?>
            </ul>
            <a class="link-more" href="/contact?service=<?= e($eng['slug']) ?>"><?= e($eng['cta']) ?></a>
          </div>
        </article>
      </div>
    </section>

    <!-- Examples of this work -->
    <?php if ($examples): ?>
    <section class="section section-rule wrap" id="examples" aria-labelledby="examples-title">
      <div class="section-head">
        <h2 class="h2" id="examples-title">Examples of<br>this work.</h2>
        <div class="section-aside">
          <a class="link-more" href="/work">See all the work</a>
        </div>
      </div>
      <div class="svc-examples">
        <?php foreach ($examples as $p): ?>
        <a class="svc-example" href="<?= e($p['href'] ?? ('/work#' . $p['id'])) ?>">
          <span class="pill"><?= e($p['badge'] ?? 'Project') ?></span>
          <p class="svc-example-title"><?= e($p['title']) ?></p>
          <?php if (!empty($p['metric'])): ?><span class="svc-example-metric"><?= e($p['metric']) ?></span>
          <?php elseif (!empty($p['status'])): ?><span class="svc-example-metric"><?= e($p['status']) ?></span><?php endif; ?>
        </a>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

    <!-- Process -->
    <section class="section section-rule wrap" aria-labelledby="process-title">
      <div class="section-head">
        <h2 class="h2" id="process-title"><?= e($s['process_a']) ?><br><?= e($s['process_b']) ?></h2>
      </div>
      <div class="process">
        <?php foreach ($s['steps'] as $step): ?>
        <div class="step">
          <span class="n"><?= e($step['n']) ?></span>
          <h4><?= e($step['title']) ?></h4>
          <p><?= e($step['text']) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- FAQ -->
    <?php if (!empty($eng['faqs'])): ?>
    <section class="section section-rule wrap" aria-labelledby="faq-title">
      <div class="section-head">
        <h2 class="h2" id="faq-title"><?= e($s['faq_a']) ?> <?= e($s['faq_b']) ?></h2>
      </div>
      <div class="faq">
        <?php foreach ($eng['faqs'] as $faq): ?>
        <details>
          <summary><?= e($faq['q']) ?></summary>
          <p><?= e($faq['a']) ?></p>
        </details>
        <?php endforeach; ?>
      </div>
    </section>
    <?php endif; ?>

    <!-- Other services + CTA -->
    <section class="section wrap">
      <div class="svc-siblings">
        <span>More services:</span>
        <?php foreach ($s['engagements'] as $other): ?>
          <?php if (($other['slug'] ?? '') !== $slug): ?>
          <a href="/services/<?= e($other['slug']) ?>"><?= e($other['name']) ?></a>
          <?php endif; ?>
        <?php endforeach; ?>
        <a href="/services">All services</a>
      </div>
      <div class="cta-band">
        <div>
          <h2 class="h2">Ready to talk about<br><?= e($eng['name']) ?>?</h2>
          <p class="lead">Or email <a href="mailto:<?= e($brand['email']) ?>" style="color:inherit"><?= e($brand['email']) ?></a></p>
        </div>
        <div class="cta-actions">
          <a class="btn btn-primary" href="/contact?service=<?= e($eng['slug']) ?>"><?= e($eng['cta']) ?></a>
          <a class="btn btn-ghost" href="/work">See the work</a>
        </div>
      </div>
    </section>

<?php require __DIR__ . '/includes/footer.php'; ?>
