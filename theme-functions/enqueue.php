<?php
// Salir si se accede directamente.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * =================================================================
 * 1. Carga de Estilos y Scripts para el Frontend (Standalone)
 * =================================================================
 */
function viceunf_enqueue_frontend_assets() {

    $theme_version = wp_get_theme()->get( 'Version' );
    $theme_uri     = get_stylesheet_directory_uri();

    // --- CSS Framework (grid, tipografía, botones, componentes) ---
    wp_enqueue_style(
        'viceunf-framework',
        $theme_uri . '/assets/css/framework.css',
        array(),
        $theme_version
    );

    wp_enqueue_style(
        'viceunf-core',
        $theme_uri . '/assets/css/core.css',
        array( 'viceunf-framework' ),
        $theme_version
    );

    // --- Vendor CSS ---
    wp_enqueue_style( 'viceunf-fontawesome', $theme_uri . '/assets/css/all.min.css', array(), '6.7.2' );
    wp_enqueue_style( 'viceunf-animate', $theme_uri . '/assets/vendors/css/animate.css', array(), '4.1.1' );
    wp_enqueue_style( 'viceunf-owl-carousel', $theme_uri . '/assets/vendors/css/owl.carousel.min.css', array(), '2.3.4' );
    wp_enqueue_style( 'viceunf-fancybox', $theme_uri . '/assets/vendors/css/jquery.fancybox.min.css', array(), '3.5.7' );

    // --- Theme Stylesheet (style.css — contiene custom overrides) ---
    wp_enqueue_style(
        'viceunf-style',
        get_stylesheet_uri(),
        array( 'viceunf-framework', 'viceunf-core', 'viceunf-fontawesome' ),
        $theme_version
    );

    // --- Vendor JS ---
    wp_enqueue_script( 'viceunf-owl-carousel', $theme_uri . '/assets/vendors/js/owl.carousel.js', array( 'jquery' ), '2.3.4', true );
    wp_enqueue_script( 'viceunf-appear', $theme_uri . '/assets/vendors/js/appear.js', array( 'jquery' ), '1.0.0', true );
    wp_enqueue_script( 'viceunf-wow', $theme_uri . '/assets/vendors/js/wow.min.js', array(), '1.1.3', true );
    wp_enqueue_script( 'viceunf-fancybox', $theme_uri . '/assets/vendors/js/jquery.fancybox.js', array( 'jquery' ), '3.5.7', true );
    wp_enqueue_script( 'viceunf-parallax', $theme_uri . '/assets/vendors/js/parallax.min.js', array( 'jquery' ), '1.5.0', true );
    wp_enqueue_script( 'viceunf-paroller', $theme_uri . '/assets/vendors/js/jquery.paroller.min.js', array( 'jquery' ), '1.4.6', true );

    // --- Theme JS ---
    wp_enqueue_script(
        'viceunf-theme',
        $theme_uri . '/assets/js/theme.js',
        array( 'jquery', 'viceunf-owl-carousel', 'viceunf-wow', 'viceunf-fancybox', 'viceunf-parallax', 'viceunf-paroller' ),
        $theme_version,
        true
    );

    wp_enqueue_script(
        'viceunf-custom',
        $theme_uri . '/assets/js/custom.js',
        array( 'jquery', 'viceunf-theme' ),
        $theme_version,
        true
    );

    // Comments reply script.
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'viceunf_enqueue_frontend_assets' );


/**
 * =================================================================
 * 2. Carga Centralizada de Estilos y Scripts para el Panel de Administración
 * =================================================================
 */
function viceunf_enqueue_admin_assets( $hook ) {
    // --- Carga Global ---
    wp_enqueue_style(
        'viceunf-fontawesome-admin',
        get_stylesheet_directory_uri() . '/assets/css/all.min.css',
        array(),
        '6.7.2'
    );

    // --- Definición de Páginas Relevantes ---
    $screen = get_current_screen();
    $is_options_page           = ( 'toplevel_page_viceunf_theme_options' == $hook );
    $is_slider_page            = ( $screen && 'slider' === $screen->post_type );
    $is_reglamento_page        = ( $screen && 'reglamento' === $screen->post_type );
    $is_reglamento_category_page = ( $screen && 'categoria_reglamento' === $screen->taxonomy );

    // --- Carga para Sliders y Página de Opciones ---
    if ( $is_options_page || $is_slider_page ) {
        wp_enqueue_style( 'viceunf-admin-options-style', get_stylesheet_directory_uri() . '/assets/css/admin-options.css' );
        wp_enqueue_script( 'viceunf-admin-search', get_stylesheet_directory_uri() . '/assets/js/admin-search.js', array(), true );
        wp_localize_script( 'viceunf-admin-search', 'viceunf_ajax_obj', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'slider_metabox_nonce_action' ),
        ) );
    }

    // --- Carga específica para Página de Opciones ---
    if ( $is_options_page ) {
        wp_enqueue_media();
        wp_enqueue_script(
            'viceunf-admin-options-manager',
            get_stylesheet_directory_uri() . '/assets/js/admin-options-manager.js',
            array( 'viceunf-admin-search' ),
            '1.0.1',
            true
        );
    }

    // --- Carga de Estilos Generales para nuestros Meta-Boxes ---
    if ( $is_slider_page || $is_reglamento_page || $is_reglamento_category_page ) {
        wp_enqueue_style( 'viceunf-admin-styles', get_stylesheet_directory_uri() . '/assets/css/admin-style.css' );
    }

    // --- INICIO: LÓGICA DE CARGA PARA REGLAMENTOS Y CATEGORÍAS ---
    if ( $is_reglamento_page || $is_reglamento_category_page ) {
        $main_script_dependencies = array();
        if ( $is_reglamento_category_page ) {
            wp_enqueue_style( 'wp-color-picker' );
            $main_script_dependencies[] = 'wp-color-picker';
        }
        wp_enqueue_script(
            'viceunf-admin-main',
            get_stylesheet_directory_uri() . '/assets/js/admin-main.js',
            $main_script_dependencies,
            '1.0.2',
            true
        );
    }
    // --- FIN: LÓGICA DE CARGA PARA REGLAMENTOS ---
}
add_action( 'admin_enqueue_scripts', 'viceunf_enqueue_admin_assets' );
