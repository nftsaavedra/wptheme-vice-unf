<?php

/**
 * The template for displaying all pages.
 *
 * @package ViceUnf
 */

if (! defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div id="content" class="site-content dt-py-default viceunf-bg-canvas">
    <div class="dt-container">
        <div class="dt-row dt-g-5">
            <?php
            $has_sidebar = is_active_sidebar('viceunf-sidebar-primary');
            $main_class  = $has_sidebar ? 'dt-col-lg-8' : 'dt-col-lg-12';
            ?>
            <div id="dt-main" class="<?php echo esc_attr($main_class); ?>">
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <div class="viceunf-card-surface">

                            <div class="dt_page_content entry-content" style="font-size: 1.6rem; line-height: 1.8; color: #334155;">
                                <?php
                                the_content();
                                wp_link_pages(array(
                                    'before' => '<div class="page-links">' . esc_html__('Páginas:', 'viceunf'),
                                    'after'  => '</div>',
                                ));
                                ?>
                            </div>
                        </div>
                    </article>

                    <?php
                    if (comments_open() || get_comments_number()) :
                        comments_template();
                    endif;
                    ?>
                <?php endwhile; ?>
            </div>
            <?php
            if ($has_sidebar) {
                get_sidebar();
            }
            ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>