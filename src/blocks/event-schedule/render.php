<?php

/**
 * Render del bloque viceunf/event-schedule.
 */
if (! defined('ABSPATH')) {
    exit;
}

$columns        = isset($attributes['columns']) ? max(2, min(4, (int) $attributes['columns'])) : 3;
$section_title  = $attributes['sectionTitle'] ?? '';
$css_vars       = '--viceunf-sched-cols: ' . $columns . ';';

$wrapper_attributes = get_block_wrapper_attributes(
    array('class' => 'viceunf-event-schedule')
);
?>
<section <?php echo $wrapper_attributes; ?> style="<?php echo esc_attr($css_vars); ?>">
    <div class="dt-container">
        <?php if ($section_title) : ?>
            <h2 class="viceunf-event-schedule__title">
                <?php echo esc_html($section_title); ?>
            </h2>
        <?php endif; ?>
        <div class="viceunf-event-schedule__grid">
            <?php echo $content; ?>
        </div>
    </div>
</section>