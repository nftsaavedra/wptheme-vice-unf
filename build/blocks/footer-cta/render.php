<?php

/**
 * Render del bloque viceunf/footer-cta.
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

$custom_logo_id = get_theme_mod('custom_logo');
$site_logo_html = '';
if ($show_logos && $custom_logo_id) {
    $logo_image    = wp_get_attachment_image($custom_logo_id, 'medium', false, array('class' => 'viceunf-footer-cta__logo-img'));
    $site_logo_html = $logo_image;
}

$wrapper_attributes = get_block_wrapper_attributes(
    array('class' => 'viceunf-footer-cta')
);
?>
<section <?php echo $wrapper_attributes; ?> style="<?php echo esc_attr($section_style); ?>">
    <div class="dt-container">
        <div class="viceunf-footer-cta__inner">

            <?php if ($title) : ?>
                <h2 class="viceunf-footer-cta__title">
                    <?php echo wp_kses_post($title); ?>
                </h2>
            <?php endif; ?>

            <?php if ($subtitle) : ?>
                <p class="viceunf-footer-cta__subtitle">
                    <?php echo esc_html($subtitle); ?>
                </p>
            <?php endif; ?>

            <?php if ($button_text && $button_url) : ?>
                <div class="viceunf-footer-cta__btn-wrap">
                    <a
                        href="<?php echo esc_url($button_url); ?>"
                        class="dt-btn dt-btn-primary viceunf-footer-cta__btn btn--effect-one"
                        style="<?php echo esc_attr($btn_style); ?>">
                        <span class="dt-btn-text"><?php echo esc_html($button_text); ?></span>
                    </a>
                </div>
            <?php endif; ?>

            <?php if ($show_logos && $site_logo_html) : ?>
                <div class="viceunf-footer-cta__logos" aria-label="<?php esc_attr_e('Logos institucionales', 'viceunf'); ?>">
                    <?php echo $site_logo_html; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>