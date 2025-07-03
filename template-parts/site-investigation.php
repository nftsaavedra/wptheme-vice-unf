<?php

/**
 * Template part para mostrar la sección de Investigación en la página de inicio.
 * Esta sección se controla desde el panel "Opciones ViceUnf" en el administrador de WordPress.
 */

// 1. Obtenemos el array completo de nuestras opciones del tema.
//    El segundo parámetro '[]' asegura que si no hay nada guardado, obtengamos un array vacío y no un error.
$options = get_option('viceunf_theme_options', []);

// 2. Verificamos si la sección está habilitada. Por defecto, estará habilitada.
$section_enabled = isset($options['investigacion_section_enabled']) ? $options['investigacion_section_enabled'] : true;

// Si la sección no está habilitada en las opciones, no mostramos nada y detenemos la ejecución.
if (! $section_enabled) {
    return;
}

// 3. Preparamos un array para almacenar los ítems válidos que vamos a mostrar.
$items_to_render = [];

// 4. Recorremos las 4 posibles configuraciones de ítems para procesar los datos.
for ($i = 1; $i <= 4; $i++) {
    $page_id = ! empty($options["item_{$i}_page_id"]) ? $options["item_{$i}_page_id"] : 0;

    // Si no se seleccionó ninguna página para este ítem, lo saltamos y continuamos con el siguiente.
    if (empty($page_id)) {
        continue;
    }

    // Obtenemos el objeto de la página para acceder a sus datos.
    $page_post = get_post($page_id);

    // Si por alguna razón la página ya no existe, la saltamos.
    if (! $page_post) {
        continue;
    }

    // Lógica para determinar el título: usa el personalizado si existe, si no, el de la página.
    $custom_title = ! empty($options["item_{$i}_custom_title"]) ? $options["item_{$i}_custom_title"] : '';
    $title = ! empty($custom_title) ? $custom_title : get_the_title($page_post);

    // Lógica para determinar la descripción: usa la personalizada si existe, si no, crea un extracto.
    $custom_desc = ! empty($options["item_{$i}_custom_desc"]) ? $options["item_{$i}_custom_desc"] : '';
    if (! empty($custom_desc)) {
        $description = $custom_desc;
    } else {
        // Generamos un extracto limpio de 10 palabras del contenido de la página.
        $description = wp_trim_words(strip_tags($page_post->post_content), 10, '...');
    }

    // Guardamos los datos procesados y listos para usar en nuestro array.
    $items_to_render[] = [
        'title'       => $title,
        'description' => $description,
        'link'        => get_permalink($page_post),
        'icon'        => ! empty($options["item_{$i}_icon"]) ? $options["item_{$i}_icon"] : 'fas fa-info-circle', // Icono por defecto.
    ];
}
?>

<section id="dt_service_one" class="dt_service dt_service--eight dt-py-default front-info">
    <div class="dt-container">
        <div class="dt-row dt-g-4 info-wrp">

            <?php if (! empty($items_to_render)) : ?>
                <?php
                $delay = 0; // Para la animación escalonada.
                // 5. Ahora recorremos el array con los datos listos y renderizamos el HTML.
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
                    $delay += 100; // Incrementamos el delay para el siguiente ítem.
                endforeach;
                ?>
            <?php else : ?>
                <?php // 6. Mensaje para el administrador si la sección está activa pero vacía. 
                ?>
                <div class="dt-col-12">
                    <div class="notice-empty-section" style="text-align: center; padding: 40px; background-color: #f1f1f1; border: 1px dashed #ccc; border-radius: 4px;">
                        <p><?php _e('La sección de Investigación está activa pero no se ha configurado ningún ítem.', 'viceunf'); ?></p>
                        <p><?php _e('Por favor, ve a <strong>Apariencia > Opciones ViceUnf</strong> para añadir contenido.', 'viceunf'); ?></p>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>