<?php

/**
 * =================================================================
 * Plantilla para la sección "Producción Científica" de la Página de Inicio
 * =================================================================
 * Este archivo ahora es completamente dinámico y se alimenta de los
 * valores guardados en "Opciones del Tema".
 */

$options = get_option('viceunf_theme_options', []);

$is_enabled = isset($options['production_section_enabled']) ? $options['production_section_enabled'] : false;
if (!$is_enabled) {
  return;
}

$subtitle    = !empty($options['production_subtitle']) ? $options['production_subtitle'] : 'Publicaciones Académicas';
$title       = !empty($options['production_title']) ? $options['production_title'] : 'Conocimiento generado para el <br><span>desarrollo científico y social</span>';
$description = !empty($options['production_description']) ? $options['production_description'] : 'Desde la Vicepresidencia de Investigación de la Universidad Nacional de Frontera promovemos la producción y difusión del conocimiento mediante revistas, libros, boletines e informes técnicos. Aquí se reúnen los principales aportes que fortalecen la investigación y el desarrollo regional y nacional.';

$items = !empty($options['production_items']) && is_array($options['production_items']) ? $options['production_items'] : [
  [
    'title' => 'Revistas Científicas',
    'description' => 'Revista UNF de Ciencia y Tecnología – Publicación semestral sobre avances científicos y tecnológicos.',
    'icon' => 'fas fa-book-open',
    'image_url' => 'http://vice.unf.edu.pe/wp-content/uploads/2025/07/REVISTAS_UNF-scaled.jpg',
    'url' => '#'
  ],
  [
    'title' => 'Libros Académicos',
    'description' => 'Consulta libros publicados por docentes e investigadores UNF.',
    'icon' => 'fas fa-book',
    'image_url' => 'http://vice.unf.edu.pe/wp-content/uploads/2025/07/LIBROS_UNF-scaled.jpg',
    'url' => '#'
  ],
  [
    'title' => 'Boletines Institucionales',
    'description' => 'Publicaciones periódicas y reportes técnicos por unidad o tema.',
    'icon' => 'fas fa-newspaper',
    'image_url' => 'http://vice.unf.edu.pe/wp-content/uploads/2025/06/lnj2xvlnj2xvlnj2.png',
    'url' => '#'
  ],
  [
    'title' => 'Repositorio Institucional',
    'description' => 'Accede a todos los documentos y publicaciones en nuestro Repositorio UNF.',
    'icon' => 'fas fa-database',
    'image_url' => 'http://vice.unf.edu.pe/wp-content/uploads/2025/06/1jxcmv1jxcmv1jxc.png',
    'url' => '#'
  ]
];

$create_animated_subtitle = function ($text) {
  $output = '';
  $chars = mb_str_split($text);
  foreach ($chars as $char) {
    if ($char === ' ') {
      $output .= '<i class="in"> </i>';
    } else {
      $output .= '<i class="in">' . esc_html($char) . '</i>';
    }
  }
  return $output;
};
?>
<section id="section-produccion-cientifica" class="dt_service dt_service--five front-service dt-py-default">
  <div class="shape-slide">
    <div class="sliders scroll">
      <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/service/services_1.png" alt="Fondo Producción Científica">
    </div>
    <div class="sliders scroll">
      <img src="<?php echo esc_url(get_stylesheet_directory_uri()); ?>/assets/images/service/services_1.png" alt="Fondo Producción Científica">
    </div>
  </div>
  <div class="dt-container">
    <div class="dt-row">
      <div class="dt-col-xl-7 dt-col-lg-8 dt-col-md-9 dt-col-12 dt-mx-auto dt-mb-6">
        <div class="dt_siteheading dt-text-center">
          <?php if (!empty($subtitle)) : ?>
            <span class="subtitle">
              <span class="dt_heading dt_heading_8">
                <span class="dt_heading_inner" style="width: 202.078px;">
                  <b class="is_on" style="opacity: 1;">
                    <?php echo $create_animated_subtitle($subtitle); ?>
                  </b>
                </span>
              </span>
            </span>
          <?php endif; ?>
          <h2 class="title"><?php echo wp_kses_post($title); ?></h2>
          <?php if (!empty($description)) : ?>
            <div class="text dt-mt-3" data-animation="fadeInUp">
              <p><?php echo wp_kses_post($description); ?></p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <?php if (!empty($items)) : ?>
      <div class="dt-row dt-g-4">
        <?php foreach ($items as $index => $item) :
          $delay = $index * 100;
          $item_title = isset($item['title']) ? $item['title'] : '';
          $item_desc  = isset($item['description']) ? $item['description'] : '';
          $item_icon  = !empty($item['icon']) ? $item['icon'] : 'fas fa-book';
          $item_url   = !empty($item['url']) ? $item['url'] : '#';
          $image_id   = isset($item['image_id']) ? absint($item['image_id']) : 0;
          $image_url  = isset($item['image_url']) ? esc_url($item['image_url']) : '';
        ?>
          <div class="dt-col-lg-3 dt-col-sm-6 dt-col-12">
            <div class="dt_item_inner" data-animation="slideInUp" data-animation-delay="<?php echo esc_attr($delay); ?>ms">
              <?php if ($image_id || $image_url) : ?>
                <div class="dt_item_image">
                  <a href="<?php echo esc_url($item_url); ?>" aria-hidden="true" tabindex="-1">
                    <?php if ($image_id) : ?>
                      <?php echo wp_get_attachment_image($image_id, 'large', false, ['alt' => esc_attr($item_title)]); ?>
                    <?php else : ?>
                      <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($item_title); ?>" title="">
                    <?php endif; ?>
                  </a>
                </div>
              <?php endif; ?>
              <div class="dt_item_holder">
                <div class="dt_item_icon"><i class="<?php echo esc_attr($item_icon); ?>"></i></div>
                <h5 class="dt_item_title">
                  <a href="<?php echo esc_url($item_url); ?>"><?php echo esc_html($item_title); ?></a>
                </h5>
                <?php if ($item_desc) : ?>
                  <p class="dt_item_text text"><?php echo esc_html($item_desc); ?></p>
                <?php endif; ?>
                <a href="<?php echo esc_url($item_url); ?>" class="readmore" aria-label="Ver más sobre <?php echo esc_attr(strip_tags($item_title)); ?>">
                  Ver más<i class="fas fa-long-arrow-right"></i>
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>
</section>