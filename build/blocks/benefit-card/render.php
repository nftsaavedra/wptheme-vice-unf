<?php

/**
 * Render del bloque viceunf/benefit-card.
 *
 * @param array    $attributes Atributos del bloque.
 * @param string   $content    N/A.
 * @param WP_Block $block      Instancia del bloque.
 */

if (! defined('ABSPATH')) {
    exit;
}

$icon            = $attributes['icon'] ?? 'fa-solid fa-star';
$title           = $attributes['title'] ?? '';
$description     = $attributes['description'] ?? '';
$gradient_start  = $attributes['gradientStart'] ?? '#1e2a4a';
$gradient_end    = $attributes['gradientEnd'] ?? '#0e1422';

// Sanitizar las clases del ícono (solo letras, números, espacios y guiones).
$icon_class = preg_replace('/[^a-zA-Z0-9\s\-]/', '', $icon);

$card_style = sprintf(
    'background: linear-gradient(135deg, %s 0%%, %s 100%%);',
    esc_attr($gradient_start),
    esc_attr($gradient_end)
);

$wrapper_attributes = get_block_wrapper_attributes(
    array('class' => 'viceunf-benefit-card')
);
?>
<div <?php echo $wrapper_attributes; ?> style="<?php echo esc_attr($card_style); ?>">
    <div class="viceunf-benefit-card__icon-wrap" aria-hidden="true">
        <i class="<?php echo esc_attr($icon_class); ?>"></i>
    </div>

    <?php if ($title) : ?>
        <h3 class="viceunf-benefit-card__title">
            <?php echo esc_html($title); ?>
        </h3>
    <?php endif; ?>

    <?php if ($description) : ?>
        <p class="viceunf-benefit-card__desc">
            <?php echo esc_html($description); ?>
        </p>
    <?php endif; ?>
</div>