<?php

/**
 * Render del bloque viceunf/benefits-grid.
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
        'class'                   => 'viceunf-benefits-grid',
        'style'                   => '--viceunf-grid-cols: ' . $columns . ';',
    )
);
?>
<section <?php echo $wrapper_attributes; ?>>
    <div class="dt-container">

        <?php if ($section_title || $section_subtitle) : ?>
            <div class="viceunf-benefits-grid__header">
                <?php if ($section_title) : ?>
                    <h2 class="viceunf-benefits-grid__title">
                        <?php echo esc_html($section_title); ?>
                    </h2>
                <?php endif; ?>
                <?php if ($section_subtitle) : ?>
                    <p class="viceunf-benefits-grid__subtitle">
                        <?php echo esc_html($section_subtitle); ?>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="viceunf-benefits-grid__grid">
            <?php echo $content; ?>
        </div>

    </div>
</section>