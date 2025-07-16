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
        '6.7.2' // Usamos la versión que especificaste.
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
 * Esta es la ÚNICA función que gestiona los assets del admin, evitando duplicados.
 *
 * @param string $hook El identificador de la página de administración actual.
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

    // --- Carga Condicional para las páginas que usan nuestros componentes ---
    $is_options_page = ('toplevel_page_viceunf_theme_options' == $hook);
    $is_slider_page = (('post.php' == $hook || 'post-new.php' == $hook) && 'slider' === get_post_type());

    if ($is_options_page || $is_slider_page) {

        // Estilos para el componente de búsqueda y la página de opciones.
        wp_enqueue_style('viceunf-admin-options-style', get_stylesheet_directory_uri() . '/assets/css/admin-options.css');

        // Script de búsqueda AJAX (se carga en ambas páginas).
        wp_enqueue_script('viceunf-admin-search', get_stylesheet_directory_uri() . '/assets/js/admin-search.js', [], true);

        // Pasamos los datos necesarios al script (solo se necesita una vez).
        wp_localize_script('viceunf-admin-search', 'viceunf_ajax_obj', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('slider_metabox_nonce_action')
        ]);
    }

    // --- NUEVO: Carga específica para la PÁGINA DE OPCIONES ---
    // Estos scripts solo son necesarios para el repetidor y el selector de imágenes.
    if ($is_options_page) {

        // 1. Encolamos los scripts de medios de WordPress, necesarios para el selector de imágenes.
        wp_enqueue_media();

        // 2. Encolamos nuestro nuevo gestor de opciones (sin jQuery).
        //    Depende de 'viceunf-admin-search' porque necesita que la función initializeAjaxSearch() exista.
        wp_enqueue_script(
            'viceunf-admin-options-manager',
            get_stylesheet_directory_uri() . '/assets/js/admin-options-manager.js',
            ['viceunf-admin-search'], // Dependencia
            '1.0.1', // Incrementamos la versión
            true // Cargar en el footer
        );
    }


    // Si estamos en la página de sliders, cargamos sus estilos adicionales.
    if ($is_slider_page) {
        wp_enqueue_style('viceunf-admin-styles', get_stylesheet_directory_uri() . '/assets/css/admin-style.css');
    }
}
add_action('admin_enqueue_scripts', 'viceunf_enqueue_admin_assets');
