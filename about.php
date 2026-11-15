<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/fallbacks.php';
$c = content();
$brand = $c['brand'];
$a = $c['about'];

$pageTitle = 'About — the team behind ' . ($brand['name'] ?? 'Nexabie');
$pageDescription = ($brand['name'] ?? 'Nexabie') . ' is a design and engineering studio in Dar es Salaam, Tanzania. We design products in Figma and build them in React, Flutter and NestJS.';

require __DIR__ . '/includes/header.php';
?>

    <section class="page-hero wrap">
      <p class="breadcrumb"><a href="index"><?= e($brand['name']) ?></a> — About</p>
      <div class="about-grid">
        <div>
          <h1 class="display"><?= e($a['title_a']) ?><br><?= e($a['title_b']) ?></h1>
          <p class="lead"><?= e($a['lead']) ?></p>
          <div class="hero-cta" style="margin-top:28px">
            <a class="btn btn-primary" href="contact"><?= e($a['cta_primary']) ?></a>
            <a class="btn btn-ghost" href="#experience"><?= e($a['cta_secondary']) ?></a>
          </div>
        </div>
        <div class="portrait">
          <?php if (!empty($a['portrait_image'])): ?>
            <img src="<?= e($a['portrait_image']) ?>" alt="<?= e($a['portrait_alt']) ?>" style="width:100%;height:100%;object-fit:cover">
          <?php else: ?>
            <?= fallback_svg('portrait') ?>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- Why we build -->
    <section class="section section-rule wrap" aria-labelledby="why-title">
      <div class="section-head">
        <h2 class="h2" id="why-title"><?= e($a['why_a']) ?><br><?= e($a['why_b']) ?></h2>
        <p class="lead"><?= e($a['why_lead']) ?></p>
      </div>
    </section>

    <!-- How we work -->
    <section class="section section-rule wrap" aria-labelledby="how-title">
      <div class="section-head">
        <h2 class="h2" id="how-title"><?= e($a['how_a']) ?> <?= e($a['how_b']) ?></h2>
      </div>
      <div class="values">
        <?php foreach ($a['values'] as $v): ?>
        <div class="value">
          <h4><?= e($v['title']) ?></h4>
          <p><?= e($v['text']) ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Experience -->
    <section class="section section-rule wrap" id="experience" aria-labelledby="exp-title">
      <div class="section-head">
        <h2 class="h2" id="exp-title"><?= e($a['exp_a']) ?> <?= e($a['exp_b']) ?></h2>
      </div>

      <div class="timeline">
        <?php foreach ($a['jobs'] as $job): ?>
        <article class="job">
          <span class="when"><?= e($job['when']) ?></span>
          <div>
            <h4><?= e($job['title']) ?></h4>
            <div class="org"><?= e($job['org']) ?></div>
            <p><?= e($job['text']) ?></p>
          </div>
        </article>
        <?php endforeach; ?>
      </div>

      <div class="skill-groups" style="margin-top:48px">
        <?php foreach ($a['skills'] as $g): ?>
        <div class="skill-group">
          <h4><?= e($g['title']) ?></h4>
          <ul>
            <?php foreach ($g['items'] as $item): ?><li><?= e($item) ?></li><?php endforeach; ?>
          </ul>
        </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- CTA -->
    <section class="section wrap">
      <div class="cta-band">
        <div>
          <h2 class="h2"><?= e($a['cta_a']) ?> <?= e($a['cta_b']) ?></h2>
          <p class="lead">Book a call or email <a href="mailto:<?= e($brand['email']) ?>" style="color:inherit"><?= e($brand['email']) ?></a></p>
        </div>
        <div class="cta-actions">
          <a class="btn btn-primary" href="contact">Book a call</a>
          <a class="btn btn-ghost" href="work">See the work</a>
        </div>
      </div>
    </section>

<?php require __DIR__ . '/includes/footer.php'; ?>
