<?php

/**
 * =================================================================
 * The template for displaying the blog posts index (home.php) for the ViceUnf theme.
 * =================================================================
 *
 * This template displays posts in a responsive grid format.
 * - 3 columns if no sidebar is active.
 * - 2 columns if a sidebar is active.
 *
 * @package ViceUnf
 */

get_header();
?>

<section id="dt_posts" class="dt_posts dt-py-default">
  <div class="dt-container">
    <div class="dt-row dt-g-5">

      <?php
      // Lógica para determinar el ancho de las columnas.
      $is_sidebar_active = is_active_sidebar('softme-sidebar-primary');
      $main_column_class = !$is_sidebar_active ? 'dt-col-lg-12' : 'dt-col-lg-8';

      // --- NUEVA LÓGICA PARA LA CUADRÍCULA ---
      // Si hay sidebar (2 columnas), cada post ocupa 6/12.
      // Si no hay sidebar (3 columnas), cada post ocupa 4/12.
      $post_column_class = !$is_sidebar_active ? 'dt-col-lg-4 dt-col-sm-6 dt-col-12' : 'dt-col-lg-6 dt-col-md-6 dt-col-12';
      ?>

      <div id="dt-main-content" class="<?php echo esc_attr($main_column_class); ?> dt-col-md-12 dt-col-12 wow fadeInUp">

        <?php if (have_posts()) : ?>

          <div class="page-header dt-mb-4">
            <h1 class="page-title"><?php single_post_title(); ?></h1>
            <div class="archive-description">
              <p>Explora nuestras últimas noticias, artículos y comunicados.</p>
            </div>
          </div>

          <?php // --- NUEVO: Contenedor de la cuadrícula --- 
          ?>
          <div class="dt-row dt-g-4">
            <?php
            // Inicia el bucle.
            while (have_posts()) :
              the_post();
            ?>
              <?php // --- NUEVO: Envolvemos cada post en su propia columna --- 
              ?>
              <div class="<?php echo esc_attr($post_column_class); ?>">
                <?php
                // Llamamos a la plantilla de parte, que no necesita cambios.
                get_template_part('template-parts/content', 'summary');
                ?>
              </div>
            <?php
            endwhile; // Fin del bucle.
            ?>
          </div><?php
                // La paginación va fuera de la fila de la cuadrícula.
                the_posts_pagination(array(
                  'prev_text' => '<i class="fa fa-angle-double-left"></i>',
                  'next_text' => '<i class="fa fa-angle-double-right"></i>',
                  'screen_reader_text' => ' ',
                ));

              else :
                get_template_part('template-parts/content', 'none');
              endif;
                ?>

      </div><?php get_sidebar(); ?>

    </div>
  </div>
</section>

<?php get_footer(); ?>