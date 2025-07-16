<?php

/**
 * =================================================================
 * Plantilla para la sección "Sobre Nosotros" de la Página de Inicio
 * =================================================================
 * Este archivo ahora es completamente dinámico y se alimenta de los
 * valores guardados en "Opciones del Tema".
 */

// 1. Obtenemos todas las opciones del tema de una sola vez.
$options = get_option('viceunf_theme_options', []);

// 2. Verificamos si la sección está habilitada. Si no, no se muestra nada.
$is_enabled = isset($options['about_section_enabled']) ? $options['about_section_enabled'] : false;
if (!$is_enabled) {
  return; // Salimos del archivo si la sección está deshabilitada.
}

// 3. Extraemos todos los valores en variables para que el código sea más limpio.
$main_image_id = isset($options['about_main_image']) ? $options['about_main_image'] : 0;
$video_url     = isset($options['about_video_url']) ? $options['about_video_url'] : '';
$subtitle      = isset($options['about_subtitle']) ? $options['about_subtitle'] : 'Sobre Nosotros';
$title         = isset($options['about_title']) ? $options['about_title'] : 'Título no definido';
$person_name   = isset($options['about_person_name']) ? $options['about_person_name'] : '';
$description   = isset($options['about_description']) ? $options['about_description'] : 'Descripción no definida.';
$about_items   = isset($options['about_items']) && is_array($options['about_items']) ? $options['about_items'] : [];

// Pequeña función para generar el subtítulo animado.
$create_animated_subtitle = function ($text) {
  $output = '';
  $chars = mb_str_split($text);
  foreach ($chars as $char) {
    $output .= '<i class="in">' . esc_html($char) . '</i>';
  }
  return $output;
};

?>
<section id="dt_protect" class="dt_protect dt_protect--one dt-py-default front-protect">
  <div class="dt-container">
    <div class="dt-row dt-g-5">
      <div class="dt-col-lg-6 dt-col-md-12 dt-col-sm-12">
        <div class="dt_image_block">
          <div class="circle_shapes">
            <div class="circle"></div>
          </div>
          <?php if ($main_image_id) : ?>
            <div class="dt_image_box image-1">
              <figure class="image">
                <?php
                // --- MEJORA: SEO Y RENDIMIENTO ---
                // 1. Obtenemos el texto 'alt' de la imagen desde la Biblioteca de Medios.
                $image_alt = get_post_meta($main_image_id, '_wp_attachment_image_alt', true);
                // 2. Si no tiene 'alt' text, usamos el título de la sección como fallback.
                $alt_text = !empty($image_alt) ? $image_alt : $title;
                // 3. Usamos wp_get_attachment_image() para generar la etiqueta <img> con 'srcset',
                //    lo que permite al navegador cargar la imagen más optimizada (responsive).
                echo wp_get_attachment_image($main_image_id, 'large', false, ['alt' => esc_attr($alt_text)]);
                ?>
              </figure>
            </div>
          <?php endif; ?>

          <?php if (!empty($video_url)) : ?>
            <div class="dt_image_box image-2">
              <figure class="image">
                <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/investigacion_play.png'); ?>" alt="Ícono de Play para Investigación">
              </figure>
              <div class="dt_image_video">
                <a href="<?php echo esc_url($video_url); ?>" class="dt_lightbox_img dt-btn-play dt-btn-primary" data-caption="">
                  <i class="fa fa-play" aria-hidden="true"></i>
                </a>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
      <div class="dt-col-lg-6 dt-col-md-12 dt-col-sm-12">
        <div class="dt_content_block">
          <div class="dt_content_box">
            <div class="dt_siteheading">
              <span class="subtitle">
                <span class="dt_heading dt_heading_8">
                  <span class="dt_heading_inner">
                    <b class="is_on" style="opacity: 1;">
                      <?php echo $create_animated_subtitle($subtitle); ?>
                    </b>
                  </span>
                </span>
              </span>
              <h2 class="title">
                <?php echo wp_kses_post($title); ?>
                <?php if (!empty($person_name)) : ?>
                  <br><span><?php echo esc_html($person_name); ?></span>
                <?php endif; ?>
              </h2>
              <div class="text dt-mt-3 wow fadeInUp" data-wow-duration="1500ms">
                <p><?php echo esc_html($description); ?></p>
              </div>
            </div>

            <?php if (!empty($about_items)) : ?>
              <div class="dt-row dt-g-4 dt-mt-2 protect-wrp">
                <?php foreach ($about_items as $index => $item) :
                  if (empty($item['page_id']) || empty($item['icon'])) {
                    continue;
                  }
                  $page_title = get_the_title($item['page_id']);
                  $page_url = get_permalink($item['page_id']);
                ?>
                  <div class="dt-col-lg-6 dt-col-sm-6 dt-col-12">
                    <div class="dt_item_inner wow slideInUp animated" data-wow-delay="<?php echo esc_attr($index * 100); ?>ms" data-wow-duration="1500ms">
                      <div class="dt_item_icon"><i class="<?php echo esc_attr($item['icon']); ?>" aria-hidden="true"></i></div>
                      <div class="dt_item_holder">
                        <h5 class="dt_item_title"><a href="<?php echo esc_url($page_url); ?>"><?php echo esc_html($page_title); ?></a></h5>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>