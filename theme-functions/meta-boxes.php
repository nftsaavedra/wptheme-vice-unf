<?php
// Salir si se accede directamente.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * =================================================================
 * Registro de Meta Boxes mediante Factory CPT (Clean Architecture)
 * =================================================================
 * Este archivo ha sido refactorizado para usar `ViceUnf_Metabox_Factory`.
 * La Factory maneja el guardado, la validación de nonces, la sanitización (DRY).
 * Aquí solo declaramos los ARRAYS de estructura.
 */

// Asegurar que la clase Factory esté cargada
require_once plugin_dir_path( __FILE__ ) . 'classes/class-metabox-factory.php';

// --- META COMPONENT PARCIAL: BUSQUEDA EN SLIDER ---
function viceunf_render_slider_content_search_html( $post, $field, $value ) {
    $link_content_id = $value;
    $link_content_title = $link_content_id ? get_the_title( $link_content_id ) : '';
    ?>
    <label>Buscar Entrada o Página</label>
    <div class="ajax-search-wrapper" data-action="viceunf_search_content">
        <div class="selected-item-view <?php echo ( $link_content_id ? 'active' : '' ); ?>">
            <span class="selected-item-title"><?php echo esc_html( $link_content_title ); ?></span>
            <button type="button" class="button-link-delete clear-selection-btn">&times;</button>
        </div>
        <div class="search-input-view <?php echo ( $link_content_id ? '' : 'active' ); ?>">
            <input type="text" class="large-text ajax-search-input" placeholder="Escribe para buscar...">
            <div class="ajax-search-results"></div>
        </div>
        <input type="hidden" class="ajax-search-hidden-id" id="slider_link_content_id" name="slider_link_content_id" value="<?php echo esc_attr( $link_content_id ); ?>">
    </div>
    <?php
}

// --- META COMPONENT PARCIAL: SELECTOR DE ARCHIVO EN REGLAMENTO ---
function viceunf_render_reglamento_file_selector_html( $post, $field, $value ) {
    $source_type  = get_post_meta( $post->ID, '_reglamento_source_type_key', true ) ?: 'upload';
    $file_id      = get_post_meta( $post->ID, '_reglamento_file_id_key', true );
    $external_url = get_post_meta( $post->ID, '_reglamento_external_url_key', true );
    $file_url     = $file_id ? wp_get_attachment_url( $file_id ) : '';
    $file_name    = $file_url ? basename( $file_url ) : '';
    ?>
    <div id="reglamento-source-selector" class="viceunf-metabox-container">
        <div class="reglamento-field radio-buttons-as-tabs">
            <input type="radio" id="source_upload" name="reglamento_source_type" value="upload" <?php checked( $source_type, 'upload' ); ?>>
            <label for="source_upload">Subir Archivo</label>
            <input type="radio" id="source_external" name="reglamento_source_type" value="external" <?php checked( $source_type, 'external' ); ?>>
            <label for="source_external">Enlace Externo</label>
        </div>
        <div id="reglamento-upload-section" class="reglamento-section-wrapper conditional-field">
            <p>Seleccione el archivo (PDF, Word, etc.) correspondiente a este reglamento.</p>
            <input type="hidden" id="reglamento_file_id" name="reglamento_file_id" value="<?php echo esc_attr( $file_id ); ?>" required>
            <button type="button" class="button" id="upload_reglamento_button">Seleccionar o Subir Archivo</button>
            <button type="button" class="button button-secondary" id="remove_reglamento_button">Quitar Archivo</button>
            <div class="file-info">
                <?php if ( $file_id && $file_url ) : ?>
                    Archivo actual: <a href="<?php echo esc_url( $file_url ); ?>" target="_blank"><?php echo esc_html( $file_name ); ?></a>
                <?php else : ?>
                    No se ha seleccionado ningún archivo.
                <?php endif; ?>
            </div>
        </div>
        <div id="reglamento-external-section" class="reglamento-section-wrapper conditional-field">
            <p>Pegue la URL completa del documento externo (ej. un PDF en Google Drive).</p>
            <label for="reglamento_external_url"><strong>URL del Archivo:</strong></label><br>
            <input type="url" id="reglamento_external_url" name="reglamento_external_url" value="<?php echo esc_url( $external_url ); ?>" placeholder="https://ejemplo.com/documento.pdf" required>
        </div>
    </div>
    <?php
}

