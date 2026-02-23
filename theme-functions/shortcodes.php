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
 * - Sin atributo `categoria`: renderiza árbol jerárquico completo con acordeones.
 * - Con atributo `categoria`: renderiza lista plana filtrada por slugs.
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

    // Vista jerárquica (árbol) o filtrada (plana)
    if ( empty( $categoria_slugs ) ) {
        $reglamentos_data = ViceUnf_Reglamentos_Service::get_reglamentos_tree();
    } else {
        $reglamentos_data = ViceUnf_Reglamentos_Service::get_reglamentos( $categoria_slugs );
    }

    ob_start();

    // Pasar la data a la vista
    get_template_part( 'template-parts/shortcodes/reglamentos', 'list', array( 'reglamentos_data' => $reglamentos_data ) );

    return ob_get_clean();
}
add_shortcode( 'listar_reglamentos', 'viceunf_listar_reglamentos_shortcode' );
