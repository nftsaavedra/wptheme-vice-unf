<?php
// Primero, comprobamos si la sección está habilitada en el Personalizador.
$section_enabled = get_theme_mod('investigacion_section_enabled', true);

if (!$section_enabled) {
    return; // Si está deshabilitada, no mostramos nada.
}

// Array para almacenar los datos de los ítems que vamos a mostrar.
$items_to_render = [];
$used_page_ids = []; // Array para asegurar que las páginas sean únicas.

// Recorremos las 4 posibles configuraciones de ítems.
for ($i = 1; $i <= 4; $i++) {
    $page_id = get_theme_mod("investigacion_item_{$i}_page_id", 0);

    // Si no hay página seleccionada o si ya fue usada, saltamos este ítem.
    if (empty($page_id) || in_array($page_id, $used_page_ids)) {
        continue;
    }

    $page_post = get_post($page_id);
    if (!$page_post) {
        continue; // Saltamos si la página no existe (p.ej. fue borrada).
    }

    // Añadimos el ID a la lista de usados.
    $used_page_ids[] = $page_id;

    // Obtenemos los datos personalizados.
    $custom_title = get_theme_mod("investigacion_item_{$i}_custom_title", '');
    $custom_desc  = get_theme_mod("investigacion_item_{$i}_custom_desc", '');
    $icon_class   = get_theme_mod("investigacion_item_{$i}_icon", 'fas fa-info-circle');

    // Lógica condicional para el título y la descripción.
    $title = !empty($custom_title) ? $custom_title : get_the_title($page_post);
    $link  = get_permalink($page_post);
    
    if (!empty($custom_desc)) {
        $description = $custom_desc;
    } else {
        // Generamos un extracto de 10 palabras del contenido de la página.
        $description = wp_trim_words(strip_tags($page_post->post_content), 10, '...');
    }

    // Guardamos los datos procesados en nuestro array.
    $items_to_render[] = [
        'title'       => $title,
        'description' => $description,
        'link'        => $link,
        'icon'        => $icon_class,
    ];
}
?>

<section id="dt_service_one" class="dt_service dt_service--eight dt-py-default front-info">
    <div class="dt-container">
        <div class="dt-row dt-g-4 info-wrp">
            <?php if (!empty($items_to_render)) : ?>
                <?php
                $delay = 0;
                foreach ($items_to_render as $item) :
                ?>
                    <div class="dt-col-lg-3 dt-col-md-6 dt-col-12">
                        <div class="dt_item_inner wow slideInUp animated" data-wow-delay="<?php echo esc_attr($delay); ?>ms" data-wow-duration="1500ms">
                            <div class="dt_item_holder">
                                <div class="num-icon-wrap">
                                    <div class="dt_item_icon">
                                        <i class="<?php echo esc_attr($item['icon']); ?>" aria-hidden="true"></i>
                                    </div>
                                    <div class="dt_item_icon_rotate"></div>
                                </div>

                                <h5 class="dt_item_title"><a href="<?php echo esc_url($item['link']); ?>"><?php echo esc_html($item['title']); ?></a></h5>

                                <div class="dt_item_content"><?php echo esc_html($item['description']); ?></div>

                                <div class="dt_item_readmore"><a class="dt-btn-arrow" href="<?php echo esc_url($item['link']); ?>">Ver más</a></div>
                            </div>
                        </div>
                    </div>
                <?php
                    $delay += 100;
                endforeach;
                ?>
            <?php else : ?>
                <div class="dt-col-12">
                    <div class="notice-empty-section" style="text-align: center; padding: 40px; background-color: #f1f1f1; border: 1px dashed #ccc;">
                        <p>Esta sección está activa pero no se han configurado ítems.</p>
                        <p>Por favor, ve a <strong>Apariencia > Personalizar > Página de Inicio > Sección: Investigación</strong> para añadir contenido.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>