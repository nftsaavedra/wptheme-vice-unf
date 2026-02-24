<?php
// Salir si se accede directamente.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * =================================================================
 * Callbacks de Opciones del Tema (Controlador Puro)
 * =================================================================
 * Responsabilidad: Pasar los datos (DB) a la vista (Renderer).
 * El HTML se maneja en ViceUnf_Admin_Fields_Renderer.
 */

// Cargar la clase Renderer
require_once plugin_dir_path( __DIR__ ) . 'theme-functions/classes/class-admin-fields-renderer.php';

// Función principal que renderiza el HTML de la página de opciones.
function viceunf_render_options_page_html() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="wrap viceunf-options-wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <h2 class="nav-tab-wrapper">
            <a href="?page=viceunf_theme_options&tab=homepage" class="nav-tab nav-tab-active">Página de Inicio</a>
        </h2>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'viceunf_options_group' );
            do_settings_sections( 'viceunf_theme_options' );
            submit_button( 'Guardar Cambios' );
            ?>
        </form>
    </div>
    <?php
}

// === Callbacks para los campos simples delegados al Renderer ===

function viceunf_render_text_field( $args ) {
    $options = get_option( 'viceunf_theme_options', [] );
    $id      = $args['id'];
    $value   = isset( $options[ $id ] ) ? $options[ $id ] : '';
    $desc    = isset( $args['description'] ) ? $args['description'] : '';
    ViceUnf_Admin_Fields_Renderer::render_text_field( $id, $value, $desc );
}

function viceunf_render_textarea_field( $args ) {
    $options = get_option( 'viceunf_theme_options', [] );
    $id      = $args['id'];
    $value   = isset( $options[ $id ] ) ? $options[ $id ] : '';
    ViceUnf_Admin_Fields_Renderer::render_textarea_field( $id, $value );
}

function viceunf_render_checkbox_field( $args ) {
    $options = get_option( 'viceunf_theme_options', [] );
    $id      = $args['id'];
    $value   = isset( $options[ $id ] ) ? $options[ $id ] : 0;
    $label   = isset( $args['label'] ) ? $args['label'] : '';
    ViceUnf_Admin_Fields_Renderer::render_checkbox_field( $id, $value, $label );
}

function viceunf_render_image_uploader_field( $args ) {
    $options   = get_option( 'viceunf_theme_options', [] );
    $id        = $args['id'];
    $value     = isset( $options[ $id ] ) ? $options[ $id ] : 0;
    $image_url = $value ? wp_get_attachment_url( $value ) : '';
    ViceUnf_Admin_Fields_Renderer::render_image_uploader_field( $id, $value, $image_url );
}

// === Componentes Complejos ===

function viceunf_render_investigacion_grid_callback() {
    $options = get_option( 'viceunf_theme_options', [] );

    echo '<p class="description">Configura los cuatro elementos destacados de la sección de investigación que aparecen en la página de inicio.</p>';
    echo '<div class="viceunf-items-grid-container">';

    for ( $i = 1; $i <= 4; $i++ ) {
        $page_id    = isset( $options["item_{$i}_page_id"] ) ? $options["item_{$i}_page_id"] : 0;
        $icon_class = isset( $options["item_{$i}_icon"] ) ? $options["item_{$i}_icon"] : 'fas fa-flask';
        $title      = isset( $options["item_{$i}_custom_title"] ) ? $options["item_{$i}_custom_title"] : '';
        $desc       = isset( $options["item_{$i}_custom_desc"] ) ? $options["item_{$i}_custom_desc"] : '';
        $page_title = $page_id ? get_the_title( $page_id ) : '';

        echo '<div class="viceunf-item-card">';
        echo "<h4>Item {$i}</h4>";

        echo '<label class="viceunf-label">Página Relacionada</label>';
        ViceUnf_Admin_Fields_Renderer::render_ajax_search_component( 'viceunf_search_pages_only', "viceunf_theme_options[item_{$i}_page_id]", $page_id, $page_title, 'Escribe para buscar una página...' );

        echo '<label class="viceunf-label">Icono (Font Awesome)</label>';
        ViceUnf_Admin_Fields_Renderer::render_ajax_search_component( 'viceunf_search_icons', "viceunf_theme_options[item_{$i}_icon]", $icon_class, $icon_class, 'Escribe para buscar un icono...' );

        echo '<label class="viceunf-label">Título Personalizado (Opcional)</label>';
        ViceUnf_Admin_Fields_Renderer::render_text_field( "item_{$i}_custom_title", $title, 'Usar el título de la página por defecto' );

        echo '<label class="viceunf-label">Descripción Corta (Opcional)</label>';
        ViceUnf_Admin_Fields_Renderer::render_textarea_field( "item_{$i}_custom_desc", $desc );

        echo '</div>';
    }

    echo '</div>';
}

