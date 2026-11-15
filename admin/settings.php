<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$paletteLabels = [
    'paper' => 'Background (Cloud White)',
    'paper_l' => 'Card background',
    'stone' => 'Muted surface',
    'stone_l' => 'Muted surface light',
    'line' => 'Borders',
    'line_soft' => 'Soft borders',
    'ink' => 'Main text (black)',
    'ink_2' => 'Secondary text',
    'ink_3' => 'Muted text',
    'accent' => 'Accent — links & numbers (dark blue)',
    'accent_dark' => 'Accent — hover',
    'accent_bright' => 'Accent — bright (Sky Blue)',
    'accent_tint' => 'Accent — pale tint',
    'red' => 'Highlight (Scarlet Red)',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = (string) ($_POST['action'] ?? 'save');

    if ($action === 'reset') {
        save_json('settings.json', ['palette' => default_palette()]);
        header('Location: settings.php?saved=1');
        exit;
    }

    if ($action === 'palette') {
        $settings = settings();
        $posted = $_POST['palette'] ?? [];
        foreach (array_keys(default_palette()) as $key) {
            $hex = trim((string) ($posted[$key] ?? ''));
            if (preg_match('/^#[0-9a-fA-F]{6}$/', $hex)) {
                $settings['palette'][$key] = strtolower($hex);
            }
        }
        save_json('settings.json', $settings);
        header('Location: settings.php?saved=1#palette');
        exit;
    }

    if ($action === 'password') {
        $current = (string) ($_POST['current'] ?? '');
        $new = (string) ($_POST['new'] ?? '');
        $confirm = (string) ($_POST['confirm'] ?? '');
        $auth = load_json('auth.json', []);
        if (empty($auth['password_hash']) || !password_verify($current, (string) $auth['password_hash'])) {
            header('Location: settings.php?error=' . rawurlencode('Current password is wrong.'));
            exit;
        }
        if (strlen($new) < 8) {
            header('Location: settings.php?error=' . rawurlencode('New password must be at least 8 characters.'));
            exit;
        }
        if ($new !== $confirm) {
            header('Location: settings.php?error=' . rawurlencode('New passwords do not match.'));
            exit;
        }
        set_password($new);
        header('Location: settings.php?saved=1');
        exit;
    }
}

$settings = settings();
$palette = array_merge(default_palette(), $settings['palette'] ?? []);

admin_head('Settings', 'settings');
?>

<h2 id="palette">Colour palette</h2>
<p class="muted">Changes apply to the whole site instantly. Scarlet Red is also used at 10% opacity for hover washes.</p>

<form class="form" method="post" action="settings.php">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="palette">
  <div class="swatches">
    <?php foreach ($paletteLabels as $key => $label): ?>
    <label class="swatch">
      <span class="chip"><input type="color" name="palette[<?= e($key) ?>]" value="<?= e($palette[$key]) ?>"></span>
      <span class="swatch-meta">
        <strong><?= e($label) ?></strong>
        <code><?= e($key) ?> · <?= e($palette[$key]) ?></code>
      </span>
    </label>
    <?php endforeach; ?>
  </div>
  <div class="form-actions">
    <button class="btn-a primary" type="submit">Save palette</button>
  </div>
</form>

<form class="form inline-form" method="post" action="settings.php" onsubmit="return confirm('Reset the palette to the Sky Blue / Cloud White / Scarlet Red defaults?')">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="reset">
  <button class="btn-a" type="submit">Reset to default palette</button>
</form>

<h2>Admin password</h2>
<form class="form narrow" method="post" action="settings.php">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="password">
  <div class="fld"><label for="cur">Current password</label><input id="cur" name="current" type="password" autocomplete="current-password" required></div>
  <div class="fld"><label for="new">New password</label><input id="new" name="new" type="password" autocomplete="new-password" required minlength="8"></div>
  <div class="fld"><label for="conf">Repeat new password</label><input id="conf" name="confirm" type="password" autocomplete="new-password" required minlength="8"></div>
  <div class="form-actions"><button class="btn-a primary" type="submit">Change password</button></div>
</form>

<?php admin_foot(); ?>
