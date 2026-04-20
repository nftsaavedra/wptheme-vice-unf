<?php

declare(strict_types=1);

namespace ViceUnf\Theme;

class Setup
{
    public function __construct()
    {
        add_action('after_setup_theme', [$this, 'init']);
        add_action('after_setup_theme', [$this, 'content_width'], 0);
        add_filter('block_categories_all', [$this, 'register_block_category']);
        add_action('init', [$this, 'register_blocks']);
        add_action('init', [$this, 'cleanup_head']);
        add_action('wp_head', [$this, 'preload_lcp_image'], 1);
        add_filter('wp_get_attachment_image_attributes', [$this, 'disable_lazy_load_lcp'], 10, 3);
        add_action('wp_head', [$this, 'add_speculation_rules']);
    }

    public function init(): void
    {
        load_theme_textdomain('viceunf', get_template_directory() . '/languages');

        add_theme_support('automatic-feed-links');
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('custom-logo', [
            'height'      => 250,
            'width'       => 250,
            'flex-width'  => true,
            'flex-height' => true,
        ]);
        add_theme_support('html5', [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        ]);
        add_theme_support('align-wide');
        add_theme_support('wp-block-styles');
        add_theme_support('responsive-embeds');
        add_theme_support('editor-styles');
        add_editor_style('assets/css/editor-style.css');
        add_theme_support('woocommerce');

        // --- FSE Block Theme (WordPress 7.0) ---
        add_theme_support('block-templates');
        add_theme_support('block-template-parts');

        register_nav_menus([
            'primary_menu' => __('Menú Principal', 'viceunf'),
        ]);

        // Tamaños de imagen optimizados para el tema
        add_image_size('viceunf-blog-thumb', 720, 480, true);
        add_image_size('viceunf-hero', 1920, 1080, true);
        add_image_size('viceunf-card', 400, 300, true);
        add_image_size('viceunf-thumbnail', 150, 150, true);
        add_image_size('viceunf-large', 1200, 800, false);
    }

    public function content_width(): void
    {
        $GLOBALS['content_width'] = apply_filters('viceunf_content_width', 1200);
    }

    public function register_blocks(): void
    {
        $blocks_dir = get_stylesheet_directory() . '/build/blocks/';
        if (is_dir($blocks_dir) && is_array($block_folders = scandir($blocks_dir))) {
            foreach ($block_folders as $folder) {
                if ('.' === $folder || '..' === $folder) {
                    continue;
                }
                $block_path = $blocks_dir . $folder;
                if (is_dir($block_path) && file_exists($block_path . '/block.json')) {
                    register_block_type($block_path);
                }
            }
        }
    }

    public function cleanup_head(): void
    {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        remove_action('wp_head', 'wp_generator');
    }

    public function preload_lcp_image(): void
    {
        if (is_singular() && has_post_thumbnail()) {
            $lcp_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
            if ($lcp_image_url) {
                echo '<link rel="preload" as="image" href="' . esc_url($lcp_image_url) . '" imagesrcset="' . esc_attr(wp_get_attachment_image_srcset(get_post_thumbnail_id(), 'full')) . '" imagesizes="100vw">' . "\n";
            }
        }
    }

    public function disable_lazy_load_lcp(array $attributes, \WP_Post $attachment, string|array $size): array
    {
        // Deshabilitar lazy loading para imágenes above-the-fold críticas
        if (is_singular() && isset($attributes['loading']) && $attachment->ID === get_post_thumbnail_id()) {
            unset($attributes['loading']);
        }
        
        // Asegurar lazy loading para imágenes en listados y related posts
        if (!is_singular() || is_home() || is_archive() || is_search()) {
            $attributes['loading'] = 'lazy';
            $attributes['decoding'] = 'async';
        }
        
        return $attributes;
    }

    public function add_speculation_rules(): void
    {
        echo '<script type="speculationrules">{"prerender":[{"where":{"and":[{"href_matches":"/*"},{"not":{"href_matches":["/wp-admin/*/","*login*","*cart*","*checkout*"]}}]},"eagerness":"moderate"}]}</script>';
    }
}
