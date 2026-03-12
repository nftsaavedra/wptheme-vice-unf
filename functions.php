<?php

declare(strict_types=1);

/**
 * Theme functions and definitions (Standalone)
 *
 * @package ViceUnf
 */

require_once get_stylesheet_directory() . '/inc/class-wp-bootstrap-navwalker.php';

$viceunf_functions_path = get_stylesheet_directory() . '/theme-functions/';

// Carga Manual de Namespaces Estructurales Principales
require_once $viceunf_functions_path . 'setup.php';
require_once $viceunf_functions_path . 'enqueue.php';

new \ViceUnf\Theme\Setup();
new \ViceUnf\Theme\Assets();

// Carga del resto de utilidades Legacy y Procedimentales
$viceunf_files = [
    'template-tags.php',
    'meta-boxes.php',
    'customizer.php',
    'admin-options.php',
    'admin-options-api.php',
    'admin-tweaks.php',
    'helpers.php',
    'shortcodes.php',
];

foreach ($viceunf_files as $viceunf_file) {
    require_once $viceunf_functions_path . $viceunf_file;
}
