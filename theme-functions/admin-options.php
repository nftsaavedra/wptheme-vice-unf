<?php
// Salir si se accede directamente.
if (! defined('ABSPATH')) exit;

/**
 * Crea y gestiona la página de opciones del tema en el panel de administración.
 * Esta versión incluye una interfaz con pestañas para una mejor organización y escalabilidad.
 */

// 1. Añade el menú al panel de administración de WordPress.
function viceunf_add_theme_options_menu()
{
  add_menu_page(
    'Opciones del Tema ViceUnf',    // Título de la página (para la etiqueta <title>)
    'Opciones del Tema',            // Texto del menú (más profesional)
    'manage_options',               // Capacidad requerida para el acceso
    'viceunf_theme_options',        // Slug de la página (el ID principal)
    'viceunf_render_options_page_html', // Función que renderiza el HTML de la página
    'dashicons-admin-generic',      // Un ícono más apropiado para "opciones"
    58                              // Posición en el menú
  );
}
add_action('admin_menu', 'viceunf_add_theme_options_menu');


// 2. Registra todas las opciones y secciones usando la Settings API.
function viceunf_register_all_theme_settings()
{
  // Registra un único grupo de guardado. Todas nuestras opciones se guardan en un solo array en la BD.
  register_setting(
    'viceunf_options_group',          // Nombre del grupo de opciones. Debe ser el mismo para todas las pestañas.
    'viceunf_theme_options',          // Nombre de la opción en la base de datos.
    'viceunf_sanitize_all_options'    // Función de sanitización centralizada.
  );

  // === PESTAÑA 1: INVESTIGACIÓN ===
  add_settings_section('viceunf_investigacion_section', 'Configuración: Sección de Investigación', null, 'viceunf_options_page_investigacion');
  add_settings_field('investigacion_section_enabled', 'Habilitar Sección', 'viceunf_render_checkbox_field', 'viceunf_options_page_investigacion', 'viceunf_investigacion_section', ['id' => 'investigacion_section_enabled', 'label' => 'Mostrar la sección de Investigación en la página de inicio.']);
  for ($i = 1; $i <= 4; $i++) {
    add_settings_field("investigacion_item_{$i}", sprintf('Ítem de Investigación %d', $i), 'viceunf_render_investigacion_item_field', 'viceunf_options_page_investigacion', 'viceunf_investigacion_section', ['item_number' => $i]);
  }

  // === PESTAÑA 2: EVENTOS ===
  add_settings_section('viceunf_eventos_section', 'Configuración: Sección de Eventos', null, 'viceunf_options_page_eventos');
  add_settings_field('eventos_section_enabled', 'Habilitar Sección', 'viceunf_render_checkbox_field', 'viceunf_options_page_eventos', 'viceunf_eventos_section', ['id' => 'eventos_section_enabled', 'label' => 'Mostrar la sección de Eventos en la página de inicio.']);
  add_settings_field('eventos_subtitulo', 'Subtítulo', 'viceunf_render_text_field', 'viceunf_options_page_eventos', 'viceunf_eventos_section', ['id' => 'eventos_subtitulo']);
  add_settings_field('eventos_titulo', 'Título', 'viceunf_render_text_field', 'viceunf_options_page_eventos', 'viceunf_eventos_section', ['id' => 'eventos_titulo', 'description' => 'Usa &lt;span&gt; para resaltar texto.']);
  add_settings_field('eventos_descripcion', 'Descripción', 'viceunf_render_textarea_field', 'viceunf_options_page_eventos', 'viceunf_eventos_section', ['id' => 'eventos_descripcion']);

  // === PESTAÑA 3: NOTICIAS ===
  add_settings_section('viceunf_noticias_section', 'Configuración: Sección de Noticias', null, 'viceunf_options_page_noticias');
  add_settings_field('noticias_section_enabled', 'Habilitar Sección', 'viceunf_render_checkbox_field', 'viceunf_options_page_noticias', 'viceunf_noticias_section', ['id' => 'noticias_section_enabled', 'label' => 'Mostrar la sección de Noticias en la página de inicio.']);
  add_settings_field('noticias_subtitulo', 'Subtítulo', 'viceunf_render_text_field', 'viceunf_options_page_noticias', 'viceunf_noticias_section', ['id' => 'noticias_subtitulo']);
  add_settings_field('noticias_titulo', 'Título', 'viceunf_render_text_field', 'viceunf_options_page_noticias', 'viceunf_noticias_section', ['id' => 'noticias_titulo']);
  add_settings_field('noticias_descripcion', 'Descripción', 'viceunf_render_textarea_field', 'viceunf_options_page_noticias', 'viceunf_noticias_section', ['id' => 'noticias_descripcion']);

  // === PESTAÑA 4: SOCIOS ACADÉMICOS ===
  add_settings_section('viceunf_socios_section', 'Configuración: Sección de Socios', null, 'viceunf_options_page_socios');
  add_settings_field('socios_section_enabled', 'Habilitar Sección', 'viceunf_render_checkbox_field', 'viceunf_options_page_socios', 'viceunf_socios_section', ['id' => 'socios_section_enabled', 'label' => 'Mostrar la sección de Socios en la página de inicio.']);
  add_settings_field('socios_titulo', 'Título de la Sección', 'viceunf_render_text_field', 'viceunf_options_page_socios', 'viceunf_socios_section', ['id' => 'socios_titulo']);
}
add_action('admin_init', 'viceunf_register_all_theme_settings');


