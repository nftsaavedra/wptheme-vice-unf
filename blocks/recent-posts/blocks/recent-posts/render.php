<?php
/**
 * Renderizador de servidor para el bloque viceunf/recent-posts.
 * Extiende las capacidades del Widget Legacy al bloque nativo.
 */

// Extraer atributos definidos en block.json y configurados en edit.js (React)
$title           = isset( $attributes['title'] ) ? $attributes['title'] : 'Entradas Recientes';
$number_of_posts = isset( $attributes['numberOfPosts'] ) ? absint( $attributes['numberOfPosts'] ) : 5;
$char_limit      = isset( $attributes['charLimit'] ) ? absint( $attributes['charLimit'] ) : 55;
$categories      = isset( $attributes['categories'] ) ? $attributes['categories'] : array();

// Construir la consulta de WordPress (WP_Query)
$query_args = array(
    'post_type'           => 'post',
    'posts_per_page'      => $number_of_posts,
    'post_status'         => 'publish',
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true, // Optimización: no calcular total de paginación
    'update_post_term_cache' => false,
);

// Aplicar filtro de categorías si el administrador seleccionó alguna en Gutenberg
if ( ! empty( $categories ) && is_array( $categories ) ) {
    $query_args['category__in'] = $categories;
}

$recent_posts = new WP_Query( $query_args );

// Encapsulador del bloque
$wrapper_attributes = get_block_wrapper_attributes();

?>
<div <?php echo $wrapper_attributes; ?>>
    <div class="viceunf-recent-posts-widget">
        
        <?php if ( ! empty( $title ) ) : ?>
            <h3 class="widget-title">
                <?php echo esc_html( $title ); ?>
            </h3>
        <?php endif; ?>

        <?php if ( $recent_posts->have_posts() ) : ?>
            <div class="viceunf-recent-posts-list">
                <?php 
                $counter = 0;
                while ( $recent_posts->have_posts() ) : $recent_posts->the_post(); 
                    $counter++;
                    $item_class = 'viceunf-recent-post-item';
                    if ( $counter % 2 === 0 ) {
                        $item_class .= ' item-par';
                    }

                    // Lógica para limitar el título original
                    $post_title = get_the_title();
                    if ( mb_strlen( $post_title ) > $char_limit ) {
                        $post_title = mb_substr( $post_title, 0, $char_limit ) . '...';
                    }
                ?>
                    <div class="<?php echo esc_attr( $item_class ); ?>">
                        
                        <?php if ( has_post_thumbnail() ) : ?>
                            <a href="<?php the_permalink(); ?>" class="viceunf-recent-post-thumbnail">
                                <?php 
                                // Usar lazy-loading nativo y wp_get_attachment_image
                                echo wp_get_attachment_image( 
                                    get_post_thumbnail_id(), 
                                    'thumbnail', 
                                    false, 
                                    array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title() ) )
                                ); 
                                ?>
                            </a>
                        <?php endif; ?>

                        <div class="viceunf-recent-post-content">
                            <h5 class="viceunf-recent-post-title">
                                <a href="<?php the_permalink(); ?>"><?php echo esc_html( $post_title ); ?></a>
                            </h5>
                        </div>

                    </div>
                <?php endwhile; ?>
            </div>
            <?php wp_reset_postdata(); ?>
        <?php else: ?>
            <p><?php esc_html_e( 'No se encontraron entradas recientes.', 'viceunf' ); ?></p>
        <?php endif; ?>
    </div>
</div>
