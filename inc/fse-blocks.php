<?php
/**
 * FSE Dynamic Blocks — WordPress 7.0 Production-Ready
 *
 * Registra bloques dinámicos de servidor que renderizan los componentes
 * Legacy del tema (Header Bootstrap, Breadcrumb, Sidebar, Footer)
 * dentro del ecosistema Full Site Editing de Gutenberg.
 *
 * @package ViceUnf
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Inyectar clases Bootstrap requeridas en el body de FSE
 */
add_filter('body_class', function (array $classes): array {
    $classes[] = 'menu__active-one';
    $classes[] = 'btn--effect-two';
    $classes[] = 'siteheading--one';
    return $classes;
});

/**
 * Registrar los 4 Dynamic Blocks del tema
 */
add_action('init', function (): void {

    // 1. Site Header — Menú Bootstrap + Topbar + Logo + Nav Móvil
    register_block_type('viceunf/site-header', [
        'api_version'     => 3,
        'render_callback' => function (): string {
            ob_start();
            do_action('viceunf_site_main_header');
            return ob_get_clean();
        },
    ]);

    // 2. Site Breadcrumb — Banner oscuro + Migas de Pan + Título
    register_block_type('viceunf/site-breadcrumb', [
        'api_version'     => 3,
        'render_callback' => function (): string {
            ob_start();
            get_template_part('template-parts/site', 'breadcrumb');
            return ob_get_clean();
        },
    ]);

    // 3. Site Sidebar — Widgets "Lo Último", "Convocatorias", etc.
    register_block_type('viceunf/site-sidebar', [
        'api_version'     => 3,
        'render_callback' => function (): string {
            if (!is_active_sidebar('viceunf-sidebar-primary')) {
                return '';
            }
            ob_start();
            ?>
            <aside id="dt-sidebar" class="dt-col-lg-4 dt_widget-area">
                <?php dynamic_sidebar('viceunf-sidebar-primary'); ?>
            </aside>
            <?php
            return ob_get_clean();
        },
    ]);

    // 4. Site Footer — Widgets Footer 1-4 + Copyright + Scroller
    register_block_type('viceunf/site-footer', [
        'api_version'     => 3,
        'render_callback' => function (): string {
            ob_start();
            ?>
            <footer id="dt_footer" class="dt_footer dt_footer--one">
                <?php do_action('viceunf_footer_widget'); ?>
                <?php do_action('viceunf_footer_bottom'); ?>
            </footer>
            <?php do_action('viceunf_top_scroller'); ?>
            <?php
            return ob_get_clean();
        },
    ]);
});
