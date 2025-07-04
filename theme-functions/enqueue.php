<?php
// Salir si se accede directamente.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Carga todos los estilos y scripts del tema para el frontend.
 */
function viceunf_enqueue_assets()
{
    wp_enqueue_style('viceunf-parent-theme-style', get_template_directory_uri() . '/style.css');
}
add_action('wp_enqueue_scripts', 'viceunf_enqueue_assets', 99);

/**
 * Carga scripts y estilos SOLO en el panel de administración para el slider.
 */
function viceunf_admin_enqueue_scripts($hook)
{
    // Solo se ejecuta en las páginas de edición de posts.
    if ('post.php' !== $hook && 'post-new.php' !== $hook) {
        return;
    }

    // Solo se ejecuta si el tipo de post es 'slider'.
    if ('slider' === get_post_type()) {
        // Estilos generales del meta box
        wp_enqueue_style('viceunf-admin-styles', get_stylesheet_directory_uri() . '/assets/css/admin-style.css', array(), '1.0.0');

        // ¡NUEVO! Carga los estilos de nuestro componente de búsqueda.
        wp_enqueue_style('viceunf-admin-options-style', get_stylesheet_directory_uri() . '/assets/css/admin-options.css', [], '1.0.3');

        // Carga el script de búsqueda reutilizable.
        wp_enqueue_script('viceunf-admin-search', get_stylesheet_directory_uri() . '/assets/js/admin-search.js', array(), '1.2.0', true);

        // Proporciona los datos necesarios al script (URL de AJAX y clave de seguridad).
        wp_localize_script('viceunf-admin-search', 'viceunf_ajax_obj', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('slider_metabox_nonce_action')
        ));
    }
}
add_action('admin_enqueue_scripts', 'viceunf_admin_enqueue_scripts');
