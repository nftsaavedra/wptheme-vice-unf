<?php
// Salir si se accede directamente.
if (! defined('ABSPATH')) exit;

/**
 * Convierte una URL de video en una URL de embed con autoplay silencioso.
 */
function get_autoplay_embed_url($url)
{
    if (empty($url)) return '';
    $embed_url = $url;
    if (preg_match('/(youtube\.com|youtu\.be)\/(watch\?v=|embed\/|v\/|)(.{11})/', $url, $matches)) {
        $embed_url = 'https://www.youtube.com/embed/' . $matches[3] . '?autoplay=1&mute=1&rel=0';
    } elseif (preg_match('/(vimeo\.com)\/(video\/)?([0-9]+)/', $url, $matches)) {
        $embed_url = 'https://player.vimeo.com/video/' . $matches[3] . '?autoplay=1&muted=1';
    }
    return $embed_url;
}



/**
 * Expone los campos personalizados del Slider a la API REST de WordPress.
 */
add_action('rest_api_init', 'viceunf_registrar_campos_slider_api');

function viceunf_registrar_campos_slider_api()
{
    $campos_slider = [
        '_slider_subtitle_key',
        '_slider_description_key',
        '_slider_text_alignment_key',
        '_slider_btn1_text_key',
        '_slider_btn2_text_key',
        '_slider_btn2_link_key',
        '_slider_video_link_key',
        '_slider_link_type_key',
        '_slider_link_url_key',
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
        'get_callback' => function ($object) {
            if (!empty($object['featured_media'])) {
                return get_the_post_thumbnail_url($object['id'], 'full');
            }
            return false;
        },
    ]);
}

function viceunf_obtener_valor_meta_api($object, $field_name, $request)
{
    return get_post_meta($object['id'], $field_name, true);
}

function viceunf_get_slider_btn1_final_href($object, $field_name, $request)
{
    $link_type = get_post_meta($object['id'], '_slider_link_type_key', true);
    $link_url = get_post_meta($object['id'], '_slider_link_url_key', true);
    $link_content_id = get_post_meta($object['id'], '_slider_link_content_id_key', true);
    if ($link_type === 'url' && !empty($link_url)) {
        return esc_url($link_url);
    }
    if ($link_type === 'content' && !empty($link_content_id)) {
        return get_permalink($link_content_id);
    }
    return '';
}

/**
 * =================================================================
 * SOBREESCRIBIR LA FUNCIÓN DE BREADCRUMBS DEL TEMA PADRE
 * =================================================================
 * 
 */
if (! function_exists('softme_page_header_breadcrumbs')) :
    function softme_page_header_breadcrumbs()
    {
        global $post;
        $homeLink = home_url();

        if (is_home() || is_front_page()) {
            // Modificación: Cambiar 'Home' por 'Inicio'
            echo '<li class="breadcrumb-item"><a href="' . esc_url($homeLink) . '">' . __('Inicio', 'viceunf') . '</a></li>';
            echo '<li class="breadcrumb-item active">';
            echo single_post_title();
            echo '</li>';
        } else {
            // Modificación: Cambiar 'Home' por 'Inicio'
            echo '<li class="breadcrumb-item"><a href="' . esc_url($homeLink) . '">' . __('Inicio', 'viceunf') . '</a></li>';

            // Aquí puedes seguir modificando el resto de las condiciones...
            if (is_category()) {
                echo '<li class="breadcrumb-item active"><a href="' . esc_url(softme_page_url()) . '">' . __('Archivo por categoría', 'viceunf') . ' "' . single_cat_title('', false) . '"</a></li>';
            } elseif (is_day()) {
                echo '<li class="breadcrumb-item active"><a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . get_the_time('Y') . '</a></li>';
                echo '<li class="breadcrumb-item active"><a href="' . esc_url(get_month_link(get_the_time('Y'), get_the_time('m'))) . '">' . get_the_time('F') . '</a></li>';
                echo '<li class="breadcrumb-item active"><a href="' . esc_url(softme_page_url()) . '">' . get_the_time('d') . '</a></li>';
            } elseif (is_month()) {
                echo '<li class="breadcrumb-item active"><a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . get_the_time('Y') . '</a></li>';
                echo '<li class="breadcrumb-item active"><a href="' . esc_url(softme_page_url()) . '">' . get_the_time('F') . '</a></li>';
            } elseif (is_year()) {
                echo '<li class="breadcrumb-item active"><a href="' . esc_url(softme_page_url()) . '">' . get_the_time('Y') . '</a></li>';
            } elseif (is_single() && !is_attachment()) {
                if (get_post_type() != 'post') {
                    $post_type = get_post_type_object(get_post_type());
                    $slug = $post_type->rewrite;
                    // Ejemplo de modificación: Añadir el nombre del tipo de contenido (ej. "Eventos")
                    echo '<li class="breadcrumb-item active"><a href="' . esc_url(home_url('/' . $slug['slug'] . '/')) . '">' . esc_html($post_type->labels->singular_name) . '</a></li>';
                    echo '<li class="breadcrumb-item active">' . get_the_title() . '</li>';
                } else {
                    $cat = get_the_category();
                    if (!empty($cat)) {
                        $cat = $cat[0];
                        echo '<li class="breadcrumb-item">' . get_category_parents($cat, TRUE, '</li><li class="breadcrumb-item">') . '</li>';
                    }
                    echo '<li class="breadcrumb-item active">' . get_the_title() . '</li>';
                }
            } elseif (is_page() && $post->post_parent) {
                $parent_id  = $post->post_parent;
                $breadcrumbs = array();
                while ($parent_id) {
                    $page = get_page($parent_id);
                    $breadcrumbs[] = '<li class="breadcrumb-item"><a href="' . esc_url(get_permalink($page->ID)) . '">' . get_the_title($page->ID) . '</a></li>';
                    $parent_id  = $page->post_parent;
                }
                $breadcrumbs = array_reverse($breadcrumbs);
                foreach ($breadcrumbs as $crumb) {
                    echo $crumb;
                }
                echo '<li class="breadcrumb-item active">' . get_the_title() . '</li>';
            } elseif (is_search()) {
                echo '<li class="breadcrumb-item active">' . __('Resultados para', 'viceunf') . ' "' . get_search_query() . '"</li>';
            } elseif (is_404()) {
                echo '<li class="breadcrumb-item active">' . __('Error 404', 'viceunf') . '</li>';
            } else {
                echo '<li class="breadcrumb-item active">' . get_the_title() . '</li>';
            }
        }
    }
endif;
