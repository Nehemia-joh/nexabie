<?php
require_once __DIR__ . '/includes/functions.php';
$c = content();
$brand = $c['brand'];
$s = $c['services'];

$pageTitle = 'Services — consultation, design & development | Nexabie';
$pageDescription = 'Three ways to work with ' . ($brand['name'] ?? 'Nexabie') . ': consultation, product design and full-stack development — with a clear process from first call to handover.';

require __DIR__ . '/includes/header.php';
?>

    <section class="page-hero wrap">
      <p class="breadcrumb"><a href="/"><?= e($brand['name']) ?></a> — Services</p>
      <h1 class="display"><?= e($s['title_a']) ?><br><?= e($s['title_b']) ?></h1>
      <p class="lead"><?= e($s['lead']) ?></p>
    </section>

    <!-- Engagements -->
    <section class="section wrap" style="padding-top:0" aria-labelledby="eng-title">
      <div class="services-list">
        <?php foreach ($s['engagements'] as $eng): ?>
        <article class="service">
          <div>
            <h3 id="eng-title"><a href="services/<?= e($eng['slug']) ?>"><?= e($eng['name']) ?></a></h3>
            <p class="lead"><?= e($eng['desc']) ?></p>
            <span class="duration"><?= e($eng['duration']) ?></span>
          </div>
          <div class="service-details">
            <p style="margin-top:0;font-weight:600;font-size:15px"><?= e($s['deliverables_label']) ?></p>
            <ul>
              <?php foreach ($eng['points'] as $pt): ?><li><?= e($pt) ?></li><?php endforeach; ?>
            </ul>
            <a class="link-more" href="contact?service=<?= e(strtolower($eng['name'])) ?>"><?= e($eng['cta']) ?></a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </section>

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
    <section class="section section-rule wrap" aria-labelledby="faq-title">
      <div class="section-head">
        <h2 class="h2" id="faq-title"><?= e($s['faq_a']) ?> <?= e($s['faq_b']) ?></h2>
      </div>
      <div class="faq">
        <?php foreach ($s['faqs'] as $faq): ?>
        <details>
          <summary><?= e($faq['q']) ?></summary>
          <p><?= e($faq['a']) ?></p>
        </details>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- CTA -->
    <section class="section wrap">
      <div class="cta-band">
        <div>
          <h2 class="h2"><?= e($s['cta_a']) ?> <?= e($s['cta_b']) ?></h2>
          <p class="lead">Or email <a href="mailto:<?= e($brand['email']) ?>" style="color:inherit"><?= e($brand['email']) ?></a></p>
        </div>
        <div class="cta-actions">
          <a class="btn btn-primary" href="contact?service=consultation"><?= e($s['cta_button']) ?></a>
          <a class="btn btn-ghost" href="work">See the work</a>
        </div>
      </div>
    </section>

<?php require __DIR__ . '/includes/footer.php'; ?>
