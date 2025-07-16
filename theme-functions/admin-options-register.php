<?php
// Salir si se accede directamente.
if (!defined('ABSPATH')) exit;

/**
 * =================================================================
 * Registro de Opciones del Tema
 * =================================================================
 * Responsabilidad: Registrar todos los settings, secciones y campos
 * para la página de opciones del tema.
 */

// 2. Registra todas las opciones, secciones y campos usando la Settings API.
add_action('admin_init', function () {
  register_setting('viceunf_options_group', 'viceunf_theme_options', 'viceunf_sanitize_all_options');

  // === PESTAÑA: PÁGINA DE INICIO ===

  // SECCIÓN 1: INVESTIGACIÓN
  add_settings_section('viceunf_investigacion_section', '1. Sección: Investigación', null, 'viceunf_theme_options');
  add_settings_field('investigacion_section_enabled', 'Habilitar Sección', 'viceunf_render_checkbox_field', 'viceunf_theme_options', 'viceunf_investigacion_section', ['id' => 'investigacion_section_enabled', 'label' => 'Mostrar esta sección en la página de inicio.']);
  add_settings_field('investigacion_items_grid', 'Items de Investigación', 'viceunf_render_investigacion_grid_callback', 'viceunf_theme_options', 'viceunf_investigacion_section');

  // SECCIÓN 2: SOBRE NOSOTROS
  add_settings_section('viceunf_about_section', '2. Sección: Sobre Nosotros', null, 'viceunf_theme_options');
  add_settings_field('about_section_enabled', 'Habilitar Sección', 'viceunf_render_checkbox_field', 'viceunf_theme_options', 'viceunf_about_section', ['id' => 'about_section_enabled', 'label' => 'Mostrar esta sección en la página de inicio.']);
  add_settings_field('about_main_image', 'Imagen Principal', 'viceunf_render_image_uploader_field', 'viceunf_theme_options', 'viceunf_about_section', ['id' => 'about_main_image']);
  add_settings_field('about_video_url', 'URL del Video', 'viceunf_render_text_field', 'viceunf_theme_options', 'viceunf_about_section', ['id' => 'about_video_url', 'description' => 'URL completa del video (ej. YouTube, Vimeo) para el botón de play.']);
  add_settings_field('about_subtitle', 'Subtítulo', 'viceunf_render_text_field', 'viceunf_theme_options', 'viceunf_about_section', ['id' => 'about_subtitle']);
  add_settings_field('about_title', 'Título', 'viceunf_render_text_field', 'viceunf_theme_options', 'viceunf_about_section', ['id' => 'about_title', 'description' => 'Puedes usar &lt;br&gt; para saltos de línea.']);
  add_settings_field('about_person_name', 'Nombre Destacado', 'viceunf_render_text_field', 'viceunf_theme_options', 'viceunf_about_section', ['id' => 'about_person_name', 'description' => 'Aparece dentro del título, envuelto en &lt;span&gt;.']);
  add_settings_field('about_description', 'Descripción', 'viceunf_render_textarea_field', 'viceunf_theme_options', 'viceunf_about_section', ['id' => 'about_description']);
  add_settings_field('about_items', 'Items de la Sección', 'viceunf_render_about_repeater_field', 'viceunf_theme_options', 'viceunf_about_section');

  // SECCIÓN 3: EVENTOS
  add_settings_section('viceunf_eventos_section', '3. Sección: Eventos', null, 'viceunf_theme_options');
  add_settings_field('eventos_section_enabled', 'Habilitar Sección', 'viceunf_render_checkbox_field', 'viceunf_theme_options', 'viceunf_eventos_section', ['id' => 'eventos_section_enabled', 'label' => 'Mostrar esta sección en la página de inicio.']);
  add_settings_field('eventos_subtitulo', 'Subtítulo', 'viceunf_render_text_field', 'viceunf_theme_options', 'viceunf_eventos_section', ['id' => 'eventos_subtitulo']);
  add_settings_field('eventos_titulo', 'Título', 'viceunf_render_text_field', 'viceunf_theme_options', 'viceunf_eventos_section', ['id' => 'eventos_titulo', 'description' => 'Usa &lt;span&gt; para resaltar.']);
  add_settings_field('eventos_descripcion', 'Descripción', 'viceunf_render_textarea_field', 'viceunf_theme_options', 'viceunf_eventos_section', ['id' => 'eventos_descripcion']);

  // SECCIÓN 4: NOTICIAS
  add_settings_section('viceunf_noticias_section', '4. Sección: Noticias', null, 'viceunf_theme_options');
  add_settings_field('noticias_section_enabled', 'Habilitar Sección', 'viceunf_render_checkbox_field', 'viceunf_theme_options', 'viceunf_noticias_section', ['id' => 'noticias_section_enabled', 'label' => 'Mostrar esta sección en la página de inicio.']);
  add_settings_field('noticias_subtitulo', 'Subtítulo', 'viceunf_render_text_field', 'viceunf_theme_options', 'viceunf_noticias_section', ['id' => 'noticias_subtitulo']);
  add_settings_field('noticias_titulo', 'Título', 'viceunf_render_text_field', 'viceunf_theme_options', 'viceunf_noticias_section', ['id' => 'noticias_titulo']);
  add_settings_field('noticias_descripcion', 'Descripción', 'viceunf_render_textarea_field', 'viceunf_theme_options', 'viceunf_noticias_section', ['id' => 'noticias_descripcion']);

  // SECCIÓN 5: SOCIOS
  add_settings_section('viceunf_socios_section', '5. Sección: Socios Académicos', null, 'viceunf_theme_options');
  add_settings_field('socios_section_enabled', 'Habilitar Sección', 'viceunf_render_checkbox_field', 'viceunf_theme_options', 'viceunf_socios_section', ['id' => 'socios_section_enabled', 'label' => 'Mostrar esta sección en la página de inicio.']);
  add_settings_field('socios_titulo', 'Título de la Sección', 'viceunf_render_text_field', 'viceunf_theme_options', 'viceunf_socios_section', ['id' => 'socios_titulo']);
});
