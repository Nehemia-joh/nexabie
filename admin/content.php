<?php
declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

/* ---------------- Field helpers ---------------- */

function f_text(string $k, string $label, array $extra = []): array
{
    return array_merge(['k' => $k, 'label' => $label, 'type' => 'text'], $extra);
}

function f_area(string $k, string $label, int $rows = 4): array
{
    return ['k' => $k, 'label' => $label, 'type' => 'textarea', 'rows' => $rows];
}

/** Render one field as HTML. $name is the full input name (e.g. home[lead]). */
function render_field(string $name, array $f, mixed $value, int $depth = 0): string
{
    $type = $f['type'] ?? 'text';
    $id = 'f_' . preg_replace('/[^a-z0-9_]/', '', strtolower(str_replace(['[', ']', ' '], '_', $name))) . '_' . ($f['k'] ?? 'x');
    $label = e($f['label'] ?? '');

    switch ($type) {
        case 'textarea':
            return '<div class="fld"><label for="' . e($id) . '">' . $label . '</label>'
                . '<textarea id="' . e($id) . '" name="' . e($name) . '" rows="' . (int) ($f['rows'] ?? 4) . '">'
                . e((string) ($value ?? '')) . '</textarea></div>';

        case 'lines':
            $txt = is_array($value) ? implode("\n", $value) : (string) ($value ?? '');
            return '<div class="fld"><label for="' . e($id) . '">' . $label . ' <span class="hint">one per line</span></label>'
                . '<textarea id="' . e($id) . '" name="' . e($name) . '" rows="' . (int) ($f['rows'] ?? 4) . '">'
                . e($txt) . '</textarea></div>';

        case 'csv':
            $txt = is_array($value) ? implode(', ', $value) : (string) ($value ?? '');
            return '<div class="fld"><label for="' . e($id) . '">' . $label . ' <span class="hint">comma-separated</span></label>'
                . '<input id="' . e($id) . '" name="' . e($name) . '" type="text" value="' . e($txt) . '"></div>';

        case 'checkbox':
            return '<div class="fld check"><input type="hidden" name="' . e($name) . '" value="0">'
                . '<input id="' . e($id) . '" name="' . e($name) . '" type="checkbox" value="1"'
                . (!empty($value) ? ' checked' : '') . '>'
                . '<label for="' . e($id) . '">' . $label . '</label></div>';

        case 'image':
            return '<div class="fld"><label for="' . e($id) . '">' . $label . ' <span class="hint">upload in Media, paste path</span></label>'
                . '<div class="img-row">'
                . '<input id="' . e($id) . '" name="' . e($name) . '" type="text" placeholder="uploads/example.jpg" value="' . e((string) ($value ?? '')) . '">'
                . (!empty($value) ? '<img class="thumb" src="' . e($value) . '" alt="">' : '')
                . '</div></div>';

        case 'strings':
            $vals = is_array($value) ? $value : [];
            $rows = '';
            foreach ($vals as $i => $v) {
                $n = $name . '[' . $i . ']';
                $rows .= '<div class="list-row single"><input type="text" name="' . e($n) . '" value="' . e((string) $v) . '">'
                    . '<button type="button" class="btn-x" data-remove title="Remove">×</button></div>';
            }
            $ph = $name . '[__I' . $depth . '__]';
            return '<div class="fld list-block" data-list>'
                . '<label>' . $label . ' <span class="hint">list</span></label>'
                . '<div class="list-rows">' . $rows . '</div>'
                . '<template><div class="list-row single"><input type="text" name="' . e($ph) . '" value="">'
                . '<button type="button" class="btn-x" data-remove title="Remove">×</button></div></template>'
                . '<button type="button" class="btn-a small" data-add>+ Add item</button></div>';

        case 'rows':
            $rows = is_array($value) ? $value : [];
            $fields = $f['fields'] ?? [];
            $body = '';
            foreach ($rows as $i => $row) {
                $row = is_array($row) ? $row : [];
                $body .= '<div class="list-row">';
                foreach ($fields as $sub) {
                    $body .= render_field($name . '[' . $i . '][' . $sub['k'] . ']', $sub, $row[$sub['k']] ?? '', $depth + 1);
                }
                $body .= '<button type="button" class="btn-x" data-remove title="Remove row">×</button></div>';
            }
            $tplFields = '';
            foreach ($fields as $sub) {
                $tplFields .= render_field($name . '[__I' . $depth . '__][' . $sub['k'] . ']', $sub, $sub['default'] ?? '', $depth + 1);
            }
            return '<div class="fld list-block" data-list>'
                . '<label>' . $label . ' <span class="hint">list</span></label>'
                . '<div class="list-rows">' . $body . '</div>'
                . '<template><div class="list-row">' . $tplFields
                . '<button type="button" class="btn-x" data-remove title="Remove row">×</button></div></template>'
                . '<button type="button" class="btn-a small" data-add>+ Add row</button></div>';

        case 'dict':
            $inner = '';
            foreach ($f['fields'] ?? [] as $sub) {
                $inner .= render_field($name . '[' . $sub['k'] . ']', $sub, is_array($value) ? ($value[$sub['k']] ?? '') : '', $depth);
            }
            return '<fieldset class="dict"><legend>' . $label . '</legend>' . $inner . '</fieldset>';

        default:
            return '<div class="fld"><label for="' . e($id) . '">' . $label . '</label>'
                . '<input id="' . e($id) . '" name="' . e($name) . '" type="text" value="' . e((string) ($value ?? '')) . '"></div>';
    }
}