// Handler customizado para guardado extra (radios condicionales) en Reglamento
function viceunf_save_reglamento_custom_callback( $post_id, $post_data ) {
    if ( isset( $post_data['reglamento_source_type'] ) ) {
        $source_type = sanitize_text_field( $post_data['reglamento_source_type'] );
        update_post_meta( $post_id, '_reglamento_source_type_key', $source_type );
        if ( 'upload' === $source_type ) {
            update_post_meta( $post_id, '_reglamento_file_id_key', sanitize_text_field( $post_data['reglamento_file_id'] ?? '' ) );
            delete_post_meta( $post_id, '_reglamento_external_url_key' );
        } elseif ( 'external' === $source_type ) {
            update_post_meta( $post_id, '_reglamento_external_url_key', esc_url_raw( $post_data['reglamento_external_url'] ?? '' ) );
            delete_post_meta( $post_id, '_reglamento_file_id_key' );
        }
    }
}

// ----------------------------------------------------------------------------------
// INICIALIZACIÓN DE METABOXES (Configuraciones declarativas)
// ----------------------------------------------------------------------------------

add_action( 'admin_init', function() {

    // 1. Meta Box: SLIDER
    new ViceUnf_Metabox_Factory( array(
        'id'        => 'slider_details_metabox',
        'title'     => 'Datos del Slider',
        'post_type' => 'slider',
        'fields'    => array(
            array( 'id' => 'slider_subtitle', 'meta_key' => '_slider_subtitle_key', 'label' => 'Subtítulo', 'section' => 'Contenido del Slider' ),
            array( 'id' => 'slider_description', 'meta_key' => '_slider_description_key', 'type' => 'textarea', 'label' => 'Descripción', 'section' => 'Contenido del Slider' ),
            array( 'id' => 'slider_text_alignment', 'meta_key' => '_slider_text_alignment_key', 'type' => 'select', 'label' => 'Alineación', 'options' => array( 'dt-text-left' => 'Izquierda', 'dt-text-center' => 'Centro', 'dt-text-right' => 'Derecha' ), 'section' => 'Contenido del Slider' ),
            
            array( 'id' => 'slider_btn1_text', 'meta_key' => '_slider_btn1_text_key', 'label' => 'Texto Botón 1', 'section' => 'Botón 1 (Principal)' ),
            array( 'id' => 'slider_link_type', 'meta_key' => '_slider_link_type_key', 'type' => 'select', 'label' => 'Tipo de Enlace', 'options' => array( 'none' => 'Ninguno', 'url' => 'URL Personalizada', 'content' => 'Enlazar a Contenido (Buscar)' ), 'section' => 'Botón 1 (Principal)' ),
            array( 'id' => 'slider_link_url', 'meta_key' => '_slider_link_url_key', 'type' => 'url', 'label' => 'URL Personalizada', 'attributes' => 'placeholder="https://ejemplo.com"', 'wrapper_id' => 'campo_url', 'wrapper_class' => 'slider-field conditional-field', 'wrapper_style' => 'display:none', 'section' => 'Botón 1 (Principal)' ),
            array( 'id' => 'slider_link_content_id', 'meta_key' => '_slider_link_content_id_key', 'type' => 'custom_html', 'render_callback' => 'viceunf_render_slider_content_search_html', 'wrapper_id' => 'campo_contenido', 'wrapper_class' => 'slider-field conditional-field', 'wrapper_style' => 'display:none', 'section' => 'Botón 1 (Principal)' ),

            array( 'id' => 'slider_btn2_text', 'meta_key' => '_slider_btn2_text_key', 'label' => 'Texto (Opcional)', 'section' => 'Botón 2 (Secundario)' ),
            array( 'id' => 'slider_btn2_link', 'meta_key' => '_slider_btn2_link_key', 'type' => 'url', 'label' => 'Enlace (Opcional)', 'section' => 'Botón 2 (Secundario)' ),

            array( 'id' => 'slider_video_link', 'meta_key' => '_slider_video_link_key', 'type' => 'url', 'label' => 'Enlace Video (Opcional)', 'section' => 'Botón de Video' ),
        )
    ) );

    // 2. Meta Box: EVENTO
    new ViceUnf_Metabox_Factory( array(
        'id'        => 'evento_details_metabox',
        'title'     => 'Detalles del Evento',
        'post_type' => 'evento',
        'fields'    => array(
            array( 'id' => 'evento_date', 'meta_key' => '_evento_date_key', 'type' => 'date', 'label' => 'Fecha del Evento (Requerido)', 'attributes' => 'required', 'wrapper_class' => 'evento-metabox-field' ),
            array( 'id' => 'evento_start_time', 'meta_key' => '_evento_start_time_key', 'type' => 'time', 'label' => 'Hora de Inicio (ej. 7:00)', 'wrapper_class' => 'evento-metabox-field' ),
            array( 'id' => 'evento_end_time', 'meta_key' => '_evento_end_time_key', 'type' => 'time', 'label' => 'Hora de Fin (ej. 13:00)', 'wrapper_class' => 'evento-metabox-field' ),
            array( 'id' => 'evento_address', 'meta_key' => '_evento_address_key', 'label' => 'Lugar / Dirección del Evento', 'wrapper_class' => 'evento-metabox-field' ),
        )
    ) );

    // 3. Meta Box: SOCIO
    new ViceUnf_Metabox_Factory( array(
        'id'        => 'socio_details_metabox',
        'title'     => 'Detalles del Socio',
        'post_type' => 'socio',
        'fields'    => array(
            array( 'id' => 'socio_url', 'meta_key' => '_socio_url_key', 'type' => 'url', 'label' => 'Enlace Web del Socio (Opcional)', 'wrapper_class' => 'socio-field' ),
        )
    ) );

    // 4. Meta Box: REGLAMENTO
    new ViceUnf_Metabox_Factory( array(
        'id'            => 'reglamento_file_metabox',
        'title'         => 'Archivo del Reglamento (Obligatorio)',
        'post_type'     => 'reglamento',
        'save_callback' => 'viceunf_save_reglamento_custom_callback',
        'fields'        => array(
            array( 'id' => 'reglamento_source_ui', 'type' => 'custom_html', 'render_callback' => 'viceunf_render_reglamento_file_selector_html' )
        )
    ) );

});


// ----------------------------------------------------------------------------------
// TAXONOMÍAS DE REGLAMENTOS (Mantenido intacto por ser estándar de WP Options/Terms API)
// ----------------------------------------------------------------------------------

$taxonomy_slug = 'categoria_reglamento';

add_action("{$taxonomy_slug}_add_form_fields", 'viceunf_add_category_meta_fields');
function viceunf_add_category_meta_fields() {
?>
    <div class="form-field term-color-wrap">
        <label for="term_meta_color">Color de la Categoría</label>
        <input type="text" name="term_meta[color]" id="term_meta_color" class="viceunf-color-picker" value="#CCCCCC">
        <p class="description">Selecciona un color para representar esta categoría.</p>
    </div>
<?php
}

add_action("{$taxonomy_slug}_edit_form_fields", 'viceunf_edit_category_meta_fields');
function viceunf_edit_category_meta_fields($term) {
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
function viceunf_save_category_meta($term_id) {
    if (isset($_POST['term_meta']) && isset($_POST['term_meta']['color'])) {
        $color = sanitize_hex_color($_POST['term_meta']['color']);
        if ($color) {
            update_term_meta($term_id, 'color', $color);
        }
    }
}