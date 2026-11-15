<?php
require_once __DIR__ . '/includes/functions.php';
$c = content();
$brand = $c['brand'];
$p = $c['privacy'];

$pageTitle = 'Privacy — ' . ($brand['name'] ?? 'Nexabie');
$pageDescription = 'How ' . ($brand['name'] ?? 'Nexabie') . ' handles your information: what we collect when you contact us, and your rights.';

require __DIR__ . '/includes/header.php';
?>

    <section class="page-hero wrap">
      <p class="breadcrumb"><a href="index"><?= e($brand['name']) ?></a> — Privacy</p>
      <h1 class="display"><?= e($p['title_a']) ?><br><?= e($p['title_b']) ?></h1>
      <p class="lead"><?= e($p['lead']) ?></p>
    </section>

    <section class="section wrap" style="padding-top:0">
      <div class="faq" style="max-width:760px">
        <?php foreach ($p['sections'] as $sec): ?>
        <details open>
          <summary><?= e($sec['q']) ?></summary>
          <p><?= e($sec['a']) ?></p>
        </details>
        <?php endforeach; ?>
      </div>
    </section>

<?php require __DIR__ . '/includes/footer.php'; ?>
