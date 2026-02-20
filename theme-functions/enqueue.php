<?php
// Salir si se accede directamente.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * =================================================================
 * 1. Carga de Estilos para el Frontend
 * =================================================================
 */
function viceunf_enqueue_frontend_assets()
{
    wp_dequeue_style('font-awesome');
    wp_deregister_style('font-awesome');

    wp_enqueue_style(
        'viceunf-fontawesome',
        get_stylesheet_directory_uri() . '/assets/css/all.min.css',
        array(),
        '6.7.2'
    );

    wp_enqueue_style(
        'viceunf-parent-theme-style',
        get_template_directory_uri() . '/style.css',
        array('viceunf-fontawesome'),
        wp_get_theme( 'softme' )->get( 'Version' )
    );
}
add_action('wp_enqueue_scripts', 'viceunf_enqueue_frontend_assets', 100);


/**
 * =================================================================
 * 2. Carga Centralizada de Estilos y Scripts para el Panel de Administración
 * =================================================================
 */
function viceunf_enqueue_admin_assets($hook)
{
    // --- Carga Global ---
    wp_enqueue_style(
        'viceunf-fontawesome-admin',
        get_stylesheet_directory_uri() . '/assets/css/all.min.css',
        array(),
        '6.7.2'
    );

    // --- Definición de Páginas Relevantes ---
    $screen = get_current_screen();
    $is_options_page = ('toplevel_page_viceunf_theme_options' == $hook);
    $is_slider_page = ($screen && 'slider' === $screen->post_type);
    $is_reglamento_page = ($screen && 'reglamento' === $screen->post_type);
    // --- CORRECCIÓN ---
    // Esta es la forma más robusta de detectar tanto la página de "Añadir" como la de "Editar" categoría.
    $is_reglamento_category_page = ($screen && 'categoria_reglamento' === $screen->taxonomy);

    // --- Carga para Sliders y Página de Opciones ---
    if ($is_options_page || $is_slider_page) {
        wp_enqueue_style('viceunf-admin-options-style', get_stylesheet_directory_uri() . '/assets/css/admin-options.css');
        wp_enqueue_script('viceunf-admin-search', get_stylesheet_directory_uri() . '/assets/js/admin-search.js', [], true);
        wp_localize_script('viceunf-admin-search', 'viceunf_ajax_obj', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('slider_metabox_nonce_action')
        ]);
    }

    // --- Carga específica para Página de Opciones ---
    if ($is_options_page) {
        wp_enqueue_media();
        wp_enqueue_script(
            'viceunf-admin-options-manager',
            get_stylesheet_directory_uri() . '/assets/js/admin-options-manager.js',
            ['viceunf-admin-search'],
            '1.0.1',
            true
        );
    }

    // --- Carga de Estilos Generales para nuestros Meta-Boxes ---
    if ($is_slider_page || $is_reglamento_page || $is_reglamento_category_page) {
        wp_enqueue_style('viceunf-admin-styles', get_stylesheet_directory_uri() . '/assets/css/admin-style.css');
    }

    // --- INICIO: LÓGICA DE CARGA PARA REGLAMENTOS Y CATEGORÍAS ---
    if ($is_reglamento_page || $is_reglamento_category_page) {

        $main_script_dependencies = [];

        if ($is_reglamento_category_page) {
            wp_enqueue_style('wp-color-picker');
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
add_action('admin_enqueue_scripts', 'viceunf_enqueue_admin_assets');
