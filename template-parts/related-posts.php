<?php

/**
 * Template Part: Entradas Relacionadas (Tarjetas)
 *
 * Muestra posts de las mismas categorías que el post actual,
 * reutilizando las clases del framework CSS del tema.
 *
 * @package ViceUnf
 */

if (! defined('ABSPATH')) {
    exit;
}

// Obtener las categorías del post actual.
$categories = get_the_category();
if (empty($categories)) {
    return;
}

$category_ids = wp_list_pluck($categories, 'term_id');
$related_count = absint(get_theme_mod('viceunf_blog_related_posts_count', 3));

$related_query = class_exists('\ViceUnf\Core\Service\PostService') ? (new \ViceUnf\Core\Service\PostService())->get_related_posts(get_the_ID(), $related_count) : new WP_Query();

if (! $related_query->have_posts()) {
    wp_reset_postdata();
    return;
}
?>

<section class="dt_related-posts dt-mt-5 dt-mb-5">
    <h3 class="dt-mb-4 viceunf-section-heading">
        <?php esc_html_e('Contenido Relacionado', 'viceunf'); ?>
        <span class="viceunf-section-heading__accent"></span>
    </h3>

    <div class="dt-row dt-g-4">
        <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
            <div class="dt-col-lg-6 dt-col-12">
                <article <?php post_class(array('viceunf-related-card')); ?>>

                    <?php if (has_post_thumbnail()) : ?>
                        <div class="viceunf-related-card__thumb">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('thumbnail', array('alt' => the_title_attribute('echo=0'))); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="viceunf-related-card__info">
                        <div class="viceunf-related-card__meta">
                            <span class="date">
                                <i class="far fa-calendar-alt dt-mr-1" aria-hidden="true"></i>
                                <?php echo get_the_date(); ?>
                            </span>
                            <?php if (has_category()) : ?>
                                <span class="catetag dt-ml-3">
                                    <i class="fas fa-folder dt-mr-1" aria-hidden="true"></i>
                                    <?php
                                    $categories = get_the_category();
                                    if (!empty($categories)) {
                                        echo esc_html($categories[0]->name);
                                    }
                                    ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <h4 class="viceunf-related-card__title">
                            <a href="<?php the_permalink(); ?>" rel="bookmark">
                                <?php the_title(); ?>
                            </a>
                        </h4>

                        <div class="viceunf-related-card__excerpt">
                            <p><?php echo wp_kses_post(wp_trim_words(get_the_content(), 15, '...')); ?></p>
                        </div>
                    </div>

                </article>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<?php wp_reset_postdata(); ?>