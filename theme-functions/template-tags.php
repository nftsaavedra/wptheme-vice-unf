<?php
/**
 * Template tags y funciones de componentes del tema.
 *
 * @package VpinUnf
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*=========================================
 Google Fonts
=========================================*/
function vpinunf_google_fonts_url() {
    $font_families = array( 'Catamaran:wght@400;500;600;700;800;900' );
    $fonts_url     = add_query_arg(
        array(
            'family'  => implode( '&family=', $font_families ),
            'display' => 'swap',
        ),
        'https://fonts.googleapis.com/css2'
    );
    require_once get_stylesheet_directory() . '/inc/wptt-webfont-loader.php';
    return wptt_get_webfont_url( esc_url_raw( $fonts_url ) );
}

function vpinunf_google_fonts_enqueue() {
    wp_enqueue_style( 'vpinunf-google-fonts', vpinunf_google_fonts_url(), array(), null );
}
add_action( 'wp_enqueue_scripts', 'vpinunf_google_fonts_enqueue' );

/*=========================================
 Clases del Body
=========================================*/
function vpinunf_body_classes( $classes ) {
    if ( is_multi_author() ) {
        $classes[] = 'group-blog';
    }
    if ( ! is_singular() ) {
        $classes[] = 'hfeed';
    }
    
    // Si estamos en un Block Theme, agregamos las clases corporativas necesarias
    if ( wp_is_block_theme() ) {
        $classes[] = 'menu__active-one';
        $classes[] = 'btn--effect-two';
        $classes[] = 'siteheading--one';
    }
    
    return $classes;
}
add_filter( 'body_class', 'vpinunf_body_classes' );

function vpinunf_post_classes( $classes ) {
    if ( is_single() ) :
        $classes[] = 'single-post';
    endif;
    return $classes;
}
add_filter( 'post_class', 'vpinunf_post_classes' );

/*=========================================
 Utilidad: Reemplazo Asociativo de Strings
=========================================*/
if ( ! function_exists( 'vpinunf_str_replace_assoc' ) ) {
    function vpinunf_str_replace_assoc( array $replace, $subject ) {
        return str_replace( array_keys( $replace ), array_values( $replace ), $subject );
    }
}

/*=========================================
 Fragmentos de Carrito WooCommerce
=========================================*/
function vpinunf_woo_add_to_cart_fragment( $fragments ) {
    ob_start();
    $count = WC()->cart->cart_contents_count;
    if ( $count > 0 ) { ?>
        <strong class="cart_count"><?php echo esc_html( $count ); ?></strong>
    <?php } else { ?>
        <strong class="cart_count"><?php esc_html_e( '0', 'vpinunf' ); ?></strong>
    <?php }
    $fragments['.cart_count'] = ob_get_clean();
    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'vpinunf_woo_add_to_cart_fragment' );

/*=========================================
 Suprimir aviso de templates de WooCommerce
=========================================*/
add_filter( 'woocommerce_show_admin_notice', function ( $show, $notice ) {
    if ( 'template_files' === $notice ) {
        return false;
    }
    return $show;
}, 10, 2 );
