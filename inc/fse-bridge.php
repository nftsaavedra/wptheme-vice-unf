<?php
/**
 * FSE Bridge API
 * Provee shortcodes de retrocompatibilidad (Legacy) para transicionar a Block Theme al 100%.
 *
 * @package ViceUnf
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Mantiene las clases corporativas en el Body aunque se destruya header.php
 */
add_filter('body_class', function ($classes) {
    if (wp_is_block_theme()) {
        $classes[] = 'menu__active-one';
        $classes[] = 'btn--effect-two';
        $classes[] = 'siteheading--one';
    }
    return $classes;
});

/**
 * Shortcode 1: Reemplazo para Header Complejo
 */
add_shortcode('viceunf_fse_header', function () {
    ob_start();
    ?>
    <div id="page" class="site">
        <a class="skip-link screen-reader-text" href="#content"><?php esc_html_e('Ir al contenido', 'viceunf'); ?></a>
        <?php
        do_action('viceunf_site_main_header');
        
        if (!is_page_template('page-templates/frontpage.php') && !is_page_template('page-templates/pagebuilder.php')) {
            get_template_part('/template-parts/site', 'breadcrumb');
        }
        ?>
        <div id="content" class="site-content">
    <?php
    return ob_get_clean();
});

/**
 * Shortcode 2: Reemplazo para Footer Complejo
 */
add_shortcode('viceunf_fse_footer', function () {
    ob_start();
    ?>
        </div><!-- #content .site-content -->
        <footer id="dt_footer" class="dt_footer dt_footer--one">
            <?php do_action('viceunf_footer_widget'); ?>
            <?php do_action('viceunf_footer_bottom'); ?>
        </footer>
        <?php do_action('viceunf_top_scroller'); ?>
    </div><!-- #page .site -->
    <?php
    return ob_get_clean();
});

/**
 * Shortcode 3: Reemplazo para Sidebar Legacy
 */
add_shortcode('viceunf_fse_sidebar', function () {
    ob_start();
    if (is_active_sidebar('viceunf-sidebar-primary')) :
    ?>
        <aside id="dt-sidebar" class="dt-col-lg-4 dt_widget-area">
            <?php dynamic_sidebar('viceunf-sidebar-primary'); ?>
        </aside>
    <?php
    endif;
    return ob_get_clean();
});
