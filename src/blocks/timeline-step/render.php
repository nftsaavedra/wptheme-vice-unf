<?php

/**
 * Render del bloque viceunf/timeline-step.
 * Los pasos se alternan izquierda/derecha via CSS :nth-child selector.
 */
if (! defined('ABSPATH')) {
    exit;
}

$step_number  = isset($attributes['stepNumber']) ? (int) $attributes['stepNumber'] : 1;
$title        = $attributes['title'] ?? '';
$description  = $attributes['description'] ?? '';
$icon         = preg_replace('/[^a-zA-Z0-9\s\-]/', '', $attributes['icon'] ?? '');
$accent_color = $attributes['accentColor'] ?? '#ff4700';

$css_vars = '--viceunf-accent: ' . esc_attr($accent_color) . ';';

$wrapper_attributes = get_block_wrapper_attributes(
    array('class' => 'viceunf-timeline-step')
);
?>
<div <?php echo $wrapper_attributes; ?> style="<?php echo esc_attr($css_vars); ?>">

    <div
        class="viceunf-timeline-step__bubble"
        style="background-color: <?php echo esc_attr($accent_color); ?>;"
        aria-hidden="true">
        <?php if ($icon) : ?>
            <i class="<?php echo esc_attr($icon); ?>"></i>
        <?php else : ?>
            <span><?php echo esc_html($step_number); ?></span>
        <?php endif; ?>
    </div>

    <div
        class="viceunf-timeline-step__card"
        style="border-left-color: <?php echo esc_attr($accent_color); ?>;">
        <?php if ($title) : ?>
            <h3 class="viceunf-timeline-step__title">
                <?php echo esc_html($title); ?>
            </h3>
        <?php endif; ?>
        <?php if ($description) : ?>
            <p class="viceunf-timeline-step__desc">
                <?php echo wp_kses_post($description); ?>
            </p>
        <?php endif; ?>
    </div>

</div>