<?php
/**
 * Render del bloque vpinunf/home-events
 * Textos desde Opciones VpinUnf. Datos de eventos desde EventosService del plugin.
 *
 * @package VpinUnf
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$theme_options = get_option( 'vpinunf_theme_options', [] );
if ( empty( $theme_options['eventos_section_enabled'] ) ) {
    return;
}

$subtitle    = $theme_options['eventos_subtitulo']   ?? 'Actividad Institucional';
$title       = $theme_options['eventos_titulo']      ?? 'Nuestros <span>Eventos</span>';
$description = $theme_options['eventos_descripcion'] ?? '';
$quantity    = (int) ( $theme_options['eventos_cantidad'] ?? 4 );

$wrapper_attrs = get_block_wrapper_attributes( [
    'class' => 'dt_events dt_events--padding',
] );

?>
<section <?php echo $wrapper_attrs; ?>>
    <div class="dt-container">
        <div class="dt-row">
            <div class="dt-col-lg-6 dt-col-md-8 dt-mx-auto">
                <div class="dt_section_title dt_text_center dt_mb_30">
                    <?php if ( $subtitle ) : ?>
                        <h5 class="dt_section_title-subtitle split-text right">
                            <span><?php echo esc_html( $subtitle ); ?></span>
                        </h5>
                    <?php endif; ?>
                    <?php if ( $title ) : ?>
                        <h2 class="dt_section_title-title">
                            <?php echo wp_kses_post( $title ); ?>
                        </h2>
                    <?php endif; ?>
                    <?php if ( $description ) : ?>
                        <p class="dt_section_title-text">
                            <?php echo nl2br( esc_html( $description ) ); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ( class_exists( '\VpinUnf\Core\Service\EventosService' ) ) :
            $service = new \VpinUnf\Core\Service\EventosService();
            $eventos = $service->get_eventos_home( $quantity );

            if ( ! empty( $eventos ) ) : ?>
                <div class="dt-row dt-g-4">
                    <?php foreach ( $eventos as $evento ) : ?>
                        <div class="dt-col-lg-3 dt-col-md-6 dt-col-sm-6">
                            <div class="dt_events_item">
                                <?php if ( ! empty( $evento['thumbnail_html'] ) ) : ?>
                                    <div class="dt_events_item-image">
                                        <a href="<?php echo esc_url( $evento['permalink'] ); ?>">
                                            <?php echo $evento['thumbnail_html']; ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <div class="dt_events_item-content">
                                    <div class="dt_events_item-date">
                                        <span><?php echo esc_html( $evento['day'] ?? '' ); ?></span>
                                        <i><?php echo esc_html( $evento['month'] ?? '' ); ?></i>
                                    </div>
                                    <h4 class="dt_events_item-title">
                                        <a href="<?php echo esc_url( $evento['permalink'] ); ?>">
                                            <?php echo esc_html( $evento['title'] ?? '' ); ?>
                                        </a>
                                    </h4>
                                    <?php if ( ! empty( $evento['has_time'] ) ) : ?>
                                        <p class="dt_events_item-text">
                                            <i class="far fa-clock"></i> 
                                            <?php echo esc_html( $evento['start_time'] . ' - ' . $evento['end_time'] ); ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $evento['address'] ) ) : ?>
                                        <p class="dt_events_item-text">
                                            <i class="fas fa-map-marker-alt"></i> 
                                            <?php echo esc_html( $evento['address'] ); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div style="text-align:center;padding:20px;color:#666;">
                    <?php esc_html_e( 'No hay eventos próximos programados.', 'vpinunf' ); ?>
                </div>
            <?php endif; ?>
        <?php else : ?>
            <div style="text-align:center;padding:20px;border:2px dashed red;color:red;">
                <?php esc_html_e( 'Error: EventosService no está disponible. Asegúrate de que el plugin vpinunf-core esté activo.', 'vpinunf' ); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
