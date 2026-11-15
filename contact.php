<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/emails.php';
$c = content();
$brand = $c['brand'];
$ct = $c['contact'];

$pageTitle = 'Start a project — contact ' . ($brand['name'] ?? 'Nexabie');
$pageDescription = 'Tell ' . ($brand['name'] ?? 'Nexabie') . ' about your idea. Book a call or send a message — no brief needed. We reply within 24 hours.';

/* ---- Handle form submission ---- */
$sent = false;
$error = false;
$formErrors = [];
$prefillService = isset($_GET['service']) && is_string($_GET['service']) ? $_GET['service'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $service = trim((string) ($_POST['service'] ?? ''));
    $message = trim((string) ($_POST['message'] ?? ''));
    $honeypot = trim((string) ($_POST['website'] ?? ''));

    if ($honeypot !== '') {
        $sent = true; // silently drop bots
    } else {
        if ($name === '') {
            $formErrors[] = 'Please add your name.';
        }
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $formErrors[] = 'Please add a valid email address.';
        }
        if ($message === '') {
            $formErrors[] = 'Please tell us a little about your idea.';
        }

        if (!$formErrors) {
            save_inquiry([
                'date' => date('c'),
                'name' => $name,
                'email' => $email,
                'service' => $service,
                'message' => $message,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            ]);

            $to = $brand['email'];
            $subject = 'Nexabie enquiry — ' . $name;
            $body = "Name: $name\nEmail: $email\nService: $service\n\n$message\n";
            $fromAddr = 'no-reply@' . parse_url(site_config()['base_url'], PHP_URL_HOST);

            if (!send_email($to, $subject, $body, $brand['name'] ?? 'Nexabie', $fromAddr, $email)) {
                error_log('Nexabie: enquiry copy could not be sent (from visitor ' . $email . ')');
            }

            // Auto-reply: the sender gets a confirmation in their own inbox too.
            $ackSubject = 'We received your message — ' . ($brand['name'] ?? 'Nexabie');
            $ackBody = "Hi $name,\n\n"
                . "Thanks for reaching out. Your message is safely with us and we will reply within 24 hours.\n\n"
                . ($service !== '' ? 'You asked about: ' . ucwords($service) . "\n\n" : '')
                . "— " . ($brand['name'] ?? 'Nexabie') . "\n"
                . $brand['email'] . "\n";
            $ackHtml = auto_reply_email_html($name, $email, $service, $message);
            if (!send_email($email, $ackSubject, $ackBody, $brand['name'] ?? 'Nexabie', $fromAddr, $brand['email'], $ackHtml)) {
                error_log('Nexabie: auto-reply could not be sent to ' . $email);
            }

            // The message is already stored (admin inbox / MySQL) — never show a lost-message error.
            $sent = true;
        } else {
            $error = true;
        }
    }
}

require __DIR__ . '/includes/header.php';
?>

    <section class="page-hero wrap">
      <p class="breadcrumb"><a href="index"><?= e($brand['name']) ?></a> — Contact</p>
      <h1 class="display"><?= e($ct['title_a']) ?><br><?= e($ct['title_b']) ?></h1>
      <p class="lead"><?= e($ct['lead']) ?></p>
      <div class="hero-note">
        <span><?= e($ct['note1']) ?></span>
        <span><?= e($ct['note2']) ?></span>
      </div>
    </section>

    <section class="section wrap" style="padding-top:0">
      <div class="contact-grid">
        <div>
          <div class="steps-what">
            <h3 class="h3"><?= e($ct['next_title']) ?></h3>
            <ol>
              <?php foreach ($ct['next_steps'] as $step): ?>
              <li><?= e($step) ?></li>
              <?php endforeach; ?>
            </ol>
          </div>

          <div class="call-options">
            <?php foreach ($ct['calls'] as $call): ?>
            <a class="call-option" href="mailto:<?= e($brand['email']) ?>?subject=<?= rawurlencode($call['subject']) ?>">
              <div>
                <h4><?= e($call['name']) ?></h4>
                <p><?= e($call['desc']) ?></p>
              </div>
              <span class="dur"><?= e($call['dur']) ?></span>
            </a>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="contact-form">
          <?php if ($sent && !$error): ?>
          <div class="form-success is-visible" id="form-success" tabindex="-1" role="status">
            <?= e($ct['success']) ?>
          </div>
          <?php elseif ($error): ?>
          <div class="form-error is-visible" role="alert">
            <?php foreach ($formErrors as $fe): ?><p style="margin:0 0 6px"><?= e($fe) ?></p><?php endforeach; ?>
            <p style="margin:0">Or email <a href="mailto:<?= e($brand['email']) ?>"><?= e($brand['email']) ?></a></p>
          </div>
          <?php endif; ?>

          <h3 class="h3"><?= e($ct['form_title']) ?></h3>
          <p class="hint"><?= e($ct['form_hint']) ?></p>

          <form id="contact-form" method="post" action="contact" novalidate>
            <input type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px">
            <div class="field">
              <label for="field-name"><?= e($ct['labels']['name']) ?></label>
              <input id="field-name" name="name" type="text" autocomplete="name" required placeholder="<?= e($ct['placeholders']['name']) ?>" value="<?= e($_POST['name'] ?? '') ?>">
            </div>
            <div class="field">
              <label for="field-email"><?= e($ct['labels']['email']) ?></label>
              <input id="field-email" name="email" type="email" autocomplete="email" required placeholder="<?= e($ct['placeholders']['email']) ?>" value="<?= e($_POST['email'] ?? '') ?>">
            </div>
            <div class="field">
              <label for="field-service"><?= e($ct['labels']['service']) ?></label>
              <select id="field-service" name="service">
                <?php
                $selected = ($_POST['service'] ?? $prefillService) ?: '';
                foreach ($ct['services_select'] as $opt):
                ?>
                <option value="<?= e($opt['value']) ?>"<?= $selected === $opt['value'] ? ' selected' : '' ?>><?= e($opt['label']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="field">
              <label for="field-message"><?= e($ct['labels']['message']) ?></label>
              <textarea id="field-message" name="message" required placeholder="<?= e($ct['placeholders']['message']) ?>"><?= e($_POST['message'] ?? '') ?></textarea>
            </div>
            <button class="btn btn-primary" type="submit" style="width:100%"><?= e($ct['labels']['submit']) ?></button>
            <p class="form-note"><?= e($ct['privacy_note']) ?> <a href="privacy">privacy policy</a>.</p>
          </form>
        </div>
      </div>
    </section>

<?php require __DIR__ . '/includes/footer.php'; ?>