/** Coerce posted strings into arrays/booleans per schema. */
function coerce_values(array $fields, array &$data): void
{
    foreach ($fields as $f) {
        $k = $f['k'];
        $type = $f['type'] ?? 'text';
        if (!array_key_exists($k, $data)) {
            continue;
        }
        if ($type === 'rows') {
            if (is_array($data[$k])) {
                foreach ($data[$k] as &$row) {
                    if (is_array($row)) {
                        coerce_values($f['fields'] ?? [], $row);
                    }
                }
                unset($row);
            }
        } elseif ($type === 'lines' || $type === 'csv') {
            if (is_string($data[$k])) {
                $sep = $type === 'lines' ? "\n" : ',';
                $data[$k] = array_values(array_filter(array_map('trim', explode($sep, $data[$k])), static fn ($v) => $v !== ''));
            }
        } elseif ($type === 'strings') {
            if (is_array($data[$k])) {
                $data[$k] = array_values(array_map(static fn ($v) => trim((string) $v), $data[$k]));
                $data[$k] = array_values(array_filter($data[$k], static fn ($v) => $v !== ''));
            }
        } elseif ($type === 'checkbox') {
            $data[$k] = ($data[$k] ?? '0') === '1' || $data[$k] === 1 || $data[$k] === true;
        }
    }
}

/* ---------------- Section schema ---------------- */

