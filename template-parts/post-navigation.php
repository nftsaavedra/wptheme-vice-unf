<?php

/**
 * Template Part: Post Navigation (Tarjetas)
 *
 * Muestra la navegación Anterior/Siguiente como tarjetas con imagen destacada.
 * Diseño: dos tarjetas lado a lado (50/50) con overlay oscuro.
 *
 * @package ViceUnf
 */

if (! defined('ABSPATH')) {
    exit;
}

$prev_post = get_previous_post();
$next_post = get_next_post();

if (! $prev_post && ! $next_post) {
    return;
}
?>

<nav class="viceunf-post-nav dt-mt-4 dt-mb-5" aria-label="<?php esc_attr_e('Navegación de entradas', 'viceunf'); ?>">
    <div class="dt-row dt-g-4">

        <?php if ($prev_post) : ?>
            <div class="dt-col-lg-6 dt-col-12">
                <a href="<?php echo esc_url(get_permalink($prev_post)); ?>" class="viceunf-post-nav__card viceunf-post-nav__card--prev" rel="prev">
                    <?php
                    $prev_thumb = get_the_post_thumbnail_url($prev_post, 'thumbnail');
                    if ($prev_thumb) : ?>
                        <div class="viceunf-post-nav__img">
                            <img src="<?php echo esc_url($prev_thumb); ?>" alt="<?php echo esc_attr(get_the_title($prev_post)); ?>">
                        </div>
                    <?php endif; ?>
                    <div class="viceunf-post-nav__content">
                        <span class="viceunf-post-nav__label">
                            <i class="fas fa-arrow-left dt-mr-2" aria-hidden="true"></i>
                            <?php esc_html_e('Anterior', 'viceunf'); ?>
                        </span>
                        <span class="viceunf-post-nav__title"><?php echo esc_html(get_the_title($prev_post)); ?></span>
                    </div>
                </a>
            </div>
        <?php else : ?>
            <div class="dt-col-lg-6 dt-col-12"></div>
        <?php endif; ?>

        <?php if ($next_post) : ?>
            <div class="dt-col-lg-6 dt-col-12">
                <a href="<?php echo esc_url(get_permalink($next_post)); ?>" class="viceunf-post-nav__card viceunf-post-nav__card--next" rel="next">
                    <?php
                    $next_thumb = get_the_post_thumbnail_url($next_post, 'thumbnail');
                    if ($next_thumb) : ?>
                        <div class="viceunf-post-nav__img">
                            <img src="<?php echo esc_url($next_thumb); ?>" alt="<?php echo esc_attr(get_the_title($next_post)); ?>">
                        </div>
                    <?php endif; ?>
                    <div class="viceunf-post-nav__content">
                        <span class="viceunf-post-nav__label">
                            <?php esc_html_e('Siguiente', 'viceunf'); ?>
                            <i class="fas fa-arrow-right dt-ml-2" aria-hidden="true"></i>
                        </span>
                        <span class="viceunf-post-nav__title"><?php echo esc_html(get_the_title($next_post)); ?></span>
                    </div>
                </a>
            </div>
        <?php endif; ?>

    </div>
</nav>