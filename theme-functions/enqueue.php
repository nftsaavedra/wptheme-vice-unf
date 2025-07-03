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
    if ('post.php' !== $hook && 'post-new.php' !== $hook) {
        return;
    }
    if ('slider' === get_post_type()) {
        wp_enqueue_style('viceunf-admin-styles', get_stylesheet_directory_uri() . '/assets/css/admin-style.css', array(), '1.0.0');
        wp_enqueue_script('viceunf-admin-search', get_stylesheet_directory_uri() . '/assets/js/admin-search.js', array(), '1.2.0', true);
        wp_localize_script('viceunf-admin-search', 'viceunf_ajax_obj', array('ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('slider_metabox_nonce_action')));
    }
}
add_action('admin_enqueue_scripts', 'viceunf_admin_enqueue_scripts');