$sections = [
    'brand' => [
        'label' => 'Brand & contact',
        'fields' => [
            f_text('name', 'Brand name'),
            f_text('mark', 'Logo letter'),
            f_area('tagline', 'Footer tagline', 2),
            f_text('location', 'Footer location line'),
            f_text('email', 'Contact email'),
            f_text('linkedin', 'LinkedIn URL'),
            f_text('github', 'GitHub URL'),
            f_text('twitter', 'X / Twitter URL'),
        ],
    ],
    'home' => [
        'label' => 'Homepage',
        'fields' => [
            f_text('kicker', 'Hero kicker line'),
            f_text('hero_before', 'Hero — text before rotating words'),
            ['k' => 'hero_words', 'label' => 'Hero — rotating words', 'type' => 'strings'],
            f_text('hero_after', 'Hero — text after rotating words'),
            f_area('lead', 'Hero lead paragraph'),
            f_text('cta_primary', 'Primary button'),
            f_text('cta_secondary', 'Secondary button'),
            f_text('note1', 'Hero note 1'),
            f_text('note2', 'Hero note 2'),
            ['k' => 'hero_media', 'label' => 'Hero image cards', 'type' => 'rows', 'fields' => [
                f_text('image', 'Image path'),
                f_text('title', 'Caption (title · subtitle)'),
                f_text('type', 'Caption tag'),
            ]],
            ['k' => 'stats', 'label' => 'Stats strip', 'type' => 'rows', 'fields' => [
                f_text('value', 'Value'), f_text('label', 'Label'),
            ]],
            f_text('work_eyebrow', 'Work section label'),
            f_text('work_link', 'Work link text'),
            f_text('work_title_a', 'Work title — line 1'),
            f_text('work_title_b', 'Work title — line 2 (italic)'),
            f_area('work_lead', 'Work lead paragraph'),
            f_text('featured', 'Featured project ids', ['type' => 'csv']),
            f_text('services_eyebrow', 'Services section label'),
            f_text('services_link', 'Services link text'),
            f_text('services_title_a', 'Services title — line 1'),
            f_text('services_title_b', 'Services title — line 2 (italic)'),
            f_text('quote_eyebrow', 'Testimonial section label'),
            f_area('quote', 'Testimonial quote', 3),
            f_text('quote_author', 'Quote — name'),
            f_text('quote_role', 'Quote — role'),
            f_text('aside_title', 'Aside card title'),
            f_area('aside_text', 'Aside card text', 3),
            f_text('aside_link', 'Aside link text'),
            f_text('cta_a', 'CTA heading — part 1'),
            f_text('cta_b', 'CTA heading — part 2 (italic)'),
        ],
    ],
    'work' => [
        'label' => 'Work / projects',
        'fields' => [
            f_text('title_a', 'Page title — line 1'),
            f_text('title_b', 'Page title — line 2 (italic)'),
            f_area('lead', 'Page lead'),
            ['k' => 'filters', 'label' => 'Filter buttons', 'type' => 'rows', 'fields' => [
                f_text('value', 'Filter value'), f_text('label', 'Filter label'),
            ]],
            ['k' => 'projects', 'label' => 'Projects', 'type' => 'rows', 'fields' => [
                f_text('id', 'ID (anchor, e.g. receiptly)'),
                f_text('badge', 'Badge (e.g. Our product)'),
                f_text('status', 'Status text (e.g. Live)'),
                f_text('status_class', 'Status style: live or soon'),
                f_text('category', 'Categories (client product team)'),
                f_text('kicker', 'Kicker line'),
                f_area('title', 'Headline', 2),
                f_text('metric', 'Metric line'),
                f_text('metric_link', 'Right-side link text'),
                f_text('image', 'Cover image', ['type' => 'image']),
                f_text('href', 'Link URL'),
                ['k' => 'wide', 'label' => 'Wide card (full width)', 'type' => 'checkbox'],
            ]],
            f_text('next_kicker', 'Closing card — number'),
            f_text('next_title', 'Closing card — title'),
            f_text('next_text', 'Closing card — text'),
            f_text('next_cta', 'Closing card — button'),
        ],
    ],
    'services' => [
        'label' => 'Services page',
        'fields' => [
            f_text('title_a', 'Page title — line 1'),
            f_text('title_b', 'Page title — line 2 (italic)'),
            f_area('lead', 'Page lead'),
            f_text('engagements_eyebrow', 'Engagements label'),
            f_text('deliverables_label', '"What you get" label'),
            ['k' => 'engagements', 'label' => 'Engagements', 'type' => 'rows', 'fields' => [
                f_text('num', 'Number (01)'),
                f_text('name', 'Name'),
                f_area('desc', 'Description', 2),
                f_text('duration', 'Duration'),
                ['k' => 'points', 'label' => 'Deliverables', 'type' => 'lines', 'rows' => 4],
                f_text('cta', 'Link text'),
            ]],
            f_text('process_eyebrow', 'Process label'),
            f_text('process_a', 'Process title — line 1'),
            f_text('process_b', 'Process title — line 2 (italic)'),
            ['k' => 'steps', 'label' => 'Process steps', 'type' => 'rows', 'fields' => [
                f_text('n', 'Number'), f_text('title', 'Title'), f_area('text', 'Text', 2),
            ]],
            f_text('faq_eyebrow', 'FAQ label'),
            f_text('faq_a', 'FAQ title — line 1'),
            f_text('faq_b', 'FAQ title — line 2 (italic)'),
            ['k' => 'faqs', 'label' => 'FAQ items', 'type' => 'rows', 'fields' => [
                f_text('q', 'Question'), f_area('a', 'Answer', 3),
            ]],
            f_text('cta_a', 'CTA — part 1'),
            f_text('cta_b', 'CTA — part 2 (italic)'),
            f_text('cta_button', 'CTA button'),
        ],
    ],
    'about' => [
        'label' => 'About page',
        'fields' => [
            f_text('title_a', 'Page title — line 1'),
            f_text('title_b', 'Page title — line 2 (italic)'),
            f_area('lead', 'Page lead'),
            f_text('cta_primary', 'Primary button'),
            f_text('cta_secondary', 'Secondary button'),
            f_text('portrait_image', 'Portrait image', ['type' => 'image']),
            f_text('portrait_alt', 'Portrait alt text'),
            f_text('why_eyebrow', 'Section 01 label'),
            f_text('why_a', 'Section 01 title — line 1'),
            f_text('why_b', 'Section 01 title — line 2 (italic)'),
            f_area('why_lead', 'Section 01 lead'),
            f_text('how_eyebrow', 'Section 02 label'),
            f_text('how_a', 'Section 02 title — line 1'),
            f_text('how_b', 'Section 02 title — line 2 (italic)'),
            ['k' => 'values', 'label' => 'How we work cards', 'type' => 'rows', 'fields' => [
                f_text('title', 'Title'), f_area('text', 'Text', 2),
            ]],
            f_text('exp_eyebrow', 'Section 03 label'),
            f_text('exp_a', 'Experience title — line 1'),
            f_text('exp_b', 'Experience title — line 2 (italic)'),
            ['k' => 'jobs', 'label' => 'Experience entries', 'type' => 'rows', 'fields' => [
                f_text('when', 'Years'), f_text('title', 'Role'), f_text('org', 'Organisation'), f_area('text', 'Description', 3),
            ]],
            ['k' => 'skills', 'label' => 'Skill groups', 'type' => 'rows', 'fields' => [
                f_text('title', 'Group title'),
                ['k' => 'items', 'label' => 'Skills', 'type' => 'lines', 'rows' => 4],
            ]],
            f_text('cta_a', 'CTA — part 1'),
            f_text('cta_b', 'CTA — part 2 (italic)'),
        ],
    ],
    'contact' => [
        'label' => 'Contact page',
        'fields' => [
            f_text('title_a', 'Page title — line 1'),
            f_text('title_b', 'Page title — line 2 (italic)'),
            f_area('lead', 'Page lead'),
            f_text('note1', 'Note 1'),
            f_text('note2', 'Note 2'),
            f_text('next_title', 'What happens next — title'),
            ['k' => 'next_steps', 'label' => 'What happens next — steps', 'type' => 'strings'],
            ['k' => 'calls', 'label' => 'Call options', 'type' => 'rows', 'fields' => [
                f_text('label', 'Label (Call 01)'), f_text('name', 'Name'), f_area('desc', 'Description', 2),
                f_text('dur', 'Duration'), f_text('subject', 'Email subject'),
            ]],
            f_text('form_title', 'Form title'),
            f_text('form_hint', 'Form hint'),
            ['k' => 'services_select', 'label' => '"What do you need?" options', 'type' => 'rows', 'fields' => [
                f_text('value', 'Value'), f_text('label', 'Label'),
            ]],
            ['k' => 'labels', 'label' => 'Form labels', 'type' => 'dict', 'fields' => [
                f_text('name', 'Name label'), f_text('email', 'Email label'), f_text('service', 'Service label'),
                f_text('message', 'Message label'), f_text('submit', 'Submit button'),
            ]],
            ['k' => 'placeholders', 'label' => 'Form placeholders', 'type' => 'dict', 'fields' => [
                f_text('name', 'Name placeholder'), f_text('email', 'Email placeholder'), f_text('message', 'Message placeholder'),
            ]],
            f_text('privacy_note', 'Privacy note (before link)'),
            f_area('success', 'Success message', 2),
            f_area('error', 'Error message', 2),
        ],
    ],
    'privacy' => [
        'label' => 'Privacy page',
        'fields' => [
            f_text('title_a', 'Page title — line 1'),
            f_text('title_b', 'Page title — line 2 (italic)'),
            f_area('lead', 'Page lead'),
            ['k' => 'sections', 'label' => 'Sections', 'type' => 'rows', 'fields' => [
                f_text('q', 'Question'), f_area('a', 'Answer', 4),
            ]],
        ],
    ],
];

