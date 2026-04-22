<?php

/**
 * Render del bloque vpinunf/benefits-grid.
 *
 * @param array    $attributes Atributos del bloque.
 * @param string   $content    Contenido renderizado de los InnerBlocks.
 * @param WP_Block $block      Instancia del bloque.
 */

if (! defined('ABSPATH')) {
    exit;
}

$columns         = isset($attributes['columns']) ? (int) $attributes['columns'] : 3;
$section_title   = $attributes['sectionTitle'] ?? '';
$section_subtitle = $attributes['sectionSubtitle'] ?? '';

// Limitar columnas al rango válido.
$columns = max(2, min(4, $columns));

$wrapper_attributes = get_block_wrapper_attributes(
    array(
        'class'                   => 'vpinunf-benefits-grid',
        'style'                   => '--vpinunf-grid-cols: ' . $columns . ';',
    )
);
$text_align = $attributes['textAlign'] ?? 'center';
?>
<section <?php echo $wrapper_attributes; ?>>
    <div class="dt-container">

        <?php if ($section_title || $section_subtitle) : ?>
            <div class="vpinunf-benefits-grid__header" style="text-align: <?php echo esc_attr($text_align); ?>; margin-bottom: 3.2rem;">
                <?php if ($section_title) : ?>
                    <h2 class="vpinunf-benefits-grid__title" style="margin-bottom: 1rem;">
                        <?php echo wp_kses_post($section_title); ?>
                    </h2>
                <?php endif; ?>
                <?php if ($section_subtitle) : ?>
                    <p class="vpinunf-benefits-grid__subtitle">
                        <?php echo wp_kses_post($section_subtitle); ?>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="vpinunf-benefits-grid__grid">
            <?php echo $content; ?>
        </div>

    </div>
</section>