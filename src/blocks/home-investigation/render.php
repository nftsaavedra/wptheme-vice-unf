<?php
/**
 * Render del bloque vpinunf/home-investigation
 * Obtiene los datos directamente desde las opciones del tema.
 *
 * @package VpinUnf
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$theme_options = get_option( 'vpinunf_theme_options', [] );
if ( empty( $theme_options['investigacion_section_enabled'] ) ) {
    return;
}

$section_title    = $attributes['sectionTitle']    ?? 'Líneas de Investigación';
$section_subtitle = $attributes['sectionSubtitle'] ?? 'Investigación';

// Reconstruir items desde las opciones del tema (item_1, item_2, item_3, item_4)
$items_to_render = [];
for ( $i = 1; $i <= 4; $i++ ) {
    $page_id = (int) ( $theme_options["item_{$i}_page_id"] ?? 0 );
    if ( ! $page_id ) {
        continue;
    }
    
    $page = get_post( $page_id );
    if ( ! $page || 'publish' !== $page->post_status ) {
        continue;
    }

    $custom_title = $theme_options["item_{$i}_custom_title"] ?? '';
    $final_title  = $custom_title ?: $page->post_title;
    $icon         = $theme_options["item_{$i}_icon"] ?? 'flaticon-book';
    $url          = get_permalink( $page_id );

    $items_to_render[] = [
        'title' => $final_title,
        'icon'  => $icon,
        'url'   => $url,
    ];
}

$wrapper_attrs = get_block_wrapper_attributes( [
    'class' => 'dt_features_style-1',
] );
?>

<div <?php echo $wrapper_attrs; ?>>
    <div class="dt-container">
        <?php if ( $section_subtitle || $section_title ) : ?>
            <div class="dt-row">
                <div class="dt-col-lg-6 dt-col-md-8 dt-mx-auto">
                    <div class="dt_section_title dt_text_center">
                        <?php if ( $section_subtitle ) : ?>
                            <h5 class="dt_section_title-subtitle">
                                <span><?php echo esc_html( $section_subtitle ); ?></span>
                            </h5>
                        <?php endif; ?>
                        <?php if ( $section_title ) : ?>
                            <h2 class="dt_section_title-title">
                                <?php echo esc_html( $section_title ); ?>
                            </h2>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ( ! empty( $items_to_render ) ) : ?>
            <div class="dt-row dt-g-4">
                <?php foreach ( $items_to_render as $item ) : ?>
                    <div class="dt-col-lg-3 dt-col-md-6">
                        <div class="dt_features_item dt_features_item-box">
                            <div class="dt_features_item-icon">
                                <i class="<?php echo esc_attr( $item['icon'] ); ?>"></i>
                            </div>
                            <div class="dt_features_item-content">
                                <h4 class="dt_features_item-title">
                                    <a href="<?php echo esc_url( $item['url'] ); ?>">
                                        <?php echo esc_html( $item['title'] ); ?>
                                    </a>
                                </h4>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ( current_user_can( 'edit_theme_options' ) ) : ?>
            <div style="padding:40px;text-align:center;border:2px dashed #007cba;color:#007cba;">
                <?php esc_html_e( 'Agrega páginas a las líneas de investigación desde Opciones VpinUnf en el menú de administración.', 'vpinunf' ); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
