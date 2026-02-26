<?php
/**
 * Template tags y funciones de componentes del tema.
 *
 * @package ViceUnf
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*=========================================
 Título del Encabezado de Página (Delegado)
=========================================*/
require_once __DIR__ . '/classes/class-page-title-builder.php';
function viceunf_page_header_title() {
    ViceUnf_Page_Title_Builder::render();
}

/*=========================================
 URL de Breadcrumbs (Deprecado - Integrado en Builder)
=========================================*/
// viceunf_page_url() ya no es necesaria con el uso de ViceUnf_Breadcrumbs_Builder::get_current_url()

/*=========================================
 Breadcrumbs (Delegado)
=========================================*/
require_once __DIR__ . '/classes/class-breadcrumbs-builder.php';
if ( ! function_exists( 'viceunf_page_header_breadcrumbs' ) ) :
    function viceunf_page_header_breadcrumbs() {
        ViceUnf_Breadcrumbs_Builder::render();
    }
endif;

/*=========================================
 Google Fonts
=========================================*/
function viceunf_google_fonts_url() {
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

function viceunf_google_fonts_enqueue() {
    wp_enqueue_style( 'viceunf-google-fonts', viceunf_google_fonts_url(), array(), null );
}
add_action( 'wp_enqueue_scripts', 'viceunf_google_fonts_enqueue' );

/*=========================================
 Clases del Body
=========================================*/
function viceunf_body_classes( $classes ) {
    if ( is_multi_author() ) {
        $classes[] = 'group-blog';
    }
    if ( ! is_singular() ) {
        $classes[] = 'hfeed';
    }
    return $classes;
}
add_filter( 'body_class', 'viceunf_body_classes' );

function viceunf_post_classes( $classes ) {
    if ( is_single() ) :
        $classes[] = 'single-post';
    endif;
    return $classes;
}
add_filter( 'post_class', 'viceunf_post_classes' );

/*=========================================
 Utilidad: Reemplazo Asociativo de Strings
=========================================*/
if ( ! function_exists( 'viceunf_str_replace_assoc' ) ) {
    function viceunf_str_replace_assoc( array $replace, $subject ) {
        return str_replace( array_keys( $replace ), array_values( $replace ), $subject );
    }
}

/*=========================================
 Preloader del Sitio
=========================================*/
if ( ! function_exists( 'viceunf_site_preloader' ) ) :
    function viceunf_site_preloader() {
        $hs_preloader = get_theme_mod( 'viceunf_hs_preloader', '1' );
        if ( '1' === $hs_preloader ) { ?>
            <div id="dt_preloader" class="dt_preloader">
                <div class="dt_preloader-inner">
                    <div class="dt_preloader-handle">
                        <button type="button" class="dt_preloader-close site--close"></button>
                        <div class="dt_preloader-animation">
                            <div class="dt_preloader-spinner"></div>
                            <div class="dt_preloader-text">
                                <?php
                                $preloader_string     = get_bloginfo( 'name' );
                                $preloader_arr_string = str_split( $preloader_string );
                                foreach ( $preloader_arr_string as $str ) {
                                    echo sprintf( __( '<span class="splitted" data-char=%1$s>%1$s</span>', 'viceunf' ), $str );
                                }
                                ?>
                            </div>
                            <p class="text-center"><?php esc_html_e( 'Cargando', 'viceunf' ); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php }
    }
endif;
add_action( 'viceunf_site_preloader', 'viceunf_site_preloader' );

/*=========================================
 Encabezado Principal del Sitio
=========================================*/
if ( ! function_exists( 'viceunf_site_main_header' ) ) :
    function viceunf_site_main_header() {
        get_template_part( 'template-parts/site', 'header' );
    }
endif;
add_action( 'viceunf_site_main_header', 'viceunf_site_main_header' );

/*=========================================
 Imagen del Header
=========================================*/
if ( ! function_exists( 'viceunf_wp_hdr_image' ) ) :
    function viceunf_wp_hdr_image() {
        if ( get_header_image() ) : ?>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-header" id="custom-header" rel="home">
                <img src="<?php echo esc_url( get_header_image() ); ?>" width="<?php echo esc_attr( get_custom_header()->width ); ?>" height="<?php echo esc_attr( get_custom_header()->height ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'title' ) ); ?>">
            </a>
        <?php endif;
    }
endif;
add_action( 'viceunf_wp_hdr_image', 'viceunf_wp_hdr_image' );

/*=========================================
 Navegación del Sitio
=========================================*/
if ( ! function_exists( 'viceunf_site_header_navigation' ) ) :
    function viceunf_site_header_navigation() {
        wp_nav_menu(
            array(
                'theme_location' => 'primary_menu',
                'container'      => '',
                'menu_class'     => 'dt_navbar-mainmenu',
                'fallback_cb'    => 'WP_Bootstrap_Navwalker::fallback',
                'walker'         => new WP_Bootstrap_Navwalker(),
            )
        );
    }
endif;
add_action( 'viceunf_site_header_navigation', 'viceunf_site_header_navigation' );

/*=========================================
 Búsqueda del Sitio
=========================================*/
if ( ! function_exists( 'viceunf_site_main_search' ) ) :
    function viceunf_site_main_search() {
        $hs_hdr_search = get_theme_mod( 'viceunf_hs_hdr_search', '1' );
        $search_result = get_theme_mod( 'viceunf_search_result', 'post' );
        if ( '1' === $hs_hdr_search ) : ?>
            <li class="dt_navbar-search-item">
                <button class="dt_navbar-search-toggle"><i class="fas fa-search" aria-hidden="true"></i></button>
                <div class="dt_search search--header">
                    <form method="get" class="dt_search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Buscar de nuevo', 'viceunf' ); ?>">
                        <label for="dt_search-form-1">
                            <?php if ( 'product' === $search_result && class_exists( 'WooCommerce' ) ) : ?>
                                <input type="hidden" name="post_type" value="product" />
                            <?php endif; ?>
                            <span class="screen-reader-text"><?php esc_html_e( 'Buscar:', 'viceunf' ); ?></span>
                            <input type="search" id="dt_search-form-1" class="dt_search-field" placeholder="<?php esc_attr_e( 'Buscar aquí', 'viceunf' ); ?>" value="" name="s" />
                        </label>
                        <button type="submit" class="dt_search-submit search-submit"><i class="fas fa-search" aria-hidden="true"></i></button>
                    </form>
                    <button type="button" class="dt_search-close"><i class="fas fa-long-arrow-alt-up" aria-hidden="true"></i></button>
                </div>
            </li>
        <?php endif;
    }
endif;
add_action( 'viceunf_site_main_search', 'viceunf_site_main_search' );

/*=========================================
 Carrito WooCommerce
=========================================*/
if ( ! function_exists( 'viceunf_woo_cart' ) ) :
    function viceunf_woo_cart() {
        $hs_hdr_cart = get_theme_mod( 'viceunf_hs_hdr_cart', '1' );
        if ( '1' === $hs_hdr_cart && class_exists( 'WooCommerce' ) ) : ?>
            <li class="dt_navbar-cart-item">
                <a href="javascript:void(0);" class="dt_navbar-cart-icon">
                    <span class="cart_icon"><i class="fas fa-shopping-cart" aria-hidden="true"></i></span>
                    <?php
                    $count = WC()->cart->cart_contents_count;
                    if ( $count > 0 ) { ?>
                        <strong class="cart_count"><?php echo esc_html( $count ); ?></strong>
                    <?php } else { ?>
                        <strong class="cart_count"><?php esc_html_e( '0', 'viceunf' ); ?></strong>
                    <?php } ?>
                </a>
                <div class="dt_navbar-shopcart">
                    <?php get_template_part( 'woocommerce/cart/mini', 'cart' ); ?>
                </div>
            </li>
        <?php endif;
    }
endif;
add_action( 'viceunf_woo_cart', 'viceunf_woo_cart' );

function viceunf_woo_add_to_cart_fragment( $fragments ) {
    ob_start();
    $count = WC()->cart->cart_contents_count;
    if ( $count > 0 ) { ?>
        <strong class="cart_count"><?php echo esc_html( $count ); ?></strong>
    <?php } else { ?>
        <strong class="cart_count"><?php esc_html_e( '0', 'viceunf' ); ?></strong>
    <?php }
    $fragments['.cart_count'] = ob_get_clean();
    return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'viceunf_woo_add_to_cart_fragment' );

/*=========================================
 Mi Cuenta
=========================================*/
if ( ! function_exists( 'viceunf_hdr_account' ) ) {
    function viceunf_hdr_account() {
        $hs_hdr_account = get_theme_mod( 'viceunf_hs_hdr_account', '1' );
        if ( '1' === $hs_hdr_account ) : ?>
            <li class="dt_navbar-user-item">
                <?php if ( is_user_logged_in() ) : ?>
                    <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="dt_user_btn"><i class="fas fa-user"></i></a>
                <?php else : ?>
                    <a href="<?php echo esc_url( wp_login_url( home_url() ) ); ?>" class="dt_user_btn"><i class="fas fa-user"></i></a>
                <?php endif; ?>
            </li>
        <?php endif;
    }
}
add_action( 'viceunf_hdr_account', 'viceunf_hdr_account' );

/*=========================================
 Logo del Sitio
=========================================*/
if ( ! function_exists( 'viceunf_site_logo' ) ) :
    function viceunf_site_logo() {
        if ( has_custom_logo() ) {
            the_custom_logo();
        } else { ?>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site--title">
                <h4 class="site-title"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h4>
            </a>
        <?php }
        $description = get_bloginfo( 'description' );
        if ( $description ) : ?>
            <p class="site-description"><?php echo esc_html( $description ); ?></p>
        <?php endif;
    }
endif;
add_action( 'viceunf_site_logo', 'viceunf_site_logo' );

/*=========================================
 Logo Móvil
=========================================*/
if ( ! function_exists( 'viceunf_site_mobile_logo' ) ) :
    function viceunf_site_mobile_logo() {
        $mobile_logo = get_theme_mod( 'viceunf_mobile_logo', esc_url( get_stylesheet_directory_uri() . '/assets/images/logo.png' ) );
        if ( ! empty( $mobile_logo ) ) { ?>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link"><img src="<?php echo esc_url( $mobile_logo ); ?>" class="custom-logo"></a>
        <?php } else { ?>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site--title">
                <h4 class="site-title"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></h4>
            </a>
        <?php }
        $description = get_bloginfo( 'description' );
        if ( $description ) : ?>
            <p class="site-description"><?php echo esc_html( $description ); ?></p>
        <?php endif;
    }
endif;
add_action( 'viceunf_site_mobile_logo', 'viceunf_site_mobile_logo' );

/*=========================================
 Widget del Footer
=========================================*/
if ( ! function_exists( 'viceunf_footer_widget' ) ) :
    function viceunf_footer_widget() { ?>
        <div class="dt_footer_middle">
            <div class="pattern-layer">
                <div class="pattern-1"><img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/images/shape/white_curved_line.png"></div>
                <div class="pattern-2"></div>
            </div>
            <div class="dt-container">
                <div class="dt-row dt-g-lg-4 dt-g-5">
                    <div class="dt-col-lg-3 dt-col-sm-6 dt-col-12 wow fadeInUp animated" data-wow-delay="00ms" data-wow-duration="1500ms">
                        <?php if ( is_active_sidebar( 'viceunf-footer-widget-1' ) ) : ?>
                            <?php dynamic_sidebar( 'viceunf-footer-widget-1' ); ?>
                        <?php endif; ?>
                    </div>
                    <div class="dt-col-lg-3 dt-col-sm-6 dt-col-12 wow fadeInUp animated" data-wow-delay="100ms" data-wow-duration="1500ms">
                        <?php if ( is_active_sidebar( 'viceunf-footer-widget-2' ) ) : ?>
                            <?php dynamic_sidebar( 'viceunf-footer-widget-2' ); ?>
                        <?php endif; ?>
                    </div>
                    <div class="dt-col-lg-3 dt-col-sm-6 dt-col-12 wow fadeInUp animated" data-wow-delay="200ms" data-wow-duration="1500ms">
                        <?php if ( is_active_sidebar( 'viceunf-footer-widget-3' ) ) : ?>
                            <?php dynamic_sidebar( 'viceunf-footer-widget-3' ); ?>
                        <?php endif; ?>
                    </div>
                    <div class="dt-col-lg-3 dt-col-sm-6 dt-col-12 wow fadeInUp animated" data-wow-delay="300ms" data-wow-duration="1500ms">
                        <?php if ( is_active_sidebar( 'viceunf-footer-widget-4' ) ) : ?>
                            <?php dynamic_sidebar( 'viceunf-footer-widget-4' ); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php }
endif;
add_action( 'viceunf_footer_widget', 'viceunf_footer_widget' );

/*=========================================
 Parte Inferior del Footer
=========================================*/
if ( ! function_exists( 'viceunf_footer_bottom' ) ) :
    function viceunf_footer_bottom() { ?>
        <div class="dt_footer_copyright">
            <div class="dt-container">
                <div class="dt-row dt-g-4 dt-mt-0">
                    <div class="dt-col-md-12 dt-col-sm-12 dt-text-sm-center dt-text-center">
                        <?php do_action( 'viceunf_footer_copyright_data' ); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php }
endif;
add_action( 'viceunf_footer_bottom', 'viceunf_footer_bottom' );

/*=========================================
 Copyright del Footer
=========================================*/
if ( ! function_exists( 'viceunf_footer_copyright_data' ) ) :
    function viceunf_footer_copyright_data() {
        $copyright_text = get_theme_mod( 'viceunf_footer_copyright_text', 'Copyright &copy; [current_year] [site_title]' );
        if ( ! empty( $copyright_text ) ) :
            $copyright_tags = array(
                '[current_year]' => date_i18n( 'Y' ),
                '[site_title]'   => get_bloginfo( 'name' ),
                '[theme_author]' => sprintf( __( '<a href="https://viceinvestigacion.unf.edu.pe/">ViceUnf</a>', 'viceunf' ) ),
            );
            ?>
            <div class="dt_footer_copyright-text">
                <?php echo apply_filters( 'viceunf_footer_copyright', wp_kses_post( viceunf_str_replace_assoc( $copyright_tags, $copyright_text ) ) ); ?>
            </div>
        <?php endif;
    }
endif;
add_action( 'viceunf_footer_copyright_data', 'viceunf_footer_copyright_data' );

/*=========================================
 Botón Ir Arriba
=========================================*/
if ( ! function_exists( 'viceunf_top_scroller' ) ) :
    function viceunf_top_scroller() {
        $hs_scroller = get_theme_mod( 'viceunf_hs_scroller', '1' );
        if ( '1' === $hs_scroller ) { ?>
            <button type="button" id="dt_uptop" class="dt_uptop">
                <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
                    <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: -0.0171453;"></path>
                </svg>
            </button>
        <?php }
    }
endif;
add_action( 'viceunf_top_scroller', 'viceunf_top_scroller' );

/*=========================================
 Estilos Inline del Personalizer
=========================================*/
if ( ! function_exists( 'viceunf_user_custom_style' ) ) :
    function viceunf_user_custom_style() {
        $print_style = '';

        // Breadcrumb
        $print_style .= viceunf_customizer_value( 'viceunf_breadcrumb_height', '.dt_pagetitle', array( 'padding-top' ), array( 12, 12, 12 ), 'rem' );
        $print_style .= viceunf_customizer_value( 'viceunf_breadcrumb_height', '.dt_pagetitle', array( 'padding-bottom' ), array( 12, 12, 12 ), 'rem' );

        $breadcrumb_img_opacity  = get_theme_mod( 'viceunf_breadcrumb_img_opacity', '0.5' );
        $breadcrumb_opacity_color = get_theme_mod( 'viceunf_breadcrumb_opacity_color', '#000' );
        $print_style .= ".dt_pagetitle .parallax-bg:after {
                    opacity: " . esc_attr( $breadcrumb_img_opacity ) . ";
                    background: " . esc_attr( $breadcrumb_opacity_color ) . ";
                }\n";

        // Logo
        $print_style .= viceunf_customizer_value( 'viceunf_hdr_logo_size', '.site--logo img', array( 'max-width' ), array( 150, 150, 150 ), 'px !important' );

        // Contenedor
        $container_width = get_theme_mod( 'viceunf_site_container_width', '1252' );
        if ( $container_width >= 768 && $container_width <= 2000 ) {
            $print_style .= ".dt-container,.dt__slider-main .owl-dots {
                    max-width: " . esc_attr( $container_width ) . "px;
                }\n";
        }

        // Sidebar
        $sidebar_width = get_theme_mod( 'viceunf_sidebar_width', 33 );
        if ( '' !== $sidebar_width ) {
            $primary_width = absint( 100 - $sidebar_width );
            $print_style .= "	@media (min-width: 992px) {#dt-main {
                max-width:" . esc_attr( $primary_width ) . "%;
                flex-basis:" . esc_attr( $primary_width ) . "%;
                }\n";
            $print_style .= "#dt-sidebar {
                max-width:" . esc_attr( $sidebar_width ) . "%;
                flex-basis:" . esc_attr( $sidebar_width ) . "%;
                }}\n";
        }

        // Tipografía Body
        $print_style .= viceunf_customizer_value( 'viceunf_body_font_size', 'body', array( 'font-size' ), array( 16, 16, 16 ), 'px' );
        $print_style .= viceunf_customizer_value( 'viceunf_body_line_height', 'body', array( 'line-height' ), array( 1.6, 1.6, 1.6 ) );
        $print_style .= viceunf_customizer_value( 'viceunf_body_ltr_space', 'body', array( 'letter-spacing' ), array( 0, 0, 0 ), 'px' );

        // Tipografía Headings
        for ( $i = 1; $i <= 6; $i++ ) {
            $print_style .= viceunf_customizer_value( 'viceunf_h' . $i . '_font_size', 'h' . $i, array( 'font-size' ), array( 36, 36, 36 ), 'px' );
            $print_style .= viceunf_customizer_value( 'viceunf_h' . $i . '_line_height', 'h' . $i, array( 'line-height' ), array( 1.2, 1.2, 1.2 ) );
            $print_style .= viceunf_customizer_value( 'viceunf_h' . $i . '_ltr_space', 'h' . $i, array( 'letter-spacing' ), array( 0, 0, 0 ), 'px' );
        }

        // Footer
        $footer_bg_color = get_theme_mod( 'viceunf_footer_bg_color', '#0e1422' );
        if ( ! empty( $footer_bg_color ) ) :
            $print_style .= ".dt_footer--one{
                    background-color: " . esc_attr( $footer_bg_color ) . ";
                }\n";
        endif;

        wp_add_inline_style( 'viceunf-framework', $print_style );
    }
