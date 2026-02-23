<?php
/**
 * The template for displaying search results pages.
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
            <?php if ( have_posts() ) : ?>
                <header class="page-header">
                    <h1 class="page-title">
                        <?php
                        printf(
                            /* translators: %s: search query */
                            esc_html__( 'Resultados de búsqueda para: %s', 'viceunf' ),
                            '<span>' . get_search_query() . '</span>'
                        );
                        ?>
                    </h1>
                </header>

                <div class="dt_posts dt-row dt-g-4">
                    <?php while ( have_posts() ) : the_post(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class( 'dt-col-12 dt_post_item' ); ?>>
                            <div class="dt_post_content">
                                <h3 class="dt_post_title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <div class="dt_post_excerpt">
                                    <?php the_excerpt(); ?>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>

                <div class="dt-pagination">
                    <?php
                    the_posts_pagination( array(
                        'mid_size'  => 2,
                        'prev_text' => '<i class="fas fa-angle-left"></i>',
                        'next_text' => '<i class="fas fa-angle-right"></i>',
                    ) );
                    ?>
                </div>
            <?php else : ?>
                <div class="no-results">
                    <h2><?php esc_html_e( 'No se encontraron resultados', 'viceunf' ); ?></h2>
                    <p><?php esc_html_e( 'Lo sentimos, pero no encontramos nada con esos términos de búsqueda. Por favor intenta con otros términos.', 'viceunf' ); ?></p>
                    <?php get_search_form(); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php get_sidebar(); ?>
    </div>
</div>

<?php get_footer(); ?>
