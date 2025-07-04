<?php
// Salir si se accede directamente.
if (! defined('ABSPATH')) exit;

/**
 * Modifica el Personalizador de WordPress para adaptarlo al tema hijo.
 * Su única responsabilidad es alterar las opciones del tema padre.
 *
 * @param WP_Customize_Manager $wp_customize Objeto del personalizador de WordPress.
 */
function viceunf_modify_parent_theme_customizer($wp_customize)
{

    // Elimina la sección de promoción "Upgrade to Softme Pro" del tema padre.
    // Se ejecuta con prioridad 20 para asegurar que se cargue DESPUÉS del tema padre.
    $wp_customize->remove_section('softme');
}
add_action('customize_register', 'viceunf_modify_parent_theme_customizer', 20);
