<?php

/**
 * Render del bloque viceunf/about-section
 *
 * Réplica exacta del marcado de template-parts/site-about.php
 * pero alimentado desde los atributos del bloque, no desde Theme Options.
 *
 * @package ViceUnf
 */

if (!defined('ABSPATH')) {
    exit;
}

$subtitle      = $attributes['subtitle'] ?? 'Sobre Nosotros';
$title         = $attributes['title'] ?? '';
$person_name   = $attributes['personName'] ?? '';
$description   = $attributes['description'] ?? '';
$main_image_id = isset($attributes['mainImageId']) ? (int) $attributes['mainImageId'] : 0;
$main_image_alt = $attributes['mainImageAlt'] ?? '';
$video_url     = $attributes['videoUrl'] ?? '';
$items         = isset($attributes['items']) && is_array($attributes['items']) ? $attributes['items'] : [];

if (empty($title) && empty($description) && $main_image_id === 0) {
    echo sprintf(
        '<div %s><p style="padding: 20px; text-align: center; border: 1px dashed orange;">Configure el bloque "Sección Nosotros" desde el panel lateral del editor.</p></div>',
        get_block_wrapper_attributes()
    );
    return;
}

$create_animated_subtitle = function ($text) {
    $output = '';
    $chars = mb_str_split($text);
    foreach ($chars as $char) {
        $output .= '<i class="in">' . esc_html($char) . '</i>';
    }
    return $output;
};

$wrapper_attributes = get_block_wrapper_attributes(['class' => 'dt_protect dt_protect--one dt-py-default']);
?>

<section <?php echo $wrapper_attributes; ?>>
    <div class="dt-container">
        <div class="dt-row dt-g-5">
            <div class="dt-col-lg-6 dt-col-md-12 dt-col-sm-12">
                <div class="dt_image_block">
                    <div class="circle_shapes">
                        <div class="circle"></div>
                    </div>
                    <?php if ($main_image_id) : ?>
                        <div class="dt_image_box image-1">
                            <figure class="image">
                                <?php
                                $image_alt = get_post_meta($main_image_id, '_wp_attachment_image_alt', true);
                                $alt_text = !empty($image_alt) ? $image_alt : (!empty($main_image_alt) ? $main_image_alt : $title);
                                echo wp_get_attachment_image($main_image_id, 'large', false, [
                                    'alt'   => esc_attr($alt_text),
                                    'style' => 'aspect-ratio: 1 / 1; object-fit: cover; width: 100%; height: auto; border-radius: 50%;'
                                ]);
                                ?>
                            </figure>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($video_url)) : ?>
                        <div class="dt_image_box image-2">
                            <figure class="image">
                                <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/investigacion_play.webp'); ?>" alt="Ícono de Play">
                            </figure>
                            <div class="dt_image_video">
                                <a href="<?php echo esc_url($video_url); ?>" class="dt_lightbox_img dt-btn-play dt-btn-primary" data-caption="">
                                    <i class="fa fa-play" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="dt-col-lg-6 dt-col-md-12 dt-col-sm-12">
                <div class="dt_content_block">
                    <div class="dt_content_box">
                        <div class="dt_siteheading">
                            <span class="subtitle">
                                <span class="dt_heading dt_heading_8">
                                    <span class="dt_heading_inner">
                                        <b class="is_on" style="opacity: 1;">
                                            <?php echo $create_animated_subtitle($subtitle); ?>
                                        </b>
                                    </span>
                                </span>
                            </span>
                            <h2 class="title">
                                <?php echo wp_kses_post($title); ?>
                                <?php if (!empty($person_name)) : ?>
                                    <br><span><?php echo esc_html($person_name); ?></span>
                                <?php endif; ?>
                            </h2>
                            <div class="text dt-mt-3 wow fadeInUp" data-wow-duration="1500ms">
                                <p><?php echo esc_html($description); ?></p>
                            </div>
                        </div>

                        <?php if (!empty($items)) : ?>
                            <div class="dt-row dt-g-4 dt-mt-2 protect-wrp">
                                <?php foreach ($items as $index => $item) :
                                    if (empty($item['title'])) {
                                        continue;
                                    }
                                    $item_url  = !empty($item['url']) ? esc_url($item['url']) : '#';
                                    $item_icon = !empty($item['icon']) ? $item['icon'] : 'fas fa-link';
                                ?>
                                    <div class="dt-col-lg-6 dt-col-sm-6 dt-col-12">
                                        <div class="dt_item_inner wow slideInUp animated" data-wow-delay="<?php echo esc_attr($index * 100); ?>ms" data-wow-duration="1500ms">
                                            <div class="dt_item_icon"><i class="<?php echo esc_attr($item_icon); ?>" aria-hidden="true"></i></div>
                                            <div class="dt_item_holder">
                                                <h5 class="dt_item_title"><a href="<?php echo $item_url; ?>"><?php echo esc_html($item['title']); ?></a></h5>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>