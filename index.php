<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/fallbacks.php';
$c = content();
$h = $c['home'];
$brand = $c['brand'];
$work = $c['work'];
$services = $c['services'];

$pageTitle = ($brand['name'] ?? 'Nexabie') . ' — Software design & development studio in Tanzania';
$pageDescription = 'Nexabie is a software design and development studio in Tanzania. We design products and build web and mobile apps — from first sketch to shipped software.';

require __DIR__ . '/includes/header.php';
?>

    <!-- Hero -->
    <section class="hero wrap">
      <p class="hero-kicker"><span class="dot" aria-hidden="true"></span> <?= e($h['kicker']) ?></p>
      <h1 class="display">
        <?= e($h['hero_before']) ?>
        <span class="rotator" aria-hidden="true">
          <?php foreach ($h['hero_words'] as $i => $w): ?>
          <span<?= $i === 0 ? ' class="is-active"' : '' ?>><?= e($w) ?></span>
          <?php endforeach; ?>
        </span>
        <span class="sr-only"><?= e($h['hero_words'][0] ?? '') ?></span>
        <?= e($h['hero_after']) ?>
      </h1>
      <p class="lead hero-lead"><?= e($h['lead']) ?></p>
      <div class="hero-cta">
        <a class="btn btn-primary" href="contact"><?= e($h['cta_primary']) ?></a>
        <a class="btn btn-ghost" href="work"><?= e($h['cta_secondary']) ?></a>
      </div>
      <div class="hero-note">
        <span><?= e($h['note1']) ?></span>
        <span><?= e($h['note2']) ?></span>
      </div>

      <div class="hero-media">
        <?php foreach (['hero-web', 'hero-mobile'] as $i => $key): $m = $h['hero_media'][$i] ?? []; ?>
        <div class="media-card">
          <?php if (!empty($m['image'])): ?>
            <img src="<?= e($m['image']) ?>" alt="<?= e($m['title']) ?>" style="width:100%;height:100%;object-fit:cover;position:absolute;inset:0">
          <?php else: ?>
            <?= fallback_svg($key) ?>
          <?php endif; ?>
          <div class="media-caption"><span><strong><?= e(explode(' · ', $m['title'] ?? '')[0]) ?></strong><?= isset($m['title']) && str_contains($m['title'], ' · ') ? ' · ' . e(explode(' · ', $m['title'], 2)[1]) : '' ?></span><span><?= e($m['type'] ?? '') ?></span></div>
        </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Stats -->
    <section class="stats" aria-label="Key numbers">
      <?php foreach ($h['stats'] as $s): ?>
      <div class="stat"><b><?= e($s['value']) ?></b><span><?= e($s['label']) ?></span></div>
      <?php endforeach; ?>
    </section>

    <!-- 01 Selected work -->
    <section class="section wrap" aria-labelledby="work-title">
      <div class="eyebrow">
        <span><span class="num">01</span> · <?= e($h['work_eyebrow']) ?></span>
        <a class="link-arrow" href="work"><?= e($h['work_link']) ?></a>
      </div>
      <div class="section-head">
        <h2 class="h2" id="work-title"><?= e($h['work_title_a']) ?><br><span class="serif-em"><?= e($h['work_title_b']) ?></span></h2>
        <p class="lead"><?= e($h['work_lead']) ?></p>
      </div>

      <div class="work-grid">
        <?php
        $featured = [];
        foreach ($h['featured'] as $id) {
            $p = project_by_id($c, (string) $id);
            if ($p) { $featured[] = $p; }
        }
        foreach ($featured as $i => $p):
            $wide = $i === 0;
        ?>
        <a class="work-card reveal<?= $wide ? ' wide' : '' ?>" href="<?= e($p['href'] ?? ('work#' . $p['id'])) ?>" data-category="<?= e($p['category']) ?>">
          <div class="work-cover">
            <?php if (!empty($p['image'])): ?>
              <img src="<?= e($p['image']) ?>" alt="<?= e($p['title']) ?>" style="width:100%;height:100%;object-fit:cover;position:absolute;inset:0">
            <?php else: ?>
              <?= fallback_svg('cover-' . $p['id']) ?>
            <?php endif; ?>
            <div class="work-meta">
              <span class="badge"><?= e($p['badge']) ?></span>
              <span class="badge <?= e($p['status_class']) ?>"><?= e($p['status']) ?></span>
            </div>
          </div>
          <div class="work-body">
            <span class="kicker"><?= e(explode(' · ', $p['kicker'])[0]) ?> · <?= e($p['badge']) ?></span>
            <h3><?= e($p['title']) ?></h3>
            <div class="work-metric"><span><?= e($p['metric']) ?></span><span><?= e($p['metric_link']) ?></span></div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- 02 Services -->
    <section class="section section-rule wrap" aria-labelledby="services-title">
      <div class="eyebrow">
        <span><span class="num">02</span> · <?= e($h['services_eyebrow']) ?></span>
        <a class="link-arrow" href="services"><?= e($h['services_link']) ?></a>
      </div>
      <div class="section-head">
        <h2 class="h2" id="services-title"><?= e($h['services_title_a']) ?><br><span class="serif-em"><?= e($h['services_title_b']) ?></span></h2>
      </div>
      <div class="services-list">
        <?php foreach ($services['engagements'] as $eng): ?>
        <article class="service reveal">
          <span class="service-num"><?= e($eng['num']) ?></span>
          <div>
            <h3><?= e($eng['name']) ?></h3>
            <p class="lead"><?= e($eng['desc']) ?></p>
            <span class="duration"><?= e(str_replace('Typical length: ', '', $eng['duration'])) ?></span>
          </div>
          <div class="service-details">
            <ul>
              <?php foreach ($eng['points'] as $pt): ?><li><?= e($pt) ?></li><?php endforeach; ?>
            </ul>
            <a class="link-arrow" href="contact?service=<?= e(strtolower($eng['name'])) ?>"><?= e($eng['cta']) ?></a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- 03 Testimonial -->
    <section class="section section-rule wrap" aria-labelledby="words-title">
      <div class="eyebrow"><span><span class="num">03</span> · <?= e($h['quote_eyebrow']) ?></span></div>
      <div class="quote-block reveal">
        <blockquote>
          <p><?= e($h['quote']) ?></p>
          <footer><strong><?= e($h['quote_author']) ?></strong> · <?= e($h['quote_role']) ?></footer>
        </blockquote>
        <aside class="quote-aside">
          <h4><?= e($h['aside_title']) ?></h4>
          <p><?= e($h['aside_text']) ?></p>
          <a class="link-arrow" href="contact"><?= e($h['aside_link']) ?></a>
        </aside>
      </div>
    </section>

    <!-- CTA -->
    <section class="section wrap">
      <div class="cta-band reveal">
        <div>
          <h2 class="h2"><?= e($h['cta_a']) ?> <span class="serif-em"><?= e($h['cta_b']) ?></span></h2>
          <p class="lead">Book a call or email <a href="mailto:<?= e($brand['email']) ?>" style="color:inherit"><?= e($brand['email']) ?></a></p>
        </div>
        <div class="cta-actions">
          <a class="btn btn-primary" href="contact">Book a call</a>
          <a class="btn btn-ghost" href="work">See the work</a>
        </div>
      </div>
    </section>

<?php require __DIR__ . '/includes/footer.php'; ?>
