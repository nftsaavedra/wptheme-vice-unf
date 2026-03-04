<?php

/**
 * Render del bloque viceunf/carousel-entities
 *
 * @package ViceUnf
 */

if (!defined('ABSPATH')) {
    exit;
}

$section_title = $attributes['sectionTitle'] ?? 'Socios Académicos';
$post_type = $attributes['postTypeOrigin'] ?? 'socio';
$limit = isset($attributes['itemsLimit']) ? (int)$attributes['itemsLimit'] : -1;

$wrapper_attributes = get_block_wrapper_attributes(['class' => 'dt_clients dt_clients--one front-clients']);

// Validamos que el CPT existe
if (!post_type_exists($post_type)) {
    echo sprintf('<div %s><p style="padding: 20px; text-align: center; border: 1px dashed red;">El post type "%s" no está registrado o el plugin core está inactivo.</p></div>', $wrapper_attributes, esc_html($post_type));
    return;
}

$args = [
    'post_type'      => $post_type,
    'post_status'    => 'publish',
    'posts_per_page' => $limit,
    'orderby'        => 'menu_order date',
    'order'          => 'ASC'
];

$query = new WP_Query($args);

if (!$query->have_posts()) {
    echo sprintf('<div %s><p style="padding: 20px; text-align: center; border: 1px dashed orange;">No hay elementos para mostrar bajo el formato "%s".</p></div>', $wrapper_attributes, esc_html($post_type));
    wp_reset_postdata();
    return;
}
?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="dt-container">
        <!-- Encabezado Clásico UI -->
        <div class="dt-partner-header-classic">
            <div class="dt-partner-line"></div>
            <div class="dt-btn-prev-partner dt-partner-btn-classic"><i class="fas fa-angle-left"></i></div>
            <?php if (!empty($section_title)) : ?>
                <h5 class="dt-partner-title">
                    <?php echo esc_html($section_title); ?>
                </h5>
            <?php endif; ?>
            <div class="dt-btn-next-partner dt-partner-btn-classic"><i class="fas fa-angle-right"></i></div>
            <div class="dt-partner-line"></div>
        </div>

        <div class="swiper dt_swiper_carousel" data-swiper-options='<?php echo json_encode([
                                                                        "loop" => true,
                                                                        "autoplay" => ["delay" => 3000, "disableOnInteraction" => false, "pauseOnMouseEnter" => true],
                                                                        "navigation" => ["nextEl" => ".dt-partner-header-classic .dt-btn-next-partner", "prevEl" => ".dt-partner-header-classic .dt-btn-prev-partner"],
                                                                        "spaceBetween" => 30,
                                                                        "slidesPerView" => 2,
                                                                        "speed" => 1000,
                                                                        "breakpoints" => [
                                                                            "0" => ["spaceBetween" => 20, "slidesPerView" => 2],
                                                                            "575" => ["spaceBetween" => 30, "slidesPerView" => 3],
                                                                            "767" => ["spaceBetween" => 30, "slidesPerView" => 4],
                                                                            "991" => ["spaceBetween" => 30, "slidesPerView" => 5],
                                                                            "1199" => ["spaceBetween" => 30, "slidesPerView" => 5]
                                                                        ]
                                                                    ], JSON_UNESCAPED_SLASHES); ?>'>
            <div class="swiper-wrapper">
                <?php
                while ($query->have_posts()) : $query->the_post();
                    $post_id = get_the_ID();
                    // Soporta metakeys distintos dependiendo de quién cargó el dato
                    $link = get_post_meta($post_id, '_socio_url_key', true);
                    if (empty($link)) {
                        $link = get_post_meta($post_id, '_socio_url', true);
                    }

                    $link_target = !empty($link) ? ' target="_blank" rel="noopener noreferrer"' : '';
                    $link_href = !empty($link) ? esc_url($link) : '#';
                ?>
                    <div class="swiper-slide dt-partner-slide" data-wow-delay="100ms" data-wow-duration="1500ms">
                        <div class="dt-partner-box">
                            <figure class="image dt-m-0 dt-w-100">
                                <a href="<?php echo $link_href; ?>" <?php echo $link_target; ?> class="dt-d-block dt-w-100">
                                    <?php
                                    if (has_post_thumbnail()) {
                                        the_post_thumbnail('medium', ['alt' => get_the_title(), 'class' => 'img-fluid dt-partner-img']);
                                    } else {
                                        echo '<span style="color:#666; font-weight: bold; text-align: center; display: block; padding-top: 30px;">' . esc_html(get_the_title()) . '</span>';
                                    }
                                    ?>
                                </a>
                            </figure>
                        </div>
                    </div>
                <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </div>
</section>