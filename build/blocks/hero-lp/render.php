<?php

/**
 * Render del bloque vpinunf/hero-lp.
 *
 * @param array    $attributes Atributos del bloque, sanitizados.
 * @param string   $content    Contenido de InnerBlocks (no aplica).
 * @param WP_Block $block      Instancia del bloque.
 */

if (! defined('ABSPATH')) {
    exit;
}

$background_image  = $attributes['backgroundImage'] ?? [];
$background_video  = $attributes['backgroundVideo'] ?? '';
$overlay_opacity   = isset($attributes['overlayOpacity']) ? (float) $attributes['overlayOpacity'] : 0.55;
$program_logo      = $attributes['programLogo'] ?? [];
$title             = $attributes['title'] ?? '';
$subtitle          = $attributes['subtitle'] ?? '';
$cta_primary_text  = $attributes['ctaPrimaryText'] ?? '';
$cta_primary_url   = $attributes['ctaPrimaryUrl'] ?? '';
$cta_secondary_text = $attributes['ctaSecondaryText'] ?? '';
$cta_secondary_url  = $attributes['ctaSecondaryUrl'] ?? '';

// Nuevos colores
$subtitle_color             = $attributes['subtitleColor'] ?? '#ff4700';
$cta_primary_bg_color       = $attributes['ctaPrimaryBgColor'] ?? '#ff4700';
$cta_primary_text_color     = $attributes['ctaPrimaryTextColor'] ?? '#ffffff';
$cta_secondary_border_color = $attributes['ctaSecondaryBorderColor'] ?? '#ffffff';
$cta_secondary_text_color   = $attributes['ctaSecondaryTextColor'] ?? '#ffffff';

// Limitar la opacidad a un rango seguro.
$overlay_opacity = max(0, min(1, $overlay_opacity));

// Construir el style del fondo.
$bg_style = '';
if ($background_image && ! empty($background_image['url'])) {
    $bg_style = 'background-image: url(' . esc_url($background_image['url']) . '); background-size: cover; background-position: center;';
}

$wrapper_attributes = get_block_wrapper_attributes(
    array('class' => 'vpinunf-hero-lp')
);
?>
<section <?php echo $wrapper_attributes; ?> style="<?php echo esc_attr($bg_style); ?>">

    <?php if ($background_video) : ?>
        <video
            class="vpinunf-hero-lp__bg-video"
            autoplay
            muted
            loop
            playsinline
            aria-hidden="true">
            <source src="<?php echo esc_url($background_video); ?>" type="video/mp4">
        </video>
    <?php endif; ?>

    <div
        class="vpinunf-hero-lp__overlay"
        aria-hidden="true"
        style="background-color: rgba(14,20,34,<?php echo esc_attr($overlay_opacity); ?>);"></div>

    <div class="vpinunf-hero-lp__inner dt-container">

        <?php if (! empty($program_logo['url'])) : ?>
            <div class="vpinunf-hero-lp__logo">
                <img
                    src="<?php echo esc_url($program_logo['url']); ?>"
                    alt="<?php echo esc_attr($program_logo['alt'] ?? ''); ?>"
                    loading="eager">
            </div>
        <?php endif; ?>

        <?php if ($subtitle) : ?>
            <p class="vpinunf-hero-lp__subtitle" style="color: <?php echo esc_attr($subtitle_color); ?>;">
                <?php echo wp_kses_post($subtitle); ?>
            </p>
        <?php endif; ?>

        <?php if ($title) : ?>
            <h1 class="vpinunf-hero-lp__title">
                <?php echo wp_kses_post($title); ?>
            </h1>
        <?php endif; ?>

        <?php if ($cta_primary_text || $cta_secondary_text) : ?>
            <div class="vpinunf-hero-lp__actions">
                <?php if ($cta_primary_text && $cta_primary_url) : ?>
                    <a
                        href="<?php echo esc_url($cta_primary_url); ?>"
                        class="dt-btn dt-btn-primary btn--effect-one"
                        style="background-color: <?php echo esc_attr($cta_primary_bg_color); ?>; color: <?php echo esc_attr($cta_primary_text_color); ?>; border-color: <?php echo esc_attr($cta_primary_bg_color); ?>;">
                        <span class="dt-btn-text" style="color: inherit;"><?php echo wp_kses_post($cta_primary_text); ?></span>
                    </a>
                <?php endif; ?>

                <?php if ($cta_secondary_text && $cta_secondary_url) : ?>
                    <a
                        href="<?php echo esc_url($cta_secondary_url); ?>"
                        class="dt-btn dt-btn-white dt-btn-border"
                        style="border: 2px solid <?php echo esc_attr($cta_secondary_border_color); ?>; color: <?php echo esc_attr($cta_secondary_text_color); ?>; background: transparent;">
                        <span class="dt-btn-text" style="color: inherit;"><?php echo wp_kses_post($cta_secondary_text); ?></span>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</section>