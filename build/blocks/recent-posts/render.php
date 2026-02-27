<?php

/**
 * Render for ViceUnf Recent Posts Block
 *
 * @package ViceUnf
 */

$title          = isset($attributes['title']) ? $attributes['title'] : 'Lo último...';
$number_of_posts = isset($attributes['numberOfPosts']) ? absint($attributes['numberOfPosts']) : 5;
$char_limit     = isset($attributes['charLimit']) ? absint($attributes['charLimit']) : 55;
$categories     = isset($attributes['categories']) ? $attributes['categories'] : array();

$wrapper_attributes = get_block_wrapper_attributes(array('class' => 'viceunf-recent-posts-block'));

// Consumir el Servicio del Plugin (Clean Architecture / DRY)
if (class_exists('\ViceUnf\Core\Service\PostService')) {
    $postService = new \ViceUnf\Core\Service\PostService();
    $recent_posts = $postService->get_recent_posts($number_of_posts, $categories);
} else {
    // Fallback de contingencia si el plugin core está desactivado
    $recent_posts = new WP_Query();
}

?>
<div <?php echo $wrapper_attributes; ?>>
    <?php if (! empty($title)) : ?>
        <h4 class="widget-title"><?php echo esc_html($title); ?></h4>
    <?php endif; ?>

    <?php if ($recent_posts->have_posts()) : ?>
        <ul class="recent-posts-list">
            <?php
            while ($recent_posts->have_posts()) :
                $recent_posts->the_post();
            ?>
                <li class="recent-post-item">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="post-thumb">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('thumbnail'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="post-info">
                        <h5 class="post-title">
                            <a href="<?php the_permalink(); ?>">
                                <?php
                                $post_title = get_the_title();
                                if (mb_strlen($post_title) > $char_limit) {
                                    echo esc_html(mb_substr($post_title, 0, $char_limit) . '...');
                                } else {
                                    echo esc_html($post_title);
                                }
                                ?>
                            </a>
                        </h5>
                        <div class="post-date">
                            <i class="far fa-calendar-alt" aria-hidden="true"></i>
                            <span class="dt-ml-1"><?php echo get_the_date(); ?></span>
                        </div>
                    </div>
                </li>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        </ul>
    <?php else : ?>
        <p><?php esc_html_e('No se encontraron entradas recientes.', 'viceunf'); ?></p>
    <?php endif; ?>
</div>