// 3. Funciones genéricas y reutilizables para renderizar los campos (Callbacks).

function viceunf_render_text_field($args)
{
  $options = get_option('viceunf_theme_options', []);
  $id = $args['id'];
  $value = $options[$id] ?? '';
  echo '<input type="text" name="viceunf_theme_options[' . esc_attr($id) . ']" value="' . esc_attr($value) . '" class="regular-text">';
  if (isset($args['description'])) {
    echo '<p class="description">' . wp_kses_post($args['description']) . '</p>';
  }
}

function viceunf_render_textarea_field($args)
{
  $options = get_option('viceunf_theme_options', []);
  $id = $args['id'];
  $value = $options[$id] ?? '';
  echo '<textarea name="viceunf_theme_options[' . esc_attr($id) . ']" rows="4" class="large-text">' . esc_textarea($value) . '</textarea>';
}

function viceunf_render_checkbox_field($args)
{
  $options = get_option('viceunf_theme_options', []);
  $id = $args['id'];
  $value = isset($options[$id]) ? $options[$id] : 0;
  echo '<label><input type="checkbox" name="viceunf_theme_options[' . esc_attr($id) . ']" value="1" ' . checked(1, $value, false) . '> ' . esc_html($args['label']) . '</label>';
}

function viceunf_render_investigacion_item_field($args)
{
  $options = get_option('viceunf_theme_options', []);
  $i = $args['item_number'];
  $page_id = $options["item_{$i}_page_id"] ?? 0;
  $icon = $options["item_{$i}_icon"] ?? 'fas fa-flask';
  $title = $options["item_{$i}_custom_title"] ?? '';
  $desc = $options["item_{$i}_custom_desc"] ?? '';
  $used_page_ids = [];
  for ($j = 1; $j <= 4; $j++) {
    if ($i != $j && !empty($options["item_{$j}_page_id"])) {
      $used_page_ids[] = $options["item_{$j}_page_id"];
    }
  }
  echo '<div class="viceunf-options-card"><label class="viceunf-label">Página Relacionada</label>';
  wp_dropdown_pages(['name' => "viceunf_theme_options[item_{$i}_page_id]", 'selected' => $page_id, 'show_option_none' => '-- Ninguna --', 'exclude' => $used_page_ids,]);
  echo '<p class="description">Las páginas ya usadas en otros ítems son excluidas de esta lista.</p>';
  echo '<label class="viceunf-label">Icono (Font Awesome)</label><input type="text" name="viceunf_theme_options[item_' . $i . '_icon]" value="' . esc_attr($icon) . '" class="regular-text">';
  echo '<label class="viceunf-label">Título Personalizado (Opcional)</label><input type="text" name="viceunf_theme_options[item_' . $i . '_custom_title]" value="' . esc_attr($title) . '" class="large-text" placeholder="Usar el título de la página por defecto">';
  echo '<label class="viceunf-label">Descripción Corta (Opcional)</label><textarea name="viceunf_theme_options[item_' . $i . '_custom_desc]" rows="3" class="large-text" placeholder="Usar el extracto de la página por defecto">' . esc_textarea($desc) . '</textarea>';
  echo '</div>';
}

