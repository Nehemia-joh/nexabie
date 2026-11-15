<?php
declare(strict_types=1);

/* Nexabie branded HTML emails — email-client-safe tables + inline styles. */

function brand_email_shell(string $preheader, string $inner): string
{
    $c = content();
    $b = $c['brand'] ?? [];
    $base = rtrim(site_config()['base_url'], '/');
    $name = $b['name'] ?? 'Nexabie';
    $mark = $b['mark'] ?? 'n';
    $tagline = $b['tagline'] ?? '';
    $location = $b['location'] ?? '';
    $email = $b['email'] ?? '';
    $hasLogo = !empty($b['logo']) && is_file(SITE_ROOT . '/' . $b['logo']);

    $header = $hasLogo
        ? '<img src="' . e($base . asset($b['logo'])) . '" alt="' . e($name) . '" style="height:34px;display:block;">'
        : '<span style="display:inline-block;width:34px;height:34px;border-radius:9px;background:#f53030;color:#ffffff;'
            . 'font-family:Georgia,\'Times New Roman\',serif;font-weight:700;font-size:20px;line-height:34px;text-align:center;">'
            . e($mark) . '</span>'
            . '<span style="font-size:19px;font-weight:700;color:#101010;letter-spacing:-0.02em;margin-left:10px;'
            . 'vertical-align:middle;font-family:-apple-system,\'Segoe UI\',Roboto,Arial,sans-serif;">' . e($name) . '</span>';

    $siteHost = parse_url($base, PHP_URL_HOST) ?: 'nexabie.co.tz';
    $socials = [];
    foreach (['linkedin' => 'LinkedIn', 'github' => 'GitHub', 'twitter' => 'X'] as $key => $label) {
        $v = (string) ($b[$key] ?? '');
        if ($v !== '' && $v !== '#') {
            $socials[] = '<a href="' . e($v) . '" style="color:#1775bb;text-decoration:none;">' . $label . '</a>';
        }
    }
    $socialLine = $socials
        ? '<p style="margin:0 0 14px;font-size:13px;">' . implode(' &nbsp;·&nbsp; ', $socials) . '</p>'
        : '';

    return '<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="x-apple-disable-message-reformatting">
<meta name="format-detection" content="telephone=no">
<title>' . e($name) . '</title>
</head>
<body style="margin:0;padding:0;background:#eef1f6;">
<div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;mso-hide:all;">' . e($preheader) . '&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;&#847;&zwnj;&nbsp;</div>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#eef1f6;">
<tr><td align="center" style="padding:26px 10px;">
  <table role="presentation" width="560" cellpadding="0" cellspacing="0" style="width:100%;max-width:560px;background:#ffffff;border-radius:14px;">
    <tr><td style="height:5px;background:#f53030;font-size:0;line-height:0;">&#8203;</td></tr>
    <tr><td style="padding:24px 32px 0;font-family:-apple-system,\'Segoe UI\',Roboto,Arial,sans-serif;">
      ' . $header . '
    </td></tr>
    <tr><td style="padding:18px 32px 4px;font-family:-apple-system,\'Segoe UI\',Roboto,Arial,sans-serif;">
      ' . $inner . '
    </td></tr>
    <tr><td style="padding:20px 32px 26px;border-top:1px solid #edf0f4;font-family:-apple-system,\'Segoe UI\',Roboto,Arial,sans-serif;">
      <p style="margin:0 0 6px;font-size:13.5px;color:#5b6470;font-style:italic;">' . e($tagline) . '</p>
      <p style="margin:0 0 6px;font-size:13px;color:#5b6470;">' . e($location) . '</p>
      <p style="margin:0 0 10px;font-size:13px;">'
        . ($email !== '' ? '<a href="mailto:' . e($email) . '" style="color:#1775bb;">' . e($email) . '</a>' : '')
        . ' &nbsp;·&nbsp; <a href="' . e($base) . '" style="color:#1775bb;">' . e($siteHost) . '</a></p>
      ' . $socialLine . '
      <p style="margin:0;font-size:12px;color:#98a2b0;">&copy; ' . date('Y') . ' ' . e($name) . ' &nbsp;·&nbsp; Dar es Salaam, Tanzania</p>
    </td></tr>
  </table>
  <p style="margin:14px 0 0;font-size:11.5px;color:#98a2b0;">You received this because you contacted us at ' . e($siteHost) . '.</p>
</td></tr>
</table>
</body>
</html>';
}

