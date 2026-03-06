<?php

/**
 * Plantilla: Socios / Partners — Página de Inicio.
 *
 * @package ViceUnf
 */

$options    = get_option('viceunf_theme_options', []);
$is_enabled = !empty($options['socios_section_enabled']);
if (!$is_enabled) {
    return;
}

$socios_titulo    = $options['viceunf_socios_titulo'] ?? 'Socios Académicos';
$socios_post_type = !empty($options['socios_post_type']) ? sanitize_key($options['socios_post_type']) : 'socio';

$socios_query = new WP_Query([
    'post_type'      => $socios_post_type,
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    'no_found_rows'  => true,
]);

if (!$socios_query->have_posts()) {
    return;
}
?>
<section id="dt_clients" class="dt_clients dt_clients--one front-clients">
    <div class="dt-container">
        <!-- Encabezado Clásico UI -->
        <div class="dt-partner-header-classic">
            <div class="dt-partner-line"></div>
            <div class="dt-btn-prev-partner dt-partner-btn-classic"><i class="fas fa-angle-left"></i></div>
            <h5 class="dt-partner-title">
                <?php echo esc_html($socios_titulo); ?>
            </h5>
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
                while ($socios_query->have_posts()) : $socios_query->the_post();
                    $socio_url = get_post_meta(get_the_ID(), '_socio_url_key', true);
                    $link_target = !empty($socio_url) ? ' target="_blank" rel="noopener noreferrer"' : '';
                    $link_href = !empty($socio_url) ? esc_url($socio_url) : '#';
                ?>
                    <div class="swiper-slide dt-partner-slide" data-wow-delay="100ms" data-wow-duration="1500ms">
                        <div class="dt-partner-box">
                            <figure class="image dt-m-0 dt-w-100">
                                <a href="<?php echo $link_href; ?>" <?php echo $link_target; ?> class="dt-d-block dt-w-100">
                                    <?php
                                    if (has_post_thumbnail()) {
                                        the_post_thumbnail('medium', ['alt' => get_the_title(), 'class' => 'img-fluid dt-partner-img']);
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