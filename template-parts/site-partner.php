<section id="dt_clients" class="dt_clients dt_clients--one front-clients">
    <div class="dt-container">
        <?php // Título dinámico desde el personalizador 
        ?>
        <h5 class="title"><?php echo esc_html(get_theme_mod('viceunf_socios_titulo', 'Socios Académicos')); ?></h5>

        <?php
        // Obtener todos los posts del tipo 'Socio' mediante el repositorio/servicio
        $socios_query = class_exists('\ViceUnf\Core\Service\SocioService') ? (new \ViceUnf\Core\Service\SocioService())->get_all_socios() : new WP_Query();

        if ($socios_query->have_posts()) :
        ?>
            <div class="swiper dt_swiper_carousel" data-swiper-options='<?php echo json_encode([
                                                                            "loop" => true,
                                                                            "autoplay" => ["delay" => 5000, "disableOnInteraction" => false, "pauseOnMouseEnter" => true],
                                                                            "navigation" => ["nextEl" => "#dt_clients .swiper-button-next", "prevEl" => "#dt_clients .swiper-button-prev"],
                                                                            "spaceBetween" => 30,
                                                                            "slidesPerView" => 2,
                                                                            "speed" => 700,
                                                                            "breakpoints" => [
                                                                                "0" => ["spaceBetween" => 30, "slidesPerView" => 2],
                                                                                "575" => ["spaceBetween" => 30, "slidesPerView" => 3],
                                                                                "767" => ["spaceBetween" => 50, "slidesPerView" => 4],
                                                                                "991" => ["spaceBetween" => 40, "slidesPerView" => 5],
                                                                                "1199" => ["spaceBetween" => 80, "slidesPerView" => 5]
                                                                            ]
                                                                        ], JSON_UNESCAPED_SLASHES); ?>'>
                <div class="swiper-wrapper">
                    <?php
                    // El Loop de WordPress
                    while ($socios_query->have_posts()) : $socios_query->the_post();
                        $socio_url = get_post_meta(get_the_ID(), '_socio_url_key', true);
                        $link_target = !empty($socio_url) ? ' target="_blank" rel="noopener noreferrer"' : '';
                        $link_href = !empty($socio_url) ? esc_url($socio_url) : '#';
                    ?>
                        <div class="swiper-slide dt_clients_logo" data-wow-delay="100ms" data-wow-duration="1500ms">
                            <figure class="image">
                                <a href="<?php echo $link_href; ?>" <?php echo $link_target; ?>>
                                    <?php
                                    if (has_post_thumbnail()) {
                                        // Muestra el logo (imagen destacada)
                                        the_post_thumbnail('medium', ['alt' => get_the_title()]);
                                    }
                                    ?>
                                </a>
                            </figure>
                        </div>
                    <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
                <!-- Swiper Navigation -->
                <div class="swiper-button-prev"><i class="fas fa-angle-left"></i></div>
                <div class="swiper-button-next"><i class="fas fa-angle-right"></i></div>
            </div>
        <?php endif; ?>
    </div>
</section>