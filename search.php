<?php

/**
 * The template for displaying search results pages.
 *
 * Refactorizado para usar el sistema de tarjetas unificado
 * (content-summary.php) y mantener coherencia visual con archive/home.
 *
 * @package ViceUnf
 */

if (! defined('ABSPATH')) {
  exit;
}

get_header();
?>

<section class="dt_posts dt-py-default viceunf-bg-canvas">
  <div class="dt-container">
    <div class="dt-row dt-g-5">

      <?php
      $is_sidebar_active = is_active_sidebar('viceunf-sidebar-primary');
      $main_column_class = ! $is_sidebar_active ? 'dt-col-lg-12' : 'dt-col-lg-8';
      $post_column_class = ! $is_sidebar_active ? 'dt-col-lg-4 dt-col-sm-6 dt-col-12' : 'dt-col-lg-6 dt-col-md-6 dt-col-12';
      ?>

      <div id="dt-main-content" class="<?php echo esc_attr($main_column_class); ?> dt-col-md-12 dt-col-12 wow fadeInUp">

        <?php if (have_posts()) : ?>

          <div class="page-header dt-mb-4">
            <h1 class="page-title">
              <?php
              printf(
                /* translators: %s: search query */
                esc_html__('Resultados de búsqueda para: %s', 'viceunf'),
                '<span>' . get_search_query() . '</span>'
              );
              ?>
            </h1>
          </div>

          <div class="dt-row dt-g-4">
            <?php while (have_posts()) : the_post(); ?>
              <div class="<?php echo esc_attr($post_column_class); ?>">
                <?php get_template_part('template-parts/content', 'summary'); ?>
              </div>
            <?php endwhile; ?>
          </div>

          <?php
          the_posts_pagination(array(
            'mid_size'  => 2,
            'prev_text' => '<i class="fa fa-angle-double-left"></i>',
            'next_text' => '<i class="fa fa-angle-double-right"></i>',
            'screen_reader_text' => ' ',
          ));
          ?>

        <?php else : ?>

          <div class="no-results dt-text-center dt-py-default">
            <h2><?php esc_html_e('No se encontraron resultados', 'viceunf'); ?></h2>
            <p class="dt-mt-3"><?php esc_html_e('Lo sentimos, no encontramos nada con esos términos. Por favor intenta con otros términos.', 'viceunf'); ?></p>
            <div class="dt-mt-4">
              <?php get_search_form(); ?>
            </div>
          </div>

        <?php endif; ?>

      </div>
      <?php get_sidebar(); ?>

    </div>
  </div>
</section>

<?php get_footer(); ?>