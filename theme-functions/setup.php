<?php
// Salir si se accede directamente.
if (! defined('ABSPATH')) exit;

/**
 * Configuración inicial del tema.
 */
function viceunf_theme_setup()
{
    load_child_theme_textdomain('viceunf');
}
add_action('after_setup_theme', 'viceunf_theme_setup');

/**
 * Importa las opciones del tema padre al activar el tema hijo.
 */
function viceunf_parent_theme_options()
{
    $softme_mods = get_option('theme_mods_softme');
    if (! empty($softme_mods)) {
        foreach ($softme_mods as $softme_mod_k => $softme_mod_v) {
            set_theme_mod($softme_mod_k, $softme_mod_v);
        }
    }
}
add_action('after_switch_theme', 'viceunf_parent_theme_options');

/**
 * Registra la barra lateral para la sección de eventos.
 */
function viceunf_register_sidebars()
{
    register_sidebar(array(
        'name'          => __('Barra Lateral de Eventos', 'viceunf'),
        'id'            => 'events-sidebar',
        'description'   => __('Widgets que aparecen en la sección de eventos de la página de inicio.', 'viceunf'),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'viceunf_register_sidebars');