<?php
/**
 * Render del bloque vpinunf/about-section
 * Obtiene los datos directamente desde las opciones del tema.
 *
 * @package VpinUnf
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$theme_options = get_option( 'vpinunf_theme_options', [] );
if ( empty( $theme_options['about_section_enabled'] ) ) {
    return;
}

$subtitle    = $theme_options['about_subtitle']    ?? 'Sobre Nosotros';
$title       = $theme_options['about_title']       ?? '';
$person_name = $theme_options['about_person_name'] ?? '';
$description = $theme_options['about_description'] ?? '';

$main_image_id  = (int) ( $theme_options['about_main_image'] ?? 0 );
$main_image_url = $theme_options['about_main_image_url'] ?? '';
$video_url      = $theme_options['about_video_url'] ?? '';
$items          = isset( $theme_options['about_items'] ) && is_array( $theme_options['about_items'] ) ? $theme_options['about_items'] : [];

$create_animated_subtitle = function ( $text ) {
    $output = '';
    $chars  = mb_str_split( wp_strip_all_tags( $text ) );
    foreach ( $chars as $char ) {
        $output .= '<i class="in">' . ( $char === ' ' ? '&nbsp;' : esc_html( $char ) ) . '</i>';
    }
    return $output;
};

$wrapper_attrs = get_block_wrapper_attributes( [
    'class' => 'dt_about',
] );
?>
<section <?php echo $wrapper_attrs; ?>>
    <div class="dt-container">
        <div class="dt-row dt-g-4">
            <div class="dt-col-lg-6 dt-col-md-12 dt-mb-5 dt-mb-lg-0">
                <div class="dt_about_image">
                    <div class="dt_about_image-wrap">
                        <?php if ( $main_image_id ) : ?>
                            <?php echo wp_get_attachment_image( $main_image_id, 'full', false, [ 'class' => 'img-fluid', 'alt' => esc_attr( $title ) ] ); ?>
                        <?php elseif ( $main_image_url ) : ?>
                            <img src="<?php echo esc_url( $main_image_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="img-fluid">
                        <?php endif; ?>

                        <?php if ( $video_url ) : ?>
                            <a href="<?php echo esc_url( $video_url ); ?>" class="dt_lightbox_img dt_about_image-play">
                                <i class="fas fa-play"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="dt-col-lg-6 dt-col-md-12 dt-my-auto">
                <div class="dt_about_content">
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
                        <p class="dt_about_content-text">
                            <?php echo nl2br( esc_html( $description ) ); ?>
                        </p>
                    <?php endif; ?>

                    <?php if ( ! empty( $items ) ) : ?>
                        <div class="dt_about_content-list">
                            <?php foreach ( $items as $item ) :
                                $page_id = (int) ( $item['page_id'] ?? 0 );
                                if ( ! $page_id ) continue;
                                $page = get_post( $page_id );
                                if ( ! $page || 'publish' !== $page->post_status ) continue;
                                $item_title = ! empty( $item['page_title'] ) ? $item['page_title'] : $page->post_title;
                                $item_icon  = $item['icon'] ?? 'flaticon-check';
                                $item_url   = get_permalink( $page_id );
                            ?>
                                <ul>
                                    <li>
                                        <i class="<?php echo esc_attr( $item_icon ); ?>"></i>
                                        <a href="<?php echo esc_url( $item_url ); ?>"><?php echo esc_html( $item_title ); ?></a>
                                    </li>
                                </ul>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( $person_name ) : ?>
                        <div class="dt_about_content-author">
                            <h5 class="name"><?php echo esc_html( $person_name ); ?></h5>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>