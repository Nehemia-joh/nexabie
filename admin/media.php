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

    if ($action === 'upload') {
        $path = handle_upload($_FILES['image'] ?? []);
        if ($path) {
            header('Location: index.php?page=media&saved=1');
        } else {
            header('Location: index.php?page=media&error=' . rawurlencode('Upload failed — check the file type (jpg, png, webp, gif, svg) and size.'));
        }
        exit;
    }

    if ($action === 'delete') {
        $path = (string) ($_POST['path'] ?? '');
        delete_upload($path);
        header('Location: index.php?page=media&saved=1');
        exit;
    }
}

$uploads = list_uploads();

admin_head('Media', 'media');
?>

<h2>Upload an image</h2>
<form class="form narrow upload-form" method="post" action="index.php?page=media" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="upload">
  <div class="fld">
    <label for="image">Image file</label>
    <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp,.gif,.svg" required>
    <p class="muted" style="font-size:13px;margin:6px 0 0">Max <?= (int) (site_config()['upload_max_mb'] ?? 5) ?> MB. JPG, PNG, WebP, GIF or SVG.</p>
  </div>
  <div class="form-actions"><button class="btn-a primary" type="submit">Upload</button></div>
</form>

<h2>All images (<?= count($uploads) ?>)</h2>
<p class="muted">Click a path to copy it, then paste it into a Content field.</p>

<?php if (!$uploads): ?>
  <p class="muted">No images yet. Until you upload covers, the site shows built-in illustrations.</p>
<?php else: ?>
<div class="gallery">
  <?php foreach ($uploads as $path): ?>
  <figure class="gal-item">
    <img src="<?= e($path) ?>" alt="" loading="lazy">
    <figcaption>
      <button type="button" class="path" data-copy="<?= e($path) ?>" title="Click to copy"><?= e($path) ?></button>
      <form method="post" action="index.php?page=media" onsubmit="return confirm('Delete this image? Content referencing it will show the fallback illustration.')">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="path" value="<?= e($path) ?>">
        <button class="btn-x" type="submit" title="Delete">×</button>
      </form>
    </figcaption>
  </figure>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php admin_foot(); ?>
