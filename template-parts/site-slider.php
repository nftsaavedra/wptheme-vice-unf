<?php
// WP_Query para obtener los 5 sliders más recientes.
$args = array(
    'post_type'      => 'slider',
    'posts_per_page' => 5,
    'orderby'        => 'date',
    'order'          => 'DESC',
);
$slider_query = new WP_Query($args);

// Solo muestra la sección si hay sliders que mostrar.
if ($slider_query->have_posts()) :
?>
    <section id="dt_slider" class="dt_slider dt_slider--five dt_slider--thumbnav dt_slider--kenburn">
        <div class="dt_owl_carousel owl-theme owl-carousel slider" data-owl-options='{
        "loop": <?php echo ($slider_query->post_count > 1) ? 'true' : 'false'; ?>,
        "items": 1,
        "navText": ["<i class=\"fas fa-angle-left\"><span class=\"imgholder\"></span></i>","<i class=\"fas fa-angle-right\"><span class=\"imgholder\"></span></i>"],
        "margin": 0,
        "dots": true,
        "nav": true,
        "animateOut": "slideOutDown",
        "animateIn": "fadeIn",
        "active": true,
        "smartSpeed": 1000,
        "autoplay": true,
        "autoplayTimeout": 30000,
        "autoplayHoverPause": false,
        "responsive": {
            "0": {"nav": false, "items": 1},
            "600": {"nav": false, "items": 1},
            "992": {"items": 1}
        }
    }'>
            <?php
            // El bucle de WordPress para generar cada slide.
            while ($slider_query->have_posts()) : $slider_query->the_post();

                // --- Lógica de Datos ---
                $subtitle = get_post_meta(get_the_ID(), '_slider_subtitle_key', true);
                $description = get_post_meta(get_the_ID(), '_slider_description_key', true);
                $text_align = get_post_meta(get_the_ID(), '_slider_text_alignment_key', true) ?: 'dt-text-left';
                $image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');

                // Botón 1 (Avanzado)
                $btn1_text = get_post_meta(get_the_ID(), '_slider_btn1_text_key', true);
                $link_type = get_post_meta(get_the_ID(), '_slider_link_type_key', true);
                $link_url = get_post_meta(get_the_ID(), '_slider_link_url_key', true);
                $link_content_id = get_post_meta(get_the_ID(), '_slider_link_content_id_key', true);
                $btn1_href = '';
                if ($link_type === 'url' && !empty($link_url)) {
                    $btn1_href = esc_url($link_url);
                } elseif ($link_type === 'content' && !empty($link_content_id)) {
                    $btn1_href = get_permalink($link_content_id);
                }

                // Botón 2 (Simple)
                $btn2_text = get_post_meta(get_the_ID(), '_slider_btn2_text_key', true);
                $btn2_link = get_post_meta(get_the_ID(), '_slider_btn2_link_key', true);

                // Botón de Video
                $original_video_link = get_post_meta(get_the_ID(), '_slider_video_link_key', true);
                $autoplay_video_link = function_exists('get_autoplay_embed_url') ? get_autoplay_embed_url($original_video_link) : $original_video_link;

                // Función para generar los spans de la animación del botón.
                $createButtonSpans = function ($text) {
                    return implode('', array_map(function ($char) {
                        return "<span>" . htmlspecialchars($char) . "</span>";
                    }, str_split($text)));
                };
            ?>

                <div class="dt_slider-item">
                    <?php if ($image_url) : ?>
                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php the_title_attribute(); ?>">
                    <?php endif; ?>
                    <div class="dt_slider-wrapper">
                        <div class="dt_slider-inner">
                            <div class="dt_slider-innercell">
                                <div class="dt-container">
                                    <div class="dt-row <?php echo esc_attr($text_align); ?>">
                                        <div class="dt-col-lg-12 dt-col-md-12 first dt-my-auto">
                                            <div class="dt_slider-content">
                                                <?php if ($subtitle) : ?><h5 class="subtitle"><?php echo esc_html($subtitle); ?></h5><?php endif; ?>
                                                <h2 class="title"><?php the_title(); ?></h2>
                                                <?php if ($description) : ?><p class="text"><?php echo nl2br(esc_html($description)); ?></p><?php endif; ?>

                                                <div class="dt_btn-group">
                                                    <?php if ($btn1_href && $btn1_text) : ?>
                                                        <a href="<?php echo $btn1_href; ?>" class="dt-btn dt-btn-primary"><span class="dt-btn-text" data-text="<?php echo esc_attr($btn1_text); ?>"><?php echo $createButtonSpans($btn1_text); ?></span></a>
                                                    <?php endif; ?>

                                                    <?php if ($btn2_link && $btn2_text) : ?>
                                                        <a href="<?php echo esc_url($btn2_link); ?>" class="dt-btn dt-btn-white"><span class="dt-btn-text" data-text="<?php echo esc_attr($btn2_text); ?>"><?php echo $createButtonSpans($btn2_text); ?></span></span></a>
                                                    <?php endif; ?>

                                                    <?php if ($autoplay_video_link) : ?>
                                                        <a href="<?php echo esc_url($autoplay_video_link); ?>" class="dt_lightbox_img dt-btn-play dt-btn-white" data-caption=""><i class="fas fa-play" aria-hidden="true"></i></a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            endwhile;
            ?>
        </div>
    </section>
<?php
endif;
wp_reset_postdata();
?>