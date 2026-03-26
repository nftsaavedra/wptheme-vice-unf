<?php

/**
 * Template de fallback principal del tema hijo.
 *
 * WordPress requiere que todo tema tenga un index.php como template de último recurso.
 * Este archivo delega al tema padre cargando su index.php.
 *
 * @package ViceUnf
 */

get_header();
?>

<section class="dt-py-default viceunf-bg-canvas">
    <div class="dt-container">
        <?php
        if (have_posts()) :
            while (have_posts()) :
                the_post();
                get_template_part('template-parts/content', get_post_type());
            endwhile;
            the_posts_navigation();
        else :
            get_template_part('template-parts/content', 'none');
        endif;
        ?>
    </div>
</section>

<?php
get_footer();
