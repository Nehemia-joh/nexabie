<?php
declare(strict_types=1);

/**
 * Decorative SVG fallbacks used when no image has been uploaded yet.
 * Keys: hero-web, hero-mobile, cover-receiptly, cover-afyacare,
 *       cover-sokolink, cover-nurupay, cover-biro, portrait
 */
function fallback_svg(string $key): string
{
    $g = 'g_' . preg_replace('/[^a-z0-9_-]/', '', $key);

    return match ($key) {
        'hero-web' => <<<SVG
<svg viewBox="0 0 620 400" preserveAspectRatio="xMidYMid slice" role="img" aria-label="Abstract illustration of a web app interface">
  <defs><linearGradient id="{$g}" x1="0" y1="0" x2="1" y2="1">
    <stop offset="0" stop-color="#e8f4fd"/><stop offset="1" stop-color="#bfe1f8"/>
  </linearGradient></defs>
  <rect width="620" height="400" fill="url(#{$g})"/>
  <rect x="60" y="54" width="500" height="300" rx="12" fill="#ffffff" stroke="#dfe4ea"/>
  <rect x="60" y="54" width="500" height="40" rx="12" fill="#f5f7fa"/>
  <circle cx="86" cy="74" r="5" fill="#dfe4ea"/><circle cx="104" cy="74" r="5" fill="#dfe4ea"/><circle cx="122" cy="74" r="5" fill="#dfe4ea"/>
  <rect x="88" y="122" width="180" height="14" rx="7" fill="#0b0b0a"/>
  <rect x="88" y="148" width="260" height="9" rx="4.5" fill="#dfe4ea"/>
  <rect x="88" y="166" width="220" height="9" rx="4.5" fill="#ebeff3"/>
  <rect x="88" y="204" width="120" height="34" rx="17" fill="#3ea0e3"/>
  <rect x="380" y="122" width="140" height="150" rx="10" fill="#e8f4fd" stroke="#dfe4ea"/>
  <rect x="400" y="146" width="100" height="8" rx="4" fill="#3ea0e3" opacity=".7"/>
  <rect x="400" y="166" width="80" height="8" rx="4" fill="#dfe4ea"/>
  <rect x="400" y="186" width="92" height="8" rx="4" fill="#dfe4ea"/>
  <rect x="400" y="216" width="60" height="30" rx="8" fill="#0b0b0a"/>
</svg>
SVG,

        'hero-mobile' => <<<SVG
<svg viewBox="0 0 380 400" preserveAspectRatio="xMidYMid slice" role="img" aria-label="Abstract illustration of a mobile app">
  <rect width="380" height="400" fill="#0b0b0a"/>
  <rect x="105" y="46" width="170" height="320" rx="24" fill="#fafafa" stroke="#33383e"/>
  <rect x="125" y="74" width="90" height="10" rx="5" fill="#0b0b0a"/>
  <rect x="125" y="96" width="130" height="7" rx="3.5" fill="#dfe4ea"/>
  <rect x="125" y="124" width="130" height="64" rx="10" fill="#e8f4fd" stroke="#dfe4ea"/>
  <rect x="137" y="142" width="70" height="8" rx="4" fill="#3ea0e3"/>
  <rect x="137" y="160" width="100" height="6" rx="3" fill="#dfe4ea"/>
  <rect x="125" y="202" width="62" height="62" rx="10" fill="#f5f7fa" stroke="#ebeff3"/>
  <rect x="193" y="202" width="62" height="62" rx="10" fill="#f5f7fa" stroke="#ebeff3"/>
  <rect x="125" y="282" width="130" height="34" rx="17" fill="#3ea0e3"/>
  <rect x="160" y="336" width="60" height="5" rx="2.5" fill="#dfe4ea"/>
</svg>
SVG,

        'cover-receiptly' => <<<SVG
<svg viewBox="0 0 1100 470" preserveAspectRatio="xMidYMid slice" role="img" aria-label="Receiptly app screens">
  <defs><linearGradient id="{$g}" x1="0" y1="0" x2="1" y2="1">
    <stop offset="0" stop-color="#e8f4fd"/><stop offset="1" stop-color="#cfe9fa"/>
  </linearGradient></defs>
  <rect width="1100" height="470" fill="url(#{$g})"/>
  <circle cx="940" cy="90" r="160" fill="#3ea0e3" opacity=".14"/>
  <circle cx="150" cy="400" r="190" fill="#0b0b0a" opacity=".08"/>
  <g transform="translate(360,70)">
    <rect width="170" height="330" rx="24" fill="#ffffff" stroke="#dfe4ea"/>
    <rect x="18" y="26" width="80" height="10" rx="5" fill="#0b0b0a"/>
    <rect x="18" y="52" width="134" height="70" rx="10" fill="#e8f4fd" stroke="#dfe4ea"/>
    <rect x="30" y="72" width="70" height="8" rx="4" fill="#3ea0e3"/>
    <rect x="30" y="92" width="100" height="6" rx="3" fill="#dfe4ea"/>
    <rect x="18" y="138" width="64" height="64" rx="10" fill="#f5f7fa"/>
    <rect x="88" y="138" width="64" height="64" rx="10" fill="#f5f7fa"/>
    <rect x="18" y="220" width="134" height="36" rx="18" fill="#3ea0e3"/>
    <rect x="18" y="274" width="134" height="36" rx="18" fill="#f5f7fa"/>
  </g>
  <g transform="translate(560,110)">
    <rect width="170" height="330" rx="24" fill="#0b0b0a"/>
    <rect x="18" y="26" width="90" height="10" rx="5" fill="#fafafa"/>
    <rect x="18" y="54" width="134" height="90" rx="10" fill="#1a1a17"/>
    <rect x="30" y="74" width="80" height="8" rx="4" fill="#3ea0e3"/>
    <rect x="30" y="96" width="110" height="6" rx="3" fill="#33383e"/>
    <rect x="30" y="114" width="90" height="6" rx="3" fill="#33383e"/>
    <rect x="18" y="164" width="134" height="44" rx="10" fill="#1a1a17"/>
    <rect x="18" y="224" width="134" height="44" rx="10" fill="#1a1a17"/>
    <rect x="18" y="284" width="134" height="30" rx="15" fill="#3ea0e3"/>
  </g>
</svg>
SVG,

        'cover-afyacare' => <<<SVG
<svg viewBox="0 0 640 400" preserveAspectRatio="xMidYMid slice" role="img" aria-label="AfyaCare booking screens">
  <rect width="640" height="400" fill="#f5f7fa"/>
  <rect x="70" y="60" width="250" height="290" rx="14" fill="#ffffff" stroke="#dfe4ea"/>
  <rect x="94" y="88" width="120" height="12" rx="6" fill="#0b0b0a"/>
  <rect x="94" y="116" width="202" height="8" rx="4" fill="#dfe4ea"/>
  <rect x="94" y="148" width="202" height="46" rx="10" fill="#e8f4fd" stroke="#dfe4ea"/>
  <rect x="94" y="208" width="202" height="46" rx="10" fill="#f5f7fa" stroke="#ebeff3"/>
  <rect x="94" y="276" width="130" height="34" rx="17" fill="#3ea0e3"/>
  <rect x="360" y="90" width="210" height="230" rx="14" fill="#0b0b0a"/>
  <rect x="384" y="118" width="100" height="10" rx="5" fill="#fafafa"/>
  <rect x="384" y="146" width="162" height="54" rx="10" fill="#1a1a17"/>
  <rect x="384" y="216" width="76" height="76" rx="10" fill="#1a1a17"/>
  <rect x="470" y="216" width="76" height="76" rx="10" fill="#1a1a17"/>
</svg>
SVG,

        'cover-sokolink' => <<<SVG
<svg viewBox="0 0 640 400" preserveAspectRatio="xMidYMid slice" role="img" aria-label="SokoLink marketplace screens">
  <defs><linearGradient id="{$g}" x1="0" y1="0" x2="0" y2="1">
    <stop offset="0" stop-color="#0b0b0a"/><stop offset="1" stop-color="#1f1f1b"/>
  </linearGradient></defs>
  <rect width="640" height="400" fill="url(#{$g})"/>
  <circle cx="520" cy="80" r="120" fill="#3ea0e3" opacity=".25"/>
  <rect x="80" y="70" width="300" height="260" rx="14" fill="#fafafa"/>
  <rect x="104" y="96" width="140" height="12" rx="6" fill="#0b0b0a"/>
  <rect x="104" y="124" width="252" height="8" rx="4" fill="#dfe4ea"/>
  <rect x="104" y="156" width="116" height="80" rx="10" fill="#e8f4fd" stroke="#dfe4ea"/>
  <rect x="232" y="156" width="124" height="80" rx="10" fill="#f5f7fa" stroke="#ebeff3"/>
  <rect x="104" y="252" width="252" height="36" rx="18" fill="#0b0b0a"/>
  <rect x="420" y="130" width="150" height="210" rx="20" fill="#ffffff" stroke="#33383e"/>
  <rect x="440" y="154" width="70" height="8" rx="4" fill="#0b0b0a"/>
  <rect x="440" y="176" width="110" height="44" rx="8" fill="#e8f4fd"/>
  <rect x="440" y="232" width="110" height="44" rx="8" fill="#f5f7fa"/>
  <rect x="440" y="292" width="110" height="30" rx="15" fill="#3ea0e3"/>
</svg>
SVG,

        'cover-nurupay' => <<<SVG
<svg viewBox="0 0 640 400" preserveAspectRatio="xMidYMid slice" role="img" aria-label="NuruPay wallet screens">
  <rect width="640" height="400" fill="#e8f4fd"/>
  <rect x="90" y="70" width="300" height="190" rx="16" fill="#0b0b0a"/>
  <rect x="116" y="100" width="90" height="8" rx="4" fill="#3ea0e3"/>
  <rect x="116" y="126" width="150" height="18" rx="6" fill="#fafafa"/>
  <rect x="116" y="164" width="110" height="34" rx="17" fill="#3ea0e3"/>
  <circle cx="340" cy="220" r="26" fill="#1a1a17"/>
  <rect x="410" y="120" width="150" height="220" rx="22" fill="#ffffff" stroke="#dfe4ea"/>
  <rect x="432" y="148" width="80" height="8" rx="4" fill="#0b0b0a"/>
  <rect x="432" y="172" width="106" height="52" rx="10" fill="#e8f4fd"/>
  <rect x="432" y="240" width="106" height="40" rx="10" fill="#f5f7fa"/>
  <rect x="432" y="294" width="106" height="30" rx="15" fill="#3ea0e3"/>
</svg>
SVG,

        'cover-biro' => <<<SVG
<svg viewBox="0 0 640 400" preserveAspectRatio="xMidYMid slice" role="img" aria-label="Biro knowledge tool">
  <rect width="640" height="400" fill="#f5f7fa"/>
  <rect x="70" y="60" width="150" height="290" rx="12" fill="#0b0b0a"/>
  <rect x="90" y="86" width="80" height="8" rx="4" fill="#fafafa"/>
  <rect x="90" y="116" width="110" height="26" rx="7" fill="#1a1a17"/>
  <rect x="90" y="152" width="110" height="26" rx="7" fill="#1a1a17"/>
  <rect x="90" y="188" width="110" height="26" rx="7" fill="#3ea0e3"/>
  <rect x="250" y="60" width="320" height="290" rx="12" fill="#ffffff" stroke="#dfe4ea"/>
  <rect x="278" y="92" width="160" height="14" rx="7" fill="#0b0b0a"/>
  <rect x="278" y="126" width="264" height="8" rx="4" fill="#dfe4ea"/>
  <rect x="278" y="146" width="240" height="8" rx="4" fill="#ebeff3"/>
  <rect x="278" y="176" width="264" height="60" rx="10" fill="#e8f4fd"/>
  <rect x="278" y="254" width="264" height="8" rx="4" fill="#dfe4ea"/>
  <rect x="278" y="274" width="200" height="8" rx="4" fill="#ebeff3"/>
</svg>
SVG,

        'portrait' => <<<SVG
<svg viewBox="0 0 480 600" preserveAspectRatio="xMidYMid slice" role="img" aria-label="Portrait placeholder">
  <rect width="480" height="600" fill="#eef2f6"/>
  <circle cx="240" cy="230" r="110" fill="#dfe4ea"/>
  <path d="M120 600c0-90 54-160 120-160s120 70 120 160" fill="#dfe4ea"/>
  <rect x="40" y="40" width="400" height="520" rx="16" fill="none" stroke="#dfe4ea"/>
  <text x="240" y="540" text-anchor="middle" font-family="Georgia, serif" font-style="italic" font-size="22" fill="#6d7680">Photo placeholder</text>
</svg>
SVG,

        default => <<<SVG
<svg viewBox="0 0 640 400" preserveAspectRatio="xMidYMid slice" role="img" aria-label="Project illustration">
  <rect width="640" height="400" fill="#eef2f6"/>
  <rect x="80" y="70" width="480" height="260" rx="14" fill="#ffffff" stroke="#dfe4ea"/>
  <rect x="110" y="104" width="180" height="14" rx="7" fill="#0b0b0a"/>
  <rect x="110" y="136" width="300" height="9" rx="4.5" fill="#dfe4ea"/>
  <rect x="110" y="158" width="260" height="9" rx="4.5" fill="#ebeff3"/>
  <rect x="110" y="200" width="140" height="36" rx="18" fill="#3ea0e3"/>
  <rect x="110" y="260" width="420" height="40" rx="10" fill="#e8f4fd" stroke="#dfe4ea"/>
</svg>
SVG,
    };
}
