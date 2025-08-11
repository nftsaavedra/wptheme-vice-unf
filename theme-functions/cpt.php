<?php
// Salir si se accede directamente.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Custom Post Types: Slider y Evento.
 */
function viceunf_register_post_types() {
    // --- CPT: Slider ---
    $slider_labels = array('name' => 'Sliders', 'singular_name' => 'Slider', 'menu_name' => 'Sliders', 'add_new_item' => 'Añadir Nuevo Slider', 'edit_item' => 'Editar Slider', 'featured_image' => 'Imagen de Fondo', 'set_featured_image' => 'Establecer Imagen de Fondo', 'remove_featured_image' => 'Quitar Imagen de Fondo', 'use_featured_image' => 'Usar como Imagen de Fondo');
    $slider_args = array('label' => 'Slider', 'description' => 'Contenido para el slider principal', 'labels' => $slider_labels, 'supports' => array('title', 'thumbnail', 'revisions'), 'public' => true, 'show_ui' => true, 'show_in_menu' => true, 'menu_position' => 5, 'menu_icon' => 'dashicons-images-alt2', 'exclude_from_search' => true, 'publicly_queryable' => true, 'capability_type' => 'post', 'show_in_rest' => true);
    register_post_type('slider', $slider_args);

    // --- CPT: Evento ---
    $evento_labels = array('name' => 'Eventos', 'singular_name' => 'Evento', 'menu_name' => 'Eventos', 'all_items' => 'Todos los Eventos', 'add_new_item' => 'Añadir Nuevo Evento', 'edit_item' => 'Editar Evento');
    $evento_args = array('label' => 'Evento', 'description' => 'Contenido para la sección de eventos', 'labels' => $evento_labels, 'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions'), 'public' => true, 'show_ui' => true, 'show_in_menu' => true, 'menu_position' => 6, 'menu_icon' => 'dashicons-calendar-alt', 'publicly_queryable' => true, 'capability_type' => 'post', 'has_archive' => true, 'show_in_rest' => true);
    register_post_type('evento', $evento_args);



    // --- AÑADIR NUEVO CPT: Socio ---
    $socio_labels = array(
        'name'               => _x( 'Socios', 'Post Type General Name', 'viceunf' ),
        'singular_name'      => _x( 'Socio', 'Post Type Singular Name', 'viceunf' ),
        'menu_name'          => __( 'Socios', 'viceunf' ),
        'name_admin_bar'     => __( 'Socio', 'viceunf' ),
        'add_new_item'       => __( 'Añadir Nuevo Socio', 'viceunf' ),
        'add_new'            => __( 'Añadir Nuevo', 'viceunf' ),
        'new_item'           => __( 'Nuevo Socio', 'viceunf' ),
        'edit_item'          => __( 'Editar Socio', 'viceunf' ),
        'view_item'          => __( 'Ver Socio', 'viceunf' ),
        'all_items'          => __( 'Todos los Socios', 'viceunf' ),
        'search_items'       => __( 'Buscar Socios', 'viceunf' ),
        'not_found'          => __( 'No se encontraron socios.', 'viceunf' ),
        'not_found_in_trash' => __( 'No se encontraron socios en la papelera.', 'viceunf' ),
        'featured_image'     => __( 'Logo del Socio', 'viceunf' ),
        'set_featured_image' => __( 'Establecer Logo del Socio', 'viceunf' ),
        'remove_featured_image' => __( 'Quitar Logo del Socio', 'viceunf' ),
    );
    $socio_args = array(
        'label'                 => __( 'Socio', 'viceunf' ),
        'description'           => __( 'Logos de Socios Académicos', 'viceunf' ),
        'labels'                => $socio_labels,
        'supports'              => array( 'title', 'thumbnail', 'page-attributes' ), // Soporte para título, logo (thumbnail) y orden.
        'public'                => false, // No necesitan tener página propia.
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 7,
        'menu_icon'             => 'dashicons-businessperson',
        'capability_type'       => 'post',
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
    );
    register_post_type( 'socio', $socio_args );
}
add_action( 'init', 'viceunf_register_post_types', 0 );