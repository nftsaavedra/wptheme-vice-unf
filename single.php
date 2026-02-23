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

// Leer opciones del Customizer (default = '1' = visible).
$show_image      = get_theme_mod( 'viceunf_blog_show_featured_image', '1' ) !== '0';
$show_date       = get_theme_mod( 'viceunf_blog_show_date', '1' ) !== '0';
$show_author     = get_theme_mod( 'viceunf_blog_show_author', '1' ) !== '0';
$show_categories = get_theme_mod( 'viceunf_blog_show_categories', '1' ) !== '0';
$show_tags       = get_theme_mod( 'viceunf_blog_show_tags', '1' ) !== '0';
$show_comments   = get_theme_mod( 'viceunf_blog_show_comments_count', '1' ) !== '0';
$show_nav        = get_theme_mod( 'viceunf_blog_show_post_navigation', '1' ) !== '0';
$show_related    = get_theme_mod( 'viceunf_blog_show_related_posts', '1' ) !== '0';
?>

<div class="dt-container">
    <div class="dt-row dt-g-5">
        <div id="dt-main" class="dt-col-lg-8">
            <?php while ( have_posts() ) : the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class( array( 'dt_post_item', 'single-post' ) ); ?>>

                    <?php if ( $show_image && has_post_thumbnail() ) : ?>
                        <div class="image">
                            <?php the_post_thumbnail( 'full' ); ?>
                        </div>
                    <?php endif; ?>

                    <div class="inner">
                        <?php if ( $show_date || $show_author ) : ?>
                            <div class="meta">
                                <ul>
                                    <?php if ( $show_date ) : ?>
                                        <li>
                                            <div class="date">
                                                <i class="far fa-calendar-alt dt-mr-2" aria-hidden="true"></i>
                                                <?php echo get_the_date(); ?>
                                            </div>
                                        </li>
                                    <?php endif; ?>
                                    <?php if ( $show_author ) : ?>
                                        <li>
                                            <div class="author">
                                                <i class="far fa-user dt-mr-2" aria-hidden="true"></i>
                                                <?php the_author(); ?>
                                            </div>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if ( $show_categories && has_category() ) : ?>
                            <div class="catetag">
                                <i class="fas fa-folder dt-mr-1" aria-hidden="true"></i>
                                <?php the_category( ', ' ); ?>
                            </div>
                        <?php endif; ?>

                        <div class="entry-content">
                            <?php
                            the_content();
                            wp_link_pages( array(
                                'before' => '<div class="page-links">' . esc_html__( 'Páginas:', 'viceunf' ),
                                'after'  => '</div>',
                            ) );
                            ?>
                        </div>

                        <?php
                        $has_tags_to_show     = $show_tags && has_tag();
                        $has_comments_to_show = $show_comments && ( comments_open() || get_comments_number() );
                        ?>
                        <?php if ( $has_tags_to_show || $has_comments_to_show ) : ?>
                            <div class="meta_bottom">
                                <?php if ( $has_tags_to_show ) : ?>
                                    <div class="tags">
                                        <i class="fas fa-tags dt-mr-1" aria-hidden="true"></i>
                                        <?php the_tags( '', '', '' ); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ( $has_comments_to_show ) : ?>
                                    <div class="comments_count">
                                        <a href="#comments" class="count">
                                            <i class="far fa-comment dt-mr-1" aria-hidden="true"></i>
                                            <?php echo get_comments_number(); ?> <?php esc_html_e( 'Comentarios', 'viceunf' ); ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>

                <?php
                // Navegación Anterior / Siguiente en formato tarjeta
                if ( $show_nav ) :
                    get_template_part( 'template-parts/post', 'navigation' );
                endif;

                // Entradas Relacionadas
                if ( $show_related ) :
                    get_template_part( 'template-parts/related', 'posts' );
                endif;

                // Comentarios
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
