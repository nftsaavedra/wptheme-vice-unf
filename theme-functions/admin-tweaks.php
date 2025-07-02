<?php
// Salir si se accede directamente.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Personaliza el pie de página del admin.
 */
add_filter('admin_footer_text', function() {
    return ''; // O 'Sitio administrado por...'
});

/**
 * Desactiva completamente la funcionalidad de comentarios.
 */
add_action('admin_init', function () {
    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url());
        exit;
    }
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
});
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);
add_filter('comments_array', '__return_empty_array', 10, 2);
add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
});
add_action('init', function () {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
});

/**
 * =================================================================
 * ENDPOINT AJAX PARA BÚSQUEDA DE CONTENIDO EN EL ADMIN
 * =================================================================
 */
add_action( 'wp_ajax_viceunf_search_content', 'viceunf_ajax_search_content_handler' );

function viceunf_ajax_search_content_handler() {
    // Seguridad: verificar el nonce
    check_ajax_referer( 'slider_metabox_nonce_action', 'nonce' );

    $search_term = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';

    if ( empty( $search_term ) ) {
        wp_send_json_error( 'Término de búsqueda vacío.' );
    }

    $query_args = array(
        'post_type'      => array( 'post', 'page' ),
        'posts_per_page' => 10,
        's'              => $search_term,
    );

    $results_query = new WP_Query( $query_args );
    $results = array();

    if ( $results_query->have_posts() ) {
        while ( $results_query->have_posts() ) {
            $results_query->the_post();
            $results[] = array(
                'id'    => get_the_ID(),
                'title' => get_the_title(),
                'type'  => get_post_type_object( get_post_type() )->labels->singular_name,
            );
        }
    }

    wp_send_json_success( $results );
}