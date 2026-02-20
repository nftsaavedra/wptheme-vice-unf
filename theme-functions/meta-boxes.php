<?php
// Salir si se accede directamente.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Helper DRY: Valida si es seguro guardar meta data.
 *
 * @param int    $post_id      ID del post.
 * @param string $nonce_name   Nombre del campo nonce en $_POST.
 * @param string $nonce_action Acción del nonce.
 * @param string $post_type    Tipo de post esperado.
 * @return bool True si se puede guardar, false si no.
 */
function viceunf_can_save_meta( $post_id, $nonce_name, $nonce_action, $post_type = '' ) {
    if ( ! isset( $_POST[ $nonce_name ] ) || ! wp_verify_nonce( $_POST[ $nonce_name ], $nonce_action ) ) {
        return false;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return false;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return false;
    }
    if ( ! empty( $post_type ) && isset( $_POST['post_type'] ) && $post_type !== $_POST['post_type'] ) {
        return false;
    }
    return true;
}

// --- META BOX PARA SLIDERS (VERSIÓN FINAL) ---
add_action('add_meta_boxes', 'viceunf_add_slider_meta_box');
function viceunf_add_slider_meta_box()
{
    add_meta_box('slider_details_metabox', 'Datos del Slider', 'viceunf_slider_metabox_html', 'slider', 'normal', 'high');
}

function viceunf_slider_metabox_html($post)
{
    wp_nonce_field('slider_metabox_nonce_action', 'slider_metabox_nonce_name');

    $subtitle = get_post_meta($post->ID, '_slider_subtitle_key', true);
    $description = get_post_meta($post->ID, '_slider_description_key', true);
    $text_align = get_post_meta($post->ID, '_slider_text_alignment_key', true);
    $btn1_text = get_post_meta($post->ID, '_slider_btn1_text_key', true);
    $link_type = get_post_meta($post->ID, '_slider_link_type_key', true);
    $link_url = get_post_meta($post->ID, '_slider_link_url_key', true);
    $link_content_id = get_post_meta($post->ID, '_slider_link_content_id_key', true);
    $link_content_title = $link_content_id ? get_the_title($link_content_id) : '';
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
            <label for="slider_link_type">Tipo de Enlace</label>
            <select id="slider_link_type" name="slider_link_type">
                <option value="none" <?php selected($link_type, 'none'); ?>>Ninguno</option>
                <option value="url" <?php selected($link_type, 'url'); ?>>URL Personalizada</option>
                <option value="content" <?php selected($link_type, 'content'); ?>>Enlazar a Contenido (Buscar)</option>
            </select>
        </div>

        <div id="campo_url" class="slider-field conditional-field" style="display:none;">
            <label for="slider_link_url">URL Personalizada</label>
            <input type="url" id="slider_link_url" name="slider_link_url" placeholder="https://ejemplo.com" value="<?php echo esc_url($link_url); ?>">
        </div>

        <div id="campo_contenido" class="slider-field conditional-field" style="display:none;">
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
        <div class="slider-field"><label for="slider_btn2_text">Texto (Opcional)</label><input type="text" id="slider_btn2_text" name="slider_btn2_text" value="<?php echo esc_attr($btn2_text); ?>"></div>
        <div class="slider-field"><label for="slider_btn2_link">Enlace (Opcional)</label><input type="url" id="slider_btn2_link" name="slider_btn2_link" value="<?php echo esc_url($btn2_link); ?>"></div>
    </div>
    <div class="slider-metabox-section">
        <h4>Botón de Video</h4>
        <div class="slider-field"><label for="slider_video_link">Enlace Video (Opcional)</label><input type="url" id="slider_video_link" name="slider_video_link" value="<?php echo esc_url($video_link); ?>"></div>
    </div>
<?php
}

