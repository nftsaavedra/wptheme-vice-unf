<?php

/**
 * Plantilla para mostrar un único post del tipo "Reglamento".
 * VERSIÓN HÍBRIDA: Muestra la vista previa del PDF (si existe) encima de la tarjeta de descarga.
 *
 * @package ViceUnf
 */

get_header();
?>

<section id="dt_single_post" class="dt_single_post dt-py-default">
  <div class="dt-container">
    <div class="dt-row dt-g-5">

      <?php
      // Lógica para determinar el ancho de las columnas.
      $is_sidebar_active = is_active_sidebar('softme-sidebar-primary');
      $main_column_class = !$is_sidebar_active ? 'dt-col-lg-12' : 'dt-col-lg-8';
      ?>

      <div id="dt-main-content" class="<?php echo esc_attr($main_column_class); ?> dt-col-md-12 dt-col-12 wow fadeInUp">
        <main id="main" class="site-main">

          <?php
          while (have_posts()) :
            the_post();

            // --- INICIO: LÓGICA DE ARCHIVO (se obtiene una sola vez) ---
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

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
              <header class="entry-header">
                <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
              </header>
              <div class="entry-content">
                <?php
                // --- PASO 1: MOSTRAR VISTA PREVIA SI ES PDF ---
                if (!empty($file_url)) {
                  $extension = strtolower(pathinfo($file_url, PATHINFO_EXTENSION));
                  if ($extension === 'pdf') {
                    echo '<div class="pdf-preview-wrapper">';
                    echo '<iframe class="pdf-preview-iframe" src="' . esc_url($file_url) . '" title="Vista previa de ' . esc_attr(get_the_title()) . '"></iframe>';
                    echo '</div>';
                  }
                }

                // --- PASO 2: MOSTRAR CONTENIDO DEL EDITOR DE WORDPRESS ---
                the_content();

                // --- PASO 3: MOSTRAR SIEMPRE LA TARJETA DE DESCARGA ---
                if (!empty($file_url)) {
                  echo '<div class="download-box-wrapper" style="margin-top: 2em;">';
                  echo '<div class="download-icon"><i class="fas fa-file-alt"></i></div>';
                  echo '<div class="download-info">';
                  echo '<h4>' . esc_html(get_the_title()) . '</h4>';
                  echo '<p>Haga clic en el botón para abrir o descargar el documento.</p>';
                  echo '</div>';
                  echo '<a href="' . esc_url($file_url) . '" target="_blank" class="button-download">Ver Documento</a>';
                  echo '</div>';
                } else {
                  echo '<p style="margin-top: 2em;">No hay ningún archivo asociado a este reglamento.</p>';
                }
                ?>
              </div>
            </article><?php
                    endwhile; // Fin del Loop.
                      ?>

        </main>
      </div><?php get_sidebar(); ?>

    </div>
  </div>
</section>

<?php
get_footer();
