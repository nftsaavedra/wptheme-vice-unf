<?php
/**
 * The template for displaying all single posts.
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
        <div id="dt-main" class="dt-col-lg-8">
            <?php while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class( 'dt_post_item' ); ?>>
                    <?php if ( has_post_thumbnail() ) : ?>
                        <div class="dt_post_media">
                            <?php the_post_thumbnail( 'full' ); ?>
                        </div>
                    <?php endif; ?>

                    <div class="dt_post_content">
                        <div class="dt_post_meta">
                            <span class="dt_post_date"><i class="far fa-calendar-alt"></i> <?php echo get_the_date(); ?></span>
                            <span class="dt_post_author"><i class="far fa-user"></i> <?php the_author(); ?></span>
                            <?php if ( has_category() ) : ?>
                                <span class="dt_post_cat"><i class="far fa-folder"></i> <?php the_category( ', ' ); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="dt_post_body entry-content">
                            <?php
                            the_content();
                            wp_link_pages( array(
                                'before' => '<div class="page-links">' . esc_html__( 'Páginas:', 'viceunf' ),
                                'after'  => '</div>',
                            ) );
                            ?>
                        </div>

                        <?php if ( has_tag() ) : ?>
                            <div class="dt_post_tags">
                                <i class="fas fa-tags"></i>
                                <?php the_tags( '', ', ', '' ); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>

                <?php
                the_post_navigation( array(
                    'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Anterior:', 'viceunf' ) . '</span> <span class="nav-title">%title</span>',
                    'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Siguiente:', 'viceunf' ) . '</span> <span class="nav-title">%title</span>',
                ) );

                if ( comments_open() || get_comments_number() ) :
                    comments_template();
                endif;
                ?>
            <?php endwhile; ?>
        </div>
        <?php get_sidebar(); ?>
    </div>
</div>

<?php get_footer(); ?>
