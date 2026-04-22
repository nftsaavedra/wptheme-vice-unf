<?php

/**
 * Render del bloque vpinunf/program-modules.
 */
if (! defined('ABSPATH')) {
    exit;
}

$columns        = isset($attributes['columns']) ? max(2, min(4, (int) $attributes['columns'])) : 3;
$progress_color = $attributes['progressColor'] ?? '#ff4700';
$section_title  = $attributes['sectionTitle'] ?? '';

$css_vars = sprintf(
    '--vpinunf-progress-color: %s; --vpinunf-mod-cols: %d;',
    esc_attr($progress_color),
    $columns
);

$wrapper_attributes = get_block_wrapper_attributes(
    array('class' => 'vpinunf-program-modules')
);
?>
<section <?php echo $wrapper_attributes; ?> style="<?php echo esc_attr($css_vars); ?>">
    <div class="dt-container">
        <?php if ($section_title) : ?>
            <h2 class="vpinunf-program-modules__title">
                <?php echo wp_kses_post($section_title); ?>
            </h2>
        <?php endif; ?>
        <div class="vpinunf-program-modules__grid">
            <?php echo $content; ?>
        </div>
    </div>
</section>