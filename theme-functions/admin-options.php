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
  $hook_suffix = add_theme_page(
    'VPIN — Opciones del Tema',
    'Opciones VpinUnf',
    'manage_options',
    'vpinunf_theme_options',
    'vpinunf_render_options_page_react'
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
      'vpinunf-admin-options-js',
      get_stylesheet_directory_uri() . '/build/admin-options.js',
      $dependencies,
      $asset['version'],
      true
    );

    wp_localize_script('vpinunf-admin-options-js', 'vpinunfAdminData', array(
      'themeUrl' => get_stylesheet_directory_uri()
    ));

    wp_enqueue_style(
      'vpinunf-admin-options-css',
      get_stylesheet_directory_uri() . '/build/style-admin-options.css',
      array('wp-components'),
      $asset['version']
    );
  });
});

/**
 * Renderiza el contenedor raíz para React.
 */
function vpinunf_render_options_page_react()
{
  if (!current_user_can('manage_options')) return;
  echo '<div id="vpinunf-settings-root">Cargando la interfaz moderna...</div>';
}

$theme_functions_path = get_stylesheet_directory() . '/theme-functions/';

// Contiene la lógica de sanitización estricta utilizada por el Endpoint REST API.
require_once $theme_functions_path . 'admin-options-sanitize.php';
