<?php

/**
 * Theme functions and definitions
 *
 * @package ViceUnf
 */

// Define la ruta a la carpeta de funciones para no repetirla.
$functions_path = get_stylesheet_directory() . '/theme-functions/';

// --- Cargas Críticas ---
// Carga clases o archivos necesarios para el resto de funciones ANTES que nada.
$customizer_class_path = $functions_path . 'controls/class-customize.php';
if (file_exists($customizer_class_path)) {
    require_once $customizer_class_path;
}

/**
 * Define el orden de carga explícito del resto de los archivos de funciones.
 */
$files_to_load = array(
    'setup.php',
    'enqueue.php',
    'cpt.php',
    'meta-boxes.php',
    'customizer.php',
    'admin-tweaks.php',
    'helpers.php',
);

// Itera sobre el array y carga cada archivo.
foreach ($files_to_load as $file) {
    $file_path = $functions_path . $file;
    if (file_exists($file_path)) {
        require_once $file_path;
    }
}

// Limpia las variables del ámbito global.
unset($functions_path, $customizer_class_path, $files_to_load, $file, $file_path);