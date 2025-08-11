<?php
// Salir si se accede directamente.
if (!defined('ABSPATH')) exit;

/**
 * =================================================================
 * Archivo Principal de Opciones del Tema ViceUnf (Orquestador)
 * =================================================================
 * Carga todos los componentes necesarios para la página de opciones.
 */

// 1. Añade el menú al panel de administración de WordPress.
add_action('admin_menu', function () {
  add_menu_page(
    'Opciones del Tema ViceUnf',
    'Opciones del Tema',
    'manage_options',
    'viceunf_theme_options',
    'viceunf_render_options_page_html', // Esta función está en admin-options-callbacks.php
    'dashicons-admin-generic',
    58
  );
});

// Carga los archivos con responsabilidades separadas.
$theme_functions_path = get_stylesheet_directory() . '/theme-functions/';

// Registra todas las secciones y campos.
require_once $theme_functions_path . 'admin-options-register.php';

// Contiene las funciones que renderizan el HTML de cada campo (callbacks).
require_once $theme_functions_path . 'admin-options-callbacks.php';

// Contiene la lógica de sanitización para guardar los datos.
require_once $theme_functions_path . 'admin-options-sanitize.php';
