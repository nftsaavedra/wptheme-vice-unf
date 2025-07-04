<?php
// Salir si se accede directamente.
if (! defined('ABSPATH')) exit;

/**
 * Crea y gestiona la página de opciones del tema en el panel de administración.
 * Esta versión incluye una interfaz con pestañas estilizada, funcional y escalable.
 */

// 1. Añade el menú al panel de administración de WordPress.
function viceunf_add_theme_options_menu()
{
  add_menu_page(
    'Opciones del Tema ViceUnf',
    'Opciones del Tema',
    'manage_options',
    'viceunf_theme_options',
    'viceunf_render_options_page_html',
    'dashicons-admin-generic',
    58
  );
}
add_action('admin_menu', 'viceunf_add_theme_options_menu');


// 2. Registra todas las opciones, secciones y campos para nuestra página.
function viceunf_register_all_theme_settings()
{
  register_setting('viceunf_options_group', 'viceunf_theme_options', 'viceunf_sanitize_all_options');

  // SECCIÓN 1: INVESTIGACIÓN
  add_settings_section('viceunf_investigacion_section', '1. Sección: Investigación', null, 'viceunf_theme_options');
  add_settings_field('investigacion_section_enabled', 'Habilitar Sección', 'viceunf_render_checkbox_field', 'viceunf_theme_options', 'viceunf_investigacion_section', ['id' => 'investigacion_section_enabled', 'label' => 'Mostrar esta sección en la página de inicio.']);
  for ($i = 1; $i <= 4; $i++) {
    add_settings_field("investigacion_item_{$i}", sprintf('Ítem de Investigación %d', $i), 'viceunf_render_investigacion_item_field', 'viceunf_theme_options', 'viceunf_investigacion_section', ['item_number' => $i]);
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

  // SECCIÓN 4: SOCIOS ACADÉMICOS
  add_settings_section('viceunf_socios_section', '4. Sección: Socios Académicos', null, 'viceunf_theme_options');
  add_settings_field('socios_section_enabled', 'Habilitar Sección', 'viceunf_render_checkbox_field', 'viceunf_theme_options', 'viceunf_socios_section', ['id' => 'socios_section_enabled', 'label' => 'Mostrar esta sección en la página de inicio.']);
  add_settings_field('socios_titulo', 'Título de la Sección', 'viceunf_render_text_field', 'viceunf_theme_options', 'viceunf_socios_section', ['id' => 'socios_titulo']);
}
add_action('admin_init', 'viceunf_register_all_theme_settings');


// 3. Funciones reutilizables que dibujan los campos HTML (Callbacks).
function viceunf_render_text_field($args)
{ /* ... (código de la respuesta anterior) ... */
}
function viceunf_render_textarea_field($args)
{ /* ... (código de la respuesta anterior) ... */
}
function viceunf_render_checkbox_field($args)
{ /* ... (código de la respuesta anterior) ... */
}
function viceunf_render_investigacion_item_field($args)
{ /* ... (código de la respuesta anterior) ... */
}


// 4. Función principal que renderiza el HTML de la página de opciones con pestañas.
function viceunf_render_options_page_html()
{
  if (!current_user_can('manage_options')) return;
  $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'homepage';
?>
  <div class="wrap viceunf-options-wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <h2 class="nav-tab-wrapper">
      <a href="?page=viceunf_theme_options&tab=homepage" class="nav-tab <?php echo $active_tab == 'homepage' ? 'nav-tab-active' : ''; ?>">Página de Inicio</a>
    </h2>
    <form action="options.php" method="post">
      <?php
      if ($active_tab == 'homepage') {
        settings_fields('viceunf_options_group');
        do_settings_sections('viceunf_theme_options');
      }
      submit_button('Guardar Cambios');
      ?>
    </form>
  </div>
<?php
}

// 5. Función de sanitización centralizada.
function viceunf_sanitize_all_options($input)
{ /* ... (código de la respuesta anterior) ... */
}

// 6. Carga los estilos y scripts para la página de opciones.
function viceunf_enqueue_admin_options_assets($hook)
{
  if ('toplevel_page_viceunf_theme_options' != $hook) {
    return;
  }
  wp_enqueue_style('viceunf-admin-options-style', get_stylesheet_directory_uri() . '/assets/css/admin-options.css', [], '1.0.1');
  wp_enqueue_script('viceunf-admin-options-script', get_stylesheet_directory_uri() . '/assets/js/admin-options.js', [], '1.0.1', true);
}
add_action('admin_enqueue_scripts', 'viceunf_enqueue_admin_options_assets');