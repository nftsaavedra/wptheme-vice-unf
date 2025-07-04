<?php
// Salir si se accede directamente.
if (! defined('ABSPATH')) exit;

// --- META BOX PARA SLIDERS (VERSIÓN FINAL) ---
add_action('add_meta_boxes', 'viceunf_add_slider_meta_box');
function viceunf_add_slider_meta_box()
{
    add_meta_box('slider_details_metabox', 'Datos del Slider', 'viceunf_slider_metabox_html', 'slider', 'normal', 'high');
}

function viceunf_slider_metabox_html($post)
{
    wp_nonce_field('slider_metabox_nonce_action', 'slider_metabox_nonce_name');

    // --- Obtener todos los valores guardados ---
    $subtitle = get_post_meta($post->ID, '_slider_subtitle_key', true);
    $description = get_post_meta($post->ID, '_slider_description_key', true);
    $text_align = get_post_meta($post->ID, '_slider_text_alignment_key', true);

    // Datos del Botón 1
    $btn1_text = get_post_meta($post->ID, '_slider_btn1_text_key', true);
    $link_type = get_post_meta($post->ID, '_slider_link_type_key', true);
    $link_url = get_post_meta($post->ID, '_slider_link_url_key', true);
    $link_content_id = get_post_meta($post->ID, '_slider_link_content_id_key', true);
    $link_content_title = $link_content_id ? get_the_title($link_content_id) : '';

    // Datos del Botón 2 y Video
    $btn2_text = get_post_meta($post->ID, '_slider_btn2_text_key', true);
    $btn2_link = get_post_meta($post->ID, '_slider_btn2_link_key', true);
    $video_link = get_post_meta($post->ID, '_slider_video_link_key', true);
?>
    <div class="slider-metabox-section">
        <h4>Contenido del Slider</h4>
        <div class="slider-field"><label for="slider_subtitle">Subtítulo</label><input type="text" id="slider_subtitle" name="slider_subtitle" value="<?php echo esc_attr($subtitle); ?>"></div>
        <div class="slider-field"><label for="slider_description">Descripción</label><textarea id="slider_description" name="slider_description" rows="3"><?php echo esc_textarea($description); ?></textarea></div>
        <div class="slider-field"><label for="slider_text_alignment">Alineación</label><select id="slider_text_alignment" name="slider_text_alignment">
                <option value="dt-text-left" <?php selected($text_align, 'dt-text-left'); ?>>Izquierda</option>
                <option value="dt-text-center" <?php selected($text_align, 'dt-text-center'); ?>>Centro</option>
                <option value="dt-text-right" <?php selected($text_align, 'dt-text-right'); ?>>Derecha</option>
            </select></div>
    </div>

    <div class="slider-metabox-section">
        <h4>Botón 1 (Principal)</h4>
        <div class="slider-field"><label for="slider_btn1_text">Texto Botón 1</label><input type="text" id="slider_btn1_text" name="slider_btn1_text" value="<?php echo esc_attr($btn1_text); ?>"></div>

        <div class="slider-field">
            <label for="slider_link_type">Tipo de Enlace para Botón 1</label>
            <select id="slider_link_type" name="slider_link_type">
                <option value="none" <?php selected($link_type, 'none'); ?>>Ninguno</option>
                <option value="url" <?php selected($link_type, 'url'); ?>>URL Personalizada</option>
                <option value="content" <?php selected($link_type, 'content'); ?>>Enlazar a Contenido (Buscar)</option>
            </select>
        </div>

        <div id="campo_url" class="slider-field" style="<?php echo ($link_type === 'url') ? 'display:block;' : 'display:none;'; ?>">
            <label for="slider_link_url">URL Personalizada</label>
            <input type="url" id="slider_link_url" name="slider_link_url" placeholder="https://ejemplo.com" value="<?php echo esc_url($link_url); ?>">
        </div>

        <div id="campo_contenido" class="slider-field" style="<?php echo ($link_type === 'content') ? 'display:block;' : 'display:none;'; ?>">
            <label>Buscar Entrada o Página</label>
            <div class="ajax-search-wrapper" data-action="viceunf_search_content">
                <div class="selected-item-view <?php echo ($link_content_id ? 'active' : ''); ?>">
                    <span class="selected-item-title"><?php echo esc_html($link_content_title); ?></span>
                    <button type="button" class="button-link-delete clear-selection-btn">&times;</button>
                </div>
                <div class="search-input-view <?php echo ($link_content_id ? '' : 'active'); ?>">
                    <input type="text" class="large-text ajax-search-input" placeholder="Escribe para buscar...">
                    <div class="ajax-search-results"></div>
                </div>
                <input type="hidden" class="ajax-search-hidden-id" id="slider_link_content_id" name="slider_link_content_id" value="<?php echo esc_attr($link_content_id); ?>">
            </div>
        </div>
    </div>

    <div class="slider-metabox-section">
        <h4>Botón 2 (Secundario)</h4>
        <div class="slider-field"><label for="slider_btn2_text">Texto Botón 2 (Opcional)</label><input type="text" id="slider_btn2_text" name="slider_btn2_text" value="<?php echo esc_attr($btn2_text); ?>"></div>
        <div class="slider-field"><label for="slider_btn2_link">Enlace Botón 2 (Opcional)</label><input type="url" id="slider_btn2_link" name="slider_btn2_link" value="<?php echo esc_url($btn2_link); ?>"></div>
    </div>
    <div class="slider-metabox-section">
        <h4>Botón de Video</h4>
        <div class="slider-field"><label for="slider_video_link">Enlace Video Lightbox (Opcional)</label><input type="url" id="slider_video_link" name="slider_video_link" value="<?php echo esc_url($video_link); ?>"></div>
    </div>
<?php
}

