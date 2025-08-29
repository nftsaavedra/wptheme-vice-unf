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
    // Primero, con prioridad alta (100), nos aseguramos de que el estilo del tema padre sea eliminado.
    wp_dequeue_style('font-awesome');
    wp_deregister_style('font-awesome');

    // Ahora, cargamos NUESTRA versión local desde el tema hijo.
    wp_enqueue_style(
        'viceunf-fontawesome',
        get_stylesheet_directory_uri() . '/assets/css/all.min.css',
        array(),
        '6.7.2'
    );

    // Carga la hoja de estilos principal del tema padre.
    wp_enqueue_style(
        'viceunf-parent-theme-style',
        get_template_directory_uri() . '/style.css',
        array('viceunf-fontawesome')
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
    // --- Carga Global en el Admin: Font Awesome ---
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
    $is_reglamento_category_page = ($screen && 'edit-categoria_reglamento' === $screen->id && 'term' === $screen->base);

    // --- Carga para Sliders y Página de Opciones ---
    if ($is_options_page || $is_slider_page) {
        wp_enqueue_style('viceunf-admin-options-style', get_stylesheet_directory_uri() . '/assets/css/admin-options.css');
        wp_enqueue_script('viceunf-admin-search', get_stylesheet_directory_uri() . '/assets/js/admin-search.js', [], true);
        wp_localize_script('viceunf-admin-search', 'viceunf_ajax_obj', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('slider_metabox_nonce_action')
        ]);
    }

    // --- Carga específica para la Página de Opciones ---
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
    if ($is_slider_page || $is_reglamento_page) {
        wp_enqueue_style('viceunf-admin-styles', get_stylesheet_directory_uri() . '/assets/css/admin-style.css');
    }

    // --- INICIO: LÓGICA DE CARGA PARA REGLAMENTOS Y CATEGORÍAS ---

    // Si estamos en la página de Reglamentos O en la de sus categorías...
    if ($is_reglamento_page || $is_reglamento_category_page) {

        // Preparamos las dependencias para nuestro script principal.
        $main_script_dependencies = [];

        // Si estamos específicamente en la página de categorías...
        if ($is_reglamento_category_page) {
            // ...cargamos los estilos del selector de color.
            wp_enqueue_style('wp-color-picker');
            // ...y añadimos el script de 'wp-color-picker' como una dependencia.
            $main_script_dependencies[] = 'wp-color-picker';
        }

        // Cargamos nuestro script principal del admin con sus dependencias correspondientes.
        wp_enqueue_script(
            'viceunf-admin-main',
            get_stylesheet_directory_uri() . '/assets/js/admin-main.js',
            $main_script_dependencies,
            '1.0.1', // Versión incrementada
            true
        );
    }
    // --- FIN: LÓGICA DE CARGA PARA REGLAMENTOS ---
}
add_action('admin_enqueue_scripts', 'viceunf_enqueue_admin_assets');
