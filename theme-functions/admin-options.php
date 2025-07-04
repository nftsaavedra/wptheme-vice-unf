<?php
// Salir si se accede directamente.
if (! defined('ABSPATH')) exit;

/**
 * Crea y gestiona la página de opciones del tema en el panel de administración.
 * Esta versión incluye una interfaz con pestañas estilizada, funcional y escalable.
 */

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

  // Todas las secciones se registran en la misma "página" de la API: 'viceunf_theme_options'
  // La lógica de pestañas en la función de renderizado se encargará de mostrarlas.

  // SECCIÓN 1: INVESTIGACIÓN
  add_settings_section('viceunf_investigacion_section', '1. Sección: Investigación', null, 'viceunf_theme_options');
  add_settings_field('investigacion_section_enabled', 'Habilitar Sección', 'viceunf_render_checkbox_field', 'viceunf_theme_options', 'viceunf_investigacion_section', ['id' => 'investigacion_section_enabled', 'label' => 'Mostrar esta sección en la página de inicio.']);
  for ($i = 1; $i <= 4; $i++) {
    add_settings_field("investigacion_item_{$i}", sprintf('Ítem %d', $i), 'viceunf_render_investigacion_item_field', 'viceunf_theme_options', 'viceunf_investigacion_section', ['item_number' => $i]);
  }

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

// 3. Funciones que renderizan el HTML de cada campo (Callbacks).
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

function viceunf_render_investigacion_item_field($args) {
    $options = get_option('viceunf_theme_options', []);
    $i = $args['item_number'];

    // Obtenemos el ID y el título de la página guardada
    $page_id = isset($options["item_{$i}_page_id"]) ? $options["item_{$i}_page_id"] : 0;
    $page_title = $page_id ? get_the_title($page_id) : '';

    // Obtenemos el resto de los campos
    $icon = isset($options["item_{$i}_icon"]) ? $options["item_{$i}_icon"] : 'fas fa-flask';
    $title = isset($options["item_{$i}_custom_title"]) ? $options["item_{$i}_custom_title"] : '';
    $desc = isset($options["item_{$i}_custom_desc"]) ? $options["item_{$i}_custom_desc"] : '';

    // Nueva estructura HTML para el selector con búsqueda
    echo '<div class="viceunf-options-card">';
    echo '  <label class="viceunf-label">Página Relacionada</label>';
    echo '  <div class="ajax-search-wrapper" data-action="viceunf_search_pages_only">';
    
    // Contenedor para el estado "seleccionado"
    echo '      <div class="selected-item-view ' . ($page_id ? 'active' : '') . '">';
    echo '          <span class="selected-item-title">' . esc_html($page_title) . '</span>';
    echo '          <button type="button" class="button-link-delete clear-selection-btn">&times;</button>';
    echo '      </div>';

    // Contenedor para el estado "búsqueda"
    echo '      <div class="search-input-view ' . ($page_id ? '' : 'active') . '">';
    echo '          <input type="text" class="large-text ajax-search-input" placeholder="Escribe 3+ letras para buscar una página...">';
    echo '          <div class="ajax-search-results"></div>';
    echo '      </div>';

    // Campo oculto que guarda el ID de la página
    echo '      <input type="hidden" class="ajax-search-hidden-id" name="viceunf_theme_options[item_' . $i . '_page_id]" value="' . esc_attr($page_id) . '">';
    
    echo '  </div>'; // Cierre de ajax-search-wrapper

    // El resto de los campos no cambian
    echo '  <label class="viceunf-label">Icono (Font Awesome)</label>';
    echo '  <input type="text" name="viceunf_theme_options[item_' . $i . '_icon]" value="' . esc_attr($icon) . '" class="regular-text">';
    
    echo '  <label class="viceunf-label">Título Personalizado (Opcional)</label>';
    echo '  <input type="text" name="viceunf_theme_options[item_' . $i . '_custom_title]" value="' . esc_attr($title) . '" class="large-text" placeholder="Usar el título de la página por defecto">';
    
    echo '  <label class="viceunf-label">Descripción Corta (Opcional)</label>';
    echo '  <textarea name="viceunf_theme_options[item_' . $i . '_custom_desc]" rows="3" class="large-text" placeholder="Usar el extracto de la página por defecto">' . esc_textarea($desc) . '</textarea>';
    echo '</div>';
}

