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
 * Crea un widget que reutiliza la estructura de tarjeta del tema.
 */
class ViceUnf_Recent_Posts_Widget extends WP_Widget
{
  public function __construct()
  {
    parent::__construct(
      'viceunf_recent_posts',
      __('ViceUnf - Entradas Recientes con Miniaturas', 'viceunf'),
      ['description' => __('Muestra las entradas recientes con miniaturas, reutilizando el diseño de tarjeta del tema.', 'viceunf')]
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

    $recent_posts = new WP_Query([
      'posts_per_page'      => $number_of_posts,
      'no_found_rows'       => true,
      'post_status'         => 'publish',
      'ignore_sticky_posts' => true,
    ]);

    if ($recent_posts->have_posts()) :
      // No necesitamos un div wrapper extra, ya que el widget en sí ya actúa como uno.
      while ($recent_posts->have_posts()) : $recent_posts->the_post();

        // Añadimos una clase 'modificadora' para los estilos específicos del sidebar.
        $post_classes = ['dt_post_item', 'dt_posts--one', 'dt-mb-4', 'dt_post_item--widget'];
?>
        <article id="post-<?php the_ID(); ?>" <?php post_class($post_classes); ?>>

          <?php if (has_post_thumbnail()) : ?>
            <div class="image">
              <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('thumbnail'); // Usamos 'thumbnail' para un tamaño optimizado. 
                ?>
              </a>
            </div>
          <?php endif; ?>

          <div class="inner">
            <div class="meta">
              <ul>
                <li>
                  <div class="date">
                    <i class="far fa-calendar-alt dt-mr-2" aria-hidden="true"></i>
                    <?php echo get_the_date(); ?>
                  </div>
                </li>
              </ul>
            </div>
            <h4 class="title">
              <a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
            </h4>
          </div>
        </article>
    <?php
      endwhile;
      wp_reset_postdata();
    endif;

    echo $args['after_widget'];
  }

  public function form($instance)
  {
    $title = isset($instance['title']) ? esc_attr($instance['title']) : __('Entradas Recientes', 'viceunf');
    $number_of_posts = isset($instance['number_of_posts']) ? absint($instance['number_of_posts']) : 5;
    ?>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Título:', 'viceunf'); ?></label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
    </p>
    <p>
      <label for="<?php echo esc_attr($this->get_field_id('number_of_posts')); ?>"><?php _e('Número de entradas a mostrar:', 'viceunf'); ?></label>
      <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number_of_posts')); ?>" name="<?php echo esc_attr($this->get_field_name('number_of_posts')); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number_of_posts); ?>" size="3" />
    </p>
<?php
  }

  public function update($new_instance, $old_instance)
  {
    $instance = [];
    $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
    $instance['number_of_posts'] = (!empty($new_instance['number_of_posts'])) ? absint($new_instance['number_of_posts']) : 5;
    return $instance;
  }
}

// Registra el widget.
function viceunf_register_custom_widgets()
{
  register_widget('ViceUnf_Recent_Posts_Widget');
}
add_action('widgets_init', 'viceunf_register_custom_widgets');
