<?php
// Salir si se accede directamente.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Carga todos los estilos y scripts del tema para el frontend.
 */
function viceunf_enqueue_assets() {
    // 1. Carga la hoja de estilos del tema padre (y la del hijo por dependencia).
    wp_enqueue_style(
        'viceunf-parent-theme-style',
        get_template_directory_uri() . '/style.css'
    );

    // 2. Carga condicional de scripts SOLO para la página principal.
    if ( is_front_page() ) {
        // Carga el script que manejará la carga asíncrona de los componentes.
        wp_enqueue_script(
            'viceunf-front-page-loader',
            get_stylesheet_directory_uri() . '/assets/js/front-page-loader.js',
            array(),    // Sin dependencias de JS, pero asume que plugins como Owl Carousel ya cargan jQuery.
            '1.0.2',    // Incrementa la versión para forzar la actualización de caché.
            true        // Cargar en el footer para no bloquear el renderizado.
        );

        // 3. Pasa datos de PHP a nuestro script de forma segura.
        // Esto le da a nuestro JS las URLs de la API que necesita.
        wp_localize_script(
            'viceunf-front-page-loader',
            'viceunf_front_obj',
            array(
                'rest_url_slider' => esc_url_raw( get_rest_url( null, 'wp/v2/slider?per_page=5&orderby=date&order=desc' ) ),
                // En el futuro, podrías añadir más URLs aquí para cargar otras secciones:
                // 'rest_url_events' => esc_url_raw( get_rest_url( null, 'wp/v2/evento?per_page=3' ) ),
            )
        );
    }
}
add_action( 'wp_enqueue_scripts', 'viceunf_enqueue_assets', 99 );


/**
 * Carga scripts JS solo en el panel de administración cuando sea necesario.
 * (Esta función permanece sin cambios, ya está correcta).
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
            '1.1.0',
            true
        );
        
        wp_localize_script( 'viceunf-admin-search', 'viceunf_ajax_obj', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'slider_metabox_nonce_action' ),
        ) );
    }
}
add_action( 'admin_enqueue_scripts', 'viceunf_admin_enqueue_scripts' );