add_action( 'save_post', 'viceunf_save_slider_data' );
function viceunf_save_slider_data( $post_id ) {
    if ( ! viceunf_can_save_meta( $post_id, 'slider_metabox_nonce_name', 'slider_metabox_nonce_action', 'slider' ) ) {
        return;
    }
    $campos_a_guardar = [
        'slider_subtitle'       => '_slider_subtitle_key',
        'slider_description'    => '_slider_description_key',
        'slider_text_alignment' => '_slider_text_alignment_key',
        'slider_btn1_text'      => '_slider_btn1_text_key',
        'slider_link_type'      => '_slider_link_type_key',
        'slider_link_url'       => '_slider_link_url_key',
        'slider_link_content_id'=> '_slider_link_content_id_key',
        'slider_btn2_text'      => '_slider_btn2_text_key',
        'slider_btn2_link'      => '_slider_btn2_link_key',
        'slider_video_link'     => '_slider_video_link_key',
    ];
    foreach ( $campos_a_guardar as $name_attribute => $meta_key ) {
        if ( isset( $_POST[ $name_attribute ] ) ) {
            $valor_sanitizado = in_array( $name_attribute, [ 'slider_link_url', 'slider_btn2_link', 'slider_video_link' ], true )
                ? esc_url_raw( $_POST[ $name_attribute ] )
                : ( 'slider_description' === $name_attribute
                    ? sanitize_textarea_field( $_POST[ $name_attribute ] )
                    : sanitize_text_field( $_POST[ $name_attribute ] ) );
            update_post_meta( $post_id, $meta_key, $valor_sanitizado );
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
    <div class="evento-metabox-field"><label for="evento_date">Fecha del Evento (Requerido)</label><input type="date" id="evento_date" name="evento_date" value="<?php echo esc_attr($event_date); ?>" required></div>
    <div class="evento-metabox-field"><label for="evento_start_time">Hora de Inicio (ej. 7:00)</label><input type="time" id="evento_start_time" name="evento_start_time" value="<?php echo esc_attr($event_start); ?>"></div>
    <div class="evento-metabox-field"><label for="evento_end_time">Hora de Fin (ej. 13:00)</label><input type="time" id="evento_end_time" name="evento_end_time" value="<?php echo esc_attr($event_end); ?>"></div>
    <div class="evento-metabox-field"><label for="evento_address">Lugar / Dirección del Evento</label><input type="text" id="evento_address" name="evento_address" value="<?php echo esc_attr($event_address); ?>"></div>
<?php
}

add_action( 'save_post', 'viceunf_save_evento_data' );
function viceunf_save_evento_data( $post_id ) {
    if ( ! viceunf_can_save_meta( $post_id, 'evento_metabox_nonce_name', 'evento_metabox_nonce_action', 'evento' ) ) {
        return;
    }
    $campos = [
        'evento_date'       => '_evento_date_key',
        'evento_start_time' => '_evento_start_time_key',
        'evento_end_time'   => '_evento_end_time_key',
        'evento_address'    => '_evento_address_key',
    ];
    foreach ( $campos as $post_key => $meta_key ) {
        if ( isset( $_POST[ $post_key ] ) ) {
            update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[ $post_key ] ) );
        }
    }
}

// --- META BOX PARA SOCIOS ---
add_action('add_meta_boxes', 'viceunf_add_socio_meta_box');
function viceunf_add_socio_meta_box()
{
    add_meta_box('socio_details_metabox', 'Detalles del Socio', 'viceunf_socio_metabox_html', 'socio', 'normal', 'high');
}

function viceunf_socio_metabox_html($post)
{
    wp_nonce_field('socio_metabox_nonce_action', 'socio_metabox_nonce_name');
    $socio_url = get_post_meta($post->ID, '_socio_url_key', true);
?>
    <p class="socio-field">
        <label for="socio_url">Enlace Web del Socio (Opcional)</label>
        <input type="url" id="socio_url" name="socio_url" value="<?php echo esc_url($socio_url); ?>">
    </p>
<?php
}

add_action( 'save_post', 'viceunf_save_socio_data' );
function viceunf_save_socio_data( $post_id ) {
    if ( ! viceunf_can_save_meta( $post_id, 'socio_metabox_nonce_name', 'socio_metabox_nonce_action', 'socio' ) ) {
        return;
    }
    if ( isset( $_POST['socio_url'] ) ) {
        update_post_meta( $post_id, '_socio_url_key', esc_url_raw( $_POST['socio_url'] ) );
    }
}

// --- INICIO: REGLAMENTOS Y CATEGORÍAS ---
// 1. Meta Box para el CPT 'reglamento'
add_action('add_meta_boxes', 'viceunf_add_reglamento_meta_box');
function viceunf_add_reglamento_meta_box()
{
    add_meta_box('reglamento_file_metabox', 'Archivo del Reglamento (Obligatorio)', 'viceunf_reglamento_metabox_html', 'reglamento', 'normal', 'high');
}

