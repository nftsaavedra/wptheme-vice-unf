<?php
// Salir si se accede directamente.
if (! defined('ABSPATH')) {
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


// ----------------------------------------------------------------------------------
// INICIALIZACIÓN DE METABOXES (Configuraciones declarativas)
// ----------------------------------------------------------------------------------

add_action('admin_init', function () {

    // Nota Arquitectónica (2025):
    // Las declaraciones de Meta Boxes para Slider, Evento, Socio y Reglamento han sido REMOVIDAS de manera segura del Tema.
    // Dicha responsabilidad concierne 100% a la persistencia y base de datos, por tanto, fueron migradas al plugin `viceunf-core`.
    // Las clases en el core ahora heredan de `AbstractMetaBox` lo que garantiza DRY, estandarización de OWASP,
    // y perfecta disociación de la UI (Headless Ready).
});


// ----------------------------------------------------------------------------------
// TAXONOMÍAS DE REGLAMENTOS (Mantenido intacto por ser estándar de WP Options/Terms API)
// ----------------------------------------------------------------------------------

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
