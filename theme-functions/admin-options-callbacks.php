<?php
// Salir si se accede directamente.
if (!defined('ABSPATH')) exit;

/**
 * =================================================================
 * Callbacks de Opciones del Tema
 * =================================================================
 * Responsabilidad: Renderizar el HTML de la página de opciones
 * y de cada uno de los campos (callbacks).
 */

// Función principal que renderiza el HTML de la página de opciones.
function viceunf_render_options_page_html()
{
  if (!current_user_can('manage_options')) return;
?>
  <div class="wrap viceunf-options-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <h2 class="nav-tab-wrapper">
      <a href="?page=viceunf_theme_options&tab=homepage" class="nav-tab nav-tab-active">Página de Inicio</a>
    </h2>
    <form action="options.php" method="post">
      <?php
      settings_fields('viceunf_options_group');
      do_settings_sections('viceunf_theme_options');
      submit_button('Guardar Cambios');
      ?>
    </form>
  </div>
<?php
}

// === Callbacks para los campos ===

function viceunf_render_text_field($args)
{
  $options = get_option('viceunf_theme_options', []);
  $id = $args['id'];
  $value = isset($options[$id]) ? $options[$id] : '';
  echo '<input type="text" name="viceunf_theme_options[' . esc_attr($id) . ']" value="' . esc_attr($value) . '" class="regular-text">';
  if (isset($args['description'])) {
    echo '<p class="description">' . wp_kses_post($args['description']) . '</p>';
  }
}

function viceunf_render_textarea_field($args)
{
  $options = get_option('viceunf_theme_options', []);
  $id = $args['id'];
  $value = isset($options[$id]) ? $options[$id] : '';
  echo '<textarea name="viceunf_theme_options[' . esc_attr($id) . ']" rows="4" class="large-text">' . esc_textarea($value) . '</textarea>';
}

function viceunf_render_checkbox_field($args)
{
  $options = get_option('viceunf_theme_options', []);
  $id = $args['id'];
  $value = isset($options[$id]) ? $options[$id] : 0;
  echo '<label><input type="checkbox" name="viceunf_theme_options[' . esc_attr($id) . ']" value="1" ' . checked(1, $value, false) . '> ' . esc_html($args['label']) . '</label>';
}

function viceunf_render_image_uploader_field($args)
{
  $options = get_option('viceunf_theme_options', []);
  $id = $args['id'];
  $value = isset($options[$id]) ? $options[$id] : 0;
  $image_url = $value ? wp_get_attachment_url($value) : '';
?>
  <div class="viceunf-image-uploader">
    <div class="image-preview-wrapper" style="<?php echo $value ? '' : 'display:none;'; ?>">
      <img src="<?php echo esc_url($image_url); ?>" class="image-preview" style="max-width: 200px; height: auto;">
    </div>
    <input type="hidden" name="viceunf_theme_options[<?php echo esc_attr($id); ?>]" value="<?php echo esc_attr($value); ?>" class="image-attachment-id">
    <button type="button" class="button upload-image-button">Subir / Elegir Imagen</button>
    <button type="button" class="button remove-image-button" style="<?php echo $value ? '' : 'display:none;'; ?>">Eliminar Imagen</button>
  </div>
<?php
}

function viceunf_render_investigacion_grid_callback()
{
  $options = get_option('viceunf_theme_options', []);

  echo '<p class="description">Configura los cuatro elementos destacados de la sección de investigación que aparecen en la página de inicio.</p>';
  echo '<div class="viceunf-items-grid-container">';

  for ($i = 1; $i <= 4; $i++) {
    $page_id = isset($options["item_{$i}_page_id"]) ? $options["item_{$i}_page_id"] : 0;
    $icon_class = isset($options["item_{$i}_icon"]) ? $options["item_{$i}_icon"] : 'fas fa-flask';
    $title = isset($options["item_{$i}_custom_title"]) ? $options["item_{$i}_custom_title"] : '';
    $desc = isset($options["item_{$i}_custom_desc"]) ? $options["item_{$i}_custom_desc"] : '';
    $page_title = $page_id ? get_the_title($page_id) : '';

    echo '<div class="viceunf-item-card">';
    echo "<h4>Item {$i}</h4>";

    // Selector de Página Relacionada
    echo '<label class="viceunf-label">Página Relacionada</label>';
    echo '<div class="ajax-search-wrapper" data-action="viceunf_search_pages_only">';
    echo '  <div class="selected-item-view ' . ($page_id ? 'active' : '') . '">';
    echo '      <span class="selected-item-title">' . esc_html($page_title) . '</span>';
    echo '      <button type="button" class="button-link-delete clear-selection-btn">&times;</button>';
    echo '  </div>';
    echo '  <div class="search-input-view ' . ($page_id ? '' : 'active') . '">';
    echo '      <input type="text" class="large-text ajax-search-input" placeholder="Escribe para buscar una página...">';
    echo '      <div class="ajax-search-results"></div>';
    echo '  </div>';
    echo '  <input type="hidden" class="ajax-search-hidden-id" name="viceunf_theme_options[item_' . $i . '_page_id]" value="' . esc_attr($page_id) . '">';
    echo '</div>';

    // Componente de Búsqueda de Iconos
    echo '<label class="viceunf-label">Icono (Font Awesome)</label>';
    echo '<div class="ajax-search-wrapper icon-search-wrapper" data-action="viceunf_search_icons">';
    echo '  <div class="selected-item-view ' . ($icon_class ? 'active' : '') . '">';
    echo '      <span class="icon-preview"><i class="' . esc_attr($icon_class) . '"></i></span>';
    echo '      <span class="selected-item-title">' . esc_html($icon_class) . '</span>';
    echo '      <button type="button" class="button-link-delete clear-selection-btn">&times;</button>';
    echo '  </div>';
    echo '  <div class="search-input-view ' . ($icon_class ? '' : 'active') . '">';
    echo '      <input type="text" class="large-text ajax-search-input icon-picker-input" placeholder="Escribe para buscar un icono...">';
    echo '      <div class="ajax-search-results"></div>';
    echo '  </div>';
    echo '  <input type="hidden" class="ajax-search-hidden-id" name="viceunf_theme_options[item_' . $i . '_icon]" value="' . esc_attr($icon_class) . '">';
    echo '</div>';

    // Campos de Título y Descripción
    echo '<label class="viceunf-label">Título Personalizado (Opcional)</label>';
    echo '<input type="text" name="viceunf_theme_options[item_' . $i . '_custom_title]" value="' . esc_attr($title) . '" class="large-text" placeholder="Usar el título de la página por defecto">';
    echo '<label class="viceunf-label">Descripción Corta (Opcional)</label>';
    echo '<textarea name="viceunf_theme_options[item_' . $i . '_custom_desc]" rows="3" class="large-text" placeholder="Usar el extracto de la página por defecto">' . esc_textarea($desc) . '</textarea>';
    echo '</div>';
  }

  echo '</div>';
}

