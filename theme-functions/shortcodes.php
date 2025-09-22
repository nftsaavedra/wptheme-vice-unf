<?php

/**
 * Archivo para registrar los shortcodes personalizados del tema.
 *
 * @package vice-unf
 */

if (! defined('ABSPATH')) {
  exit; // Salir si se accede directamente al archivo.
}

/**
 * Shortcode configurable para listar los Reglamentos.
 * VERSIÓN MEJORADA: Utiliza el color de la categoría para el diseño.
 *
 * @param array $atts Atributos del shortcode.
 * @return string HTML renderizado.
 */
function vice_unf_listar_reglamentos_configurable_shortcode($atts)
{
  // ... (El código de esta sección no cambia) ...
  $atts = shortcode_atts(
    array('categoria' => ''),
    $atts,
    'listar_reglamentos'
  );
  $categoria_slugs = ! empty($atts['categoria']) ? array_map('sanitize_title', explode(',', $atts['categoria'])) : array();
  ob_start();
  function vice_unf_get_reglamento_url($post_id)
  {
    $source_type = get_post_meta($post_id, '_reglamento_source_type_key', true);
    if ($source_type === 'external') {
      return get_post_meta($post_id, '_reglamento_external_url_key', true);
    }
    if ($source_type === 'upload') {
      $file_id = get_post_meta($post_id, '_reglamento_file_id_key', true);
      return $file_id ? wp_get_attachment_url($file_id) : '#';
    }
    return '#';
  }
  $taxonomy_name = 'categoria_reglamento';

  // CASO A: Filtrar por categoría(s) - No se aplica color aquí porque no hay títulos de grupo.
  if (! empty($categoria_slugs)) {
    // ... (Esta sección de código no cambia) ...
    $args = array('post_type' => 'reglamento', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC', 'tax_query' => array(array('taxonomy' => $taxonomy_name, 'field' => 'slug', 'terms' => $categoria_slugs)));
    $query = new WP_Query($args);
    if ($query->have_posts()) {
      echo '<div class="reglamentos-container">';
      echo '<ul class="reglamentos-list">';
      while ($query->have_posts()) {
        $query->the_post();
        $reglamento_url = vice_unf_get_reglamento_url(get_the_ID());
        echo '<li><a href="' . esc_url($reglamento_url) . '" target="_blank"><i class="fas fa-file-alt"></i><span>' . esc_html(get_the_title()) . '</span></a></li>';
      }
      echo '</ul>';
      echo '</div>';
    } else {
      echo '<p>No se encontraron reglamentos en las categorías especificadas.</p>';
    }
    wp_reset_postdata();

    // CASO B: Agrupar por categorías - Aquí aplicaremos el color.
  } else {
    $terms = get_terms(array('taxonomy' => $taxonomy_name, 'hide_empty' => true));
    if (! empty($terms) && ! is_wp_error($terms)) {
      echo '<div class="reglamentos-container grouped">';
      foreach ($terms as $term) {
        // --- INICIO DE CAMBIOS ---
        // 1. Obtenemos el color guardado para esta categoría.
        $color = get_term_meta($term->term_id, 'color', true) ?: '#CCCCCC'; // Usamos gris si no hay color definido.

        // 2. Creamos una variable de CSS (--category-color) en el contenedor del grupo.
        echo '<div class="reglamento-category-group" style="--category-color: ' . esc_attr($color) . ';">';
        // --- FIN DE CAMBIOS ---

        echo '<h3 class="category-title">' . esc_html($term->name) . '</h3>';
        $args = array('post_type' => 'reglamento', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC', 'tax_query' => array(array('taxonomy' => $taxonomy_name, 'field' => 'term_id', 'terms' => $term->term_id)));
        $query = new WP_Query($args);

        if ($query->have_posts()) {
          echo '<ul class="reglamentos-list">';
          while ($query->have_posts()) {
            $query->the_post();
            $reglamento_url = vice_unf_get_reglamento_url(get_the_ID());
            echo '<li><a href="' . esc_url($reglamento_url) . '" target="_blank"><i class="fas fa-file-alt"></i><span>' . esc_html(get_the_title()) . '</span></a></li>';
          }
          echo '</ul>';
        }
        wp_reset_postdata();
        echo '</div>';
      }
      echo '</div>';
    } else {
      echo '<p>No hay reglamentos para mostrar.</p>';
    }
  }

  return ob_get_clean();
}
add_shortcode('listar_reglamentos', 'vice_unf_listar_reglamentos_configurable_shortcode');
