<?php

/**
 * Theme functions and definitions
 *
 * @package ViceUnf
 */

// Define la ruta a la carpeta de funciones para no repetirla.
$functions_path = get_stylesheet_directory() . '/theme-functions/';

/**
 * Define el orden de carga explícito.
 */
$files_to_load = array(
    'setup.php',
    'enqueue.php',
    'cpt.php',
    'meta-boxes.php',
    'customizer.php',
    'admin-options.php',
    'admin-tweaks.php',
    'helpers.php',
    'custom-widgets.php',
);

// Itera sobre el array y carga cada archivo.
foreach ($files_to_load as $file) {
    $file_path = $functions_path . $file;
    if (file_exists($file_path)) {
        require_once $file_path;
    }
}

// Limpia las variables del ámbito global.
unset($functions_path, $files_to_load, $file, $file_path);
