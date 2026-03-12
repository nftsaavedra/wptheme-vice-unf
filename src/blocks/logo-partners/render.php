<?php

/**
 * Render del bloque viceunf/logo-partners.
 * Lee los logos desde el CPT 'socio' (plugin viceunf-core).
 *
 * Dependencia: Post Type 'socio' registrado en el plugin viceunf-core.
 * Si el plugin no está activo, el bloque muestra un mensaje de fallback.
 */
if (! defined('ABSPATH')) {
    exit;
}

$section_title    = $attributes['sectionTitle'] ?? '';
$grayscale        = $attributes['grayscaleDefault'] ?? true;

// Verificar que el CPT 'socio' está registrado antes de consultar.
if (! post_type_exists('socio')) {
    return;
}

if (class_exists('\ViceUnf\Core\Service\SocioService')) {
    $socios = (new \ViceUnf\Core\Service\SocioService())->get_all_socios();
} else {
    $socios = new WP_Query(array(
        'post_type'              => 'socio',
        'post_status'            => 'publish',
        'posts_per_page'         => -1,
        'orderby'                => 'menu_order',
        'order'                  => 'ASC',
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ));
}

if (! $socios->have_posts()) {
    wp_reset_postdata();
    return;
}

$grayscale_class = $grayscale ? 'viceunf-logo-partners--grayscale' : '';

$wrapper_attributes = get_block_wrapper_attributes(
    array('class' => 'viceunf-logo-partners ' . $grayscale_class)
);
?>
<section <?php echo $wrapper_attributes; ?>>
    <div class="dt-container">

        <?php if ($section_title) : ?>
            <h2 class="viceunf-logo-partners__title">
                <?php echo esc_html($section_title); ?>
            </h2>
        <?php endif; ?>

        <div class="viceunf-logo-partners__track">
            <?php while ($socios->have_posts()) : $socios->the_post();
                $post_id   = get_the_ID();
                $logo_id   = get_post_thumbnail_id($post_id);
                $link      = get_post_meta($post_id, '_socio_url', true);
                $name      = get_the_title();

                if (! $logo_id) {
                    continue;
                }

                $logo_img = wp_get_attachment_image(
                    $logo_id,
                    'medium',
                    false,
                    array(
                        'class' => 'viceunf-logo-partners__img',
                        'alt'   => esc_attr($name),
                        'loading' => 'lazy',
                    )
                );

                $tag      = $link ? 'a' : 'div';
                $href_attr = $link ? ' href="' . esc_url($link) . '" target="_blank" rel="noopener noreferrer"' : '';
            ?>
                <<?php echo $tag; ?> class="viceunf-logo-partners__item" <?php echo $href_attr; ?>>
                    <?php echo $logo_img; ?>
                </<?php echo $tag; ?>>
            <?php endwhile;
            wp_reset_postdata(); ?>
        </div>

    </div>
</section>