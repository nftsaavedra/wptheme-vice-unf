<?php

/**
 * =================================================================
 * Archivo de Widgets Personalizados para el Tema ViceUnf
 * =================================================================
 *
 * @package ViceUnf
 */

if (!defined('ABSPATH')) exit;

/**
 * Clase ViceUnf_Recent_Posts_Widget
 * Muestra título y miniatura de entradas, AHORA CON FILTRO DE CATEGORÍA.
 */
class ViceUnf_Recent_Posts_Widget extends WP_Widget
{
  public function __construct()
  {
    parent::__construct(
      'viceunf_recent_posts',
      __('ViceUnf - Entradas Recientes con Miniaturas', 'viceunf'),
      ['description' => __('Muestra una lista de entradas recientes, con opción de filtrar por categoría.', 'viceunf')]
    );
  }

  public function widget($args, $instance)
  {
    echo $args['before_widget'];

    $title = apply_filters('widget_title', empty($instance['title']) ? __('Entradas Recientes', 'viceunf') : $instance['title'], $instance, $this->id_base);
    if ($title) {
      echo $args['before_title'] . esc_html($title) . $args['after_title'];
    }

    $number_of_posts = !empty($instance['number_of_posts']) ? absint($instance['number_of_posts']) : 5;
    $char_limit = !empty($instance['char_limit']) ? absint($instance['char_limit']) : 55;
    // --- NUEVO: Obtenemos las categorías seleccionadas ---
    $selected_categories = !empty($instance['categories']) ? $instance['categories'] : [];

    // --- NUEVO: Modificamos los argumentos de la consulta ---
    $query_args = [
      'posts_per_page'      => $number_of_posts,
      'no_found_rows'       => true,
      'post_status'         => 'publish',
      'ignore_sticky_posts' => true,
    ];

    // Si hay categorías seleccionadas, las añadimos a la consulta
    if (!empty($selected_categories)) {
      $query_args['category__in'] = $selected_categories;
    }

    $recent_posts = new WP_Query($query_args);

    if ($recent_posts->have_posts()) :
      echo '<div class="viceunf-recent-posts-list">';
      $counter = 0;

      while ($recent_posts->have_posts()) : $recent_posts->the_post();
        $counter++;
        $item_class = 'viceunf-recent-post-item';
        if ($counter % 2 == 0) {
          $item_class .= ' item-par';
        }

        $post_title = get_the_title();
        if (mb_strlen($post_title) > $char_limit) {
          $post_title = mb_substr($post_title, 0, $char_limit) . '...';
        }
?>
        <div class="<?php echo esc_attr($item_class); ?>">
          <?php if (has_post_thumbnail()) : ?>
            <a href="<?php the_permalink(); ?>" class="viceunf-recent-post-thumbnail">
              <?php the_post_thumbnail('thumbnail'); ?>
            </a>
          <?php endif; ?>

          <div class="viceunf-recent-post-content">
            <h5 class="viceunf-recent-post-title">
              <a href="<?php the_permalink(); ?>"><?php echo esc_html($post_title); ?></a>
            </h5>
          </div>
        </div>
    <?php
      endwhile;
      echo '</div>';
      wp_reset_postdata();
    endif;

    echo $args['after_widget'];
  }

  public function form($instance)
  {
    $title = isset($instance['title']) ? esc_attr($instance['title']) : __('Entradas Recientes', 'viceunf');
    $number_of_posts = isset($instance['number_of_posts']) ? absint($instance['number_of_posts']) : 5;
    $char_limit = isset($instance['char_limit']) ? absint($instance['char_limit']) : 55;
    // --- NUEVO: Obtenemos las categorías seleccionadas y todas las categorías disponibles ---
    $selected_categories = isset($instance['categories']) && is_array($instance['categories']) ? $instance['categories'] : [];
    $all_categories = get_categories(['hide_empty' => 0]);
    ?>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Título:', 'viceunf'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('number_of_posts')); ?>"><?php _e('Número de entradas a mostrar:', 'viceunf'); ?></label>
      <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number_of_posts')); ?>" name="<?php echo esc_attr($this->get_field_name('number_of_posts')); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number_of_posts); ?>" size="3" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('char_limit')); ?>"><?php _e('Límite de caracteres para el título:', 'viceunf'); ?></label>
      <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('char_limit')); ?>" name="<?php echo esc_attr($this->get_field_name('char_limit')); ?>" type="number" step="1" min="10" value="<?php echo esc_attr($char_limit); ?>" size="3" />
    </p>

    <p><strong><?php _e('Filtrar por categoría(s):', 'viceunf'); ?></strong></p>
    <div style="max-height: 150px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background-color: #fff;">
      <?php foreach ($all_categories as $category) : ?>
        <p>
          <input type="checkbox" id="<?php echo esc_attr($this->get_field_id('categories') . '-' . $category->term_id); ?>" name="<?php echo esc_attr($this->get_field_name('categories')); ?>[]" value="<?php echo esc_attr($category->term_id); ?>" <?php checked(in_array($category->term_id, $selected_categories)); ?>>
          <label for="<?php echo esc_attr($this->get_field_id('categories') . '-' . $category->term_id); ?>"><?php echo esc_html($category->name); ?></label>
        </p>
      <?php endforeach; ?>
    </div>
    <p class="description"><?php _e('Si no seleccionas ninguna, se mostrarán las entradas de todas las categorías.', 'viceunf'); ?></p>

<?php
  }

  public function update($new_instance, $old_instance)
  {
    $instance = [];
    $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
    $instance['number_of_posts'] = (!empty($new_instance['number_of_posts'])) ? absint($new_instance['number_of_posts']) : 5;
    $instance['char_limit'] = (!empty($new_instance['char_limit'])) ? absint($new_instance['char_limit']) : 55;

    // --- NUEVO: Guardar las categorías seleccionadas ---
    $instance['categories'] = (!empty($new_instance['categories']) && is_array($new_instance['categories'])) ? array_map('absint', $new_instance['categories']) : [];

    return $instance;
  }
}

// Registra el widget.
function viceunf_register_custom_widgets()
{
  register_widget('ViceUnf_Recent_Posts_Widget');
}
add_action('widgets_init', 'viceunf_register_custom_widgets');
