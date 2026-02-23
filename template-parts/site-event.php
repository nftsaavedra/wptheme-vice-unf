<?php

/**
 * =================================================================
 * Plantilla para la sección de "Eventos" de la Página de Inicio.
 * =================================================================
 * - Dinamizada para usar la página de Opciones del Tema.
 * - Muestra los eventos más recientes (pasados y futuros).
 * - Lógica de coloreado para fechas de eventos pasados.
 * - Todas las fechas y horas respetan la zona horaria de WordPress.
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
                        // Obtenemos la data purgada desde la capa de servicio (Validando que el plugin exista)
                        $eventos = array();
                        if ( class_exists( 'ViceUnf_Eventos_Service' ) ) {
                            $eventos = ViceUnf_Eventos_Service::get_eventos_home( 4 );
                        } else {
                            echo '<div class="alert alert-warning">Se requiere activar el plugin <strong>ViceUnf Core</strong> para visualizar los eventos.</div>';
                        }

                        if ( ! empty( $eventos ) ) :
                            foreach ( $eventos as $evento ) :
                                $date_color_class = $evento['is_past'] ? 'past-event' : 'future-event';
                        ?>
                                <aside class="dt_event_box wow fadeInUp" data-wow-delay="100ms" data-wow-duration="1500ms">
                                    <div class="dt_event_img">
                                        <div class="image">
                                            <a href="<?php echo esc_url( $evento['permalink'] ); ?>">
                                                <?php echo $evento['thumbnail_html']; ?>
                                            </a>
                                        </div>
                                        <?php if ( $evento['has_date'] ) : ?>
                                            <div class="date <?php echo $date_color_class; ?>">
                                                <span class="d"><?php echo esc_html( $evento['day'] ); ?></span>
                                                <span class="m"><?php echo esc_html( $evento['month'] ); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="dt_event_content">
                                        <h4 class="title"><a href="<?php echo esc_url( $evento['permalink'] ); ?>"><?php echo esc_html( $evento['title'] ); ?></a></h4>
                                        <div class="meta">
                                            <?php if ( $evento['has_time'] ) : ?>
                                                <span class="time"><?php echo esc_html( $evento['start_time'] ); ?> - <?php echo esc_html( $evento['end_time'] ); ?></span>
                                                &nbsp;&nbsp;-&nbsp;&nbsp;
                                            <?php endif; ?>
                                            <?php if ( $evento['address'] ) : ?>
                                                <span class="address"><?php echo esc_html( $evento['address'] ); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="line"></div>
                                        <div class="description">
                                            <?php echo wp_kses_post( $evento['excerpt'] ); ?>
                                        </div>
                                    </div>
                                </aside>
                        <?php
                            endforeach;
                        else :
                            echo '<p>' . esc_html__( 'No hay eventos para mostrar en este momento.', 'viceunf' ) . '</p>';
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