<?php
// Salir si se accede directamente.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * =================================================================
 * 1. Carga de Estilos y Scripts para el Frontend (Standalone)
 * =================================================================
 */
function viceunf_enqueue_frontend_assets()
{

    $theme_version = wp_get_theme()->get('Version');
    $theme_uri     = get_stylesheet_directory_uri();

    // --- CSS Framework (grid, tipografía, botones, componentes) ---
    wp_enqueue_style(
        'viceunf-framework',
        $theme_uri . '/assets/css/framework.min.css',
        array(),
        $theme_version
    );

    wp_enqueue_style(
        'viceunf-core',
        $theme_uri . '/assets/css/core.css',
        array('viceunf-framework'),
        $theme_version
    );

    // --- Vendor CSS Condicional ---
    wp_enqueue_style('viceunf-fontawesome', $theme_uri . '/assets/css/all.min.css', array(), '6.7.2');
    wp_enqueue_style('viceunf-animate', $theme_uri . '/assets/vendors/css/animate.css', array(), '4.1.1');

    if (is_front_page() || is_singular()) {
        wp_enqueue_style('viceunf-swiper', $theme_uri . '/assets/vendors/css/swiper-bundle.min.css', array(), '11.0.0');
        wp_enqueue_script('viceunf-swiper', $theme_uri . '/assets/vendors/js/swiper-bundle.min.js', array(), '11.0.0', array(
            'strategy'  => 'defer',
            'in_footer' => true,
        ));
    }

    if (is_singular() || is_page_template('page-templates/frontpage.php')) {
        wp_enqueue_style('viceunf-glightbox', $theme_uri . '/assets/vendors/css/glightbox.min.css', array(), '3.3.0');
        wp_enqueue_script('viceunf-glightbox', $theme_uri . '/assets/vendors/js/glightbox.min.js', array(), '3.3.0', array(
            'strategy'  => 'defer',
            'in_footer' => true,
        ));
    }

    // --- Theme Stylesheet (style.css — contiene custom overrides) ---
    wp_enqueue_style(
        'viceunf-style',
        get_stylesheet_uri(),
        array('viceunf-framework', 'viceunf-core', 'viceunf-fontawesome'),
        $theme_version
    );

    // --- Theme JS ---
    wp_enqueue_script(
        'viceunf-theme',
        $theme_uri . '/assets/js/theme.js',
        array(),
        $theme_version,
        array(
            'strategy'  => 'defer',
            'in_footer' => true,
        )
    );

    wp_enqueue_script(
        'viceunf-custom',
        $theme_uri . '/assets/js/custom.js',
        array('viceunf-theme'),
        $theme_version,
        array(
            'strategy'  => 'defer',
            'in_footer' => true,
        )
    );

    // Comments reply script.
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'viceunf_enqueue_frontend_assets');

/**
 * =================================================================
 * 1.2. Preload de Recursos Críticos (Fonts)
 * =================================================================
 */
function viceunf_preload_critical_assets()
{
    $theme_uri = get_stylesheet_directory_uri();
    echo '<link rel="preload" href="' . esc_url($theme_uri . '/assets/webfonts/fa-solid-900.woff2') . '" as="font" type="font/woff2" crossorigin>' . "\n";
}
add_action('wp_head', 'viceunf_preload_critical_assets', 1);

/**
 * =================================================================
 * 1.6. Desacoplar jQuery del Frontend
 * =================================================================
 *
 * jQuery ya no es necesario en el frontend del tema.
 * Se desencola condicionalmente para no impactar plugins de terceros en el admin.
 * Si un plugin frontend necesita jQuery, WordPress lo volverá a cargar automáticamente
 * al declararlo como dependencia vía wp_enqueue_script().
 */
function viceunf_dequeue_jquery_frontend()
{
    if (! is_admin()) {
        wp_dequeue_script('jquery');
    }
}
add_action('wp_enqueue_scripts', 'viceunf_dequeue_jquery_frontend', 99);

