<?php
// Salir si se accede directamente.
if (! defined('ABSPATH')) exit;

/**
 * Custom Post Types y Taxonomías del Tema.
 */
function viceunf_register_post_types()
{
    // --- CPT: Slider ---
    $slider_labels = array('name' => 'Sliders', 'singular_name' => 'Slider', 'menu_name' => 'Sliders', 'add_new_item' => 'Añadir Nuevo Slider', 'edit_item' => 'Editar Slider', 'featured_image' => 'Imagen de Fondo', 'set_featured_image' => 'Establecer Imagen de Fondo', 'remove_featured_image' => 'Quitar Imagen de Fondo', 'use_featured_image' => 'Usar como Imagen de Fondo');
    $slider_args = array('label' => 'Slider', 'description' => 'Contenido para el slider principal', 'labels' => $slider_labels, 'supports' => array('title', 'thumbnail', 'revisions'), 'public' => true, 'show_ui' => true, 'show_in_menu' => true, 'menu_position' => 5, 'menu_icon' => 'dashicons-images-alt2', 'exclude_from_search' => true, 'publicly_queryable' => true, 'capability_type' => 'post', 'show_in_rest' => true);
    register_post_type('slider', $slider_args);

    // --- CPT: Evento ---
    $evento_labels = array('name' => 'Eventos', 'singular_name' => 'Evento', 'menu_name' => 'Eventos', 'all_items' => 'Todos los Eventos', 'add_new_item' => 'Añadir Nuevo Evento', 'edit_item' => 'Editar Evento');
    $evento_args = array('label' => 'Evento', 'description' => 'Contenido para la sección de eventos', 'labels' => $evento_labels, 'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions'), 'public' => true, 'show_ui' => true, 'show_in_menu' => true, 'menu_position' => 6, 'menu_icon' => 'dashicons-calendar-alt', 'publicly_queryable' => true, 'capability_type' => 'post', 'has_archive' => true, 'show_in_rest' => true);
    register_post_type('evento', $evento_args);

    // --- CPT: Socio ---
    $socio_labels = array(
        'name'                  => _x('Socios', 'Post Type General Name', 'viceunf'),
        'singular_name'         => _x('Socio', 'Post Type Singular Name', 'viceunf'),
        'menu_name'             => __('Socios', 'viceunf'),
        'name_admin_bar'        => __('Socio', 'viceunf'),
        'add_new_item'          => __('Añadir Nuevo Socio', 'viceunf'),
        'add_new'               => __('Añadir Nuevo', 'viceunf'),
        'new_item'              => __('Nuevo Socio', 'viceunf'),
        'edit_item'             => __('Editar Socio', 'viceunf'),
        'view_item'             => __('Ver Socio', 'viceunf'),
        'all_items'             => __('Todos los Socios', 'viceunf'),
        'search_items'          => __('Buscar Socios', 'viceunf'),
        'not_found'             => __('No se encontraron socios.', 'viceunf'),
        'not_found_in_trash'    => __('No se encontraron socios en la papelera.', 'viceunf'),
        'featured_image'        => __('Logo del Socio', 'viceunf'),
        'set_featured_image'    => __('Establecer Logo del Socio', 'viceunf'),
        'remove_featured_image' => __('Quitar Logo del Socio', 'viceunf'),
    );
    $socio_args = array(
        'label'                 => __('Socio', 'viceunf'),
        'description'           => __('Logos de Socios Académicos', 'viceunf'),
        'labels'                => $socio_labels,
        'supports'              => array('title', 'thumbnail', 'page-attributes'),
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 7,
        'menu_icon'             => 'dashicons-businessperson',
        'capability_type'       => 'post',
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
    );
    register_post_type('socio', $socio_args);


    // --- INICIO: NUEVO CÓDIGO PARA REGLAMENTOS ---

    // 1. CPT: Reglamento
    $reglamento_labels = array(
        'name'               => _x('Reglamentos', 'Post Type General Name', 'viceunf'),
        'singular_name'      => _x('Reglamento', 'Post Type Singular Name', 'viceunf'),
        'menu_name'          => __('Reglamentos', 'viceunf'),
        'name_admin_bar'     => __('Reglamento', 'viceunf'),
        'add_new_item'       => __('Añadir Nuevo Reglamento', 'viceunf'),
        'add_new'            => __('Añadir Nuevo', 'viceunf'),
        'new_item'           => __('Nuevo Reglamento', 'viceunf'),
        'edit_item'          => __('Editar Reglamento', 'viceunf'),
        'view_item'          => __('Ver Reglamento', 'viceunf'),
        'all_items'          => __('Todos los Reglamentos', 'viceunf'),
        'search_items'       => __('Buscar Reglamentos', 'viceunf'),
        'not_found'          => __('No se encontraron reglamentos.', 'viceunf'),
        'not_found_in_trash' => __('No se encontraron reglamentos en la papelera.', 'viceunf'),
    );
    $reglamento_args = array(
        'label'                 => __('Reglamento', 'viceunf'),
        'description'           => __('Documentos normativos y reglamentos', 'viceunf'),
        'labels'                => $reglamento_labels,
        'supports'              => array('title', 'editor', 'revisions'), // Soporte para título y descripción (editor).
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 8, // Posición en el menú para que no choque con los existentes.
        'menu_icon'             => 'dashicons-media-document',
        'capability_type'       => 'post',
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'show_in_rest'          => true, // Clave para que el bloque de Gutenberg pueda acceder a los datos.
    );
    register_post_type('reglamento', $reglamento_args);

    // 2. Taxonomía para Reglamentos: Categoría de Reglamento
    $categoria_labels = array(
        'name'              => _x('Categorías de Reglamento', 'taxonomy general name', 'viceunf'),
        'singular_name'     => _x('Categoría de Reglamento', 'taxonomy singular name', 'viceunf'),
        'search_items'      => __('Buscar Categorías', 'viceunf'),
        'all_items'         => __('Todas las Categorías', 'viceunf'),
        'parent_item'       => __('Categoría Padre', 'viceunf'),
        'parent_item_colon' => __('Categoría Padre:', 'viceunf'),
        'edit_item'         => __('Editar Categoría', 'viceunf'),
        'update_item'       => __('Actualizar Categoría', 'viceunf'),
        'add_new_item'      => __('Añadir Nueva Categoría', 'viceunf'),
        'new_item_name'     => __('Nombre de la Nueva Categoría', 'viceunf'),
        'menu_name'         => __('Categorías', 'viceunf'),
    );
    $categoria_args = array(
        'hierarchical'      => true,
        'labels'            => $categoria_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'categoria-reglamento'),
        'show_in_rest'      => true,
    );
    register_taxonomy('categoria_reglamento', array('reglamento'), $categoria_args); // Asociada al CPT 'reglamento'.

    // --- FIN: NUEVO CÓDIGO PARA REGLAMENTOS ---
}
add_action('init', 'viceunf_register_post_types', 0);
