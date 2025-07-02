<section id="dt_clients" class="dt_clients dt_clients--one front-clients">
    <div class="dt-container">

        <?php // Título dinámico desde el personalizador ?>
        <h5 class="title"><?php echo esc_html( get_theme_mod('socios_titulo', 'Socios Académicos') ); ?></h5>
        
        <?php
        // WP_Query para obtener todos los posts del tipo 'Socio'
        $args = array(
            'post_type'      => 'socio',
            'posts_per_page' => -1, // Mostrar todos los socios
            'orderby'        => 'menu_order', // Permite ordenar manualmente (con un plugin)
            'order'          => 'ASC',
        );
        $socios_query = new WP_Query( $args );

        if ( $socios_query->have_posts() ) :
        ?>
        <div class="dt_owl_carousel owl-theme owl-carousel" data-owl-options='{
            "loop": true,
            "autoplay": true,
            "autoplayTimeout": 5000,
            "autoplayHoverPause": true,
            "nav": true,
            "navText": ["<i class=\"fas fa-angle-left\"></i>","<i class=\"fas fa-angle-right\"></i>"],
            "dots": false,
            "margin": 30,
            "items": 2,
            "smartSpeed": 700,
            "stagePadding": 17,
            "responsive": {
                "0": {"margin": 30, "items": 2},
                "575": {"margin": 30, "items": 3},
                "767": {"margin": 50, "items": 4},
                "991": {"margin": 40, "items": 5},
                "1199": {"margin": 80, "items": 5}
            }
        }'>
            <?php
            // El Loop de WordPress
            while ( $socios_query->have_posts() ) : $socios_query->the_post();
                $socio_url = get_post_meta( get_the_ID(), '_socio_url_key', true );
                $link_target = !empty($socio_url) ? ' target="_blank" rel="noopener noreferrer"' : '';
                $link_href = !empty($socio_url) ? esc_url($socio_url) : '#';
            ?>
                <div class="dt_clients_logo wow fadeInUp" data-wow-delay="100ms" data-wow-duration="1500ms">
                    <figure class="image">
                        <a href="<?php echo $link_href; ?>"<?php echo $link_target; ?>>
                            <?php 
                            if ( has_post_thumbnail() ) {
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
        <?php endif; ?>
    </div>
</section>