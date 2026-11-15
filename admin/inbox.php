<?php
declare(strict_types=1);

if (!defined('NEXABIE_ROUTER')) {
    http_response_code(404);
    exit('Not found');
}

require __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'delete_one') {
        delete_inquiry(isset($_POST['id']) ? (int) $_POST['id'] : null, (int) ($_POST['index'] ?? -1));
        header('Location: inbox&saved=1');
        exit;
    }
    if ($action === 'clear') {
        clear_inquiries();
        header('Location: inbox&saved=1');
        exit;
    }
}

$inquiries = load_inquiries();

admin_head('Inbox', 'inbox');
?>

<?php if (!$inquiries): ?>
  <p class="muted">No messages yet. Enquiries sent through the contact form appear here (and are emailed to <?= e(content()['brand']['email'] ?? '') ?>).</p>
<?php else: ?>
<div class="form-actions" style="margin-bottom:18px">
  <form method="post" action="inbox" onsubmit="return confirm('Delete ALL messages?')">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="clear">
    <button class="btn-a" type="submit">Clear all</button>
  </form>
</div>

<div class="inbox">
  <?php foreach ($inquiries as $i => $row): ?>
  <article class="msg">
    <header>
      <strong><?= e($row['name']) ?></strong>
      <a href="mailto:<?= e($row['email']) ?>"><?= e($row['email']) ?></a>
      <span class="muted"><?= e(date('d M Y, H:i', strtotime((string) $row['date']))) ?></span>
      <?php if (!empty($row['service'])): ?><span class="pill"><?= e($row['service']) ?></span><?php endif; ?>
      <form method="post" action="inbox" onsubmit="return confirm('Delete this message?')">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="delete_one">
        <input type="hidden" name="id" value="<?= (int) ($row['id'] ?? 0) ?>">
        <input type="hidden" name="index" value="<?= (int) $i ?>">
        <button class="btn-x" type="submit" title="Delete">×</button>
      </form>
    </header>
    <p><?= nl2br(e((string) $row['message'])) ?></p>
  </article>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php admin_foot(); ?>
