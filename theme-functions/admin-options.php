<?php
// Salir si se accede directamente.
if (!defined('ABSPATH')) exit;

/**
 * =================================================================
 * Archivo de Opciones del Tema ViceUnf
 * =================================================================
 * Gestiona la página "Opciones del Tema" en el panel de administración,
 * utilizando la Settings API de WordPress para una solución robusta y escalable.
 */


/**
 * Devuelve una lista curada de iconos de Font Awesome 6 para el selector.
 * Centralizar esta lista aquí facilita su actualización en el futuro.
 * @return array La lista de clases de iconos.
 */
function viceunf_get_fontawesome_icon_list()
{
  return array(
    'fas fa-flask',
    'fas fa-project-diagram',
    'fas fa-user-graduate',
    'fas fa-file-alt',
    'fas fa-info-circle',
    'fas fa-check',
    'fas fa-times',
    'fas fa-home',
    'fas fa-book',
    'fas fa-book-open',
    'fas fa-newspaper',
    'fas fa-database',
    'fas fa-sitemap',
    'fas fa-lightbulb',
    'fas fa-rocket',
    'fas fa-cogs',
    'fas fa-calendar-alt',
    'fas fa-folder',
    'fas fa-play',
    'fas fa-user',
    'fas fa-envelope',
    'fas fa-phone',
    'fas fa-map-marker-alt',
    'fas fa-globe',
    'fab fa-facebook',
    'fab fa-twitter',
    'fab fa-linkedin',
    'fab fa-youtube',
  );
}

// 1. Añade el menú al panel de administración de WordPress.
add_action('admin_menu', function () {
  add_menu_page(
    'Opciones del Tema ViceUnf',
    'Opciones del Tema',
    'manage_options',
    'viceunf_theme_options',
    'viceunf_render_options_page_html',
    'dashicons-admin-generic',
    58
  );
});

// 2. Registra todas las opciones, secciones y campos usando la Settings API.
add_action('admin_init', function () {
  register_setting('viceunf_options_group', 'viceunf_theme_options', 'viceunf_sanitize_all_options');

  // === PESTAÑA: PÁGINA DE INICIO ===
  // Todas las secciones se registran en la misma "página" de la API: 'viceunf_theme_options'

  // SECCIÓN 1: INVESTIGACIÓN
  add_settings_section('viceunf_investigacion_section', '1. Sección: Investigación', null, 'viceunf_theme_options');
  add_settings_field('investigacion_section_enabled', 'Habilitar Sección', 'viceunf_render_checkbox_field', 'viceunf_theme_options', 'viceunf_investigacion_section', ['id' => 'investigacion_section_enabled', 'label' => 'Mostrar esta sección en la página de inicio.']);

  // --- MODIFICADO ---
  // En lugar de registrar 4 campos separados (lo que crea 4 filas de tabla),
  // registramos UN solo campo que renderizará nuestra rejilla completa.
  add_settings_field('investigacion_items_grid', 'Items de Investigación', 'viceunf_render_investigacion_grid_callback', 'viceunf_theme_options', 'viceunf_investigacion_section');

  // --- ELIMINADO ---
  // Ya no necesitamos el bucle que creaba los campos individuales.
  // for ($i = 1; $i <= 4; $i++) {
  //  add_settings_field("investigacion_item_{$i}", sprintf('Ítem %d', $i), 'viceunf_render_investigacion_item_field', 'viceunf_theme_options', 'viceunf_investigacion_section', ['item_number' => $i]);
  // }

  // SECCIÓN 2: EVENTOS
  add_settings_section('viceunf_eventos_section', '2. Sección: Eventos', null, 'viceunf_theme_options');
  add_settings_field('eventos_section_enabled', 'Habilitar Sección', 'viceunf_render_checkbox_field', 'viceunf_theme_options', 'viceunf_eventos_section', ['id' => 'eventos_section_enabled', 'label' => 'Mostrar esta sección en la página de inicio.']);
  add_settings_field('eventos_subtitulo', 'Subtítulo', 'viceunf_render_text_field', 'viceunf_theme_options', 'viceunf_eventos_section', ['id' => 'eventos_subtitulo']);
  add_settings_field('eventos_titulo', 'Título', 'viceunf_render_text_field', 'viceunf_theme_options', 'viceunf_eventos_section', ['id' => 'eventos_titulo', 'description' => 'Usa &lt;span&gt; para resaltar.']);
  add_settings_field('eventos_descripcion', 'Descripción', 'viceunf_render_textarea_field', 'viceunf_theme_options', 'viceunf_eventos_section', ['id' => 'eventos_descripcion']);

  // SECCIÓN 3: NOTICIAS
  add_settings_section('viceunf_noticias_section', '3. Sección: Noticias', null, 'viceunf_theme_options');
  add_settings_field('noticias_section_enabled', 'Habilitar Sección', 'viceunf_render_checkbox_field', 'viceunf_theme_options', 'viceunf_noticias_section', ['id' => 'noticias_section_enabled', 'label' => 'Mostrar esta sección en la página de inicio.']);
  add_settings_field('noticias_subtitulo', 'Subtítulo', 'viceunf_render_text_field', 'viceunf_theme_options', 'viceunf_noticias_section', ['id' => 'noticias_subtitulo']);
  add_settings_field('noticias_titulo', 'Título', 'viceunf_render_text_field', 'viceunf_theme_options', 'viceunf_noticias_section', ['id' => 'noticias_titulo']);
  add_settings_field('noticias_descripcion', 'Descripción', 'viceunf_render_textarea_field', 'viceunf_theme_options', 'viceunf_noticias_section', ['id' => 'noticias_descripcion']);

  // SECCIÓN 4: SOCIOS
  add_settings_section('viceunf_socios_section', '4. Sección: Socios Académicos', null, 'viceunf_theme_options');
  add_settings_field('socios_section_enabled', 'Habilitar Sección', 'viceunf_render_checkbox_field', 'viceunf_theme_options', 'viceunf_socios_section', ['id' => 'socios_section_enabled', 'label' => 'Mostrar esta sección en la página de inicio.']);
  add_settings_field('socios_titulo', 'Título de la Sección', 'viceunf_render_text_field', 'viceunf_theme_options', 'viceunf_socios_section', ['id' => 'socios_titulo']);
});

