<?php

/**
 * Render del bloque viceunf/module-card.
 * Genera el SVG semicircular de progreso en PHP para evitar dependencias JS en frontend.
 */
if (! defined('ABSPATH')) {
    exit;
}

$label           = $attributes['label'] ?? '';
$icon            = preg_replace('/[^a-zA-Z0-9\s\-]/', '', $attributes['icon'] ?? 'fa-solid fa-book');
$progress        = isset($attributes['progressPercent']) ? max(0, min(100, (int) $attributes['progressPercent'])) : 75;
$bullet_points   = $attributes['bulletPoints'] ?? [];
$card_color      = $attributes['cardColor'] ?? '#1e2a4a';

// Cálculo SVG para el arco semicircular.
$radius     = 40;
$circumference = M_PI * $radius;
$offset     = $circumference - ($circumference * $progress / 100);

$wrapper_attributes = get_block_wrapper_attributes(
    array('class' => 'viceunf-module-card')
);
?>
<div <?php echo $wrapper_attributes; ?> style="background-color: <?php echo esc_attr($card_color); ?>;">

    <?php if ($label) : ?>
        <p class="viceunf-module-card__label"><?php echo esc_html($label); ?></p>
    <?php endif; ?>

    <div class="viceunf-module-card__progress" aria-hidden="true">
        <svg viewBox="0 0 100 56" class="viceunf-module-card__arc" aria-hidden="true">
            <path
                d="M 10,50 A 40,40 0 0,1 90,50"
                fill="none"
                stroke="rgba(255,255,255,0.15)"
                stroke-width="8"
                stroke-linecap="round" />
            <path
                d="M 10,50 A 40,40 0 0,1 90,50"
                fill="none"
                stroke="var(--viceunf-progress-color, #ff4700)"
                stroke-width="8"
                stroke-linecap="round"
                stroke-dasharray="<?php echo esc_attr($circumference); ?>"
                stroke-dashoffset="<?php echo esc_attr($offset); ?>" />
        </svg>
        <div class="viceunf-module-card__progress-icon">
            <i class="<?php echo esc_attr($icon); ?>" aria-hidden="true"></i>
        </div>
    </div>

    <div class="viceunf-module-card__body">
        <?php if ($bullet_points) : ?>
            <ul class="viceunf-module-card__bullets" aria-label="<?php esc_attr_e('Puntos clave del módulo', 'viceunf'); ?>">
                <?php foreach ($bullet_points as $point) : ?>
                    <li><?php echo esc_html($point); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

</div>