<?php
declare(strict_types=1);

if (!defined('NEXABIE_ROUTER')) {
    http_response_code(404);
    exit('Not found');
}

require __DIR__ . '/bootstrap.php';

$samples = [
    'Hero headline' => 'We bring your web app into focus.',
    'Section title' => 'Products and client work, from first message to launch.',
    'Stats'         => '40+ projects shipped · 1,200+ users · <24h reply',
    'Footer tagline' => 'Ideas, built into software.',
];

admin_head('Font test — SNEAKER', 'font');
?>

<p class="muted">SNEAKER by MJType — the display font behind every headline on the site. Type anything to try it.</p>

<div class="tester-controls">
  <div class="fld tester-text">
    <label for="t-text">Sample text</label>
    <textarea id="t-text" rows="2">We bring your web app into focus.</textarea>
  </div>
  <div class="fld tester-size">
    <label for="t-size">Size — <output id="t-size-out" for="t-size">64</output> px</label>
    <input id="t-size" type="range" min="20" max="140" value="64">
  </div>
  <div class="fld tester-bg">
    <label id="t-bg-label">Background</label>
    <div class="seg" role="group" aria-labelledby="t-bg-label">
      <button type="button" class="seg-btn on" data-bg="paper">Cloud white</button>
      <button type="button" class="seg-btn" data-bg="ink">Black</button>
      <button type="button" class="seg-btn" data-bg="blue">Sky blue</button>
      <button type="button" class="seg-btn" data-bg="red">Scarlet</button>
    </div>
  </div>
</div>

<div class="tester-samples">
  <span class="muted">Jump to:</span>
  <?php foreach ($samples as $label => $text): ?>
  <button type="button" class="btn-a small" data-sample="<?= e($text) ?>"><?= e($label) ?></button>
  <?php endforeach; ?>
</div>

<div class="type-preview" id="t-preview" data-bg="paper">
  <div class="type-line" id="t-out">We bring your web app into focus.</div>
</div>

<p class="muted">The same line in Instrument Sans, the site’s text font:</p>
<div class="type-plain" id="t-plain">We bring your web app into focus.</div>

<script>
(function () {
  var text = document.getElementById("t-text");
  var out = document.getElementById("t-out");
  var plain = document.getElementById("t-plain");
  var size = document.getElementById("t-size");
  var sizeOut = document.getElementById("t-size-out");
  var preview = document.getElementById("t-preview");

  function sync() {
    var v = text.value || " ";
    out.textContent = v;
    plain.textContent = v;
    out.style.fontSize = size.value + "px";
    sizeOut.textContent = size.value;
  }

  text.addEventListener("input", sync);
  size.addEventListener("input", sync);

  document.querySelectorAll(".seg-btn").forEach(function (btn) {
    btn.addEventListener("click", function () {
      document.querySelectorAll(".seg-btn").forEach(function (b) { b.classList.remove("on"); });
      btn.classList.add("on");
      preview.setAttribute("data-bg", btn.getAttribute("data-bg"));
    });
  });

  document.querySelectorAll("[data-sample]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      text.value = btn.getAttribute("data-sample");
      sync();
      text.focus();
    });
  });

  sync();
})();
</script>

<?php admin_foot();