add_action('save_post', 'viceunf_save_slider_data');
function viceunf_save_slider_data($post_id)
{
    if (!isset($_POST['slider_metabox_nonce_name']) || !wp_verify_nonce($_POST['slider_metabox_nonce_name'], 'slider_metabox_nonce_action') || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !current_user_can('edit_post', $post_id) || !isset($_POST['post_type']) || 'slider' !== $_POST['post_type']) return;
    $campos_a_guardar = ['slider_subtitle' => '_slider_subtitle_key', 'slider_description' => '_slider_description_key', 'slider_text_alignment' => '_slider_text_alignment_key', 'slider_btn1_text' => '_slider_btn1_text_key', 'slider_link_type' => '_slider_link_type_key', 'slider_link_url' => '_slider_link_url_key', 'slider_link_content_id' => '_slider_link_content_id_key', 'slider_btn2_text' => '_slider_btn2_text_key', 'slider_btn2_link' => '_slider_btn2_link_key', 'slider_video_link' => '_slider_video_link_key'];
    foreach ($campos_a_guardar as $name_attribute => $meta_key) {
        if (isset($_POST[$name_attribute])) {
            $valor_sanitizado = in_array($name_attribute, ['slider_link_url', 'slider_btn2_link', 'slider_video_link']) ? esc_url_raw($_POST[$name_attribute]) : ($name_attribute === 'slider_description' ? sanitize_textarea_field($_POST[$name_attribute]) : sanitize_text_field($_POST[$name_attribute]));
            update_post_meta($post_id, $meta_key, $valor_sanitizado);
        }
    }
}

// --- META BOX PARA EVENTOS ---

add_action('add_meta_boxes', 'viceunf_add_evento_meta_box');
function viceunf_add_evento_meta_box()
{
    add_meta_box('evento_details_metabox', 'Detalles del Evento', 'viceunf_evento_metabox_html', 'evento', 'normal', 'high');
}

function viceunf_evento_metabox_html($post)
{
    wp_nonce_field('evento_metabox_nonce_action', 'evento_metabox_nonce_name');
    $event_date = get_post_meta($post->ID, '_evento_date_key', true);
    $event_start = get_post_meta($post->ID, '_evento_start_time_key', true);
    $event_end = get_post_meta($post->ID, '_evento_end_time_key', true);
    $event_address = get_post_meta($post->ID, '_evento_address_key', true);
?>
    <style>
        .evento-metabox-field {
            margin-bottom: 15px;
        }

        .evento-metabox-field label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .evento-metabox-field input {
            width: 100%;
            max-width: 300px;
        }
    </style>
    <div class="evento-metabox-field"><label for="evento_date">Fecha del Evento (Requerido)</label><input type="date" id="evento_date" name="evento_date" value="<?php echo esc_attr($event_date); ?>" required></div>
    <div class="evento-metabox-field"><label for="evento_start_time">Hora de Inicio (ej. 7:00)</label><input type="time" id="evento_start_time" name="evento_start_time" value="<?php echo esc_attr($event_start); ?>"></div>
    <div class="evento-metabox-field"><label for="evento_end_time">Hora de Fin (ej. 13:00)</label><input type="time" id="evento_end_time" name="evento_end_time" value="<?php echo esc_attr($event_end); ?>"></div>
    <div class="evento-metabox-field"><label for="evento_address">Lugar / Dirección del Evento</label><input type="text" id="evento_address" name="evento_address" value="<?php echo esc_attr($event_address); ?>" style="max-width:100%"></div>
<?php
}

add_action('save_post', 'viceunf_save_evento_data');
function viceunf_save_evento_data($post_id)
{
    if (!isset($_POST['evento_metabox_nonce_name']) || !wp_verify_nonce($_POST['evento_metabox_nonce_name'], 'evento_metabox_nonce_action') || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !current_user_can('edit_post', $post_id) || (isset($_POST['post_type']) && 'evento' !== $_POST['post_type'])) return;
    $campos = ['evento_date' => '_evento_date_key', 'evento_start_time' => '_evento_start_time_key', 'evento_end_time' => '_evento_end_time_key', 'evento_address' => '_evento_address_key'];
    foreach ($campos as $post_key => $meta_key) {
        if (isset($_POST[$post_key])) {
            update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$post_key]));
        }
    }
}


// --- META BOX PARA SOCIOS ---

add_action('add_meta_boxes', 'viceunf_add_socio_meta_box');
function viceunf_add_socio_meta_box()
{
    add_meta_box(
        'socio_details_metabox',
        'Detalles del Socio',
        'viceunf_socio_metabox_html',
        'socio', // Aplicar al CPT 'socio'
        'normal',
        'high'
    );
}

function viceunf_socio_metabox_html($post)
{
    wp_nonce_field('socio_metabox_nonce_action', 'socio_metabox_nonce_name');
    $socio_url = get_post_meta($post->ID, '_socio_url_key', true);
?>
    <p>
        <label for="socio_url" style="font-weight:bold; display:block; margin-bottom:5px;">Enlace Web del Socio (Opcional)</label>
        <input type="url" id="socio_url" name="socio_url" value="<?php echo esc_url($socio_url); ?>" style="width:100%;">
    </p>
<?php
}

add_action('save_post', 'viceunf_save_socio_data');
function viceunf_save_socio_data($post_id)
{
    if (!isset($_POST['socio_metabox_nonce_name']) || !wp_verify_nonce($_POST['socio_metabox_nonce_name'], 'socio_metabox_nonce_action') || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || !current_user_can('edit_post', $post_id) || (isset($_POST['post_type']) && 'socio' !== $_POST['post_type'])) return;

    if (isset($_POST['socio_url'])) {
        update_post_meta($post_id, '_socio_url_key', esc_url_raw($_POST['socio_url']));
    }
}
