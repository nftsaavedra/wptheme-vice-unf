<?php

/**
 * Template part for displaying a message that posts cannot be found.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package SoftMe
 */

?>
<div id="post-<?php the_ID(); ?>" <?php post_class('dt_post_item dt_posts--one dt-mb-4'); ?>>
  <div class="inner">
    <?php
    if (is_home() && current_user_can('publish_posts')) :

      printf(
        '<p>' . wp_kses(
          /* translators: 1: link to WP admin new post page. */
          __('¿Listo para publicar tu primera entrada? <a href="%1$s">Comienza aquí</a>.', 'softme'),
          array(
            'a' => array(
              'href' => array(),
            ),
          )
        ) . '</p>',
        esc_url(admin_url('post-new.php'))
      );

    elseif (is_search()) :
    ?>

      <p><?php esc_html_e('Lo sentimos, no se encontraron resultados para tu búsqueda. Por favor intenta con otras palabras clave.', 'softme'); ?></p>
    <?php
      get_search_form();

    else :
    ?>

      <p><?php esc_html_e('Parece que no encontramos lo que buscas. Quizás una búsqueda pueda ayudarte.', 'softme'); ?></p>
    <?php
      get_search_form();

    endif;
    ?>
  </div>
</div>