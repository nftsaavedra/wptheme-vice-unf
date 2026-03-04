<?php

/**
 * Plantilla principal para el detalle de un Evento (Single Evento)
 * Heredando estrictamente la grilla y clases de diseño del tema principal (single.php)
 */

if (! defined('ABSPATH')) {
    exit;
}

get_header();

// Extraemos los metadatos personalizados del evento
$lugar = get_post_meta(get_the_ID(), '_evento_lugar', true) ?: get_post_meta(get_the_ID(), '_evento_address_key', true);
$mapa_url = get_post_meta(get_the_ID(), '_evento_mapa_url', true);

$horarios = get_post_meta(get_the_ID(), '_evento_horarios', true);
if (empty($horarios) || !is_array($horarios)) {
    $horarios = [];
    // Leer llaves probables antiguas genradas por /theme-functions/meta-boxes.php
    $fecha_antigua = get_post_meta(get_the_ID(), '_evento_date_key', true);
    if (!empty($fecha_antigua)) {
        $horarios[] = [
            'etiqueta' => '',
            'fecha'    => $fecha_antigua,
            'inicio'   => get_post_meta(get_the_ID(), '_evento_start_time_key', true) ?: '08:00',
            'fin'      => get_post_meta(get_the_ID(), '_evento_end_time_key', true) ?: '12:00'
        ];
    }
}
?>

<div id="content" class="site-content viceunf-bg-canvas">
    <section id="dt_posts" class="dt_posts dt-py-default">
        <div class="dt-container">
            <div class="dt-row dt-g-4">

                <!-- Columna Principal (Event Content) -->
                <div id="dt-main" class="dt-col-lg-8 dt-col-md-12 dt-col-12">
                    <?php while (have_posts()) : the_post(); ?>
                        <div class="dt-row dt-g-4">
                            <div class="dt-col-lg-12 dt-col-sm-12 dt-col-12">
                                <article id="post-<?php the_ID(); ?>" <?php post_class(array('dt_post_item', 'dt_posts--one', 'dt-mb-4', 'single-post')); ?>>

                                    <div class="viceunf-card-surface">

                                        <div class="inner">
                                            <!-- Meta del Evento (Fecha publicación, etc nativo) -->
                                            <div class="meta">
                                                <ul>
                                                    <li>
                                                        <div class="date">
                                                            <i class="far fa-calendar-alt dt-mr-2" aria-hidden="true"></i>
                                                            <?php echo get_the_date(); ?>
                                                        </div>
                                                    </li>
                                                    <?php if (!empty($lugar)) : ?>
                                                        <li>
                                                            <div class="author">
                                                                <i class="fas fa-map-marker-alt dt-mr-2" aria-hidden="true"></i>
                                                                <?php echo esc_html($lugar); ?>
                                                                <?php if (!empty($mapa_url)) : ?>
                                                                    <a href="<?php echo esc_url($mapa_url); ?>" target="_blank" rel="noopener noreferrer" style="color: var(--dt-color-primary); font-size: 0.9em; margin-left: 6px; text-decoration: underline;">
                                                                        (Ver en Google Maps)
                                                                    </a>
                                                                <?php endif; ?>
                                                            </div>
                                                        </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>

                                            <!-- Título migrado al card header -->

                                            <!-- Contenido general del evento renderizado por bloques -->
                                            <div class="content">
                                                <?php
                                                the_content();
                                                wp_link_pages(array(
                                                    'before' => '<div class="page-links">' . esc_html__('Páginas:', 'viceunf'),
                                                    'after'  => '</div>',
                                                ));
                                                ?>
                                            </div>

                                            <!-- Módulo: Cronograma del Evento (Inyectado estéticamente dentro del inner) -->
                                            <?php if (!empty($horarios)) : ?>
                                                <div class="evento-schedule-box dt-mt-4 p-4 border rounded" style="background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.08);">
                                                    <h5 class="dt-mb-3"><i class="far fa-clock dt-mr-2"></i> <?php _e('Cronograma del Evento', 'viceunf-core'); ?></h5>
                                                    <ul class="schedule-list" style="list-style:none; padding:0; margin:0;">
                                                        <?php foreach ($horarios as $index => $horario) :
                                                            $fecha_obj = DateTime::createFromFormat('Y-m-d', $horario['fecha']);
                                                            $fecha_str = $fecha_obj ? wp_date('l, j \d\e F', $fecha_obj->getTimestamp()) : $horario['fecha'];
                                                            $hora_inicio = date("g:i A", strtotime($horario['inicio']));
                                                            $hora_fin = date("g:i A", strtotime($horario['fin']));
                                                        ?>
                                                            <li class="dt-mb-3" style="border-bottom: 1px dashed rgba(0,0,0,0.1); padding-bottom: 10px;">
                                                                <?php if (!empty($horario['etiqueta'])) : ?>
                                                                    <strong style="color: var(--dt-color-primary); display:block; font-size: 0.9em; text-transform:uppercase; margin-bottom: 4px;">
                                                                        <?php echo esc_html($horario['etiqueta']); ?>
                                                                    </strong>
                                                                <?php endif; ?>
                                                                <div style="font-size: 1.1em; font-weight: 600;">
                                                                    <i class="far fa-calendar-check dt-mr-2" style="color: #888;"></i><?php echo ucfirst($fecha_str); ?>
                                                                </div>
                                                                <div style="color: #666; font-size: 0.95em; padding-left: 24px;">
                                                                    <?php echo $hora_inicio . ' - ' . $hora_fin; ?>
                                                                </div>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            <?php endif; ?>

                                            <!-- Tags u otras opciones como compartir pueden ir aquí según tu tema -->
                                            <div class="meta_bottom">
                                                <div class="tags">
                                                    <span class="badge" style="background: var(--dt-color-primary); color: #fff; padding: 4px 10px; border-radius: 4px; font-size: 0.8em;"><?php _e('Evento Universitario', 'viceunf-core'); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                </article>
                            </div>
                        </div>

                    <?php endwhile; ?>
                </div>

                <!-- Sidebar Nativo del Tema -->
                <?php get_sidebar(); ?>

            </div>
        </div>
    </section>
</div>

<?php
get_footer();