// 4. Función principal que renderiza el HTML de la página con la estructura de pestañas.
function viceunf_render_options_page_html()
{
  if (!current_user_can('manage_options')) return;
  $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'investigacion';
?>
  <div class="wrap viceunf-options-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <h2 class="nav-tab-wrapper">
      <a href="?page=viceunf_theme_options&tab=investigacion" class="nav-tab <?php echo $active_tab == 'investigacion' ? 'nav-tab-active' : ''; ?>">Investigación</a>
      <a href="?page=viceunf_theme_options&tab=eventos" class="nav-tab <?php echo $active_tab == 'eventos' ? 'nav-tab-active' : ''; ?>">Eventos</a>
      <a href="?page=viceunf_theme_options&tab=noticias" class="nav-tab <?php echo $active_tab == 'noticias' ? 'nav-tab-active' : ''; ?>">Noticias</a>
      <a href="?page=viceunf_theme_options&tab=socios" class="nav-tab <?php echo $active_tab == 'socios' ? 'nav-tab-active' : ''; ?>">Socios</a>
    </h2>
    <form action="options.php" method="post">
      <?php
      settings_fields('viceunf_options_group');
      if ($active_tab == 'investigacion') {
        do_settings_sections('viceunf_options_page_investigacion');
      } elseif ($active_tab == 'eventos') {
        do_settings_sections('viceunf_options_page_eventos');
      } elseif ($active_tab == 'noticias') {
        do_settings_sections('viceunf_options_page_noticias');
      } elseif ($active_tab == 'socios') {
        do_settings_sections('viceunf_options_page_socios');
      }
      submit_button('Guardar Cambios');
      ?>
    </form>
  </div>
  <?php
}

// 5. Función de sanitización centralizada y robusta.
function viceunf_sanitize_all_options($input)
{
  $current_options = get_option('viceunf_theme_options', []);
  $sanitized_input = [];

  // Esta línea es crucial: combina los datos antiguos con los nuevos que vienen del formulario.
  // Así, cuando guardas una pestaña, no se borran los datos de las otras.
  $input = array_merge($current_options, $input);

  // Sanitiza cada campo esperado.
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

// 6. Añade CSS para mejorar la apariencia de nuestra página de opciones.
function viceunf_load_admin_options_styles()
{
  // Solo carga este CSS en nuestra página de opciones.
  if (isset($_GET['page']) && $_GET['page'] == 'viceunf_theme_options') {
  ?>
    <style type="text/css">
      .viceunf-options-wrap .form-table {
        background: #fff;
        border-radius: 4px;
        padding: 20px;
      }

      .viceunf-options-card {
        padding: 15px;
        border: 1px solid #ccd0d4;
        background: #f6f7f7;
        border-radius: 4px;
        margin-bottom: 15px;
      }

      .viceunf-options-card .viceunf-label {
        display: block;
        font-weight: 600;
        margin-top: 10px;
        margin-bottom: 3px;
      }
    </style>
<?php
  }
}
add_action('admin_head', 'viceunf_load_admin_options_styles');
