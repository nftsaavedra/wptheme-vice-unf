<?php

/**
 * =================================================================
 * The template for displaying the blog posts index (home.php) for the ViceUnf theme.
 * =================================================================
 *
 * This template is used for the page set as the "Posts page" in WordPress's reading settings.
 * It displays posts in a vertical list format, with each post styled as a card.
 *
 * @package ViceUnf
 */

get_header();
?>

<section id="dt_posts" class="dt_posts dt-py-default">
  <div class="dt-container">
    <div class="dt-row dt-g-5">

      <?php
      // Se define la clase de la columna principal. Si el sidebar está activo,
      // el contenido principal ocupa 8 de 12 columnas. Si no, ocupa las 12.
      $main_column_class = !is_active_sidebar('softme-sidebar-primary') ? 'dt-col-lg-12' : 'dt-col-lg-8';
      ?>

      <div id="dt-main-content" class="<?php echo esc_attr($main_column_class); ?> dt-col-md-12 dt-col-12 wow fadeInUp">

        <?php if (have_posts()) : ?>

          <div class="page-header dt-mb-4">
            <?php // En home.php, usamos un título estático o dinámico en lugar de un título de archivo. 
            ?>
            <div class="archive-description">
              <p>Explora nuestras últimas noticias, artículos y comunicados.</p>
            </div>
          </div><?php
                // Inicia el bucle de WordPress para mostrar cada post.
                while (have_posts()) :
                  the_post();

                  // Reutilizamos las mismas clases de diseño para mantener la coherencia visual.
                  $post_classes = ['dt_post_item', 'dt_posts--one', 'dt-mb-4'];
                ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class($post_classes); ?>>

              <?php if (has_post_thumbnail()) : ?>

                <a class="image" href="<?php the_permalink(); ?>">
                  <?php the_post_thumbnail('large', ['alt' => the_title_attribute('echo=0')]); ?>
                </a>

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
                <div class="catetag">
                  <i class="fas fa-folder dt-mr-1" aria-hidden="true"></i>
                  <?php the_category(', '); ?>
                </div>

                <h4 class="title">
                  <a href="<?php the_permalink(); ?>" rel="bookmark">
                    <?php the_title(); ?>
                  </a>
                </h4>

                <div class="content">
                  <?php
                  // Extracto controlado a 35 palabras.
                  echo '<p>' . wp_kses_post(wp_trim_words(get_the_content(), 35, '...')) . '</p>';
                  ?>
                  <a href="<?php the_permalink(); ?>" class="more-link">Leer más</a>
                </div>
              </div>
            </article>

        <?php
                endwhile; // Fin del bucle.

                // Paginación.
                the_posts_pagination(array(
                  'prev_text' => '<i class="fa fa-angle-double-left"></i>',
                  'next_text' => '<i class="fa fa-angle-double-right"></i>',
                  'screen_reader_text' => ' ',
                ));

              else :
                // Si no hay posts, muestra la plantilla 'content-none.php'
                get_template_part('template-parts/content', 'none');

              endif;
        ?>

      </div><?php
            // Llama al archivo sidebar.php para mostrar la barra lateral.
            get_sidebar();
            ?>

    </div>
  </div>
</section>

<?php get_footer(); ?>