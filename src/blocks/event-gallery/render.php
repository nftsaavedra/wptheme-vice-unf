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
    'class' => 'vpinunf-event-gallery vpinunf-event-gallery--' . $unique_id
]);

// Configuración Swiper Principal
$main_options = [
    'loop' => true,
    'effect' => 'fade',
    'fadeEffect' => ['crossFade' => true],
    'speed' => 900,
    'autoplay' => ['delay' => 5000, 'disableOnInteraction' => false],
    'keyboard' => ['enabled' => true],
    'navigation' => [
        'nextEl' => '.vpinunf-event-gallery__next',
        'prevEl' => '.vpinunf-event-gallery__prev',
    ],
    'pagination' => [
        'el' => '.vpinunf-event-gallery__pagination',
        'clickable' => true,
    ],
    'thumbs' => ['swiper' => '.' . $unique_id . '-thumbs'],
    'allowTouchMove' => true,
];

// Configuración Swiper Miniaturas (Debajo)
$thumbs_options = [
    'spaceBetween' => 12,
    'slidesPerView' => 4,
    'watchSlidesProgress' => true,
    'freeMode' => true,
    'breakpoints' => [
        '320' => [
            'slidesPerView' => 3,
            'spaceBetween' => 8,
        ],
        '768' => [
            'slidesPerView' => 4,
            'spaceBetween' => 12,
        ],
        '1024' => [
            'slidesPerView' => 5,
            'spaceBetween' => 16,
        ]
    ]
];
?>

<div <?php echo $wrapper_attributes; ?>>
    <div class="vpinunf-event-gallery__container">
        <!-- Main Swiper -->
        <div class="swiper vpinunf-event-gallery__main dt_swiper_carousel" data-swiper-options='<?php echo json_encode($main_options); ?>'>
            <div class="swiper-wrapper">
                <?php foreach ($images as $img) : ?>
                    <div class="swiper-slide">
                        <div class="vpinunf-event-gallery__main-item">
                            <?php 
                            if (!empty($img['id'])) {
                                echo wp_get_attachment_image($img['id'], 'large', false, ['class' => 'vpinunf-event-gallery__img']);
                            } else {
                                echo '<img src="' . esc_url($img['url']) . '" alt="' . esc_attr($img['alt']) . '" loading="lazy" class="vpinunf-event-gallery__img" />';
                            }
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Basic Navigation for stability -->
            <button type="button" class="vpinunf-event-gallery__nav vpinunf-event-gallery__prev" aria-label="<?php esc_attr_e('Imagen anterior', 'vpinunf'); ?>">
                <i class="fas fa-chevron-left" aria-hidden="true"></i>
            </button>
            <button type="button" class="vpinunf-event-gallery__nav vpinunf-event-gallery__next" aria-label="<?php esc_attr_e('Siguiente imagen', 'vpinunf'); ?>">
                <i class="fas fa-chevron-right" aria-hidden="true"></i>
            </button>
        </div>

        <!-- Thumbs Swiper (Now Below) -->
        <div class="swiper vpinunf-event-gallery__thumbs <?php echo $unique_id; ?>-thumbs dt_swiper_carousel" data-swiper-options='<?php echo json_encode($thumbs_options); ?>'>
            <div class="swiper-wrapper">
                <?php foreach ($images as $img) : ?>
                    <div class="swiper-slide">
                        <div class="vpinunf-event-gallery__thumb-item">
                            <?php 
                            if (!empty($img['id'])) {
                                echo wp_get_attachment_image($img['id'], 'medium', false, ['class' => 'vpinunf-event-gallery__thumb-img']);
                            } else {
                                echo '<img src="' . esc_url($img['url']) . '" alt="' . esc_attr($img['alt']) . '" loading="lazy" class="vpinunf-event-gallery__thumb-img" />';
                            }
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
