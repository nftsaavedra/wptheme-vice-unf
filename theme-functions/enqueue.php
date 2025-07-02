<?php
// Salir si se accede directamente.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Carga todos los estilos y scripts del tema para el frontend.
 */
function viceunf_enqueue_assets() {
    // Carga la hoja de estilos del tema padre (y la del hijo por dependencia).
    wp_enqueue_style(
        'viceunf-parent-theme-style',
        get_template_directory_uri() . '/style.css'
    );

    // Carga condicional de scripts SOLO para la página principal.
    if ( is_front_page() ) {
        wp_enqueue_script(
            'viceunf-front-page-loader',
            get_stylesheet_directory_uri() . '/assets/js/front-page-loader.js',
            array(),    // Sin dependencias
            '1.0.0',    // Versión
            true        // Cargar en el footer
        );

        // Pasa la URL de la API REST a nuestro script del frontend.
        wp_localize_script(
            'viceunf-front-page-loader',
            'viceunf_front_obj',
            array(
                'rest_url_slider' => esc_url_raw( get_rest_url( null, 'wp/v2/slider?per_page=5&orderby=date&order=desc' ) ),
            )
        );
    }
}
add_action( 'wp_enqueue_scripts', 'viceunf_enqueue_assets', 99 );


/**
 * Carga scripts JS solo en el panel de administración para el slider.
 */
function viceunf_admin_enqueue_scripts( $hook ) {
    if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
        return;
    }

    if ( 'slider' === get_post_type() ) {
        wp_enqueue_script(
            'viceunf-admin-search',
            get_stylesheet_directory_uri() . '/assets/js/admin-search.js',
            array(), // Sin dependencias de jQuery
            '1.2.0', // Versión actualizada
            true
        );
        
        wp_localize_script( 'viceunf-admin-search', 'viceunf_ajax_obj', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'slider_metabox_nonce_action' ),
        ) );
    }
}
add_action( 'admin_enqueue_scripts', 'viceunf_admin_enqueue_scripts' );