// 4. Función principal que renderiza la página de opciones.
function viceunf_render_options_page_html()
{
  if (!current_user_can('manage_options')) return;
?>
  <div class="wrap viceunf-options-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <h2 class="nav-tab-wrapper">
      <a href="?page=viceunf_theme_options" class="nav-tab nav-tab-active">Página de Inicio</a>
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

// 5. Función de sanitización centralizada.
function viceunf_sanitize_all_options($input)
{
  $current_options = get_option('viceunf_theme_options', []);
  $sanitized_input = [];
  $input = array_merge($current_options, $input);

  // Sanitiza todos los campos esperados
  $sanitized_input['investigacion_section_enabled'] = isset($input['investigacion_section_enabled']) ? 1 : 0;
  for ($i = 1; $i <= 4; $i++) {
    if (isset($input["item_{$i}_page_id"])) $sanitized_input["item_{$i}_page_id"] = absint($input["item_{$i}_page_id"]);
    if (isset($input["item_{$i}_icon"])) $sanitized_input["item_{$i}_icon"] = sanitize_text_field($input["item_{$i}_icon"]);
    if (isset($input["item_{$i}_custom_title"])) $sanitized_input["item_{$i}_custom_title"] = sanitize_text_field($input["item_{$i}_custom_title"]);
    if (isset($input["item_{$i}_custom_desc"])) $sanitized_input["item_{$i}_custom_desc"] = sanitize_textarea_field($input["item_{$i}_custom_desc"]);
  }
  $sanitized_input['eventos_section_enabled'] = isset($input['eventos_section_enabled']) ? 1 : 0;
  if (isset($input['eventos_subtitulo'])) $sanitized_input['eventos_subtitulo'] = sanitize_text_field($input['eventos_subtitulo']);
  if (isset($input['eventos_titulo'])) $sanitized_input['eventos_titulo'] = wp_kses_post($input['eventos_titulo']);
  if (isset($input['eventos_descripcion'])) $sanitized_input['eventos_descripcion'] = sanitize_textarea_field($input['eventos_descripcion']);

  $sanitized_input['noticias_section_enabled'] = isset($input['noticias_section_enabled']) ? 1 : 0;
  if (isset($input['noticias_subtitulo'])) $sanitized_input['noticias_subtitulo'] = sanitize_text_field($input['noticias_subtitulo']);
  if (isset($input['noticias_titulo'])) $sanitized_input['noticias_titulo'] = wp_kses_post($input['noticias_titulo']);
  if (isset($input['noticias_descripcion'])) $sanitized_input['noticias_descripcion'] = sanitize_textarea_field($input['noticias_descripcion']);

  $sanitized_input['socios_section_enabled'] = isset($input['socios_section_enabled']) ? 1 : 0;
  if (isset($input['socios_titulo'])) $sanitized_input['socios_titulo'] = sanitize_text_field($input['socios_titulo']);

  return $sanitized_input;
}


function viceunf_enqueue_admin_options_assets($hook) {
    // Solo carga en nuestra página de opciones.
    if ('toplevel_page_viceunf_theme_options' != $hook) {
        return;
    }
    
    // Carga el CSS de la página de opciones
    wp_enqueue_style('viceunf-admin-options-style', get_stylesheet_directory_uri() . '/assets/css/admin-options.css', [], '1.0.4');
    
    // Carga el SCRIPT de búsqueda y le pasa los datos necesarios (URL de AJAX y nonce).
    wp_enqueue_script('viceunf-admin-search', get_stylesheet_directory_uri() . '/assets/js/admin-search.js', [], '1.1.0', true);
    wp_localize_script('viceunf-admin-search', 'viceunf_ajax_obj', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('slider_metabox_nonce_action') // Reutilizamos el mismo nonce.
    ));
}
add_action('admin_enqueue_scripts', 'viceunf_enqueue_admin_options_assets');