<section id="dt_posts" class="dt_posts dt-py-default front-posts">
    <div class="dt-container">
        <div class="dt-row">
            <div class="dt-col-xl-7 dt-col-lg-8 dt-col-md-9 dt-col-12 dt-mx-auto dt-mb-6">
                <div class="dt_siteheading dt-text-center">
                    <?php
                    // Obtenemos los datos del Personalizador
                    $subtitulo = get_theme_mod('noticias_subtitulo', "Actualidad Académica");
                    $titulo = get_theme_mod('noticias_titulo', "Últimas Noticias y Artículos");
                    $descripcion = get_theme_mod('noticias_descripcion', "Conoce las novedades, logros y actividades académicas de la Vicepresidencia Académica.");
                    ?>

                    <?php if ( !empty($subtitulo) ) : ?>
                    <span class="subtitle"><?php echo esc_html($subtitulo); ?></span>
                    <?php endif; ?>

                    <?php if ( !empty($titulo) ) : ?>
                    <h2 class="title"><?php echo wp_kses_post($titulo); ?></h2>
                    <?php endif; ?>
                    
                    <?php if ( !empty($descripcion) ) : ?>
                    <div class="text dt-mt-3 wow fadeInUp" data-wow-duration="1500ms">
                        <p><?php echo esc_html($descripcion); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="dt-row dt-g-4">
            <?php
            // WP_Query para obtener las últimas 3 entradas del blog
            $args = array(
                'post_type'           => 'post',
                'posts_per_page'      => 3,
                'ignore_sticky_posts' => 1,
            );
            $blog_query = new WP_Query( $args );

            if ( $blog_query->have_posts() ) :
                while ( $blog_query->have_posts() ) : $blog_query->the_post();
            ?>
            <div class="dt-col-lg-4 dt-col-sm-6 dt-col-12 wow fadeInUp" data-wow-delay="100ms" data-wow-duration="1500ms">
                
                <article id="post-<?php the_ID(); ?>" <?php post_class('dt_post_item dt_posts--one dt-mb-4'); ?>>
                    
                    <?php if ( has_post_thumbnail() ) : ?>
                    <div class="image">
                        <?php the_post_thumbnail('medium'); ?>
                        <a href="<?php the_permalink(); ?>"></a>
                    </div>
                    <?php endif; ?>

                    <div class="inner">
                        <div class="meta">
                            <ul>
                                <li>
                                    <div class="date">
                                        <i class="far fa-calendar-alt dt-mr-2" aria-hidden="true"></i>
                                        <?php echo get_the_date('j \d\e F \d\e Y'); ?>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="catetag">
                            <i class="fas fa-folder dt-mr-1" aria-hidden="true"></i>
                            <?php the_category(' , '); ?>
                        </div>
                        
                        <h4 class="title">
                            <a href="<?php the_permalink(); ?>" rel="bookmark">
                                <?php the_title(); ?>
                            </a>
                        </h4>

                        <div class="content">
                            <?php echo '<p>' . wp_trim_words( get_the_excerpt(), 25, '...' ) . '</p>'; ?>
                            <a href="<?php the_permalink(); ?>" class="more-link">Leer más</a>
                        </div>
                    </div>
                </article>
            </div>
            <?php
                endwhile;
                wp_reset_postdata();
            else :
                echo '<p>No se encontraron entradas.</p>';
            endif;
            ?>
        </div>

        <?php
        // --- BLOQUE DEL BOTÓN "VER MÁS" AÑADIDO AQUÍ ---
        
        // Obtenemos el ID de la página asignada como "Página de entradas" en Ajustes > Lectura
        $page_for_posts_id = get_option( 'page_for_posts' );
        
        // Si hay una página de entradas configurada, mostramos el botón
        if ( $page_for_posts_id ) :
        ?>
        <div class="dt_btn-group" style="text-align: center; margin-top: 40px;">
            <a href="<?php echo esc_url( get_permalink( $page_for_posts_id ) ); ?>" class="dt-btn dt-btn-primary">
                <span class="dt-btn-text">Ver todas las noticias</span>
            </a>
        </div>
        <?php endif; ?>
        
    </div>
</section>