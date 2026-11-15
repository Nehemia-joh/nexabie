<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/fallbacks.php';
$c = content();
$brand = $c['brand'];
$w = $c['work'];

$pageTitle = 'Work — apps and websites ' . ($brand['name'] ?? 'Nexabie') . ' designed and built';
$pageDescription = 'Selected work from ' . ($brand['name'] ?? 'Nexabie') . ': client projects and products we design, build and run.';

require __DIR__ . '/includes/header.php';
?>

    <section class="page-hero wrap">
      <p class="breadcrumb"><a href="index"><?= e($brand['name']) ?></a> — Work</p>
      <h1 class="display"><?= e($w['title_a']) ?> <span class="serif-em"><?= e($w['title_b']) ?></span></h1>
      <p class="lead"><?= e($w['lead']) ?></p>
    </section>

    <section class="section wrap" style="padding-top:0">
      <div class="filters" role="group" aria-label="Filter projects">
        <?php foreach ($w['filters'] as $i => $f): ?>
        <button class="filter-btn<?= $i === 0 ? ' is-active' : '' ?>" type="button" data-filter="<?= e($f['value']) ?>" aria-pressed="<?= $i === 0 ? 'true' : 'false' ?>"><?= e($f['label']) ?></button>
        <?php endforeach; ?>
      </div>

      <div class="work-grid">
        <?php foreach ($w['projects'] as $p): ?>
        <a class="work-card reveal<?= !empty($p['wide']) ? ' wide' : '' ?>" id="<?= e($p['id']) ?>" href="<?= e($p['href'] ?? ('#' . $p['id'])) ?>" data-category="<?= e($p['category']) ?>">
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
            <span class="kicker"><?= e($p['kicker']) ?></span>
            <h3><?= e($p['title']) ?></h3>
            <div class="work-metric"><span><?= e($p['metric']) ?></span><span><?= e($p['metric_link']) ?></span></div>
          </div>
        </a>
        <?php endforeach; ?>

        <div class="work-card wide reveal" data-category="all" style="display:flex;align-items:center;justify-content:center;background:var(--accent-tint);border-style:dashed;min-height:220px;text-align:center;padding:36px">
          <div>
            <span class="kicker" style="font-size:13.5px;font-weight:550;color:var(--ink-3);text-transform:uppercase;letter-spacing:.05em"><?= e($w['next_kicker']) ?></span>
            <h3 class="h3" style="margin:10px 0 8px"><?= e($w['next_title']) ?></h3>
            <p class="muted" style="margin:0 0 18px"><?= e($w['next_text']) ?></p>
            <a class="btn btn-primary" href="contact"><?= e($w['next_cta']) ?></a>
          </div>
        </div>
      </div>
    </section>

<?php require __DIR__ . '/includes/footer.php'; ?>
