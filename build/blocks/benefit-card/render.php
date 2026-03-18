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

$icon            = isset( $attributes['icon'] ) ? $attributes['icon'] : 'fa-solid fa-star';
$icon_color      = isset( $attributes['iconColor'] ) ? $attributes['iconColor'] : '#ea5a0b';
$title           = isset( $attributes['title'] ) ? $attributes['title'] : '';
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
    <?php if ( $icon ) : ?>
        <div class="viceunf-benefit-card__icon-wrap">
            <i class="<?php echo esc_attr( $icon_class ); ?>" style="color: <?php echo esc_attr( $icon_color ); ?>;" aria-hidden="true"></i>
        </div>
    <?php endif; ?>

    <?php if ($title) : ?>
        <h3 class="viceunf-benefit-card__title">
            <?php echo wp_kses_post($title); ?>
        </h3>
    <?php endif; ?>

    <?php if ($description) : ?>
        <p class="viceunf-benefit-card__desc">
            <?php echo wp_kses_post($description); ?>
        </p>
    <?php endif; ?>
</div>