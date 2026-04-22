<?php

/**
 * Render del bloque vpinunf/footer-cta.
 *
 * @param array    $attributes Atributos del bloque.
 * @param string   $content    N/A — Sin InnerBlocks.
 * @param WP_Block $block      Instancia del bloque.
 */

if (! defined('ABSPATH')) {
    exit;
}

$title        = $attributes['title'] ?? '';
$subtitle     = $attributes['subtitle'] ?? '';
$button_text  = $attributes['buttonText'] ?? '';
$button_url   = $attributes['buttonUrl'] ?? '';
$button_color = $attributes['buttonColor'] ?? '';
$bg_color     = $attributes['bgColor'] ?? '#0e1422';
$show_logos   = $attributes['showLogos'] ?? false;

$section_style = 'background-color: ' . esc_attr($bg_color) . ';';

$btn_style = '';
if ($button_color) {
    $btn_style = sprintf(
        'background-color: %s; border-color: %s;',
        esc_attr($button_color),
        esc_attr($button_color)
    );
}

$site_logo_html = '';
if ($show_logos) {
    // Usar get_custom_logo() — función oficial FSE-compatible, no depende del Customizer
    $site_logo_html = get_custom_logo();
}

$wrapper_attributes = get_block_wrapper_attributes(
    array('class' => 'vpinunf-footer-cta')
);
?>
<section <?php echo $wrapper_attributes; ?> style="<?php echo esc_attr($section_style); ?>">
    <div class="dt-container">
        <div class="vpinunf-footer-cta__inner">

            <?php if ($title) : ?>
                <h2 class="vpinunf-footer-cta__title">
                    <?php echo wp_kses_post($title); ?>
                </h2>
            <?php endif; ?>

            <?php if ($subtitle) : ?>
                <p class="vpinunf-footer-cta__subtitle">
                    <?php echo wp_kses_post($subtitle); ?>
                </p>
            <?php endif; ?>

            <?php if ($button_text && $button_url) : ?>
                <div class="vpinunf-footer-cta__btn-wrap">
                    <a
                        href="<?php echo esc_url($button_url); ?>"
                        class="dt-btn dt-btn-primary vpinunf-footer-cta__btn btn--effect-one"
                        style="<?php echo esc_attr($btn_style); ?>">
                        <span class="dt-btn-text"><?php echo wp_kses_post($button_text); ?></span>
                    </a>
                </div>
            <?php endif; ?>

            <?php if ($show_logos && $site_logo_html) : ?>
                <div class="vpinunf-footer-cta__logos" aria-label="<?php esc_attr_e('Logos institucionales', 'vpinunf'); ?>">
                    <?php echo $site_logo_html; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>