/**
 * =================================================================
 * 1.7. Evitar Render-Blocking para Vendor CSS
 * =================================================================
 *
 * Transforma las etiquetas <link> de hojas de estilo no críticas (vendor)
 * en precargas asincrónicas, mejorando el FCP y LCP en PageSpeed Insights.
 */
function viceunf_defer_non_critical_css($tag, $handle, $href, $media)
{
    $non_critical_handles = [
        'viceunf-fontawesome',
        'viceunf-animate',
        'viceunf-swiper',
        'viceunf-glightbox'
    ];

    if (in_array($handle, $non_critical_handles, true)) {
        return "<link rel='preload' as='style' href='" . esc_url($href) . "' onload=\"this.onload=null;this.rel='stylesheet'\" media='all'>\n" .
            "<noscript><link rel='stylesheet' href='" . esc_url($href) . "' media='all'></noscript>\n";
    }

    return $tag;
}
add_filter('style_loader_tag', 'viceunf_defer_non_critical_css', 10, 4);

/**
 * =================================================================
 * 2. Carga Centralizada de Estilos y Scripts para el Panel de Administración
 * =================================================================
 */
function viceunf_enqueue_admin_assets($hook)
{
    // --- Carga Global ---
    wp_enqueue_style(
        'viceunf-fontawesome-admin',
        get_stylesheet_directory_uri() . '/assets/css/all.min.css',
        array(),
        '6.7.2'
    );

    // --- Definición de Páginas Relevantes ---
    $screen = get_current_screen();
    $is_options_page           = ('toplevel_page_viceunf_theme_options' == $hook);
    $is_slider_page            = ($screen && 'slider' === $screen->post_type);
    $is_reglamento_page        = ($screen && 'reglamento' === $screen->post_type);
    $is_reglamento_category_page = ($screen && 'categoria_reglamento' === $screen->taxonomy);

    // --- Carga para Sliders y Página de Opciones ---
    if ($is_options_page || $is_slider_page) {
        wp_enqueue_style('viceunf-admin-options-style', get_stylesheet_directory_uri() . '/assets/css/admin-options.css');
        wp_enqueue_script('viceunf-admin-search', get_stylesheet_directory_uri() . '/assets/js/admin-search.js', array(), true);
        wp_localize_script('viceunf-admin-search', 'viceunf_ajax_obj', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('slider_metabox_nonce_action'),
        ));
    }

    // --- Carga específica para Página de Opciones ---
    if ($is_options_page) {
        wp_enqueue_media();
        wp_enqueue_script(
            'viceunf-admin-options-manager',
            get_stylesheet_directory_uri() . '/assets/js/admin-options-manager.js',
            array('viceunf-admin-search'),
            '1.0.1',
            true
        );
    }

    // --- Carga de Estilos Generales para nuestros Meta-Boxes ---
    if ($is_slider_page || $is_reglamento_page || $is_reglamento_category_page) {
        wp_enqueue_style('viceunf-admin-styles', get_stylesheet_directory_uri() . '/assets/css/admin-style.css');
    }

    // --- INICIO: LÓGICA DE CARGA PARA REGLAMENTOS Y CATEGORÍAS ---
    if ($is_reglamento_page || $is_reglamento_category_page) {
        $main_script_dependencies = array();
        if ($is_reglamento_category_page) {
            wp_enqueue_style('wp-color-picker');
            $main_script_dependencies[] = 'wp-color-picker';
        }
        wp_enqueue_script(
            'viceunf-admin-main',
            get_stylesheet_directory_uri() . '/assets/js/admin-main.js',
            $main_script_dependencies,
            '1.0.2',
            true
        );
    }
    // --- FIN: LÓGICA DE CARGA PARA REGLAMENTOS ---
}
add_action('admin_enqueue_scripts', 'viceunf_enqueue_admin_assets');
