<?php

/**
 * Render del bloque viceunf/schedule-card.
 */
if (! defined('ABSPATH')) {
    exit;
}

$date          = $attributes['date'] ?? '';
$session_label = $attributes['sessionLabel'] ?? '';
$time          = $attributes['time'] ?? '';
$location      = $attributes['location'] ?? '';
$header_color  = $attributes['headerColor'] ?? '#ff4700';

$wrapper_attributes = get_block_wrapper_attributes(
    array('class' => 'viceunf-schedule-card')
);
?>
<div <?php echo $wrapper_attributes; ?>>

    <div class="viceunf-schedule-card__header" style="background-color: <?php echo esc_attr($header_color); ?>;">
        <div class="viceunf-schedule-card__rings" aria-hidden="true">
            <div class="viceunf-schedule-card__ring"></div>
            <div class="viceunf-schedule-card__ring"></div>
            <div class="viceunf-schedule-card__ring"></div>
            <div class="viceunf-schedule-card__ring"></div>
            <div class="viceunf-schedule-card__ring"></div>
        </div>
    </div>

    <div class="viceunf-schedule-card__body">
        <?php if ($date) : ?>
            <div class="viceunf-schedule-card__date">
                <?php echo esc_html($date); ?>
            </div>
        <?php endif; ?>

        <?php if ($session_label) : ?>
            <h4 class="viceunf-schedule-card__session">
                <?php echo esc_html($session_label); ?>
            </h4>
        <?php endif; ?>

        <?php if ($time) : ?>
            <p class="viceunf-schedule-card__time">
                <i class="fa-regular fa-clock" aria-hidden="true"></i>
                <?php echo esc_html($time); ?>
            </p>
        <?php endif; ?>

        <?php if ($location) : ?>
            <p class="viceunf-schedule-card__location">
                <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                <?php echo esc_html($location); ?>
            </p>
        <?php endif; ?>
    </div>

</div>