<?php
/**
 * Render del bloque vpinunf/home-production
 * Obtiene los datos directamente desde las opciones del tema.
 *
 * @package VpinUnf
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$theme_options = get_option( 'vpinunf_theme_options', [] );
if ( empty( $theme_options['production_section_enabled'] ) ) {
    return;
}

$subtitle    = $theme_options['production_subtitle']    ?? 'Publicaciones Académicas';
$title       = $theme_options['production_title']       ?? 'Producción Científica';
$description = $theme_options['production_description'] ?? '';
$items       = isset( $theme_options['production_items'] ) && is_array( $theme_options['production_items'] ) ? $theme_options['production_items'] : [];

$create_animated_subtitle = function ( $text ) {
    $output = '';
    $chars  = mb_str_split( wp_strip_all_tags( $text ) );
    foreach ( $chars as $char ) {
        $output .= '<i class="in">' . ( $char === ' ' ? '&nbsp;' : esc_html( $char ) ) . '</i>';
    }
    return $output;
};

$wrapper_attrs = get_block_wrapper_attributes( [
    'class' => 'dt_publications',
] );
?>
<section <?php echo $wrapper_attrs; ?>>
    <div class="dt-container">
        <div class="dt-row">
            <div class="dt-col-lg-5 dt-col-md-12">
                <div class="dt_publications-content">
                    <div class="dt_section_title dt_text_left dt_mb_30">
                        <?php if ( $subtitle ) : ?>
                            <h5 class="dt_section_title-subtitle split-text right">
                                <span><?php echo $create_animated_subtitle( $subtitle ); ?></span>
                            </h5>
                        <?php endif; ?>
                        <?php if ( $title ) : ?>
                            <h2 class="dt_section_title-title">
                                <?php echo wp_kses_post( $title ); ?>
                            </h2>
                        <?php endif; ?>
                    </div>
                    <?php if ( $description ) : ?>
                        <p class="dt_publications-text">
                            <?php echo nl2br( esc_html( $description ) ); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="dt-col-lg-7 dt-col-md-12">
                <?php if ( ! empty( $items ) ) : ?>
                    <div class="dt-row dt-g-4">
                        <?php foreach ( $items as $item ) :
                            $item_title = $item['title'] ?? '';
                            $item_desc  = $item['description'] ?? '';
                            $item_icon  = $item['icon'] ?? '';
                            $item_url   = $item['url'] ?? '';
                        ?>
                            <div class="dt-col-md-6 dt-col-sm-6">
                                <div class="dt_publications_item dt_publications_item-box">
                                    <?php if ( $item_icon ) : ?>
                                        <div class="dt_publications_item-icon">
                                            <i class="<?php echo esc_attr( $item_icon ); ?>"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="dt_publications_item-content">
                                        <?php if ( $item_title ) : ?>
                                            <h4 class="dt_publications_item-title">
                                                <?php if ( $item_url ) : ?>
                                                    <a href="<?php echo esc_url( $item_url ); ?>" target="_blank" rel="noopener noreferrer">
                                                        <?php echo esc_html( $item_title ); ?>
                                                    </a>
                                                <?php else : ?>
                                                    <?php echo esc_html( $item_title ); ?>
                                                <?php endif; ?>
                                            </h4>
                                        <?php endif; ?>
                                        <?php if ( $item_desc ) : ?>
                                            <p class="dt_publications_item-text">
                                                <?php echo esc_html( $item_desc ); ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if ( $item_url ) : ?>
                                            <a href="<?php echo esc_url( $item_url ); ?>" class="dt_publications_item-link" target="_blank" rel="noopener noreferrer">
                                                Ver más <i class="fas fa-arrow-right"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php elseif ( current_user_can( 'edit_theme_options' ) ) : ?>
                    <div style="padding:40px;text-align:center;border:2px dashed #007cba;color:#007cba;">
                        <?php esc_html_e( 'Agrega items de producción científica desde Opciones VpinUnf en el menú de administración.', 'vpinunf' ); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