function viceunf_reglamento_metabox_html($post)
{
    wp_nonce_field('reglamento_metabox_nonce_action', 'reglamento_metabox_nonce_name');
    $source_type  = get_post_meta($post->ID, '_reglamento_source_type_key', true) ?: 'upload'; // 'upload' por defecto
    $file_id      = get_post_meta($post->ID, '_reglamento_file_id_key', true);
    $external_url = get_post_meta($post->ID, '_reglamento_external_url_key', true);
    $file_url     = wp_get_attachment_url($file_id);
    $file_name    = $file_url ? basename($file_url) : '';
?>
    <div id="reglamento-source-selector" class="viceunf-metabox-container">
        <div class="reglamento-field radio-buttons-as-tabs">
            <input type="radio" id="source_upload" name="reglamento_source_type" value="upload" <?php checked($source_type, 'upload'); ?>>
            <label for="source_upload">Subir Archivo</label>
            <input type="radio" id="source_external" name="reglamento_source_type" value="external" <?php checked($source_type, 'external'); ?>>
            <label for="source_external">Enlace Externo</label>
        </div>
        <div id="reglamento-upload-section" class="reglamento-section-wrapper conditional-field">
            <p>Seleccione el archivo (PDF, Word, etc.) correspondiente a este reglamento.</p>
            <input type="hidden" id="reglamento_file_id" name="reglamento_file_id" value="<?php echo esc_attr($file_id); ?>" required>
            <button type="button" class="button" id="upload_reglamento_button">Seleccionar o Subir Archivo</button>
            <button type="button" class="button button-secondary" id="remove_reglamento_button">Quitar Archivo</button>
            <div class="file-info">
                <?php if ($file_id && $file_url) : ?>
                    Archivo actual: <a href="<?php echo esc_url($file_url); ?>" target="_blank"><?php echo esc_html($file_name); ?></a>
                <?php else : ?>
                    No se ha seleccionado ningún archivo.
                <?php endif; ?>
            </div>
        </div>
        <div id="reglamento-external-section" class="reglamento-section-wrapper conditional-field">
            <p>Pegue la URL completa del documento externo (ej. un PDF en otro sitio web o en Google Drive).</p>
            <label for="reglamento_external_url"><strong>URL del Archivo:</strong></label><br>
            <input type="url" id="reglamento_external_url" name="reglamento_external_url" value="<?php echo esc_url($external_url); ?>" placeholder="https://ejemplo.com/documento.pdf" required>
        </div>
    </div>
<?php
}

add_action( 'save_post_reglamento', 'viceunf_save_reglamento_data' );
function viceunf_save_reglamento_data( $post_id ) {
    if ( ! viceunf_can_save_meta( $post_id, 'reglamento_metabox_nonce_name', 'reglamento_metabox_nonce_action' ) ) {
        return;
    }
    if ( isset( $_POST['reglamento_source_type'] ) ) {
        $source_type = sanitize_text_field( $_POST['reglamento_source_type'] );
        update_post_meta( $post_id, '_reglamento_source_type_key', $source_type );
        if ( 'upload' === $source_type ) {
            update_post_meta( $post_id, '_reglamento_file_id_key', sanitize_text_field( $_POST['reglamento_file_id'] ?? '' ) );
            delete_post_meta( $post_id, '_reglamento_external_url_key' );
        } elseif ( 'external' === $source_type ) {
            update_post_meta( $post_id, '_reglamento_external_url_key', esc_url_raw( $_POST['reglamento_external_url'] ?? '' ) );
            delete_post_meta( $post_id, '_reglamento_file_id_key' );
        }
    }
}

// 2. Campos para la Taxonomía 'categoria_reglamento'
$taxonomy_slug = 'categoria_reglamento';

add_action("{$taxonomy_slug}_add_form_fields", 'viceunf_add_category_meta_fields');
function viceunf_add_category_meta_fields()
{
?>
    <div class="form-field term-color-wrap">
        <label for="term_meta_color">Color de la Categoría</label>
        <input type="text" name="term_meta[color]" id="term_meta_color" class="viceunf-color-picker" value="#CCCCCC">
        <p class="description">Selecciona un color para representar esta categoría.</p>
    </div>
<?php
}

add_action("{$taxonomy_slug}_edit_form_fields", 'viceunf_edit_category_meta_fields');
function viceunf_edit_category_meta_fields($term)
{
    $color = get_term_meta($term->term_id, 'color', true) ?: '#CCCCCC';
?>
    <tr class="form-field term-color-wrap">
        <th scope="row" valign="top"><label for="term_meta_color">Color de la Categoría</label></th>
        <td>
            <input type="text" name="term_meta[color]" id="term_meta_color" class="viceunf-color-picker" value="<?php echo esc_attr($color); ?>">
            <p class="description">Selecciona un color para representar esta categoría.</p>
        </td>
    </tr>
<?php
}

add_action("edited_{$taxonomy_slug}", 'viceunf_save_category_meta');
add_action("create_{$taxonomy_slug}", 'viceunf_save_category_meta');
function viceunf_save_category_meta($term_id)
{
    if (isset($_POST['term_meta']) && isset($_POST['term_meta']['color'])) {
        $color = sanitize_hex_color($_POST['term_meta']['color']);
        if ($color) {
            update_term_meta($term_id, 'color', $color);
        }
    }
}
// --- FIN: REGLAMENTOS Y CATEGORÍAS ---