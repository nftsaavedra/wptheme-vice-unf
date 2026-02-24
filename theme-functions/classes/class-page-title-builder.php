<?php
// Salir si se accede directamente.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Clase ViceUnf_Page_Title_Builder
 *
 * Responsabilidad: Generar el título correcto para la cabecera de la página
 * dependiendo del contexto actual (Archivo, 404, Búsqueda, Single, etc).
 */
class ViceUnf_Page_Title_Builder {

    /**
     * Devuelve el título de la página actual en HTML.
     */
    public static function render() {
        if ( is_archive() ) {
            echo '<h1>';
            if ( is_day() ) :
                printf( esc_html__( '%1$s %2$s', 'viceunf' ), esc_html__( 'Archivos', 'viceunf' ), get_the_date() );
            elseif ( is_month() ) :
                printf( esc_html__( '%1$s %2$s', 'viceunf' ), esc_html__( 'Archivos', 'viceunf' ), get_the_date( 'F Y' ) );
            elseif ( is_year() ) :
                printf( esc_html__( '%1$s %2$s', 'viceunf' ), esc_html__( 'Archivos', 'viceunf' ), get_the_date( 'Y' ) );
            elseif ( is_author() ) :
                printf( esc_html__( '%1$s %2$s', 'viceunf' ), esc_html__( 'Publicaciones de', 'viceunf' ), get_the_author() );
            elseif ( is_category() ) :
                printf( esc_html__( '%1$s %2$s', 'viceunf' ), esc_html__( 'Categoría', 'viceunf' ), single_cat_title( '', false ) );
            elseif ( is_tag() ) :
                printf( esc_html__( '%1$s %2$s', 'viceunf' ), esc_html__( 'Etiqueta', 'viceunf' ), single_tag_title( '', false ) );
            elseif ( class_exists( 'WooCommerce' ) && is_shop() ) :
                printf( esc_html__( '%1$s %2$s', 'viceunf' ), esc_html__( 'Tienda', 'viceunf' ), single_tag_title( '', false ) );
            elseif ( is_archive() ) :
                the_archive_title( '<h1>', '</h1>' );
            endif;
            echo '</h1>';
        } elseif ( is_404() ) {
            echo '<h1>';
            printf( esc_html__( '%1$s ', 'viceunf' ), esc_html__( '404', 'viceunf' ) );
            echo '</h1>';
        } elseif ( is_search() ) {
            echo '<h1>';
            printf( esc_html__( '%1$s %2$s', 'viceunf' ), esc_html__( 'Resultados de búsqueda para', 'viceunf' ), get_search_query() );
            echo '</h1>';
        } else {
            echo '<h1>' . esc_html( get_the_title() ) . '</h1>';
        }
    }
}