endif;
add_action( 'wp_enqueue_scripts', 'viceunf_user_custom_style' );

/*=========================================
 Funciones de Estilo del Customizer (valores responsivos)
=========================================*/
function viceunf_media_range( $css_prop, $obj_value, $default, $media = 'desktop', $ext = '' ) {
    if ( is_string( $obj_value ) && is_array( json_decode( $obj_value, true ) ) ) {
        $json  = json_decode( $obj_value );
        $value = '';
        if ( 'desktop' === $media && $json->desktop != $default ) {
            if ( is_array( $css_prop ) ) {
                $value = $css_prop[0] . ': ' . esc_attr( $json->desktop ) . $ext . ';';
                if ( count( $css_prop ) > 1 ) {
                    $value .= $css_prop[1] . ': ' . esc_attr( $json->desktop ) . $ext . ';';
                }
            } else {
                $value = $css_prop . ': ' . esc_attr( $json->desktop ) . $ext . ';';
            }
        }
        if ( 'mobile' === $media && $json->mobile != $default ) {
            if ( is_array( $css_prop ) ) {
                $value = $css_prop[0] . ': ' . esc_attr( $json->mobile ) . $ext . ';';
                if ( count( $css_prop ) > 1 ) {
                    $value .= $css_prop[1] . ': ' . esc_attr( $json->mobile ) . $ext . ';';
                }
            } else {
                $value = $css_prop . ': ' . esc_attr( $json->mobile ) . $ext . ';';
            }
        }
        if ( 'tablet' === $media && $json->tablet != $default ) {
            if ( is_array( $css_prop ) ) {
                $value = $css_prop[0] . ': ' . esc_attr( $json->tablet ) . $ext . ';';
                if ( count( $css_prop ) > 1 ) {
                    $value .= $css_prop[1] . ': ' . esc_attr( $json->tablet ) . $ext . ';';
                }
            } else {
                $value = $css_prop . ': ' . esc_attr( $json->tablet ) . $ext . ';';
            }
        }
        return $value;
    }
    return false;
}