function viceunf_render_about_repeater_field()
{
  $options = get_option('viceunf_theme_options', []);
  $items = isset($options['about_items']) && is_array($options['about_items']) ? $options['about_items'] : [];
?>
  <div id="about-repeater-container" class="viceunf-repeater-container">
    <p class="description">Añade, elimina y reordena los items. El título y el enlace se tomarán de la página que selecciones.</p>

    <div class="repeater-items-wrapper">
      <?php if (!empty($items)) : foreach ($items as $index => $item) :
          $page_id = isset($item['page_id']) ? $item['page_id'] : 0;
          $icon_class = isset($item['icon']) ? $item['icon'] : '';
          $page_title = $page_id ? get_the_title($page_id) : '';
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
                <div class="ajax-search-wrapper icon-search-wrapper" data-action="viceunf_search_icons">
                  <div class="selected-item-view <?php echo $icon_class ? 'active' : ''; ?>">
                    <span class="icon-preview"><i class="<?php echo esc_attr($icon_class); ?>"></i></span>
                    <span class="selected-item-title"><?php echo esc_html($icon_class); ?></span>
                    <button type="button" class="button-link-delete clear-selection-btn">&times;</button>
                  </div>
                  <div class="search-input-view <?php echo $icon_class ? '' : 'active'; ?>">
                    <input type="text" class="large-text ajax-search-input icon-picker-input" placeholder="Buscar un icono...">
                    <div class="ajax-search-results"></div>
                  </div>
                  <input type="hidden" class="ajax-search-hidden-id" name="viceunf_theme_options[about_items][<?php echo esc_attr($index); ?>][icon]" value="<?php echo esc_attr($icon_class); ?>">
                </div>
              </div>
              <div class="field-group">
                <label class="viceunf-label">Página Relacionada</label>
                <div class="ajax-search-wrapper" data-action="viceunf_search_pages_only">
                  <div class="selected-item-view <?php echo $page_id ? 'active' : ''; ?>">
                    <span class="selected-item-title"><?php echo esc_html($page_title); ?></span>
                    <button type="button" class="button-link-delete clear-selection-btn">&times;</button>
                  </div>
                  <div class="search-input-view <?php echo $page_id ? '' : 'active'; ?>">
                    <input type="text" class="large-text ajax-search-input" placeholder="Buscar una página...">
                    <div class="ajax-search-results"></div>
                  </div>
                  <input type="hidden" class="ajax-search-hidden-id" name="viceunf_theme_options[about_items][<?php echo esc_attr($index); ?>][page_id]" value="<?php echo esc_attr($page_id); ?>">
                </div>
              </div>
            </div>
          </div>
      <?php endforeach;
      endif; ?>
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
            <div class="ajax-search-wrapper icon-search-wrapper" data-action="viceunf_search_icons">
              <div class="selected-item-view"><span class="icon-preview"></span><span class="selected-item-title"></span><button type="button" class="button-link-delete clear-selection-btn">&times;</button></div>
              <div class="search-input-view active"><input type="text" class="large-text ajax-search-input icon-picker-input" placeholder="Buscar un icono...">
                <div class="ajax-search-results"></div>
              </div>
              <input type="hidden" class="ajax-search-hidden-id" name="viceunf_theme_options[about_items][__INDEX__][icon]" value="">
            </div>
          </div>
          <div class="field-group">
            <label class="viceunf-label">Página Relacionada</label>
            <div class="ajax-search-wrapper" data-action="viceunf_search_pages_only">
              <div class="selected-item-view"><span class="selected-item-title"></span><button type="button" class="button-link-delete clear-selection-btn">&times;</button></div>
              <div class="search-input-view active"><input type="text" class="large-text ajax-search-input" placeholder="Buscar una página...">
                <div class="ajax-search-results"></div>
              </div>
              <input type="hidden" class="ajax-search-hidden-id" name="viceunf_theme_options[about_items][__INDEX__][page_id]" value="">
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
<?php
}
