<?php

/**
 * Plantilla para mostrar un único post del tipo "Reglamento".
 * VERSIÓN MODERNA UI/UX: Muestra vista previa del PDF en un contenedor responsive
 * y una tarjeta de descarga premium.
 *
 * @package ViceUnf
 */

get_header();
?>

<section id="dt_single_post" class="dt_single_post dt-py-default viceunf-single-doc-section">
  <div class="dt-container">
    <div class="dt-row dt-g-5 justify-content-center">

      <?php
      // Lógica para determinar el ancho de las columnas.
      $is_sidebar_active = is_active_sidebar('viceunf-sidebar-primary');
      $main_column_class = !$is_sidebar_active ? 'dt-col-lg-10 dt-col-xl-9' : 'dt-col-lg-8';
      ?>

      <div id="dt-main-content" class="<?php echo esc_attr($main_column_class); ?> dt-col-md-12 dt-col-12 wow fadeInUp">
        <main id="main" class="site-main">

          <?php
          while (have_posts()) :
            the_post();

            // --- INICIO: LÓGICA DE ARCHIVO ---
            $source_type = get_post_meta(get_the_ID(), '_reglamento_source_type_key', true);
            $file_url = '';

            if ($source_type === 'external') {
              $file_url = get_post_meta(get_the_ID(), '_reglamento_external_url_key', true);
            } elseif ($source_type === 'upload') {
              $file_id = get_post_meta(get_the_ID(), '_reglamento_file_id_key', true);
              $file_url = $file_id ? wp_get_attachment_url($file_id) : '';
            }
            // --- FIN: LÓGICA DE ARCHIVO ---
          ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class('viceunf-single-doc__article'); ?>>

              <header class="viceunf-single-doc__header">
                <?php
                // Mostrar categorías como "chips"
                $terms = get_the_terms(get_the_ID(), 'categoria_reglamento');
                if ($terms && !is_wp_error($terms)) {
                  echo '<div class="viceunf-single-doc__tags">';
                  foreach ($terms as $term) {
                    echo '<span class="viceunf-single-doc__tag">' . esc_html($term->name) . '</span>';
                  }
                  echo '</div>';
                }
                ?>

                <?php the_title('<h1 class="viceunf-single-doc__title">', '</h1>'); ?>

                <div class="viceunf-single-doc__meta">
                  <span class="meta-item"><i class="far fa-calendar-alt"></i> Publicado: <?php echo get_the_date(); ?></span>
                  <span class="meta-item"><i class="far fa-clock"></i> Actualizado: <?php echo get_the_modified_date(); ?></span>
                </div>
              </header>

              <div class="viceunf-single-doc__content entry-content">
                <?php the_content(); ?>
              </div>

              <?php if (!empty($file_url)) :
                $extension = strtolower(pathinfo($file_url, PATHINFO_EXTENSION));
                $is_pdf = ($extension === 'pdf');
              ?>
                <div class="viceunf-single-doc__media">
                  <?php if ($is_pdf) : ?>
                    <!-- Vista previa del PDF nativa y responsiva -->
                    <div class="viceunf-single-doc__preview" style="width: 100%; height: 85vh; margin-bottom: 2.5rem; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                      <object data="<?php echo esc_url($file_url); ?>#toolbar=0&navpanes=0&scrollbar=0&view=FitH" type="application/pdf" width="100%" height="100%" style="display: block;">
                        <div style="padding: 2rem; text-align: center; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
                          <i class="fas fa-file-pdf" style="font-size: 3rem; color: #ef4444; margin-bottom: 1rem;"></i>
                          <p style="margin-bottom: 1rem;">El visor de PDF integrado no es compatible con el navegador actual.</p>
                          <a href="<?php echo esc_url($file_url); ?>" target="_blank" class="dt-btn dt-btn-solid">Descargar el archivo PDF directly</a>
                        </div>
                      </object>
                    </div>
                  <?php endif; ?>

                  <!-- Tarjeta de Descarga Premium -->
                  <div class="viceunf-single-doc__download-card">
                    <div class="card-icon">
                      <i class="fas fa-file-<?php echo $is_pdf ? 'pdf' : 'alt'; ?>"></i>
                    </div>
                    <div class="card-info">
                      <h3 class="card-title">Documento</h3>
                      <p class="card-desc">Accede a la versión en <?php echo strtoupper($extension); ?> del documento para su lectura completa o impresión.</p>
                    </div>
                    <div class="card-action">
                      <a href="<?php echo esc_url($file_url); ?>" target="_blank" rel="noopener noreferrer" class="dt-btn dt-btn-solid">
                        <span class="dt-btn-text">
                          <span><i class="fas fa-arrow-down"></i> Descargar</span>
                        </span>
                      </a>
                    </div>
                  </div>
                </div>
              <?php else : ?>
                <!-- Estado vacío cuando no hay archivo -->
                <div class="viceunf-single-doc__empty">
                  <i class="fas fa-folder-open"></i>
                  <p>Este documento no tiene un archivo digital anexo para descargar.</p>
                </div>
              <?php endif; ?>

            </article>

          <?php endwhile; // Fin del Loop. 
          ?>

          <!-- Controles de navegación interior -->
          <div class="viceunf-single-doc__nav-wrap dt-mt-4 dt-mb-5">
            <a href="javascript:history.back()" class="dt-btn dt-btn-outline viceunf-single-doc__back-btn">
              <span class="dt-btn-text">
                <span><i class="fas fa-arrow-left"></i> Volver a la lista</span>
              </span>
            </a>
          </div>

          <!-- Documentos Relacionados -->
          <?php
          $terms = get_the_terms(get_the_ID(), 'categoria_reglamento');
          if ($terms && !is_wp_error($terms)) {
            $term_ids = wp_list_pluck($terms, 'term_id');

            $related_args = [
              'post_type'      => get_post_type(),
              'posts_per_page' => 3,
              'post__not_in'   => [get_the_ID()],
              'tax_query'      => [
                [
                  'taxonomy' => 'categoria_reglamento',
                  'field'    => 'term_id',
                  'terms'    => $term_ids,
                ]
              ]
            ];

            $related_query = new WP_Query($related_args);

            if ($related_query->have_posts()) :
          ?>
              <div class="viceunf-single-doc__related">
                <h3 class="viceunf-single-doc__related-title">Documentos Relacionados</h3>
                <div class="dt-row dt-g-3">
                  <?php while ($related_query->have_posts()) : $related_query->the_post();
                    $rel_file_url = '';
                    $rel_source = get_post_meta(get_the_ID(), '_reglamento_source_type_key', true);
                    if ($rel_source === 'external') {
                      $rel_file_url = get_post_meta(get_the_ID(), '_reglamento_external_url_key', true);
                    } elseif ($rel_source === 'upload') {
                      $rel_file_id = get_post_meta(get_the_ID(), '_reglamento_file_id_key', true);
                      $rel_file_url = $rel_file_id ? wp_get_attachment_url($rel_file_id) : '';
                    }
                    $rel_is_pdf = (strtolower(pathinfo($rel_file_url, PATHINFO_EXTENSION)) === 'pdf');
                  ?>
                    <div class="dt-col-md-4 dt-col-12">
                      <a href="<?php the_permalink(); ?>" class="viceunf-related-card viceunf-related-doc-card">
                        <div class="viceunf-related-doc-card__icon">
                          <i class="fas fa-file-<?php echo $rel_is_pdf ? 'pdf' : 'alt'; ?>"></i>
                        </div>
                        <div class="viceunf-related-doc-card__info">
                          <span class="viceunf-related-doc-card__date"><?php echo get_the_date('M Y'); ?></span>
                          <h4 class="viceunf-related-doc-card__title"><?php echo wp_trim_words(get_the_title(), 8, '...'); ?></h4>
                        </div>
                      </a>
                    </div>
                  <?php endwhile;
                  wp_reset_postdata(); ?>
                </div>
              </div>
          <?php
            endif;
          }
          ?>
        </main>
      </div>
      <?php get_sidebar(); ?>
    </div>
  </div>
</section>

<?php
get_footer();
