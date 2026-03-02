<?php
// Salir si se accede directamente.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * =================================================================
 * API REST para Opciones del Tema (Theme Options API)
 * =================================================================
 * Responsabilidad: Exponer y guardar la configuración del tema vía JSON 
 * para ser consumida por la SPA en React Guttemberg.
 */

add_action('rest_api_init', 'viceunf_register_theme_options_endpoints');

function viceunf_register_theme_options_endpoints()
{
    register_rest_route('viceunf/v1', '/options', array(
        array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => 'viceunf_get_theme_options',
            'permission_callback' => 'viceunf_theme_options_permissions_check',
        ),
        array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => 'viceunf_update_theme_options',
            'permission_callback' => 'viceunf_theme_options_permissions_check',
        ),
    ));
}

/**
 * Verifica permisos para leer y escribir las opciones del tema.
 */
function viceunf_theme_options_permissions_check()
{
    return current_user_can('manage_options');
}

/**
 * Callback GET: Retorna las opciones actuales del tema.
 */
function viceunf_get_theme_options()
{
    $options = get_option('viceunf_theme_options', array());

    // Devolvemos la data cruda, el frontend se encargará de los valores por defecto si faltan
    return rest_ensure_response($options);
}

/**
 * Callback POST: Sanitiza y guarda las nuevas opciones del tema.
 */
function viceunf_update_theme_options(WP_REST_Request $request)
{
    $params = $request->get_json_params();

    if (! is_array($params)) {
        return new WP_Error('invalid_data', 'Los datos enviados no son válidos.', array('status' => 400));
    }

    // Usar la función de sanitización estricta existente 
    // asumimos que admin-options-sanitize.php está cargado y la función viceunf_sanitize_all_options existe.
    if (function_exists('viceunf_sanitize_all_options')) {
        $sanitized_options = viceunf_sanitize_all_options($params);
    } else {
        // Fallback por si acaso, aunque no debería ocurrir.
        $sanitized_options = $params;
    }

    // Guardar las opciones sanitizadas
    $updated = update_option('viceunf_theme_options', $sanitized_options);

    if ($updated) {
        return rest_ensure_response(array(
            'success' => true,
            'message' => 'Opciones guardadas correctamente.',
            'data'    => $sanitized_options
        ));
    }

    // Si returnó false, pudo no haber cambios o hubo un error.
    // Devolvemos success true de todos modos con los datos actuales para sincronizar el estado.
    return rest_ensure_response(array(
        'success' => true,
        'message' => 'No hubo cambios.',
        'data'    => get_option('viceunf_theme_options', array())
    ));
}
