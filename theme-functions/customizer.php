<?php
// Salir si se accede directamente.
if (! defined('ABSPATH')) exit;

/**
 * Registra y configura TODAS las opciones del personalizador para el tema ViceUnf.
 *
 * @param WP_Customize_Manager $wp_customize Objeto del personalizador de WordPress.
 */
function viceunf_customize_register($wp_customize)
{

    // =================================================================
    // 1. PANEL PRINCIPAL PARA LA PÁGINA DE INICIO
    // =================================================================
    // Esto agrupará todas las secciones en un solo lugar.
    $wp_customize->add_panel('viceunf_frontpage_panel', array(
        'title'      => __('Página de Inicio (ViceUnf)', 'viceunf'),
        'priority'   => 30,
        'description' => __('Gestiona el contenido de las secciones de la página de inicio.', 'viceunf'),
    ));

    // =================================================================
    // 2. NUEVA SECCIÓN: INVESTIGACIÓN
    // =================================================================
    $wp_customize->add_section('investigacion_section_settings', array(
        'title'      => __('Sección: Investigación', 'viceunf'),
        'panel'      => 'viceunf_frontpage_panel',
        'priority'   => 10,
    ));

    $wp_customize->add_setting('investigacion_section_enabled', array('default' => true, 'sanitize_callback' => 'wp_validate_boolean'));
    $wp_customize->add_control('investigacion_section_enabled_control', array('label' => __('Habilitar esta sección', 'viceunf'), 'section' => 'investigacion_section_settings', 'type' => 'checkbox'));

    for ($i = 1; $i <= 4; $i++) {
        $wp_customize->add_setting("investigacion_item_{$i}_page_id", array('default' => 0, 'sanitize_callback' => 'absint'));
        $wp_customize->add_control("investigacion_item_{$i}_page_id_control", array(
            'label'       => sprintf(__('Ítem %d: Página Relacionada', 'viceunf'), $i),
            'section'     => 'investigacion_section_settings',
            'type' => 'dropdown-pages',
        ));
        $wp_customize->add_setting("investigacion_item_{$i}_icon", array('default' => 'fas fa-flask', 'sanitize_callback' => 'sanitize_text_field'));
        $wp_customize->add_control("investigacion_item_{$i}_icon_control", array('label' => sprintf(__('Ítem %d: Icono', 'viceunf'), $i), 'section' => 'investigacion_section_settings', 'type' => 'text'));
        $wp_customize->add_setting("investigacion_item_{$i}_custom_title", array('default' => '', 'sanitize_callback' => 'sanitize_text_field'));
        $wp_customize->add_control("investigacion_item_{$i}_custom_title_control", array('label' => sprintf(__('Ítem %d: Título (Opcional)', 'viceunf'), $i), 'section' => 'investigacion_section_settings', 'type' => 'text'));
        $wp_customize->add_setting("investigacion_item_{$i}_custom_desc", array('default' => '', 'sanitize_callback' => 'sanitize_textarea_field'));
        $wp_customize->add_control("investigacion_item_{$i}_custom_desc_control", array('label' => sprintf(__('Ítem %d: Descripción (Opcional)', 'viceunf'), $i), 'section' => 'investigacion_section_settings', 'type' => 'textarea'));
    }

    // =================================================================
    // 3. TUS SECCIONES ORIGINALES (INTACTAS Y DENTRO DEL PANEL)
    // =================================================================

    // --- Sección de Eventos ---
    $wp_customize->add_section('eventos_seccion_settings', array(
        'title'      => __('Sección: Eventos', 'viceunf'),
        'panel'      => 'viceunf_frontpage_panel', // Asignado al panel
        'priority'   => 20,
    ));
    $wp_customize->add_setting('eventos_subtitulo');
    $wp_customize->add_control('eventos_subtitulo_control', array(
        'label'      => __('Subtítulo', 'viceunf'),
        'section'    => 'eventos_seccion_settings',
        'type'       => 'text',
    ));
    $wp_customize->add_setting('eventos_titulo');
    $wp_customize->add_control('eventos_titulo_control', array(
        'label'      => __('Título Principal', 'viceunf'),
        'description' => 'Usa &lt;span&gt; para resaltar.',
        'section'    => 'eventos_seccion_settings',
        'type'       => 'text',
    ));
    $wp_customize->add_setting('eventos_descripcion');
    $wp_customize->add_control('eventos_descripcion_control', array(
        'label'      => __('Descripción de la sección', 'viceunf'),
        'section'    => 'eventos_seccion_settings',
        'type'       => 'textarea',
    ));

    // --- Sección de Noticias ---
    $wp_customize->add_section('noticias_seccion_settings', array(
        'title'      => __('Sección: Noticias (Blog)', 'viceunf'),
        'panel'      => 'viceunf_frontpage_panel', // Asignado al panel
        'priority'   => 30,
    ));
    $wp_customize->add_setting('noticias_subtitulo', array(
        'default'           => "What’s Happening",
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('noticias_subtitulo_control', array(
        'label'      => __('Subtítulo', 'viceunf'),
        'section'    => 'noticias_seccion_settings',
        'type'       => 'text',
    ));
    $wp_customize->add_setting('noticias_titulo', array(
        'default'           => "Latest News & Articles from the <br><span>Posts</span>",
        'sanitize_callback' => 'wp_kses_post',
    ));
    $wp_customize->add_control('noticias_titulo_control', array(
        'label'      => __('Título Principal', 'viceunf'),
        'description' => 'Usa &lt;span&gt; para resaltar.',
        'section'    => 'noticias_seccion_settings',
        'type'       => 'text',
    ));
    $wp_customize->add_setting('noticias_descripcion', array(
        'default'           => "Amet consectur...",
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('noticias_descripcion_control', array(
        'label'      => __('Descripción de la sección', 'viceunf'),
        'section'    => 'noticias_seccion_settings',
        'type'       => 'textarea',
    ));

    // --- Sección Socios ---
    $wp_customize->add_section('socios_seccion_settings', array(
        'title'      => __('Sección de Socios', 'viceunf'),
        'panel'      => 'viceunf_frontpage_panel', // Asignado al panel
        'priority'   => 40,
    ));
    $wp_customize->add_setting('socios_titulo', array(
        'default'           => 'Socios Académicos',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('socios_titulo_control', array(
        'label'      => __('Título de la Sección', 'viceunf'),
        'section'    => 'socios_seccion_settings',
        'type'       => 'text',
    ));

    // =================================================================
    // 4. ELIMINAR SECCIÓN DEL TEMA PADRE
    // =================================================================
    // Esto previene que la sección de "Upgrade" del tema padre aparezca.
    $wp_customize->remove_section('softme');
}
add_action('customize_register', 'viceunf_customize_register', 20);