function auto_reply_email_html(string $name, string $email, string $service, string $message): string
{
    $c = content();
    $b = $c['brand'] ?? [];
    $ct = $c['contact'] ?? [];
    $about = $c['about'] ?? [];
    $base = rtrim(site_config()['base_url'], '/');
    $first = preg_split('/\s+/', trim($name))[0] ?? $name;
    $brandName = $b['name'] ?? 'Nexabie';

    $serviceLabel = '';
    if ($service !== '') {
        $serviceLabel = ucwords($service);
        foreach ($ct['services_select'] ?? [] as $opt) {
            if (($opt['value'] ?? '') === $service) {
                $serviceLabel = $opt['label'];
                break;
            }
        }
    }

    $hasPortrait = !empty($about['portrait_image']) && is_file(SITE_ROOT . '/' . $about['portrait_image']);
    if ($hasPortrait) {
        $signature = '<table role="presentation" cellpadding="0" cellspacing="0" style="margin:4px 0 0;">
        <tr>
          <td style="vertical-align:top;padding-right:14px;">
            <img src="' . e($base . asset($about['portrait_image'])) . '" width="84" alt="' . e($brandName) . ' team"
              style="width:84px;height:84px;object-fit:cover;border-radius:12px;display:block;border:1px solid #e7ebf0;">
          </td>
          <td style="vertical-align:middle;font-family:-apple-system,\'Segoe UI\',Roboto,Arial,sans-serif;">
            <p style="margin:0 0 4px;font-size:15px;color:#3c4450;">Warm regards,</p>
            <p style="margin:0;font-size:16px;font-weight:700;color:#101010;">The ' . e($brandName) . ' team</p>
            <p style="margin:3px 0 0;font-size:13px;color:#8a94a3;">Dar es Salaam, Tanzania &nbsp;·&nbsp; UTC+3</p>
          </td>
        </tr></table>';
    } else {
        $signature = '<p style="margin:4px 0 0;font-size:15px;color:#3c4450;">Warm regards,</p>'
            . '<p style="margin:6px 0 0;font-size:16px;font-weight:700;color:#101010;">The ' . e($brandName) . ' team</p>'
            . '<p style="margin:4px 0 0;font-size:13px;color:#8a94a3;">Dar es Salaam, Tanzania &nbsp;·&nbsp; UTC+3</p>';
    }

    $summary = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#fafafa;border:1px solid #e7ebf0;border-radius:10px;margin:0 0 22px;">
    <tr><td style="padding:15px 18px;font-family:-apple-system,\'Segoe UI\',Roboto,Arial,sans-serif;">
      <p style="margin:0 0 8px;font-size:13.5px;color:#5b6470;">Here&rsquo;s what you sent us:</p>'
      . ($serviceLabel !== ''
          ? '<span style="display:inline-block;background:#e8f4fc;color:#1775bb;border-radius:999px;padding:3px 11px;font-size:12.5px;font-weight:600;">'
            . e($serviceLabel) . '</span> '
          : '')
      . '<span style="display:inline-block;background:#ffffff;color:#5b6470;border:1px solid #e7ebf0;border-radius:999px;padding:3px 11px;font-size:12.5px;">'
      . e($email) . '</span>
      <p style="margin:12px 0 0;padding-left:13px;border-left:3px solid #3ea0e3;font-size:14.5px;line-height:1.6;color:#3c4450;white-space:pre-wrap;">'
      . e($message) . '</p>
    </td></tr></table>';

    $buttons = '<table role="presentation" cellpadding="0" cellspacing="0" style="margin:22px 0 6px;">
    <tr>
      <td style="background:#101010;border-radius:8px;">
        <a href="mailto:' . e($b['email'] ?? '') . '?subject=' . rawurlencode('Re: your message to ' . $brandName) . '"
          style="display:inline-block;padding:13px 22px;font-size:14.5px;font-weight:600;color:#ffffff;text-decoration:none;
          font-family:-apple-system,\'Segoe UI\',Roboto,Arial,sans-serif;">Reply to this email</a>
      </td>
      <td width="12" style="width:12px;">&#8203;</td>
      <td style="border:1px solid #101010;border-radius:8px;">
        <a href="' . e($base) . '/work"
          style="display:inline-block;padding:12px 21px;font-size:14.5px;font-weight:600;color:#101010;text-decoration:none;
          font-family:-apple-system,\'Segoe UI\',Roboto,Arial,sans-serif;">See our work</a>
      </td>
    </tr></table>';

    $inner = '<p style="margin:0 0 10px;font-size:15px;color:#5b6470;">Hi ' . e($first) . ',</p>'
        . '<h1 style="margin:0 0 12px;font-size:27px;line-height:1.25;color:#101010;font-weight:700;letter-spacing:-0.02em;">'
        . 'Your message is <span style="color:#1775bb;font-family:Georgia,\'Times New Roman\',serif;font-style:italic;font-weight:400;">with us</span>.'
        . '</h1>'
        . '<p style="margin:0 0 20px;font-size:15.5px;line-height:1.6;color:#3c4450;">'
        . e($ct['success'] ?? 'Thanks for reaching out. We will reply within 24 hours.') . '</p>'
        . $summary
        . $signature
        . $buttons;

    $preheader = 'Thanks — your message is safely with us. We reply within 24 hours.';
    return brand_email_shell($preheader, $inner);
}
