<?php

declare(strict_types=1);

/**
 * Theme functions and definitions (Standalone)
 *
 * @package VpinUnf
 */

// Constantes de versión centralizadas para mantenimiento
define('VPINUNF_FONTAWESOME_VERSION', '6.7.2');
define('VPINUNF_SWIPER_VERSION', '11.0.0');
define('VPINUNF_GLIGHTBOX_VERSION', '3.3.0');
define('VPINUNF_ANIMATE_VERSION', '4.1.1');

add_action('admin_notices', function (): void {
    if (!defined('VPINUNF_CORE_VERSION')) {
        echo '<div class="notice notice-error is-dismissible"><p><strong>Error Crítico:</strong> El tema <em>VpinUnf</em> requiere que el plugin <strong>VpinUnf Core</strong> esté instalado y activado.</p></div>';
    }
});



$vpinunf_functions_path = get_stylesheet_directory() . '/theme-functions/';

// Carga Manual de Namespaces Estructurales Principales
require_once $vpinunf_functions_path . 'setup.php';
require_once $vpinunf_functions_path . 'enqueue.php';
require_once $vpinunf_functions_path . 'class-image-optimizer.php';
require_once $vpinunf_functions_path . 'class-image-optimizer-admin.php';

new \VpinUnf\Theme\Setup();
new \VpinUnf\Theme\Assets();
new \VpinUnf\ImageOptimizer();
new \VpinUnf\ImageOptimizerAdmin();

// Carga del resto de utilidades Legacy y Procedimentales
$vpinunf_files = [
    'template-tags.php',
    'meta-boxes.php',
    'admin-tweaks.php',
    'helpers.php',
    'admin-options-sanitize.php',
    'admin-options-api.php',
    'admin-options.php',
];

foreach ($vpinunf_files as $vpinunf_file) {
    require_once $vpinunf_functions_path . $vpinunf_file;
}
