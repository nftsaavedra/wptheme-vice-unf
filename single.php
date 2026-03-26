<?php

/**
 * The template for displaying all single posts.
 *
 * @package ViceUnf
 */

if (! defined('ABSPATH')) {
    exit;
}

get_header();

// Leer opciones del Customizer (default = '1' = visible).
$show_image      = get_theme_mod('viceunf_blog_show_featured_image', '1') !== '0';
$show_date       = get_theme_mod('viceunf_blog_show_date', '1') !== '0';
$show_author     = get_theme_mod('viceunf_blog_show_author', '1') !== '0';
$show_categories = get_theme_mod('viceunf_blog_show_categories', '1') !== '0';
$show_tags       = get_theme_mod('viceunf_blog_show_tags', '1') !== '0';
$show_comments   = get_theme_mod('viceunf_blog_show_comments_count', '1') !== '0';
$show_nav        = get_theme_mod('viceunf_blog_show_post_navigation', '1') !== '0';
$show_related    = get_theme_mod('viceunf_blog_show_related_posts', '1') !== '0';
?>

    <section id="dt_posts" class="dt_posts dt-py-default viceunf-bg-canvas">
        <div class="dt-container">
            <div class="dt-row dt-g-4">
                <div id="dt-main" class="dt-col-lg-8 dt-col-md-12 dt-col-12">
                    <?php while (have_posts()) : the_post(); ?>
                        <div class="dt-row dt-g-4">
                            <div class="dt-col-lg-12 dt-col-sm-12 dt-col-12">
                                <article id="post-<?php the_ID(); ?>" <?php post_class(array('dt_post_item', 'dt_posts--one', 'dt-mb-4', 'single-post')); ?>>

                                    <!-- Main Card Container -->
                                    <div class="viceunf-card-surface">
                                        
                                        <header class="viceunf-card-header dt-mb-3">
                                            <h1 class="entry-title dt-mb-2"><?php the_title(); ?></h1>
                                            <?php if (has_post_thumbnail() && $show_image) : ?>
                                                <div class="viceunf-entry-thumbnail dt-mb-3">
                                                    <?php the_post_thumbnail('full', ['class' => 'img-fluid dt-rounded']); ?>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($show_date || $show_author || ($show_categories && has_category())) : ?>
                                                <div class="meta viceunf-post-meta">
                                                    <?php if ($show_categories && has_category()) : ?>
                                                        <div class="catetag viceunf-card-chip">
                                                            <i class="fas fa-folder dt-mr-1" aria-hidden="true"></i>
                                                            <?php the_category(', '); ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if ($show_date) : ?>
                                                        <div class="date">
                                                            <i class="far fa-calendar-alt dt-mr-2" aria-hidden="true"></i>
                                                            <?php echo get_the_date(); ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if ($show_author) : ?>
                                                        <div class="author">
                                                            <i class="far fa-user dt-mr-2" aria-hidden="true"></i>
                                                            <?php the_author(); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </header>

                                        <div class="viceunf-card-inner dt-p-4">



                                            <div class="content viceunf-entry-content">
                                                <?php
                                                the_content();
                                                wp_link_pages(array(
                                                    'before' => '<div class="page-links">' . esc_html__('Páginas:', 'viceunf'),
                                                    'after'  => '</div>',
                                                ));
                                                ?>
                                            </div>

                                            <?php
                                            $has_tags_to_show     = $show_tags && has_tag();
                                            $has_comments_to_show = $show_comments && (comments_open() || get_comments_number());
                                            ?>
                                            <?php if ($has_tags_to_show || $has_comments_to_show) : ?>
                                                <footer class="meta_bottom viceunf-post-footer">
                                                    <?php if ($has_tags_to_show) : ?>
                                                        <div class="tags viceunf-tag-list">
                                                            <i class="fas fa-tags dt-mr-1" aria-hidden="true"></i>
                                                            <?php
                                                            $tags = get_the_tags();
                                                            if ($tags) {
                                                                foreach ($tags as $tag) {
                                                                    $tag_link = get_tag_link($tag->term_id);
                                                                    echo '<a href="' . esc_url($tag_link) . '" class="viceunf-card-chip">' . esc_html($tag->name) . '</a>';
                                                                }
                                                            }
                                                            ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <?php if ($has_comments_to_show) : ?>
                                                        <div class="comments_count">
                                                            <a href="#comments" class="count viceunf-comments-link">
                                                                <i class="far fa-comment dt-mr-1" aria-hidden="true"></i>
                                                                <?php echo get_comments_number(); ?> <?php esc_html_e('Comentarios', 'viceunf'); ?>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                </footer>
                                            <?php endif; ?>
                                        </div> <!-- End .viceunf-card-inner -->
                                    </div> <!-- End .viceunf-card-surface -->
                                </article>
                            </div>
                        </div>

                        <?php
                        // Navegación Anterior / Siguiente en formato tarjeta
                        if ($show_nav) :
                            get_template_part('template-parts/post', 'navigation');
                        endif;

                        // Entradas Relacionadas con prevencion de Errores Fatales
                        if ($show_related) :
                            if (class_exists('ViceUnf\Core\Service\PostService') && method_exists('ViceUnf\Core\Service\PostService', 'get_related_posts')) {
                                get_template_part('template-parts/related', 'posts');
                            }
                        endif;

                        // Comentarios
                        if (comments_open() || get_comments_number()) :
                            comments_template();
                        endif;
                        ?>
                    <?php endwhile; ?>
                </div>
                <?php get_sidebar(); ?>
            </div>
        </div>
    </section>

<?php get_footer(); ?>