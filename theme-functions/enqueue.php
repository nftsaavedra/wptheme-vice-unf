<?php
// Salir si se accede directamente.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * =================================================================
 * 1. Carga de Estilos y Scripts para el Frontend
 * =================================================================
 */
function viceunf_enqueue_frontend_assets()
{
    // --- GESTIÓN DE FONT AWESOME (CONTROL TOTAL) ---
    // Con prioridad 100, nos aseguramos de que esto se ejecute después del tema padre.
    // Primero, removemos la versión de Font Awesome del tema padre para evitar duplicados.
    wp_dequeue_style('font-awesome');
    wp_deregister_style('font-awesome');

    // Ahora, cargamos NUESTRA versión local desde el tema hijo.
    wp_enqueue_style(
        'viceunf-fontawesome',
        get_stylesheet_directory_uri() . '/assets/css/all.min.css',
        array(),
        '6.7.2' // Actualiza esto si cambias la versión.
    );

    // Carga la hoja de estilos principal del tema padre.
    wp_enqueue_style(
        'viceunf-parent-theme-style',
        get_template_directory_uri() . '/style.css',
        array('viceunf-fontawesome') // Hacemos que dependa de nuestra versión para el orden correcto.
    );
}
add_action('wp_enqueue_scripts', 'viceunf_enqueue_frontend_assets', 100);


/**
 * =================================================================
 * 2. Carga Centralizada de Estilos y Scripts para el Panel de Administración
 * =================================================================
 * Esta es la ÚNICA función que gestiona los assets del admin.
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

    // --- Carga Condicional para la Página de Opciones del Tema ---
    if ('toplevel_page_viceunf_theme_options' == $hook) {

        // Estilos para la página de opciones y sus componentes.
        wp_enqueue_style('viceunf-admin-options-style', get_stylesheet_directory_uri() . '/assets/css/admin-options.css');

        // Script para la búsqueda AJAX de páginas.
        wp_enqueue_script('viceunf-admin-search', get_stylesheet_directory_uri() . '/assets/js/admin-search.js', ['jquery'], true);

        // Pasamos los datos necesarios a nuestro script de búsqueda.
        wp_localize_script('viceunf-admin-search', 'viceunf_ajax_obj', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('slider_metabox_nonce_action')
        ]);
    }

    // --- Carga Condicional para la Página de Edición de un Slider ---
    if (('post.php' == $hook || 'post-new.php' == $hook) && 'slider' === get_post_type()) {

        // Estilos específicos para el meta box del slider.
        wp_enqueue_style('viceunf-admin-styles', get_stylesheet_directory_uri() . '/assets/css/admin-style.css');

        // Estilos reutilizables para el componente de búsqueda.
        wp_enqueue_style('viceunf-admin-options-style', get_stylesheet_directory_uri() . '/assets/css/admin-options.css');

        // Script de búsqueda AJAX reutilizable.
        wp_enqueue_script('viceunf-admin-search', get_stylesheet_directory_uri() . '/assets/js/admin-search.js', ['jquery'], true);

        // Pasamos los datos necesarios al script.
        wp_localize_script('viceunf-admin-search', 'viceunf_ajax_obj', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('slider_metabox_nonce_action')
        ]);
    }
}
add_action('admin_enqueue_scripts', 'viceunf_enqueue_admin_assets');
