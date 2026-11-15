<?php
declare(strict_types=1);

if (!defined('NEXABIE_ROUTER')) {
    http_response_code(404);
    exit('Not found');
}

require __DIR__ . '/bootstrap.php';

$inquiries = load_json('inquiries.json', []);
$content = content();
$projects = $content['work']['projects'] ?? [];
$uploads = list_uploads();

admin_head('Dashboard', 'dashboard');
?>

<div class="cards">
  <a class="card" href="index.php?page=content">
    <span class="big"><?= count($projects) ?></span>
    <span>Projects &amp; pages</span>
    <em>Edit wording →</em>
  </a>
  <a class="card" href="index.php?page=media">
    <span class="big"><?= count($uploads) ?></span>
    <span>Images uploaded</span>
    <em>Manage media →</em>
  </a>
  <a class="card" href="index.php?page=inbox">
    <span class="big"><?= count($inquiries) ?></span>
    <span>Messages received</span>
    <em>Open inbox →</em>
  </a>
  <a class="card" href="index.php?page=settings">
    <span class="big">🎨</span>
    <span>Palette &amp; settings</span>
    <em>Change colours →</em>
  </a>
</div>

<h2>Quick actions</h2>
<div class="quick">
  <a class="btn-a" href="index.php?page=content&amp;section=home">Homepage wording</a>
  <a class="btn-a" href="index.php?page=content&amp;section=work">Projects</a>
  <a class="btn-a" href="index.php?page=content&amp;section=services">Services</a>
  <a class="btn-a" href="index.php?page=content&amp;section=brand">Brand &amp; contact</a>
  <a class="btn-a" href="index.php?page=settings#palette">Colour palette</a>
  <a class="btn-a" href="../index.php" target="_blank" rel="noopener">View site ↗</a>
</div>

<?php if ($inquiries): ?>
<h2>Latest messages</h2>
<div class="table-scroll">
<table class="table">
  <thead><tr><th>When</th><th>Name</th><th>Email</th><th>Service</th><th>Message</th></tr></thead>
  <tbody>
  <?php foreach (array_slice($inquiries, 0, 5) as $row): ?>
    <tr>
      <td class="nowrap"><?= e(date('d M Y, H:i', strtotime((string) $row['date']))) ?></td>
      <td><?= e($row['name']) ?></td>
      <td><a href="mailto:<?= e($row['email']) ?>"><?= e($row['email']) ?></a></td>
      <td><?= e($row['service'] ?? '') ?></td>
      <td class="clip"><?= e(mb_strimwidth((string) $row['message'], 0, 90, '…')) ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php endif; ?>

<?php admin_foot(); ?>
