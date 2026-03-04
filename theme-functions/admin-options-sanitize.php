<?php
// Salir si se accede directamente.
if (!defined('ABSPATH')) exit;

/**
 * =================================================================
 * Sanitización de Opciones del Tema
 * =================================================================
 * Responsabilidad: Centralizar toda la lógica de validación y limpieza
 * de los datos enviados desde la página de opciones.
 */

function viceunf_sanitize_all_options($input)
{
  $current_options = get_option('viceunf_theme_options', []);
  $sanitized_input = [];

  // Lista de todos los campos que son checkboxes.
  $checkbox_keys = [
    'investigacion_section_enabled',
    'about_section_enabled',
    'eventos_section_enabled',
    'viceunf_noticias_section_enabled',
    'socios_section_enabled',
    'production_section_enabled',
  ];

  foreach ($checkbox_keys as $key) {
    $sanitized_input[$key] = (isset($input[$key]) && $input[$key] == 1) ? 1 : 0;
  }

  // Reglas para el resto de los campos de texto/textarea.
  $other_fields_rules = [
    // Campos de "Sobre Nosotros"
    'about_main_image'  => 'absint', // Guardamos el ID del adjunto como entero
    'about_video_url'   => 'esc_url_raw',
    'about_subtitle'    => 'sanitize_text_field',
    'about_title'       => 'wp_kses_post',
    'about_person_name' => 'sanitize_text_field',
    'about_description' => 'sanitize_textarea_field',
    // Campos existentes
    'eventos_subtitulo'     => 'sanitize_text_field',
    'eventos_titulo'        => 'wp_kses_post',
    'eventos_descripcion'   => 'sanitize_textarea_field',
    'viceunf_noticias_subtitulo'    => 'sanitize_text_field',
    'viceunf_noticias_titulo'       => 'wp_kses_post',
    'viceunf_noticias_descripcion'  => 'sanitize_textarea_field',
    'viceunf_socios_titulo'         => 'sanitize_text_field',
    // Campos de "Producción Científica"
    'production_subtitle'    => 'sanitize_text_field',
    'production_title'       => 'wp_kses_post',
    'production_description' => 'sanitize_textarea_field',
  ];

  foreach ($other_fields_rules as $key => $sanitize_callback) {
    if (isset($input[$key])) {
      $sanitized_input[$key] = call_user_func($sanitize_callback, $input[$key]);
    }
  }

  // Sanitización especial para array de strings
  if (isset($input['single_document_post_types']) && is_array($input['single_document_post_types'])) {
    $sanitized_input['single_document_post_types'] = array_map('sanitize_text_field', $input['single_document_post_types']);
  } elseif (isset($input['single_document_post_types']) && empty($input['single_document_post_types'])) {
    $sanitized_input['single_document_post_types'] = [];
  }

  // Sanitización para los items de investigación (fijos)
  for ($i = 1; $i <= 4; $i++) {
    if (isset($input["item_{$i}_page_id"])) $sanitized_input["item_{$i}_page_id"] = absint($input["item_{$i}_page_id"]);
    if (isset($input["item_{$i}_icon"])) $sanitized_input["item_{$i}_icon"] = sanitize_text_field($input["item_{$i}_icon"]);
    if (isset($input["item_{$i}_custom_title"])) $sanitized_input["item_{$i}_custom_title"] = sanitize_text_field($input["item_{$i}_custom_title"]);
    if (isset($input["item_{$i}_custom_desc"])) $sanitized_input["item_{$i}_custom_desc"] = sanitize_textarea_field($input["item_{$i}_custom_desc"]);
  }

  // Sanitización para el repetidor de "Sobre Nosotros"
  if (!empty($input['about_items']) && is_array($input['about_items'])) {
    $sanitized_items = [];
    // Reindexamos para asegurar claves consecutivas
    $reindexed_items = array_values($input['about_items']);

    foreach ($reindexed_items as $item) {
      // Solo procesamos el item si tiene una página y un icono asignado
      if (!empty($item['page_id']) && !empty($item['icon'])) {
        $sanitized_item = [
          'page_id' => absint($item['page_id']),
          'icon'    => sanitize_text_field($item['icon']),
        ];
        $sanitized_items[] = $sanitized_item;
      }
    }
    $sanitized_input['about_items'] = $sanitized_items;
  } else {
    $sanitized_input['about_items'] = [];
  }

  // Sanitización para el repetidor de "Producción Científica"
  if (!empty($input['production_items']) && is_array($input['production_items'])) {
    $sanitized_p_items = [];
    $reindexed_p_items = array_values($input['production_items']);

    foreach ($reindexed_p_items as $item) {
      if (!empty($item['title'])) {
        $sanitized_item = [
          'title'       => sanitize_text_field($item['title']),
          'description' => sanitize_textarea_field(isset($item['description']) ? $item['description'] : ''),
          'icon'        => sanitize_text_field(isset($item['icon']) ? $item['icon'] : ''),
          'image_id'    => absint(isset($item['image_id']) ? $item['image_id'] : 0),
          'image_url'   => esc_url_raw(isset($item['image_url']) ? $item['image_url'] : ''),
          'url'         => esc_url_raw(isset($item['url']) ? $item['url'] : ''),
        ];
        $sanitized_p_items[] = $sanitized_item;
      }
    }
    $sanitized_input['production_items'] = $sanitized_p_items;
  } else {
    $sanitized_input['production_items'] = [];
  }

  // Fusionamos los datos nuevos y sanitizados con las opciones existentes.
  return array_merge($current_options, $sanitized_input);
}
