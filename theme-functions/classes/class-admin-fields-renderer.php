<?php
// Salir si se accede directamente.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Clase ViceUnf_Admin_Fields_Renderer
 *
 * Responsabilidad Única: Renderizar componentes HTML para la pantalla de Opciones del Tema.
 * No realiza consultas a la BD, no guarda datos.
 * Aplica principios encapsulando el markup de los inputs de Settings API.
 */
class ViceUnf_Admin_Fields_Renderer {

    /**
     * Renderiza un campo input tipo text
     */
    public static function render_text_field( $id, $value, $description = '' ) {
        echo '<input type="text" name="viceunf_theme_options[' . esc_attr( $id ) . ']" value="' . esc_attr( $value ) . '" class="regular-text">';
        if ( ! empty( $description ) ) {
            echo '<p class="description">' . wp_kses_post( $description ) . '</p>';
        }
    }

    /**
     * Renderiza un campo textarea
     */
    public static function render_textarea_field( $id, $value ) {
        echo '<textarea name="viceunf_theme_options[' . esc_attr( $id ) . ']" rows="4" class="large-text">' . esc_textarea( $value ) . '</textarea>';
    }

    /**
     * Renderiza un checkbox
     */
    public static function render_checkbox_field( $id, $value, $label ) {
        echo '<label><input type="checkbox" name="viceunf_theme_options[' . esc_attr( $id ) . ']" value="1" ' . checked( 1, $value, false ) . '> ' . esc_html( $label ) . '</label>';
    }

    /**
     * Renderiza un campo de subida de imagen (Media Uploader)
     */
    public static function render_image_uploader_field( $id, $value, $image_url ) {
        ?>
        <div class="viceunf-image-uploader">
            <div class="image-preview-wrapper" style="<?php echo $value ? '' : 'display:none;'; ?>">
                <img src="<?php echo esc_url( $image_url ); ?>" class="image-preview" style="max-width: 200px; height: auto;">
            </div>
            <input type="hidden" name="viceunf_theme_options[<?php echo esc_attr( $id ); ?>]" value="<?php echo esc_attr( $value ); ?>" class="image-attachment-id">
            <button type="button" class="button upload-image-button">Subir / Elegir Imagen</button>
            <button type="button" class="button remove-image-button" style="<?php echo $value ? '' : 'display:none;'; ?>">Eliminar Imagen</button>
        </div>
        <?php
    }

    /**
     * Renderiza el componente de búsqueda AJAX de contenido
     */
    public static function render_ajax_search_component( $action, $input_name, $current_id, $current_title, $placeholder ) {
        $wrapper_class = ( 'viceunf_search_icons' === $action ) ? 'ajax-search-wrapper icon-search-wrapper' : 'ajax-search-wrapper';
        $input_class   = ( 'viceunf_search_icons' === $action ) ? 'large-text ajax-search-input icon-picker-input' : 'large-text ajax-search-input';
        ?>
        <div class="<?php echo esc_attr( $wrapper_class ); ?>" data-action="<?php echo esc_attr( $action ); ?>">
            <div class="selected-item-view <?php echo $current_id ? 'active' : ''; ?>">
                <?php if ( 'viceunf_search_icons' === $action ) : ?>
                    <span class="icon-preview"><i class="<?php echo esc_attr( $current_id ); ?>"></i></span>
                <?php endif; ?>
                <span class="selected-item-title"><?php echo esc_html( $current_title ); ?></span>
                <button type="button" class="button-link-delete clear-selection-btn">&times;</button>
            </div>
            <div class="search-input-view <?php echo $current_id ? '' : 'active'; ?>">
                <input type="text" class="<?php echo esc_attr( $input_class ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>">
                <div class="ajax-search-results"></div>
            </div>
            <input type="hidden" class="ajax-search-hidden-id" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $current_id ); ?>">
        </div>
        <?php
    }
}
