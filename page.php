<?php
/**
 * The template for displaying all pages.
 *
 * @package ViceUnf
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<div class="dt-container">
    <div class="dt-row dt-g-5">
        <div id="dt-main" class="dt-col-lg-12">
            <?php while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <div class="dt_page_content entry-content">
                        <?php
                        the_content();
                        wp_link_pages( array(
                            'before' => '<div class="page-links">' . esc_html__( 'Páginas:', 'viceunf' ),
                            'after'  => '</div>',
                        ) );
                        ?>
                    </div>
                </article>

                <?php
                if ( comments_open() || get_comments_number() ) :
                    comments_template();
                endif;
                ?>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
