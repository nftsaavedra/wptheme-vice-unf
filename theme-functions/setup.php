<?php
// Salir si se accede directamente.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Configuración inicial del tema (standalone).
 */
function viceunf_theme_setup()
{
    load_theme_textdomain('viceunf', get_template_directory() . '/languages');

    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height'      => 250,
        'width'       => 250,
        'flex-width'  => true,
        'flex-height' => true,
    ));
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));
    add_theme_support('custom-background', array(
        'default-color' => 'ffffff',
        'default-image' => '',
    ));
    add_theme_support('custom-header', array(
        'default-image'      => '',
        'default-text-color' => '000',
        'width'              => 1920,
        'height'             => 1080,
        'flex-width'         => true,
        'flex-height'        => true,
    ));
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('align-wide');
    add_theme_support('wp-block-styles');
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor-style.css');
    add_theme_support('woocommerce');

    register_nav_menus(array(
        'primary_menu' => __('Menú Principal', 'viceunf'),
    ));

    add_image_size('viceunf-blog-thumb', 720, 480, true);
}
add_action('after_setup_theme', 'viceunf_theme_setup');

/**
 * Establece el ancho del contenido en función del diseño del tema.
 */
function viceunf_content_width()
{
    $GLOBALS['content_width'] = apply_filters('viceunf_content_width', 1200);
}
add_action('after_setup_theme', 'viceunf_content_width', 0);

/**
 * Registra las áreas de widgets del tema.
 */
function viceunf_register_sidebars()
{
    // Sidebar principal.
    register_sidebar(array(
        'name'          => __('Barra Lateral Principal', 'viceunf'),
        'id'            => 'viceunf-sidebar-primary',
        'description'   => __('Barra lateral principal del sitio.', 'viceunf'),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));

    // Footer widgets (4 columnas).
    for ($i = 1; $i <= 4; $i++) {
        register_sidebar(array(
            /* translators: %d: footer widget column number */
            'name'          => sprintf(__('Footer Widget %d', 'viceunf'), $i),
            'id'            => 'viceunf-footer-widget-' . $i,
            'description'   => sprintf(__('Columna %d del footer.', 'viceunf'), $i),
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget'  => '</aside>',
            'before_title'  => '<h4 class="widget-title">',
            'after_title'   => '</h4>',
        ));
    }

    // Sidebar de eventos.
    register_sidebar(array(
        'name'          => __('Barra Lateral de Eventos', 'viceunf'),
        'id'            => 'events-sidebar',
        'description'   => __('Widgets que aparecen en la sección de eventos de la página de inicio.', 'viceunf'),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));

    // WooCommerce sidebar.
    if (class_exists('WooCommerce')) {
        register_sidebar(array(
            'name'          => __('Barra Lateral WooCommerce', 'viceunf'),
            'id'            => 'viceunf-woocommerce-sidebar',
            'description'   => __('Barra lateral para las páginas de WooCommerce.', 'viceunf'),
            'before_widget' => '<aside id="%1$s" class="widget %2$s">',
            'after_widget'  => '</aside>',
            'before_title'  => '<h4 class="widget-title">',
            'after_title'   => '</h4>',
        ));
    }
}
add_action('widgets_init', 'viceunf_register_sidebars');

// --- INICIO: REGISTRO DE BLOQUES PERSONALIZADOS ---
/**
 * Crea una categoría personalizada para los bloques del tema.
 */
function viceunf_register_block_category($categories)
{
    $categories[] = array(
        'slug'  => 'viceunf-blocks',
        'title' => __('ViceUnf Blocks', 'viceunf'),
    );
    return $categories;
}
add_filter('block_categories_all', 'viceunf_register_block_category');

/**
 * Registra los bloques personalizados del tema.
 */
function viceunf_register_blocks()
{
    $blocks_dir = get_stylesheet_directory() . '/build/blocks/';
    if (is_dir($blocks_dir)) {
        $block_folders = scandir($blocks_dir);
        foreach ($block_folders as $folder) {
            if ('.' === $folder || '..' === $folder) {
                continue;
            }
            $block_path = $blocks_dir . $folder;
            if (is_dir($block_path)) {
                register_block_type($block_path);
            }
        }
    }
}
add_action('init', 'viceunf_register_blocks');
// --- FIN: REGISTRO DE BLOQUES PERSONALIZADOS ---

/**
 * Filtro para usar la plantilla single-documento.php para varios CPTs de documentos
 */
function viceunf_document_single_template($original_template)
{
    global $post;

    // Lista de Post Types que usarán esta plantilla de documento moderna
    $options = get_option('viceunf_theme_options', []);
    $document_post_types = isset($options['single_document_post_types']) && is_array($options['single_document_post_types'])
        ? $options['single_document_post_types']
        : ['reglamento']; // Fallback por defecto

    if (in_array($post->post_type, $document_post_types)) {
        $custom_template = locate_template(['single-documento.php']);
        if ($custom_template) {
            return $custom_template;
        }
    }

    return $original_template;
}
add_filter('single_template', 'viceunf_document_single_template');

/**
 * =================================================================
 * 1.8. Limpieza del Core de WordPress (Performance 100/100)
 * =================================================================
 * 
 * Elimina scripts y metadatos innecesarios del <head> para mejorar 
 * el primer renderizado (FCP) y reducir el tiempo de bloqueo (TBT).
 */
function viceunf_cleanup_head()
{
    // Remover Emojis
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

    // Remover oEmbed
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');

    // Remover metadatos legados
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'rest_output_link_wp_head', 10);
    remove_action('wp_head', 'wp_resource_hints', 2);
}
add_action('init', 'viceunf_cleanup_head');

/**
 * =================================================================
 * 1.9. Optimización LCP y Speculation Rules (Performance 100/100)
 * =================================================================
 * 
 * Estrategias para mejorar el Largest Contentful Paint (LCP) y 
 * permitir navegación instantánea.
 */

// 1. Preload de la imagen destacada (LCP)
function viceunf_preload_lcp_image()
{
    if (is_singular() && has_post_thumbnail()) {
        $lcp_image_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
        if ($lcp_image_url) {
            echo '<link rel="preload" as="image" href="' . esc_url($lcp_image_url) . '" imagesrcset="' . esc_attr(wp_get_attachment_image_srcset(get_post_thumbnail_id(), 'full')) . '" imagesizes="100vw">' . "\n";
        }
    }
}
add_action('wp_head', 'viceunf_preload_lcp_image', 1);

// 2. Desactivar Lazy Load para la imagen del LCP
function viceunf_disable_lazy_load_lcp($attributes, $attachment, $size)
{
    if (is_singular() && isset($attributes['loading']) && $attachment->ID === get_post_thumbnail_id()) {
        unset($attributes['loading']);
    }
    return $attributes;
}
add_filter('wp_get_attachment_image_attributes', 'viceunf_disable_lazy_load_lcp', 10, 3);

// 3. Speculation Rules API (Navegación instantánea)
function viceunf_add_speculation_rules()
{
?>
    <script type="speculationrules">
        {
      "prerender": [{
        "where": {
          "and": [
            { "href_matches": "/*" },
            { "not": { "href_matches": ["/wp-admin/*", "/wp-login.php*", "/cart/*", "/checkout/*"] } }
          ]
        },
        "eagerness": "moderate"
      }]
    }
    </script>
<?php
}
add_action('wp_head', 'viceunf_add_speculation_rules');
