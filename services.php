<?php
require_once __DIR__ . '/includes/functions.php';
$c = content();
$brand = $c['brand'];
$s = $c['services'];

$pageTitle = 'Services — consultation, product design and development | ' . ($brand['name'] ?? 'Nexabie');
$pageDescription = 'Three ways to work with ' . ($brand['name'] ?? 'Nexabie') . ': consultation, product design and full-stack development — with a clear process from first call to handover.';

require __DIR__ . '/includes/header.php';
?>

    <section class="page-hero wrap">
      <p class="breadcrumb"><a href="index.php"><?= e($brand['name']) ?></a> — Services</p>
      <h1 class="display"><?= e($s['title_a']) ?> <span class="serif-em"><?= e($s['title_b']) ?></span></h1>
      <p class="lead"><?= e($s['lead']) ?></p>
    </section>

    <!-- Engagements -->
    <section class="section wrap" style="padding-top:0" aria-labelledby="eng-title">
      <div class="eyebrow"><span><span class="num">01</span> · <?= e($s['engagements_eyebrow']) ?></span></div>
      <div class="services-list">
        <?php foreach ($s['engagements'] as $eng): ?>
        <article class="service reveal">
          <span class="service-num"><?= e($eng['num']) ?></span>
          <div>
            <h3 id="eng-title"><?= e($eng['name']) ?></h3>
            <p class="lead"><?= e($eng['desc']) ?></p>
            <span class="duration"><?= e($eng['duration']) ?></span>
          </div>
          <div class="service-details">
            <p style="margin-top:0;font-weight:600;font-size:15px"><?= e($s['deliverables_label']) ?></p>
            <ul>
              <?php foreach ($eng['points'] as $pt): ?><li><?= e($pt) ?></li><?php endforeach; ?>
            </ul>
            <a class="link-arrow" href="contact.php?service=<?= e(strtolower($eng['name'])) ?>"><?= e($eng['cta']) ?></a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Process -->
    <section class="section section-rule wrap" aria-labelledby="process-title">
      <div class="eyebrow"><span><span class="num">02</span> · <?= e($s['process_eyebrow']) ?></span></div>
      <div class="section-head">
        <h2 class="h2" id="process-title"><?= e($s['process_a']) ?><br><span class="serif-em"><?= e($s['process_b']) ?></span></h2>
      </div>
      <div class="process reveal">
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
      <div class="eyebrow"><span><span class="num">03</span> · <?= e($s['faq_eyebrow']) ?></span></div>
      <div class="section-head">
        <h2 class="h2" id="faq-title"><?= e($s['faq_a']) ?> <span class="serif-em"><?= e($s['faq_b']) ?></span></h2>
      </div>
      <div class="faq reveal">
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
      <div class="cta-band reveal">
        <div>
          <h2 class="h2"><?= e($s['cta_a']) ?> <span class="serif-em"><?= e($s['cta_b']) ?></span></h2>
          <p class="lead">Or email <a href="mailto:<?= e($brand['email']) ?>" style="color:inherit"><?= e($brand['email']) ?></a></p>
        </div>
        <div class="cta-actions">
          <a class="btn btn-primary" href="contact.php?service=consultation"><?= e($s['cta_button']) ?></a>
          <a class="btn btn-ghost" href="work.php">See the work</a>
        </div>
      </div>
    </section>

<?php require __DIR__ . '/includes/footer.php'; ?>
