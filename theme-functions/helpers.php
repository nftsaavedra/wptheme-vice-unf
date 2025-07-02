<?php
// Salir si se accede directamente.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Convierte una URL de video en una URL de embed con autoplay silencioso.
 */
function get_autoplay_embed_url( $url ) {
    if ( empty( $url ) ) return '';
    $embed_url = $url;
    if ( preg_match( '/(youtube\.com|youtu\.be)\/(watch\?v=|embed\/|v\/|)(.{11})/', $url, $matches ) ) {
        $embed_url = 'https://www.youtube.com/embed/' . $matches[3] . '?autoplay=1&mute=1&rel=0';
    } elseif ( preg_match( '/(vimeo\.com)\/(video\/)?([0-9]+)/', $url, $matches ) ) {
        $embed_url = 'https://player.vimeo.com/video/' . $matches[3] . '?autoplay=1&muted=1';
    }
    return $embed_url;
}



/**
 * Expone los campos personalizados del Slider a la API REST de WordPress.
 */
add_action( 'rest_api_init', 'viceunf_registrar_campos_slider_api' );

function viceunf_registrar_campos_slider_api() {
    $campos_slider = [
        '_slider_subtitle_key', '_slider_description_key', '_slider_text_alignment_key',
        '_slider_btn1_text_key', '_slider_btn2_text_key', '_slider_btn2_link_key',
        '_slider_video_link_key', '_slider_link_type_key', '_slider_link_url_key',
        '_slider_link_content_id_key',
    ];

    foreach ($campos_slider as $campo) {
        register_rest_field('slider', $campo, [
            'get_callback' => 'viceunf_obtener_valor_meta_api',
        ]);
    }
    
    // Campo "calculado" para el enlace final del botón 1
    register_rest_field('slider', 'btn1_final_href', [
        'get_callback' => 'viceunf_get_slider_btn1_final_href',
    ]);
    
    // CAMPO NUEVO: Exponemos la URL de la imagen destacada de forma explícita
    register_rest_field('slider', 'featured_image_url', [
        'get_callback' => function($object) {
            if (!empty($object['featured_media'])) {
                return get_the_post_thumbnail_url($object['id'], 'full');
            }
            return false;
        },
    ]);
}

function viceunf_obtener_valor_meta_api($object, $field_name, $request) {
    return get_post_meta($object['id'], $field_name, true);
}

function viceunf_get_slider_btn1_final_href($object, $field_name, $request) {
    $link_type = get_post_meta($object['id'], '_slider_link_type_key', true);
    $link_url = get_post_meta($object['id'], '_slider_link_url_key', true);
    $link_content_id = get_post_meta($object['id'], '_slider_link_content_id_key', true);
    if ($link_type === 'url' && !empty($link_url)) { return esc_url($link_url); }
    if ($link_type === 'content' && !empty($link_content_id)) { return get_permalink($link_content_id); }
    return '';
}