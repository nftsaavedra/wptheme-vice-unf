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
 * Obtiene la URL del archivo de un reglamento según su tipo de fuente.
 *
 * @param int $post_id ID del post de tipo 'reglamento'.
 * @return string URL del archivo o '#' si no se encuentra.
 */
function viceunf_get_reglamento_url( $post_id ) {
  $source_type = get_post_meta( $post_id, '_reglamento_source_type_key', true );

  if ( 'external' === $source_type ) {
    return get_post_meta( $post_id, '_reglamento_external_url_key', true );
  }

  if ( 'upload' === $source_type ) {
    $file_id = get_post_meta( $post_id, '_reglamento_file_id_key', true );
    return $file_id ? wp_get_attachment_url( $file_id ) : '#';
  }

  return '#';
}

/**
 * Renderiza un item individual de la lista de reglamentos.
 *
 * @param int $post_id ID del post de tipo 'reglamento'.
 */
function viceunf_render_reglamento_item( $post_id ) {
  $file_url  = viceunf_get_reglamento_url( $post_id );
  $permalink = get_permalink( $post_id );

  echo '<li>';
  echo '<a href="' . esc_url( $permalink ) . '" class="reglamento-main-link">';
  echo '<i class="fas fa-file-alt"></i>';
  echo '<span>' . esc_html( get_the_title( $post_id ) ) . '</span>';
  echo '</a>';

  if ( ! empty( $file_url ) && '#' !== $file_url ) {
    echo '<a href="' . esc_url( $file_url ) . '" target="_blank" class="button-download-shortcode">' . esc_html__( 'Descargar', 'viceunf' ) . '</a>';
  }

  echo '</li>';
}

/**
 * Shortcode configurable para listar los Reglamentos.
 * Enlace principal al post y botón de descarga directa.
 *
 * @param array $atts Atributos del shortcode.
 * @return string HTML renderizado.
 */
function viceunf_listar_reglamentos_shortcode( $atts ) {
  $atts = shortcode_atts( array( 'categoria' => '' ), $atts, 'listar_reglamentos' );
  $categoria_slugs = ! empty( $atts['categoria'] ) ? array_map( 'sanitize_title', explode( ',', $atts['categoria'] ) ) : array();
  $taxonomy_name = 'categoria_reglamento';

  ob_start();

  if ( ! empty( $categoria_slugs ) ) {
    $args = array(
      'post_type'      => 'reglamento',
      'posts_per_page' => -1,
      'orderby'        => 'title',
      'order'          => 'ASC',
      'tax_query'      => array(
        array(
          'taxonomy' => $taxonomy_name,
          'field'    => 'slug',
          'terms'    => $categoria_slugs,
        ),
      ),
    );

    $query = new WP_Query( $args );

    if ( $query->have_posts() ) {
      echo '<div class="reglamentos-container">';
      echo '<ul class="reglamentos-list">';
      while ( $query->have_posts() ) {
        $query->the_post();
        viceunf_render_reglamento_item( get_the_ID() );
      }
      echo '</ul>';
      echo '</div>';
    } else {
      echo '<p>' . esc_html__( 'No se encontraron reglamentos en las categorías especificadas.', 'viceunf' ) . '</p>';
    }
    wp_reset_postdata();
  } else {
    $terms = get_terms( array( 'taxonomy' => $taxonomy_name, 'hide_empty' => true ) );

    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
      echo '<div class="reglamentos-container grouped">';
      foreach ( $terms as $term ) {
        $color = get_term_meta( $term->term_id, 'color', true ) ?: '#CCCCCC';
        echo '<div class="reglamento-category-group" style="--category-color: ' . esc_attr( $color ) . ';">';
        echo '<h3 class="category-title">' . esc_html( $term->name ) . '</h3>';

        $args = array(
          'post_type'      => 'reglamento',
          'posts_per_page' => -1,
          'orderby'        => 'title',
          'order'          => 'ASC',
          'tax_query'      => array(
            array(
              'taxonomy' => $taxonomy_name,
              'field'    => 'term_id',
              'terms'    => $term->term_id,
            ),
          ),
        );

        $query = new WP_Query( $args );

        if ( $query->have_posts() ) {
          echo '<ul class="reglamentos-list">';
          while ( $query->have_posts() ) {
            $query->the_post();
            viceunf_render_reglamento_item( get_the_ID() );
          }
          echo '</ul>';
        }
        wp_reset_postdata();
        echo '</div>';
      }
      echo '</div>';
    } else {
      echo '<p>' . esc_html__( 'No hay reglamentos para mostrar.', 'viceunf' ) . '</p>';
    }
  }

  return ob_get_clean();
}
add_shortcode( 'listar_reglamentos', 'viceunf_listar_reglamentos_shortcode' );
