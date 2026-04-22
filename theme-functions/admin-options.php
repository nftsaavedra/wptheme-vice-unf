<?php
// Salir si se accede directamente.
if (!defined('ABSPATH')) exit;

/**
 * =================================================================
 * Archivo Principal de Opciones del Tema VPIN (Orquestador React)
 * =================================================================
 * Carga el backend en React para administrar las opciones del tema.
 */

add_action('admin_menu', function () {
  $hook_suffix = add_menu_page(
    'VPIN — Opciones del Tema',
    'VPIN Opciones',
    'manage_options',
    'viceunf_theme_options',
    'viceunf_render_options_page_react',
    'dashicons-welcome-learn-more',
    58
  );

  // Encolar scripts solo en esta página
  add_action('admin_enqueue_scripts', function ($hook) use ($hook_suffix) {
    if ($hook !== $hook_suffix) return;

    $asset_file_path = get_stylesheet_directory() . '/build/admin-options.asset.php';
    if (file_exists($asset_file_path)) {
      $asset = require $asset_file_path;
    } else {
      $asset = array('dependencies' => array('wp-element', 'wp-components', 'wp-api-fetch', 'wp-i18n'), 'version' => time());
    }

    // Agregar dependencias críticas — wp-media-utils para la librería nativa de medios
    $dependencies = array_unique(array_merge($asset['dependencies'], array('wp-media-utils')));

    wp_enqueue_media(); // Necesario para componentes que usan Media (Logo de socios)

    wp_enqueue_script(
      'viceunf-admin-options-js',
      get_stylesheet_directory_uri() . '/build/admin-options.js',
      $dependencies,
      $asset['version'],
      true
    );

    wp_localize_script('viceunf-admin-options-js', 'viceunfAdminData', array(
      'themeUrl' => get_stylesheet_directory_uri()
    ));

    wp_enqueue_style(
      'viceunf-admin-options-css',
      get_stylesheet_directory_uri() . '/build/style-admin-options.css',
      array('wp-components'),
      $asset['version']
    );
  });
});

/**
 * Renderiza el contenedor raíz para React.
 */
function viceunf_render_options_page_react()
{
  if (!current_user_can('manage_options')) return;
  echo '<div id="viceunf-settings-root">Cargando la interfaz moderna...</div>';
}

$theme_functions_path = get_stylesheet_directory() . '/theme-functions/';

// Contiene la lógica de sanitización estricta utilizada por el Endpoint REST API.
require_once $theme_functions_path . 'admin-options-sanitize.php';
