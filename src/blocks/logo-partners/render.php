<?php

/**
 * Render del bloque viceunf/logo-partners.
 * Lee los logos desde el CPT 'socio' (plugin vpinunf-core).
 *
 * Dependencia: Post Type 'socio' registrado en el plugin vpinunf-core.
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

if (!class_exists('\VpinUnf\Core\Service\SocioService')) {
    return;
}

$socios = (new \VpinUnf\Core\Service\SocioService())->get_all_socios();

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
                <?php echo wp_kses_post($section_title); ?>
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