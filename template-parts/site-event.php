<section id="dt_event" class="dt_event dt-py-default pg-event">
    <div class="dt-container">
        <div class="dt-row dt-g-4">
            <div id="dt-main" class="dt-col-lg-8 dt-col-sm-12 dt-col-12">
                <div class="dt-row">
                    <div class="dt-col-lg-12 dt-col-sm-12 dt-col-12">

                        <div class="dt_siteheading dt-mb-5">
                            <?php 
                            $subtitulo = get_theme_mod('eventos_subtitulo', 'Nuestra Empresa');
                            $titulo = get_theme_mod('eventos_titulo', 'Eventos & <span>Fiesta</span>');
                            $descripcion = get_theme_mod('eventos_descripcion', 'SoftMe ayuda a clientes de todo el mundo...');
                            ?>
                            <?php if (!empty($subtitulo)) : ?><span class="subtitle"><?php echo esc_html($subtitulo); ?></span><?php endif; ?>
                            <h2 class="title"><?php echo wp_kses_post($titulo); ?></h2>
                            <?php if (!empty($descripcion)) : ?><div class="text dt-mt-3 wow fadeInUp" data-wow-duration="1500ms"><p><?php echo esc_html($descripcion); ?></p></div><?php endif; ?>
                        </div>

                        <?php
                        $args = array(
                            'post_type'      => 'evento',
                            'posts_per_page' => 3,
                            'meta_key'       => '_evento_date_key',
                            'orderby'        => 'meta_value_date',
                            'order'          => 'DESC',
                        );
                        $eventos_query = new WP_Query( $args );

                        if ( $eventos_query->have_posts() ) :
                            $today = date('Y-m-d');

                            while ( $eventos_query->have_posts() ) : $eventos_query->the_post();
                                $event_date_raw = get_post_meta( get_the_ID(), '_evento_date_key', true );
                                $event_start    = get_post_meta( get_the_ID(), '_evento_start_time_key', true );
                                $event_end      = get_post_meta( get_the_ID(), '_evento_end_time_key', true );
                                $event_address  = get_post_meta( get_the_ID(), '_evento_address_key', true );
                                
                                $date_color_class = ( !empty($event_date_raw) && $event_date_raw < $today ) ? 'past-event' : 'future-event';

                                // --- INICIO DE LA CORRECCIÓN DE ZONA HORARIA ---
                                // Creamos un objeto de fecha, especificando que la fecha guardada ya está en la zona horaria del sitio.
                                $datetime_object = new DateTime($event_date_raw, wp_timezone());
                                $corrected_timestamp = $datetime_object->getTimestamp();

                                // Ahora usamos el timestamp corregido para mostrar la fecha.
                                $event_day = wp_date('d', $corrected_timestamp);
                                $event_month = wp_date('M', $corrected_timestamp);
                                // --- FIN DE LA CORRECCIÓN ---
                        ?>
                        <aside class="dt_event_box wow fadeInUp" data-wow-delay="100ms" data-wow-duration="1500ms">
                            <div class="dt_event_img">
                                <div class="image">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php if ( has_post_thumbnail() ) { the_post_thumbnail('large'); } ?>
                                    </a>
                                </div>
                                <?php if ( $event_date_raw ) : ?>
                                <div class="date <?php echo $date_color_class; ?>">
                                    <span class="d"><?php echo esc_html($event_day); ?></span>
                                    <span class="m"><?php echo esc_html($event_month); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="dt_event_content">
                                <h4 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                <div class="meta">
                                    <?php if ( $event_start && $event_end ) : ?>
                                    <span class="time"><?php echo esc_html( wp_date('g:i a', strtotime($event_start)) ); ?> - <?php echo esc_html( wp_date('g:i a', strtotime($event_end)) ); ?></span>
                                    &nbsp;&nbsp;-&nbsp;&nbsp;
                                    <?php endif; ?>
                                    <?php if ( $event_address ) : ?>
                                    <span class="address"><?php echo esc_html($event_address); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="line"></div>
                                <div class="description">
                                    <?php echo wp_trim_words( get_the_excerpt(), 20, '[...]' ); ?>
                                </div>
                            </div>
                        </aside>
                        <?php
                            endwhile;
                            wp_reset_postdata();
                        else :
                            echo '<p>No hay eventos programados.</p>';
                        endif;
                        ?>

                        <div class="dt_btn-group" style="text-align: center; margin-top: 40px;">
                            <a href="<?php echo esc_url( get_post_type_archive_link( 'evento' ) ); ?>" class="dt-btn dt-btn-primary">
                                <span class="dt-btn-text">Ver todos los eventos</span>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
            
            <div id="dt-sidebar" class="dt-col-lg-4 dt-col-sm-12 dt-col-12">
                <div class="dt_widget-area">
                    <?php if ( is_active_sidebar( 'events-sidebar' ) ) { dynamic_sidebar( 'events-sidebar' ); } ?>
                </div>
            </div>
        </div>
    </div>
</section>