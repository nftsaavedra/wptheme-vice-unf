<?php
// Salir si se accede directamente.
if (! defined('ABSPATH')) {
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
    // Cargamos nuestra versión local en TODAS las páginas del admin para asegurar la consistencia.
    wp_enqueue_style(
        'viceunf-fontawesome-admin',
        get_stylesheet_directory_uri() . '/assets/css/all.min.css',
        array(),
        '6.7.2'
    );

    // --- Carga Condicional para las páginas que usan nuestros componentes ---
    // Verificamos si estamos en la página de opciones O en la página de edición de un slider.
    $is_options_page = ('toplevel_page_viceunf_theme_options' == $hook);
    $is_slider_page = (('post.php' == $hook || 'post-new.php' == $hook) && 'slider' === get_post_type());

    if ($is_options_page || $is_slider_page) {

        // Estilos para el componente de búsqueda y la página de opciones.
        wp_enqueue_style('viceunf-admin-options-style', get_stylesheet_directory_uri() . '/assets/css/admin-options.css');

        // Script de búsqueda AJAX (se carga en ambas páginas).
        wp_enqueue_script('viceunf-admin-search', get_stylesheet_directory_uri() . '/assets/js/admin-search.js', ['jquery'], true);

        // Pasamos los datos necesarios al script (solo se necesita una vez).
        wp_localize_script('viceunf-admin-search', 'viceunf_ajax_obj', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('slider_metabox_nonce_action')
        ]);
    }

    // Si estamos en la página de sliders, cargamos sus estilos adicionales.
    if ($is_slider_page) {
        wp_enqueue_style('viceunf-admin-styles', get_stylesheet_directory_uri() . '/assets/css/admin-style.css');
    }
}
add_action('admin_enqueue_scripts', 'viceunf_enqueue_admin_assets');
