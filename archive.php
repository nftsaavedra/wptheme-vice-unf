<?php

/**
 * =================================================================
 * The template for displaying archive pages for the ViceUnf theme.
 * =================================================================
 *
 * This template displays posts in a vertical list format. Each post
 * is styled as a card, respecting the sidebar layout.
 *
 * @package ViceUnf
 */

get_header();
?>

<section class="dt_posts dt-py-default">
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
            <?php
            // Muestra el título y la descripción del archivo (ej. "Categoría: Noticias")
            the_archive_title('<h1 class="page-title">', '</h1>');
            the_archive_description('<div class="archive-description">', '</div>');
            ?>
          </div><?php
                // Inicia el bucle de WordPress para mostrar cada post en una lista.
                while (have_posts()) :
                  the_post();
                  // Definimos las clases de diseño para cada tarjeta de post, basadas en tu ejemplo.
                  $post_classes = ['dt_post_item', 'dt_posts--one', 'dt-mb-4'];
                ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class($post_classes); ?>>

              <?php if (has_post_thumbnail()) : ?>

                <div class="image">

                  <?php
                    // Usamos 'large' para una buena calidad de imagen.
                    // Esta función genera srcset para imágenes adaptables, mejorando el rendimiento.
                    the_post_thumbnail('large', ['alt' => the_title_attribute('echo=0')]);
                  ?>
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
                  // Extracto controlado a 35 palabras. Puedes ajustar este número.
                  echo '<p>' . wp_kses_post(wp_trim_words(get_the_content(), 35, '...')) . '</p>';
                  ?>
                  <a href="<?php the_permalink(); ?>" class="more-link">Leer más</a>
                </div>
              </div>
            </article>

        <?php
                endwhile; // Fin del bucle.

                // Muestra la paginación.
                the_posts_pagination(array(
                  'prev_text' => '<i class="fa fa-angle-double-left"></i>',
                  'next_text' => '<i class="fa fa-angle-double-right"></i>',
                  'screen_reader_text' => ' ', // Oculta el texto "Navegación de entradas" si no lo deseas.
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