<?php

/**
 * Render del bloque viceunf/visual-timeline.
 */
if (! defined('ABSPATH')) {
    exit;
}

$line_color    = $attributes['lineColor'] ?? '#ff4700';
$section_title = $attributes['sectionTitle'] ?? '';
$css_vars      = '--viceunf-timeline-line: ' . esc_attr($line_color) . ';';

$wrapper_attributes = get_block_wrapper_attributes(
    array('class' => 'viceunf-visual-timeline')
);
?>
<section <?php echo $wrapper_attributes; ?> style="<?php echo esc_attr($css_vars); ?>">
    <div class="dt-container">
        <?php if ($section_title) : ?>
            <h2 class="viceunf-visual-timeline__title">
                <?php echo esc_html($section_title); ?>
            </h2>
        <?php endif; ?>
        <div class="viceunf-visual-timeline__track">
            <div class="viceunf-visual-timeline__line" aria-hidden="true"
                style="background-color: <?php echo esc_attr($line_color); ?>;"></div>
            <?php echo $content; ?>
        </div>
    </div>
</section>