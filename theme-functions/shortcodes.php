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
 * VERSIÓN MEJORADA: Enlace principal al post y botón de descarga directa.
 *
 * @param array $atts Atributos del shortcode.
 * @return string HTML renderizado.
 */
function vice_unf_listar_reglamentos_configurable_shortcode($atts)
{
  // ... (La lógica de atributos y la función auxiliar no cambian) ...
  $atts = shortcode_atts(array('categoria' => ''), $atts, 'listar_reglamentos');
  $categoria_slugs = ! empty($atts['categoria']) ? array_map('sanitize_title', explode(',', $atts['categoria'])) : array();
  ob_start();
  if (!function_exists('vice_unf_get_reglamento_url')) {
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
  }
  $taxonomy_name = 'categoria_reglamento';

  // Función para renderizar un item de la lista (para no repetir código - DRY)
  function render_reglamento_item($post_id)
  {
    $file_url = vice_unf_get_reglamento_url($post_id);
    $permalink = get_permalink($post_id);

    // --- INICIO DE LA NUEVA ESTRUCTURA HTML ---
    echo '<li>';
    // El enlace principal ahora va al permalink del post
    echo '<a href="' . esc_url($permalink) . '" class="reglamento-main-link">';
    echo '<i class="fas fa-file-alt"></i>';
    echo '<span>' . esc_html(get_the_title($post_id)) . '</span>';
    echo '</a>';
    // El nuevo botón de descarga va directamente al archivo
    if (!empty($file_url) && $file_url !== '#') {
      echo '<a href="' . esc_url($file_url) . '" target="_blank" class="button-download-shortcode">Descargar</a>';
    }
    echo '</li>';
    // --- FIN DE LA NUEVA ESTRUCTURA HTML ---
  }

  if (! empty($categoria_slugs)) {
    // ... (La lógica de consulta no cambia) ...
    $args = array('post_type' => 'reglamento', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC', 'tax_query' => array(array('taxonomy' => $taxonomy_name, 'field' => 'slug', 'terms' => $categoria_slugs)));
    $query = new WP_Query($args);
    if ($query->have_posts()) {
      echo '<div class="reglamentos-container">';
      echo '<ul class="reglamentos-list">';
      while ($query->have_posts()) {
        $query->the_post();
        render_reglamento_item(get_the_ID()); // Usamos la nueva función
      }
      echo '</ul>';
      echo '</div>';
    } else {
      echo '<p>No se encontraron reglamentos en las categorías especificadas.</p>';
    }
    wp_reset_postdata();
  } else {
    $terms = get_terms(array('taxonomy' => $taxonomy_name, 'hide_empty' => true));
    if (! empty($terms) && ! is_wp_error($terms)) {
      echo '<div class="reglamentos-container grouped">';
      foreach ($terms as $term) {
        $color = get_term_meta($term->term_id, 'color', true) ?: '#CCCCCC';
        echo '<div class="reglamento-category-group" style="--category-color: ' . esc_attr($color) . ';">';
        echo '<h3 class="category-title">' . esc_html($term->name) . '</h3>';
        $args = array('post_type' => 'reglamento', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC', 'tax_query' => array(array('taxonomy' => $taxonomy_name, 'field' => 'term_id', 'terms' => $term->term_id)));
        $query = new WP_Query($args);
        if ($query->have_posts()) {
          echo '<ul class="reglamentos-list">';
          while ($query->have_posts()) {
            $query->the_post();
            render_reglamento_item(get_the_ID()); // Usamos la nueva función
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