function viceunf_customizer_value( $control, $css_selector, $css_prop, array $default, $ext = '' ) {
    if ( $control ) {
        $control = get_theme_mod( $control );
        $return  = '';
        if ( is_string( $control ) && is_array( json_decode( $control, true ) ) ) {
            $desktop_val = viceunf_media_range( $css_prop, $control, $default[0], 'desktop', $ext );
            $tablet_val  = viceunf_media_range( $css_prop, $control, $default[1], 'tablet', $ext );
            $mobile_val  = viceunf_media_range( $css_prop, $control, $default[2], 'mobile', $ext );
            if ( ! empty( $desktop_val ) ) {
                $return  = $css_selector . ' { ';
                $return .= $desktop_val;
                $return .= '} ';
            }
            if ( ! empty( $tablet_val ) ) {
                $return .= '@media (max-width:768px) {';
                $return .= $css_selector . ' { ';
                $return .= $tablet_val;
                $return .= '} } ';
            }
            if ( ! empty( $mobile_val ) ) {
                $return .= '@media (max-width:480px) {';
                $return .= $css_selector . ' { ';
                $return .= $mobile_val;
                $return .= '} } ';
            }
        } else {
            if ( ! empty( $control ) && $control != $default[0] ) {
                $return .= $css_selector . ' { ';
                $return .= esc_attr( $control ) . $ext . ';';
                $return .= ' } ';
            }
        }
        return $return;
    }
    return false;
}

/*=========================================
 Suprimir aviso de templates de WooCommerce
=========================================*/
add_filter( 'woocommerce_show_admin_notice', function ( $show, $notice ) {
    if ( 'template_files' === $notice ) {
        return false;
    }
    return $show;
}, 10, 2 );
