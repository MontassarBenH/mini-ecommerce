<?php
/**
 * View: Produkt-Badge (Bestseller / Neu / Sale)
 *
 * Erwartete Variablen:
 * @var string $type   // 'bestseller', 'new', 'sale'
 * @var string $label  // sichtbarer Text, z.B. '🏆 Bestseller'
 */

$type  = isset($type) ? $type : 'default';
$label = isset($label) ? $label : '';
$badgeClass = 'product-badge--' . preg_replace('/[^a-z0-9_-]/i', '', $type);
?>
<?php if (!empty($label)): ?>
    <div class="product-badge <?= htmlspecialchars($badgeClass, ENT_QUOTES, 'UTF-8') ?>">
        <span class="product-badge__label">
            <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
        </span>
    </div>
<?php endif; ?>
