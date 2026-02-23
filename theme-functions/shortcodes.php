<?php

/**
 * Archivo para registrar los shortcodes personalizados del tema.
 *
 * @package ViceUnf
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Shortcode configurable para listar los Reglamentos.
 * Actúa solo como Controlador.
 *
 * @param array $atts Atributos del shortcode.
 * @return string HTML renderizado.
 */
function viceunf_listar_reglamentos_shortcode( $atts ) {
    $atts = shortcode_atts( array( 'categoria' => '' ), $atts, 'listar_reglamentos' );
    $categoria_slugs = ! empty( $atts['categoria'] ) ? array_map( 'sanitize_title', explode( ',', $atts['categoria'] ) ) : array();

    if ( ! class_exists( 'ViceUnf_Reglamentos_Service' ) ) {
        return '<div class="alert alert-warning">Se requiere activar el plugin <strong>ViceUnf Core</strong> para visualizar este componente.</div>';
    }

    // Obtener la data desde el servicio de dominio
    $reglamentos_data = ViceUnf_Reglamentos_Service::get_reglamentos( $categoria_slugs );

    ob_start();

    // Pasar la data a la vista
    get_template_part( 'template-parts/shortcodes/reglamentos', 'list', array( 'reglamentos_data' => $reglamentos_data ) );

    return ob_get_clean();
}
add_shortcode( 'listar_reglamentos', 'viceunf_listar_reglamentos_shortcode' );
