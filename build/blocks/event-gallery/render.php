<?php
/**
 * Render for Event Gallery Block (Swiper.js Version)
 */

if (! defined('ABSPATH')) {
    exit;
}

$images = isset($attributes['images']) ? $attributes['images'] : [];

if (empty($images)) {
    return;
}

$unique_id = 'swiper-' . wp_generate_password(6, false);
$wrapper_attributes = get_block_wrapper_attributes([
    'class' => 'viceunf-event-gallery viceunf-event-gallery--' . $unique_id
]);

// Configuración Swiper Principal
$main_options = [
    'loop' => true,
    'effect' => 'fade',
    'fadeEffect' => ['crossFade' => true],
    'speed' => 900,
    'autoplay' => ['delay' => 5000, 'disableOnInteraction' => false],
    'navigation' => [
        'nextEl' => '.viceunf-event-gallery__next',
        'prevEl' => '.viceunf-event-gallery__prev',
    ],
    'pagination' => [
        'el' => '.viceunf-event-gallery__pagination',
        'clickable' => true,
        'dynamicBullets' => true,
    ],
    'allowTouchMove' => true,
];
?>

<div <?php echo $wrapper_attributes; ?>>
    <div class="viceunf-event-gallery__container">
        <!-- Main Swiper -->
        <div class="swiper viceunf-event-gallery__main dt_swiper_carousel" data-swiper-options='<?php echo json_encode($main_options); ?>'>
            <div class="swiper-wrapper">
                <?php foreach ($images as $img) : ?>
                    <div class="swiper-slide">
                        <div class="viceunf-event-gallery__main-item">
                            <?php 
                            if (!empty($img['id'])) {
                                echo wp_get_attachment_image($img['id'], 'large', false, ['class' => 'viceunf-event-gallery__img']);
                            } else {
                                echo '<img src="' . esc_url($img['url']) . '" alt="' . esc_attr($img['alt']) . '" class="viceunf-event-gallery__img" />';
                            }
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Modern Navigation Controls (Inside Container) -->
            <button type="button" class="viceunf-event-gallery__nav viceunf-event-gallery__prev" aria-label="<?php esc_attr_e('Anterior', 'viceunf'); ?>">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button type="button" class="viceunf-event-gallery__nav viceunf-event-gallery__next" aria-label="<?php esc_attr_e('Siguiente', 'viceunf'); ?>">
                <i class="fas fa-chevron-right"></i>
            </button>

            <!-- Modern Pagination Dots -->
            <div class="swiper-pagination viceunf-event-gallery__pagination"></div>
        </div>
    </div>
</div>
