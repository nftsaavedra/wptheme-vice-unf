<?php

/**
 * =================================================================
 * Plantilla para la sección de "Eventos" de la Página de Inicio.
 * =================================================================
 * - Dinamizada para usar la página de Opciones del Tema.
 * - Muestra los eventos más recientes (pasados y futuros).
 * - Mantiene la lógica de coloreado para fechas de eventos pasados.
 * - CORREGIDO: Todas las fechas y horas respetan la zona horaria de WordPress.
 *
 * @package ViceUnf
 */

// 1. Obtenemos todas las opciones del tema.
$options = get_option('viceunf_theme_options', []);

// 2. Verificamos si la sección está habilitada.
$is_enabled = isset($options['eventos_section_enabled']) ? $options['eventos_section_enabled'] : false;
if (!$is_enabled) {
    return;
}

// 3. Extraemos los valores desde las Opciones del Tema.
$subtitulo   = isset($options['eventos_subtitulo']) ? $options['eventos_subtitulo'] : 'Actividad Institucional';
$titulo      = isset($options['eventos_titulo']) ? $options['eventos_titulo'] : 'Nuestros <span>Eventos</span>';
$descripcion = isset($options['eventos_descripcion']) ? $options['eventos_descripcion'] : 'Un recuento de nuestras actividades más importantes.';

?>
<section id="dt_event" class="dt_event dt-py-default pg-event">
    <div class="dt-container">
        <div class="dt-row dt-g-4">
            <div id="dt-main" class="dt-col-lg-8 dt-col-sm-12 dt-col-12">
                <div class="dt-row">
                    <div class="dt-col-lg-12 dt-col-sm-12 dt-col-12">

                        <div class="dt_siteheading dt-mb-5">
                            <?php if (!empty($subtitulo)) : ?><span class="subtitle"><?php echo esc_html($subtitulo); ?></span><?php endif; ?>
                            <h2 class="title"><?php echo wp_kses_post($titulo); ?></h2>
                            <?php if (!empty($descripcion)) : ?><div class="text dt-mt-3 wow fadeInUp" data-wow-duration="1500ms">
                                    <p><?php echo esc_html($descripcion); ?></p>
                                </div><?php endif; ?>
                        </div>

                        <?php
                        $args = array(
                            'post_type'      => 'evento',
                            'posts_per_page' => 4,
                            'meta_key'       => '_evento_date_key',
                            'orderby'        => 'meta_value_date',
                            'order'          => 'DESC',
                        );
                        $eventos_query = new WP_Query($args);

                        if ($eventos_query->have_posts()) :
                            $today_timestamp = strtotime(date('Y-m-d', current_time('timestamp')));

                            while ($eventos_query->have_posts()) : $eventos_query->the_post();
                                $event_date_raw = get_post_meta(get_the_ID(), '_evento_date_key', true);
                                $event_start    = get_post_meta(get_the_ID(), '_evento_start_time_key', true);
                                $event_end      = get_post_meta(get_the_ID(), '_evento_end_time_key', true);
                                $event_address  = get_post_meta(get_the_ID(), '_evento_address_key', true);

                                // Lógica para colorear eventos pasados
                                $event_timestamp = strtotime($event_date_raw);
                                $date_color_class = ($event_timestamp < $today_timestamp) ? 'past-event' : 'future-event';

                                // Lógica de zona horaria para el DÍA y MES
                                $datetime_object = new DateTime($event_date_raw, wp_timezone());
                                $corrected_timestamp = $datetime_object->getTimestamp();
                                $event_day = wp_date('d', $corrected_timestamp);
                                $event_month = wp_date('M', $corrected_timestamp);
                        ?>
                                <aside class="dt_event_box wow fadeInUp" data-wow-delay="100ms" data-wow-duration="1500ms">
                                    <div class="dt_event_img">
                                        <div class="image">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php
                                                if (has_post_thumbnail()) {
                                                    echo wp_get_attachment_image(get_post_thumbnail_id(), 'large');
                                                }
                                                ?>
                                            </a>
                                        </div>
                                        <?php if ($event_date_raw) : ?>
                                            <div class="date <?php echo $date_color_class; ?>">
                                                <span class="d"><?php echo esc_html($event_day); ?></span>
                                                <span class="m"><?php echo esc_html($event_month); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="dt_event_content">
                                        <h4 class="title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                        <div class="meta">
                                            <?php if ($event_start && $event_end) :
                                                // --- CORRECCIÓN DE ZONA HORARIA PARA LA HORA ---
                                                // Combinamos la fecha y la hora para darle a PHP el contexto completo.
                                                $start_datetime_str = $event_date_raw . ' ' . $event_start;
                                                $end_datetime_str   = $event_date_raw . ' ' . $event_end;

                                                // Creamos los objetos DateTime, especificando la zona horaria de WordPress.
                                                $start_datetime_obj = new DateTime($start_datetime_str, wp_timezone());
                                                $end_datetime_obj   = new DateTime($end_datetime_str, wp_timezone());

                                                // Usamos wp_date con los timestamps correctos.
                                            ?>
                                                <span class="time"><?php echo esc_html(wp_date('g:i a', $start_datetime_obj->getTimestamp())); ?> - <?php echo esc_html(wp_date('g:i a', $end_datetime_obj->getTimestamp())); ?></span>
                                                &nbsp;&nbsp;-&nbsp;&nbsp;
                                            <?php endif; ?>
                                            <?php if ($event_address) : ?>
                                                <span class="address"><?php echo esc_html($event_address); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="line"></div>
                                        <div class="description">
                                            <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                                        </div>
                                    </div>
                                </aside>
                        <?php
                            endwhile;
                            wp_reset_postdata();
                        else :
                            echo '<p>No hay eventos para mostrar en este momento.</p>';
                        endif;
                        ?>

                        <div class="dt_btn-group" style="text-align: center; margin-top: 40px;">
                            <a href="<?php echo esc_url(get_post_type_archive_link('evento')); ?>" class="dt-btn dt-btn-primary">
                                <span class="dt-btn-text">Ver todos los eventos</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div id="dt-sidebar" class="dt-col-lg-4 dt-col-sm-12 dt-col-12">
                <div class="dt_widget-area">
                    <?php if (is_active_sidebar('events-sidebar')) {
                        dynamic_sidebar('events-sidebar');
                    } ?>
                </div>
            </div>
        </div>
    </div>
</section>