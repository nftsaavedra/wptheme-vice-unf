<?php
// Salir si se accede directamente.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Registra todas las opciones del personalizador para el tema.
 * Esta función asume que la clase necesaria (class-customize.php)
 * ya ha sido cargada por el functions.php principal.
 */
function viceunf_customize_register( $wp_customize ) {
    
    // --- Sección de Eventos ---
    $wp_customize->add_section( 'eventos_seccion_settings', array(
        'title'      => __( 'Sección de Eventos', 'viceunf' ),
        'priority'   => 30,
    ));
    $wp_customize->add_setting( 'eventos_subtitulo' );
    $wp_customize->add_control( 'eventos_subtitulo_control', array(
        'label'      => __( 'Subtítulo', 'viceunf' ),
        'section'    => 'eventos_seccion_settings',
        'type'       => 'text',
    ));
    $wp_customize->add_setting( 'eventos_titulo' );
    $wp_customize->add_control( 'eventos_titulo_control', array(
        'label'      => __( 'Título Principal', 'viceunf' ),
        'description'=> 'Usa &lt;span&gt; para resaltar.',
        'section'    => 'eventos_seccion_settings',
        'type'       => 'text',
    ));
    $wp_customize->add_setting( 'eventos_descripcion' );
    $wp_customize->add_control( 'eventos_descripcion_control', array(
        'label'      => __( 'Descripción de la sección', 'viceunf' ),
        'section'    => 'eventos_seccion_settings',
        'type'       => 'textarea',
    ));

    // --- Sección de Noticias ---
    $wp_customize->add_section( 'noticias_seccion_settings', array(
        'title'      => __( 'Sección de Noticias (Blog)', 'viceunf' ),
        'priority'   => 31,
    ));
    $wp_customize->add_setting( 'noticias_subtitulo', array(
        'default'           => "What’s Happening",
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control( 'noticias_subtitulo_control', array(
        'label'      => __( 'Subtítulo', 'viceunf' ),
        'section'    => 'noticias_seccion_settings',
        'type'       => 'text',
    ));
    $wp_customize->add_setting( 'noticias_titulo', array(
        'default'           => "Latest News & Articles from the <br><span>Posts</span>",
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control( 'noticias_titulo_control', array(
        'label'      => __( 'Título Principal', 'viceunf' ),
        'description'=> 'Usa &lt;span&gt; para resaltar.',
        'section'    => 'noticias_seccion_settings',
        'type'       => 'text',
    ));
    $wp_customize->add_setting( 'noticias_descripcion', array(
        'default'           => "Amet consectur...",
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control( 'noticias_descripcion_control', array(
        'label'      => __( 'Descripción de la sección', 'viceunf' ),
        'section'    => 'noticias_seccion_settings',
        'type'       => 'textarea',
    ));

    // --- Sección Socios ---
    $wp_customize->add_section( 'socios_seccion_settings', array(
        'title'      => __( 'Sección de Socios', 'viceunf' ),
        'priority'   => 32,
    ));
    $wp_customize->add_setting( 'socios_titulo', array(
        'default'           => 'Socios Académicos',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control( 'socios_titulo_control', array(
        'label'      => __( 'Título de la Sección', 'viceunf' ),
        'section'    => 'socios_seccion_settings',
        'type'       => 'text',
    ));
}
add_action( 'customize_register', 'viceunf_customize_register' );