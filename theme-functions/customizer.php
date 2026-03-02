<?php
// Salir si se accede directamente.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Registra las opciones del Personalizador de WordPress para el tema.
 *
 * @param WP_Customize_Manager $wp_customize Objeto del personalizador.
 */
function viceunf_customize_register($wp_customize)
{

    // --- Panel padre: VPIN ---
    $wp_customize->add_panel('viceunf_panel', array(
        'title'       => __('VPIN — Configuración del Tema', 'viceunf'),
        'description' => __('Opciones de personalización del tema VPIN — Vicepresidencia de Investigación, UNF.', 'viceunf'),
        'priority'    => 30,
    ));

    // --- Sección: Header ---
    $wp_customize->add_section('viceunf_header_section', array(
        'title'    => __('Configuración del Header', 'viceunf'),
        'panel'    => 'viceunf_panel',
        'priority' => 10,
    ));

    // Preloader
    $wp_customize->add_setting('viceunf_hs_preloader', array(
        'default'           => '1',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control('viceunf_hs_preloader', array(
        'label'   => __('Mostrar Preloader', 'viceunf'),
        'section' => 'viceunf_header_section',
        'type'    => 'select',
        'choices' => array(
            '1' => __('Sí', 'viceunf'),
            '0' => __('No', 'viceunf'),
        ),
    ));

    // Sticky Header
    $wp_customize->add_setting('viceunf_sticky_header', array(
        'default'           => '1',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('viceunf_sticky_header', array(
        'label'   => __('Header Pegajoso (Sticky)', 'viceunf'),
        'section' => 'viceunf_header_section',
        'type'    => 'select',
        'choices' => array(
            '1' => __('Sí', 'viceunf'),
            '0' => __('No', 'viceunf'),
        ),
    ));

    // Búsqueda
    $wp_customize->add_setting('viceunf_hs_hdr_search', array(
        'default'           => '1',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('viceunf_hs_hdr_search', array(
        'label'   => __('Mostrar Búsqueda en Header', 'viceunf'),
        'section' => 'viceunf_header_section',
        'type'    => 'select',
        'choices' => array(
            '1' => __('Sí', 'viceunf'),
            '0' => __('No', 'viceunf'),
        ),
    ));

    // Tipo de Resultado de Búsqueda
    $wp_customize->add_setting('viceunf_search_result', array(
        'default'           => 'post',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('viceunf_search_result', array(
        'label'   => __('Tipo de Resultado de Búsqueda', 'viceunf'),
        'section' => 'viceunf_header_section',
        'type'    => 'select',
        'choices' => array(
            'post'    => __('Publicaciones', 'viceunf'),
            'product' => __('Productos', 'viceunf'),
        ),
    ));

    // Carrito
    $wp_customize->add_setting('viceunf_hs_hdr_cart', array(
        'default'           => '1',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('viceunf_hs_hdr_cart', array(
        'label'   => __('Mostrar Carrito en Header', 'viceunf'),
        'section' => 'viceunf_header_section',
        'type'    => 'select',
        'choices' => array(
            '1' => __('Sí', 'viceunf'),
            '0' => __('No', 'viceunf'),
        ),
    ));

    // Cuenta
    $wp_customize->add_setting('viceunf_hs_hdr_account', array(
        'default'           => '1',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('viceunf_hs_hdr_account', array(
        'label'   => __('Mostrar Cuenta en Header', 'viceunf'),
        'section' => 'viceunf_header_section',
        'type'    => 'select',
        'choices' => array(
            '1' => __('Sí', 'viceunf'),
            '0' => __('No', 'viceunf'),
        ),
    ));

    // Tamaño del Logo
    $wp_customize->add_setting('viceunf_hdr_logo_size', array(
        'default'           => '150',
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('viceunf_hdr_logo_size', array(
        'label'       => __('Tamaño del Logo (px)', 'viceunf'),
        'section'     => 'viceunf_header_section',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 50,
            'max'  => 500,
            'step' => 5,
        ),
    ));

    // Logo Móvil
    $wp_customize->add_setting('viceunf_mobile_logo', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'viceunf_mobile_logo', array(
        'label'   => __('Logo para Móvil', 'viceunf'),
        'section' => 'viceunf_header_section',
    )));

    // --- Sección: Breadcrumb ---
    $wp_customize->add_section('viceunf_breadcrumb_section', array(
        'title'    => __('Configuración del Breadcrumb', 'viceunf'),
        'panel'    => 'viceunf_panel',
        'priority' => 20,
    ));

    $wp_customize->add_setting('viceunf_hs_breadcrumb', array(
        'default'           => '1',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('viceunf_hs_breadcrumb', array(
        'label'   => __('Mostrar Breadcrumb', 'viceunf'),
        'section' => 'viceunf_breadcrumb_section',
        'type'    => 'select',
        'choices' => array(
            '1' => __('Sí', 'viceunf'),
            '0' => __('No', 'viceunf'),
        ),
    ));

    $wp_customize->add_setting('viceunf_breadcrumb_bg_img', array(
        'default'           => get_stylesheet_directory_uri() . '/assets/images/background/page_title.webp',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'viceunf_breadcrumb_bg_img', array(
        'label'   => __('Imagen de Fondo del Breadcrumb', 'viceunf'),
        'section' => 'viceunf_breadcrumb_section',
    )));

    $wp_customize->add_setting('viceunf_breadcrumb_img_opacity', array(
        'default'           => '0.5',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('viceunf_breadcrumb_img_opacity', array(
        'label'       => __('Opacidad de la Imagen de Fondo', 'viceunf'),
        'section'     => 'viceunf_breadcrumb_section',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 0,
            'max'  => 1,
            'step' => 0.1,
        ),
    ));

    $wp_customize->add_setting('viceunf_breadcrumb_opacity_color', array(
        'default'           => '#000',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'viceunf_breadcrumb_opacity_color', array(
        'label'   => __('Color de la Capa de Opacidad', 'viceunf'),
        'section' => 'viceunf_breadcrumb_section',
    )));

    $wp_customize->add_setting('viceunf_breadcrumb_type', array(
        'default'           => 'theme',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('viceunf_breadcrumb_type', array(
        'label'   => __('Tipo de Breadcrumb', 'viceunf'),
        'section' => 'viceunf_breadcrumb_section',
        'type'    => 'select',
        'choices' => array(
            'theme'    => __('Tema (por defecto)', 'viceunf'),
            'yoast'    => __('Yoast SEO', 'viceunf'),
            'rankmath' => __('Rank Math', 'viceunf'),
            'navxt'    => __('NavXT', 'viceunf'),
        ),
    ));

    // --- Sección: Footer ---
    $wp_customize->add_section('viceunf_footer_section', array(
        'title'    => __('Configuración del Footer', 'viceunf'),
        'panel'    => 'viceunf_panel',
        'priority' => 30,
    ));

    $wp_customize->add_setting('viceunf_footer_copyright_text', array(
        'default'           => '&copy; [current_year] Vicepresidencia de Investigación — [site_title]',
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('viceunf_footer_copyright_text', array(
        'label'       => __('Texto de Copyright', 'viceunf'),
        'description' => __('Variables: [current_year], [site_title], [theme_author]', 'viceunf'),
        'section'     => 'viceunf_footer_section',
        'type'        => 'textarea',
    ));

    $wp_customize->add_setting('viceunf_footer_bg_color', array(
        'default'           => '#0e1422',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'viceunf_footer_bg_color', array(
        'label'   => __('Color de Fondo del Footer', 'viceunf'),
        'section' => 'viceunf_footer_section',
    )));

    $wp_customize->add_setting('viceunf_hs_scroller', array(
        'default'           => '1',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('viceunf_hs_scroller', array(
        'label'   => __('Mostrar Botón "Ir Arriba"', 'viceunf'),
        'section' => 'viceunf_footer_section',
        'type'    => 'select',
        'choices' => array(
            '1' => __('Sí', 'viceunf'),
            '0' => __('No', 'viceunf'),
        ),
    ));

    // --- Sección: Layout ---
    $wp_customize->add_section('viceunf_layout_section', array(
        'title'    => __('Configuración de Disposición', 'viceunf'),
        'panel'    => 'viceunf_panel',
        'priority' => 40,
    ));

    $wp_customize->add_setting('viceunf_site_container_width', array(
        'default'           => 1252,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('viceunf_site_container_width', array(
        'label'       => __('Ancho del Contenedor (px)', 'viceunf'),
        'section'     => 'viceunf_layout_section',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 768,
            'max'  => 2000,
            'step' => 10,
        ),
    ));

    $wp_customize->add_setting('viceunf_sidebar_width', array(
        'default'           => 33,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('viceunf_sidebar_width', array(
        'label'       => __('Ancho del Sidebar (%)', 'viceunf'),
        'section'     => 'viceunf_layout_section',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 20,
            'max'  => 50,
            'step' => 1,
        ),
    ));

    // --- Sección: Blog / Single Post ---
    $wp_customize->add_section('viceunf_blog_section', array(
        'title'    => __('Configuración del Blog', 'viceunf'),
        'panel'    => 'viceunf_panel',
        'priority' => 25,
    ));

    // Controles de visibilidad de metadatos del post
    $blog_controls = array(
        'viceunf_blog_show_featured_image' => __('Mostrar Imagen Destacada', 'viceunf'),
        'viceunf_blog_show_date'           => __('Mostrar Fecha', 'viceunf'),
        'viceunf_blog_show_author'         => __('Mostrar Autor', 'viceunf'),
        'viceunf_blog_show_categories'     => __('Mostrar Categorías', 'viceunf'),
        'viceunf_blog_show_tags'           => __('Mostrar Etiquetas', 'viceunf'),
        'viceunf_blog_show_comments_count' => __('Mostrar Contador de Comentarios', 'viceunf'),
        'viceunf_blog_show_post_navigation' => __('Mostrar Navegación Anterior/Siguiente', 'viceunf'),
        'viceunf_blog_show_related_posts'  => __('Mostrar Contenido Relacionado', 'viceunf'),
    );

    foreach ($blog_controls as $setting_id => $label) {
        $wp_customize->add_setting($setting_id, array(
            'default'           => '1',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));
        $wp_customize->add_control($setting_id, array(
            'label'   => $label,
            'section' => 'viceunf_blog_section',
            'type'    => 'select',
            'choices' => array(
                '1' => __('Sí', 'viceunf'),
                '0' => __('No', 'viceunf'),
            ),
        ));
    }

    // Número de entradas relacionadas
    $wp_customize->add_setting('viceunf_blog_related_posts_count', array(
        'default'           => 3,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('viceunf_blog_related_posts_count', array(
        'label'       => __('Número de Contenido Relacionado', 'viceunf'),
        'section'     => 'viceunf_blog_section',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 1,
            'max'  => 12,
            'step' => 1,
        ),
    ));
}
add_action('customize_register', 'viceunf_customize_register');

/**
 * Migra los theme_mods del tema padre (softme_*) a los nuevos (viceunf_*).
 * Se ejecuta solo una vez.
 */
function viceunf_migrate_theme_mods()
{
    if (get_option('viceunf_mods_migrated')) {
        return;
    }

    $migrations = array(
        'softme_hs_preloader_option' => 'viceunf_hs_preloader',
        'softme_sticky_header'       => 'viceunf_sticky_header',
        'softme_hs_hdr_sticky'       => 'viceunf_sticky_header',
        'softme_hs_hdr_search'       => 'viceunf_hs_hdr_search',
        'softme_search_result'       => 'viceunf_search_result',
        'softme_hs_hdr_cart'         => 'viceunf_hs_hdr_cart',
        'softme_hs_hdr_account'      => 'viceunf_hs_hdr_account',
        'hdr_logo_size'              => 'viceunf_hdr_logo_size',
        'softme_mobile_logo'         => 'viceunf_mobile_logo',
        'softme_hs_breadcrumb'       => 'viceunf_hs_breadcrumb',
        'softme_hs_site_breadcrumb'  => 'viceunf_hs_breadcrumb',
        'softme_breadcrumb_bg_img'   => 'viceunf_breadcrumb_bg_img',
        'softme_breadcrumb_img_opacity'  => 'viceunf_breadcrumb_img_opacity',
        'softme_breadcrumb_opacity_color' => 'viceunf_breadcrumb_opacity_color',
        'softme_breadcrumb_type'     => 'viceunf_breadcrumb_type',
        'softme_footer_copyright_text' => 'viceunf_footer_copyright_text',
        'softme_footer_bg_color'     => 'viceunf_footer_bg_color',
        'softme_hs_scroller_option'  => 'viceunf_hs_scroller',
        'softme_site_container_width' => 'viceunf_site_container_width',
        'softme_sidebar_width'       => 'viceunf_sidebar_width',
    );

    foreach ($migrations as $old_key => $new_key) {
        $old_value = get_theme_mod($old_key);
        if (false !== $old_value && ! get_theme_mod($new_key)) {
            set_theme_mod($new_key, $old_value);
        }
    }

    update_option('viceunf_mods_migrated', true);
}
add_action('admin_init', 'viceunf_migrate_theme_mods');