/* ---------------- Save ---------------- */

$section = (string) ($_GET['section'] ?? $_POST['section'] ?? 'brand');
if (!isset($sections[$section])) {
    $section = 'brand';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $content = content();
    $data = $_POST[$section] ?? [];
    if (!is_array($data)) {
        $data = [];
    }
    coerce_values($sections[$section]['fields'], $data);
    $content[$section] = $data;
    save_json('content.json', $content);
    header('Location: content.php?section=' . rawurlencode($section) . '&saved=1');
    exit;
}

$content = content();
$value = $content[$section] ?? [];

admin_head('Content — ' . $sections[$section]['label'], 'content');
?>

<div class="tabs">
  <?php foreach ($sections as $key => $s): ?>
    <a class="tab<?= $key === $section ? ' on' : '' ?>" href="content.php?section=<?= e($key) ?>"><?= e($s['label']) ?></a>
  <?php endforeach; ?>
</div>

<form class="form" method="post" action="content.php?section=<?= e($section) ?>">
  <?= csrf_field() ?>
  <input type="hidden" name="section" value="<?= e($section) ?>">

  <?php foreach ($sections[$section]['fields'] as $f): ?>
    <?= render_field($section . '[' . $f['k'] . ']', $f, is_array($value) ? ($value[$f['k']] ?? '') : '', 0) ?>
  <?php endforeach; ?>

  <div class="form-actions">
    <button class="btn-a primary" type="submit">Save changes</button>
    <a class="btn-a" href="../index.php" target="_blank" rel="noopener">View site ↗</a>
  </div>
</form>

<?php admin_foot(); ?>
