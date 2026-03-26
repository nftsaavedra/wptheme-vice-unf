<?php

declare(strict_types=1);

/**
 * Theme functions and definitions (Standalone)
 *
 * @package ViceUnf
 */

// Constantes de versión centralizadas para mantenimiento
define('VICEUNF_FONTAWESOME_VERSION', '6.7.2');
define('VICEUNF_SWIPER_VERSION', '11.0.0');
define('VICEUNF_GLIGHTBOX_VERSION', '3.3.0');
define('VICEUNF_ANIMATE_VERSION', '4.1.1');

add_action('admin_notices', function (): void {
    if (!defined('VICEUNF_CORE_VERSION')) {
        echo '<div class="notice notice-error is-dismissible"><p><strong>Error Crítico:</strong> El tema <em>ViceUnf</em> requiere que el plugin <strong>ViceUnf Core</strong> esté instalado y activado.</p></div>';
    }
});

require_once get_stylesheet_directory() . '/inc/class-wp-bootstrap-navwalker.php';

$viceunf_functions_path = get_stylesheet_directory() . '/theme-functions/';

// Carga Manual de Namespaces Estructurales Principales
require_once $viceunf_functions_path . 'setup.php';
require_once $viceunf_functions_path . 'enqueue.php';
require_once $viceunf_functions_path . 'class-image-optimizer.php';
require_once $viceunf_functions_path . 'class-image-optimizer-admin.php';

new \ViceUnf\Theme\Setup();
new \ViceUnf\Theme\Assets();
new \ViceUnf\ImageOptimizer();
new \ViceUnf\ImageOptimizerAdmin();

// Carga del resto de utilidades Legacy y Procedimentales
$viceunf_files = [
    'template-tags.php',
    'meta-boxes.php',
    'customizer.php',
    'admin-options.php',
    'admin-options-api.php',
    'admin-tweaks.php',
    'helpers.php',
];

foreach ($viceunf_files as $viceunf_file) {
    require_once $viceunf_functions_path . $viceunf_file;
}

// FSE Dynamic Blocks — WordPress 7.0
require_once get_stylesheet_directory() . '/inc/fse-blocks.php';
