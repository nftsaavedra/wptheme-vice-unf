<?php
// Salir si se accede directamente.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Clase ViceUnf_Breadcrumbs_Builder
 *
 * Responsabilidad: Construir la navegación de migas de pan (Breadcrumbs)
 * según la jerarquía y contexto de WordPress.
 */
class ViceUnf_Breadcrumbs_Builder {

    /**
     * Devuelve la URL de la página actual de manera segura con APIs de WordPress.
     * (Reemplaza el uso inseguro de $_SERVER)
     */
    public static function get_current_url() {
        global $wp;
        return home_url( add_query_arg( array(), $wp->request ) );
    }

    /**
     * Renderiza el listado HTML de los breadcrumbs.
     */
    public static function render() {
        global $post;
        $home_link = home_url();

        if ( is_home() || is_front_page() ) {
            echo '<li class="breadcrumb-item"><a href="' . esc_url( $home_link ) . '">' . esc_html__( 'Inicio', 'viceunf' ) . '</a></li>';
            echo '<li class="breadcrumb-item active">';
            echo single_post_title();
            echo '</li>';
            return;
        }

        echo '<li class="breadcrumb-item"><a href="' . esc_url( $home_link ) . '">' . esc_html__( 'Inicio', 'viceunf' ) . '</a></li>';
        
        $current_url = self::get_current_url();

        if ( is_category() ) {
            echo '<li class="breadcrumb-item active"><a href="' . esc_url( $current_url ) . '">' . esc_html__( 'Archivo por categoría', 'viceunf' ) . ' "' . single_cat_title( '', false ) . '"</a></li>';
        } elseif ( is_day() ) {
            echo '<li class="breadcrumb-item active"><a href="' . esc_url( get_year_link( get_the_time( 'Y' ) ) ) . '">' . get_the_time( 'Y' ) . '</a>';
            echo '<li class="breadcrumb-item active"><a href="' . esc_url( get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) ) . '">' . get_the_time( 'F' ) . '</a>';
            echo '<li class="breadcrumb-item active"><a href="' . esc_url( $current_url ) . '">' . get_the_time( 'd' ) . '</a></li>';
        } elseif ( is_month() ) {
            echo '<li class="breadcrumb-item active"><a href="' . esc_url( get_year_link( get_the_time( 'Y' ) ) ) . '">' . get_the_time( 'Y' ) . '</a>';
            echo '<li class="breadcrumb-item active"><a href="' . esc_url( $current_url ) . '">' . get_the_time( 'F' ) . '</a></li>';
        } elseif ( is_year() ) {
            echo '<li class="breadcrumb-item active"><a href="' . esc_url( $current_url ) . '">' . get_the_time( 'Y' ) . '</a></li>';
        } elseif ( is_single() && ! is_attachment() && is_page( 'single-product' ) ) {
            if ( 'post' !== get_post_type() ) {
                $cat = get_the_category();
                $cat = $cat[0];
                echo '<li class="breadcrumb-item">';
                echo get_category_parents( $cat->term_id, true, '' );
                echo '</li>';
                echo '<li class="breadcrumb-item active"><a href="' . esc_url( $current_url ) . '">' . get_the_title() . '</a></li>';
            }
        } elseif ( is_page() && $post->post_parent ) {
            $parent_id   = $post->post_parent;
            $breadcrumbs = array();
            while ( $parent_id ) {
                $page          = get_post( $parent_id );
                $breadcrumbs[] = '<li class="breadcrumb-item active"><a href="' . esc_url( get_permalink( $page->ID ) ) . '">' . get_the_title( $page->ID ) . '</a>';
                $parent_id     = $page->post_parent;
            }
            $breadcrumbs = array_reverse( $breadcrumbs );
            foreach ( $breadcrumbs as $crumb ) {
                echo $crumb;
            }
            echo '<li class="breadcrumb-item active"><a href="' . esc_url( $current_url ) . '">' . get_the_title() . '</a></li>';
        } elseif ( is_search() ) {
            echo '<li class="breadcrumb-item active"><a href="' . esc_url( $current_url ) . '">' . get_search_query() . '</a></li>';
        } elseif ( is_404() ) {
            echo '<li class="breadcrumb-item active"><a href="' . esc_url( $current_url ) . '">' . esc_html__( 'Error 404', 'viceunf' ) . '</a></li>';
        } else {
            echo '<li class="breadcrumb-item active"><a href="' . esc_url( $current_url ) . '">' . get_the_title() . '</a></li>';
        }
    }
}