function viceunf_render_about_repeater_field() {
    $options = get_option( 'viceunf_theme_options', [] );
    $items   = isset( $options['about_items'] ) && is_array( $options['about_items'] ) ? $options['about_items'] : [];
    ?>
    <div id="about-repeater-container" class="viceunf-repeater-container">
        <p class="description">Añade, elimina y reordena los items. El título y el enlace se tomarán de la página que selecciones.</p>

        <div class="repeater-items-wrapper">
            <?php
            if ( ! empty( $items ) ) :
                foreach ( $items as $index => $item ) :
                    $page_id    = isset( $item['page_id'] ) ? $item['page_id'] : 0;
                    $icon_class = isset( $item['icon'] ) ? $item['icon'] : '';
                    $page_title = $page_id ? get_the_title( $page_id ) : '';
                    ?>
                    <div class="repeater-item">
                        <div class="repeater-item-controls">
                            <button type="button" class="button-link move-repeater-item-up" title="Subir">↑</button>
                            <button type="button" class="button-link move-repeater-item-down" title="Bajar">↓</button>
                            <button type="button" class="button-link-delete remove-repeater-item" title="Eliminar Ítem">&times;</button>
                        </div>
                        <div class="repeater-item-fields">
                            <div class="field-group">
                                <label class="viceunf-label">Icono</label>
                                <?php ViceUnf_Admin_Fields_Renderer::render_ajax_search_component( 'viceunf_search_icons', "viceunf_theme_options[about_items][{$index}][icon]", $icon_class, $icon_class, 'Buscar un icono...' ); ?>
                            </div>
                            <div class="field-group">
                                <label class="viceunf-label">Página Relacionada</label>
                                <?php ViceUnf_Admin_Fields_Renderer::render_ajax_search_component( 'viceunf_search_pages_only', "viceunf_theme_options[about_items][{$index}][page_id]", $page_id, $page_title, 'Buscar una página...' ); ?>
                            </div>
                        </div>
                    </div>
                <?php
                endforeach;
            endif;
            ?>
        </div>

        <button type="button" id="add-about-item" class="button button-primary add-repeater-item">
            <span class="dashicons dashicons-plus-alt"></span> Añadir Nuevo Ítem
        </button>

        <template id="about-repeater-template">
            <div class="repeater-item">
                <div class="repeater-item-controls">
                    <button type="button" class="button-link move-repeater-item-up" title="Subir">↑</button>
                    <button type="button" class="button-link move-repeater-item-down" title="Bajar">↓</button>
                    <button type="button" class="button-link-delete remove-repeater-item" title="Eliminar Ítem">&times;</button>
                </div>
                <div class="repeater-item-fields">
                    <div class="field-group">
                        <label class="viceunf-label">Icono</label>
                        <?php ViceUnf_Admin_Fields_Renderer::render_ajax_search_component( 'viceunf_search_icons', "viceunf_theme_options[about_items][__INDEX__][icon]", '', '', 'Buscar un icono...' ); ?>
                    </div>
                    <div class="field-group">
                        <label class="viceunf-label">Página Relacionada</label>
                        <?php ViceUnf_Admin_Fields_Renderer::render_ajax_search_component( 'viceunf_search_pages_only', "viceunf_theme_options[about_items][__INDEX__][page_id]", '', '', 'Buscar una página...' ); ?>
                    </div>
                </div>
            </div>
        </template>
    </div>
    <?php
}
