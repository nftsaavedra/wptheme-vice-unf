<?php
/**
 * Render del bloque vpinunf/home-slider
 *
 * Utiliza el CPT 'slider' mediante el SliderService del plugin vpinunf-core
 * para generar un bloque dinámico 100% nativo FSE.
 *
 * @package VpinUnf
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$autoplay_delay  = (int) ( $attributes['autoplayDelay']  ?? 5000 );
$effect          = $attributes['effect']         ?? 'fade';
$loop            = (bool) ( $attributes['loop']          ?? true );
$show_navigation = (bool) ( $attributes['showNavigation'] ?? true );
$show_pagination = (bool) ( $attributes['showPagination'] ?? true );

// Integración dinámica con el CPT 'slider' mediante SliderService
$slider_query = null;
if ( class_exists( '\VpinUnf\Core\Service\SliderService' ) ) {
    $slider_service = new \VpinUnf\Core\Service\SliderService();
    $slider_query   = $slider_service->get_front_sliders( -1 ); // Traer todos los sliders activos
}

if ( ! $slider_query || ! $slider_query->have_posts() ) {
    if ( current_user_can( 'edit_posts' ) ) {
        echo sprintf(
            '<div %s><div style="padding:60px;text-align:center;border:2px dashed #e05e00;border-radius:8px;color:#e05e00;">%s</div></div>',
            get_block_wrapper_attributes(),
            esc_html__( 'No hay sliders configurados. Por favor, agrega sliders desde el menú "Sliders" en el panel de administración.', 'vpinunf' )
        );
    }
    return;
}

$swiper_opts = wp_json_encode( [
    'loop'        => $loop && $slider_query->post_count > 1,
    'slidesPerView' => 1,
    'spaceBetween'  => 0,
    'speed'         => 1000,
    'autoplay'      => [ 'delay' => $autoplay_delay, 'disableOnInteraction' => false ],
    'effect'        => $effect,
    'fadeEffect'    => [ 'crossFade' => true ],
    'pagination'    => $show_pagination ? [ 'el' => '#vpinunf-slider .swiper-pagination', 'clickable' => true ] : false,
    'navigation'    => $show_navigation ? [ 'nextEl' => '#vpinunf-slider .swiper-button-next', 'prevEl' => '#vpinunf-slider .swiper-button-prev' ] : false,
], JSON_UNESCAPED_SLASHES );

$create_btn_spans = static function ( string $text ): string {
    $output = '';
    $chars  = mb_str_split( $text );
    foreach ( $chars as $index => $char ) {
        $delay   = $index * 0.01;
        $output .= "<span style='--delay:{$delay}s;'>" . esc_html( $char ) . '</span>';
    }
    return $output;
};

$wrapper_attrs = get_block_wrapper_attributes( [
    'class' => 'dt_slider dt_slider--five dt_slider--thumbnav dt_slider--kenburn',
    'id'    => 'vpinunf-slider',
] );
?>
<section <?php echo $wrapper_attrs; ?>>
    <div class="swiper dt_swiper_carousel slider"
         data-swiper-options='<?php echo esc_attr( $swiper_opts ); ?>'>
        <div class="swiper-wrapper">
            <?php while ( $slider_query->have_posts() ) : $slider_query->the_post();
                $post_id    = get_the_ID();
                $subtitle   = get_post_meta( $post_id, '_slider_subtitle_key', true );
                $title      = get_the_title();
                $description = get_post_meta( $post_id, '_slider_description_key', true );
                $text_align  = get_post_meta( $post_id, '_slider_text_alignment_key', true ) ?: 'dt-text-left';
                $btn1_text   = get_post_meta( $post_id, '_slider_btn1_text_key', true );
                $btn1_url    = get_post_meta( $post_id, 'btn1_final_href', true ); // Puede que necesite resolverse aquí si el meta no lo guarda directo
                // Helper para $btn1_url si no viene del REST API directo:
                if ( empty( $btn1_url ) ) {
                    $link_type = get_post_meta( $post_id, '_slider_link_type_key', true );
                    if ( $link_type === 'external' ) {
                        $btn1_url = get_post_meta( $post_id, '_slider_link_url_key', true );
                    } elseif ( $link_type === 'internal' ) {
                        $content_id = get_post_meta( $post_id, '_slider_link_content_id_key', true );
                        if ( $content_id ) {
                            $btn1_url = get_permalink( $content_id );
                        }
                    }
                }
                
                $btn2_text   = get_post_meta( $post_id, '_slider_btn2_text_key', true );
                $btn2_url    = get_post_meta( $post_id, '_slider_btn2_link_key', true );
                $video_url   = get_post_meta( $post_id, '_slider_video_link_key', true );
            ?>
                <div class="swiper-slide dt_slider-item">
                    <?php if ( has_post_thumbnail() ) :
                        echo wp_get_attachment_image( get_post_thumbnail_id(), 'full', false, [
                            'class'   => 'dt-slider-bg',
                            'loading' => 'lazy',
                            'alt'     => esc_attr( $title ),
                        ] );
                    endif; ?>

                    <div class="dt_slider-wrapper">
                        <div class="dt_slider-inner">
                            <div class="dt_slider-innercell">
                                <div class="dt-container">
                                    <div class="dt-row <?php echo esc_attr( $text_align ); ?>">
                                        <div class="dt-col-lg-12 dt-col-md-12 first dt-my-auto">
                                            <div class="dt_slider-content">
                                                <?php if ( $subtitle ) : ?>
                                                    <h5 class="subtitle"><?php echo esc_html( $subtitle ); ?></h5>
                                                <?php endif; ?>
                                                <h2 class="title"><?php echo esc_html( $title ); ?></h2>
                                                <?php if ( $description ) : ?>
                                                    <p class="text"><?php echo nl2br( esc_html( $description ) ); ?></p>
                                                <?php endif; ?>

                                                <div class="dt_btn-group">
                                                    <?php if ( $btn1_url && $btn1_text ) : ?>
                                                        <a href="<?php echo esc_url( $btn1_url ); ?>"
                                                           class="dt-btn dt-btn-primary">
                                                            <span class="dt-btn-text" data-text="<?php echo esc_attr( $btn1_text ); ?>">
                                                                <?php echo $create_btn_spans( $btn1_text ); ?>
                                                            </span>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if ( $btn2_url && $btn2_text ) : ?>
                                                        <a href="<?php echo esc_url( $btn2_url ); ?>"
                                                           class="dt-btn dt-btn-white">
                                                            <span class="dt-btn-text" data-text="<?php echo esc_attr( $btn2_text ); ?>">
                                                                <?php echo $create_btn_spans( $btn2_text ); ?>
                                                            </span>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if ( $video_url ) : ?>
                                                        <a href="<?php echo esc_url( $video_url ); ?>"
                                                           class="dt_lightbox_img dt-btn-play dt-btn-white"
                                                           data-caption="">
                                                            <i class="fas fa-play" aria-hidden="true"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>

        <?php if ( $show_navigation ) : ?>
            <div class="swiper-button-prev"><i class="fas fa-angle-left"><span class="imgholder"></span></i></div>
            <div class="swiper-button-next"><i class="fas fa-angle-right"><span class="imgholder"></span></i></div>
        <?php endif; ?>
        <?php if ( $show_pagination ) : ?>
            <div class="swiper-pagination"></div>
        <?php endif; ?>
    </div>
</section>
