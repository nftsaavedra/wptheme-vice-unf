<?php

declare(strict_types=1);

namespace ViceUnf\Theme;

class Assets
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
        add_filter('script_loader_tag', [$this, 'force_defer_scripts'], 10, 3);
        add_action('wp_head', [$this, 'preload_critical_assets'], 1);
        add_action('wp_enqueue_scripts', [$this, 'dequeue_jquery_frontend'], 99);
        add_filter('style_loader_tag', [$this, 'defer_non_critical_css'], 10, 4);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('enqueue_block_assets', [$this, 'enqueue_block_editor_assets']);
    }

    public function enqueue_block_editor_assets(): void
    {
        if (is_admin()) {
            wp_enqueue_style('viceunf-fontawesome-editor', get_stylesheet_directory_uri() . '/assets/css/all.min.css', [], VICEUNF_FONTAWESOME_VERSION);
        }
    }

    public function enqueue_frontend_assets(): void
    {
        $theme_version = wp_get_theme()->get('Version');
        $theme_uri     = get_stylesheet_directory_uri();

        wp_enqueue_style('viceunf-framework', $theme_uri . '/assets/css/framework.min.css', [], $theme_version);
        wp_enqueue_style('viceunf-core', $theme_uri . '/assets/css/core.css', ['viceunf-framework'], $theme_version);
        wp_enqueue_style('viceunf-fontawesome', $theme_uri . '/assets/css/all.min.css', [], VICEUNF_FONTAWESOME_VERSION);
        wp_enqueue_style('viceunf-animate', $theme_uri . '/assets/vendors/css/animate.css', [], VICEUNF_ANIMATE_VERSION);

        if (is_front_page() || is_singular()) {
            wp_enqueue_style('viceunf-swiper', $theme_uri . '/assets/vendors/css/swiper-bundle.min.css', [], VICEUNF_SWIPER_VERSION);
            wp_enqueue_script('viceunf-swiper', $theme_uri . '/assets/vendors/js/swiper-bundle.min.js', [], VICEUNF_SWIPER_VERSION, ['strategy' => 'defer', 'in_footer' => true]);
        }

        if (is_front_page() || is_singular() || is_page_template('page-templates/frontpage.php')) {
            wp_enqueue_style('viceunf-glightbox', $theme_uri . '/assets/vendors/css/glightbox.min.css', [], VICEUNF_GLIGHTBOX_VERSION);
            wp_enqueue_script('viceunf-glightbox', $theme_uri . '/assets/vendors/js/glightbox.min.js', [], VICEUNF_GLIGHTBOX_VERSION, ['strategy' => 'defer', 'in_footer' => true]);
        }

        wp_enqueue_style('viceunf-style', get_stylesheet_uri(), ['viceunf-framework', 'viceunf-core', 'viceunf-fontawesome'], $theme_version);
        wp_enqueue_script('viceunf-theme', $theme_uri . '/assets/js/theme.js', [], $theme_version, ['strategy' => 'defer', 'in_footer' => true]);
        wp_enqueue_script('viceunf-custom', $theme_uri . '/assets/js/custom.js', ['viceunf-theme'], $theme_version, ['strategy' => 'defer', 'in_footer' => true]);

        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }

        /* Bypass del Loader Gutenberiano: Encola recursivamente el CSS de los bloques personalizados (Fase 2) */
        $blocks_dir = get_stylesheet_directory() . '/build/blocks/';
        if (is_dir($blocks_dir) && is_array($block_folders = scandir($blocks_dir))) {
            foreach ($block_folders as $folder) {
                if ('.' !== $folder && '..' !== $folder) {
                    $style_path = $blocks_dir . $folder . '/style-index.css';
                    if (file_exists($style_path)) {
                        wp_enqueue_style('viceunf-block-' . $folder, $theme_uri . '/build/blocks/' . $folder . '/style-index.css', [], $theme_version);
                    }
                }
            }
        }
    }

    public function force_defer_scripts(string $tag, string $handle, string $src): string
    {
        if (is_admin()) {
            return $tag;
        }

        if (strpos($tag, 'defer') !== false || strpos($tag, 'async') !== false || strpos($tag, 'type="module"') !== false) {
            return $tag;
        }

        return preg_replace('/(<script\b[^>]*)\bsrc=/', '$1 defer="defer" src=', $tag) ?? $tag;
    }

    public function preload_critical_assets(): void
    {
        echo '<link rel="preload" href="' . esc_url(get_stylesheet_directory_uri() . '/assets/webfonts/fa-solid-900.woff2') . '" as="font" type="font/woff2" crossorigin>' . "\n";
    }

    public function dequeue_jquery_frontend(): void
    {
        if (!is_admin()) {
            wp_dequeue_script('jquery');
        }
    }

    public function defer_non_critical_css(string $tag, string $handle, string $href, string $media): string
    {
        $non_critical_handles = ['viceunf-fontawesome', 'viceunf-animate', 'viceunf-swiper', 'viceunf-glightbox'];

        if (in_array($handle, $non_critical_handles, true)) {
            return "<link rel='preload' as='style' href='" . esc_url($href) . "' onload=\"this.onload=null;this.rel='stylesheet'\" media='all'>\n" .
                   "<noscript><link rel='stylesheet' href='" . esc_url($href) . "' media='all'></noscript>\n";
        }

        return $tag;
    }

    public function enqueue_admin_assets(string $hook): void
    {
        wp_enqueue_style('viceunf-fontawesome-admin', get_stylesheet_directory_uri() . '/assets/css/all.min.css', [], VICEUNF_FONTAWESOME_VERSION);

        $screen = get_current_screen();
        if (!$screen) return;

        $is_options_page           = ('toplevel_page_viceunf_theme_options' == $hook);
        $is_slider_page            = (isset($screen->post_type) && 'slider' === $screen->post_type);
        $is_dependencia_page       = (isset($screen->post_type) && 'dependencia' === $screen->post_type);
        $is_reglamento_page        = (isset($screen->post_type) && 'reglamento' === $screen->post_type);
        $is_reglamento_category    = (isset($screen->taxonomy) && 'categoria_reglamento' === $screen->taxonomy);

        if ($is_options_page || $is_slider_page || $is_dependencia_page) {
            wp_enqueue_style('viceunf-admin-options-style', get_stylesheet_directory_uri() . '/assets/css/admin-options.css');
            wp_enqueue_script('viceunf-admin-search', get_stylesheet_directory_uri() . '/assets/js/admin-search.js', [], true);
            wp_localize_script('viceunf-admin-search', 'viceunf_ajax_obj', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('viceunf_ajax_nonce_action'),
            ]);
        }

        if ($screen->is_block_editor()) {
            wp_enqueue_style('viceunf-admin-options-style', get_stylesheet_directory_uri() . '/assets/css/admin-options.css');
            wp_add_inline_script(
                'wp-blocks',
                'window.ajaxurl = window.ajaxurl || "' . admin_url('admin-ajax.php') . '";' .
                'window.viceunf_ajax_obj = window.viceunf_ajax_obj || ' . wp_json_encode([
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce'    => wp_create_nonce('viceunf_ajax_nonce_action'),
                ]) . ';',
                'before'
            );
        }

        if ($is_options_page) {
            wp_enqueue_media();
            wp_enqueue_script(
                'viceunf-admin-options-manager',
                get_stylesheet_directory_uri() . '/assets/js/admin-options-manager.js',
                ['viceunf-admin-search'],
                '1.0.1',
                true
            );
        }

        if ($is_slider_page || $is_reglamento_page || $is_reglamento_category) {
            wp_enqueue_style('viceunf-admin-styles', get_stylesheet_directory_uri() . '/assets/css/admin-style.css');
        }

        if ($is_reglamento_page || $is_reglamento_category) {
            $main_script_dependencies = [];
            if ($is_reglamento_category) {
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
    }
}