// 3. Funciones reutilizables que renderizan los campos HTML (Callbacks).
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

// --- ELIMINADO ---
// Esta función ya no es necesaria, ya que su lógica se ha movido a la nueva función `viceunf_render_investigacion_grid_callback`.
// function viceunf_render_investigacion_item_field(...) { ... }


// --- NUEVO ---
// Esta es la nueva función de callback que renderiza toda la rejilla.
function viceunf_render_investigacion_grid_callback()
{
  $options = get_option('viceunf_theme_options', []);

  echo '<p class="description">Configura los cuatro elementos destacados de la sección de investigación que aparecen en la página de inicio.</p>';

  // Contenedor principal para la rejilla de CSS (definido en admin-options.css)
  echo '<div class="viceunf-items-grid-container">';

  for ($i = 1; $i <= 4; $i++) {
    // Obtenemos los valores guardados para cada item
    $page_id = isset($options["item_{$i}_page_id"]) ? $options["item_{$i}_page_id"] : 0;
    $icon_class = isset($options["item_{$i}_icon"]) ? $options["item_{$i}_icon"] : 'fas fa-flask';
    $title = isset($options["item_{$i}_custom_title"]) ? $options["item_{$i}_custom_title"] : '';
    $desc = isset($options["item_{$i}_custom_desc"]) ? $options["item_{$i}_custom_desc"] : '';
    $page_title = $page_id ? get_the_title($page_id) : '';

    // Contenedor individual para cada tarjeta de la rejilla
    echo '<div class="viceunf-item-card">';
    echo "<h4>Item {$i}</h4>";

    // --- Selector de Página Relacionada ---
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

    // --- Componente de Búsqueda de Iconos ---
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

    // --- Campos de Título y Descripción ---
    echo '<label class="viceunf-label">Título Personalizado (Opcional)</label>';
    echo '<input type="text" name="viceunf_theme_options[item_' . $i . '_custom_title]" value="' . esc_attr($title) . '" class="large-text" placeholder="Usar el título de la página por defecto">';

    echo '<label class="viceunf-label">Descripción Corta (Opcional)</label>';
    echo '<textarea name="viceunf_theme_options[item_' . $i . '_custom_desc]" rows="3" class="large-text" placeholder="Usar el extracto de la página por defecto">' . esc_textarea($desc) . '</textarea>';

    echo '</div>'; // Cierre de .viceunf-item-card
  }

  echo '</div>'; // Cierre de .viceunf-items-grid-container
}

// 4. Función principal que renderiza el HTML de la página de opciones.
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

// 5. Función de sanitización centralizada y robusta (CORREGIDA).
function viceunf_sanitize_all_options($input)
{
  $current_options = get_option('viceunf_theme_options', []);
  $sanitized_input = [];

  // --- CORRECCIÓN INICIA AQUÍ ---

  // Lista de todos los campos que son checkboxes.
  $checkbox_keys = [
    'investigacion_section_enabled',
    'eventos_section_enabled',
    'noticias_section_enabled',
    'socios_section_enabled',
  ];

  // Verificamos cada checkbox explícitamente.
  // Si el campo existe en el input, significa que está marcado (valor 1).
  // Si no existe, significa que está desmarcado (valor 0).
  foreach ($checkbox_keys as $key) {
    $sanitized_input[$key] = (isset($input[$key]) && $input[$key] == 1) ? 1 : 0;
  }

  // Ahora, procesamos el resto de los campos de texto y textarea.
  $other_fields_rules = [
    'eventos_subtitulo'     => 'sanitize_text_field',
    'eventos_titulo'        => 'wp_kses_post',
    'eventos_descripcion'   => 'sanitize_textarea_field',
    'noticias_subtitulo'    => 'sanitize_text_field',
    'noticias_titulo'       => 'wp_kses_post',
    'noticias_descripcion'  => 'sanitize_textarea_field',
    'socios_titulo'         => 'sanitize_text_field',
  ];

  foreach ($other_fields_rules as $key => $sanitize_callback) {
    // Solo sanitizamos si el campo fue enviado.
    if (isset($input[$key])) {
      $sanitized_input[$key] = call_user_func($sanitize_callback, $input[$key]);
    }
  }
  // --- CORRECCIÓN TERMINA AQUÍ ---


  // Sanitización especial para los items de investigación (esta parte no cambia).
  for ($i = 1; $i <= 4; $i++) {
    if (isset($input["item_{$i}_page_id"])) $sanitized_input["item_{$i}_page_id"] = absint($input["item_{$i}_page_id"]);
    if (isset($input["item_{$i}_icon"])) $sanitized_input["item_{$i}_icon"] = sanitize_text_field($input["item_{$i}_icon"]);
    if (isset($input["item_{$i}_custom_title"])) $sanitized_input["item_{$i}_custom_title"] = sanitize_text_field($input["item_{$i}_custom_title"]);
    if (isset($input["item_{$i}_custom_desc"])) $sanitized_input["item_{$i}_custom_desc"] = sanitize_textarea_field($input["item_{$i}_custom_desc"]);
  }

  // Fusionamos los datos nuevos y sanitizados con las opciones existentes.
  return array_merge($current_options, $sanitized_input);
}
