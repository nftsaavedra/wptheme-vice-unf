<?php
/**
 * Template Part: Entradas Relacionadas (Tarjetas)
 *
 * Muestra posts de las mismas categorías que el post actual,
 * reutilizando las clases del framework CSS del tema.
 *
 * @package ViceUnf
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Obtener las categorías del post actual.
$categories = get_the_category();
if ( empty( $categories ) ) {
    return;
}

$category_ids = wp_list_pluck( $categories, 'term_id' );
$related_count = absint( get_theme_mod( 'viceunf_blog_related_posts_count', 3 ) );

$related_query = new WP_Query( array(
    'post_type'           => 'post',
    'posts_per_page'      => $related_count,
    'post_status'         => 'publish',
    'post__not_in'        => array( get_the_ID() ),
    'category__in'        => $category_ids,
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
    'update_post_term_cache' => false,
    'orderby'             => 'date',
    'order'               => 'DESC',
) );

if ( ! $related_query->have_posts() ) {
    wp_reset_postdata();
    return;
}
?>

<section class="dt_related-posts dt-mt-5 dt-mb-5">
    <h3 class="dt-mb-4 viceunf-section-heading">
        <?php esc_html_e( 'Entradas Relacionadas', 'viceunf' ); ?>
        <span class="viceunf-section-heading__accent"></span>
    </h3>

    <div class="dt-row dt-g-4">
        <?php while ( $related_query->have_posts() ) : $related_query->the_post(); ?>
            <div class="dt-col-lg-4 dt-col-sm-6 dt-col-12">
                <article <?php post_class( array( 'dt_post_item', 'dt_posts--one', 'dt-mb-4' ) ); ?>>

                    <?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php the_permalink(); ?>">
                            <div class="image">
                                <?php the_post_thumbnail( 'large', array( 'alt' => the_title_attribute( 'echo=0' ) ) ); ?>
                            </div>
                        </a>
                    <?php endif; ?>

                    <div class="inner">
                        <div class="meta">
                            <ul>
                                <li>
                                    <div class="date">
                                        <i class="far fa-calendar-alt dt-mr-2" aria-hidden="true"></i>
                                        <?php echo get_the_date(); ?>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <?php if ( has_category() ) : ?>
                            <div class="catetag">
                                <i class="fas fa-folder dt-mr-1" aria-hidden="true"></i>
                                <?php the_category( ', ' ); ?>
                            </div>
                        <?php endif; ?>

                        <h4 class="title">
                            <a href="<?php the_permalink(); ?>" rel="bookmark">
                                <?php the_title(); ?>
                            </a>
                        </h4>

                        <div class="content">
                            <p><?php echo wp_kses_post( wp_trim_words( get_the_content(), 20, '...' ) ); ?></p>
                            <a href="<?php the_permalink(); ?>" class="more-link"><?php esc_html_e( 'Leer más', 'viceunf' ); ?></a>
                        </div>
                    </div>

                </article>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<?php wp_reset_postdata(); ?>
