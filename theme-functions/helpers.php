<?php
// Salir si se accede directamente.
if (! defined('ABSPATH')) exit;

/**
 * Convierte una URL de video en una URL de embed con autoplay silencioso.
 */
function get_autoplay_embed_url($url)
{
    if (empty($url)) return '';
    $embed_url = $url;
    if (preg_match('/(youtube\.com|youtu\.be)\/(watch\?v=|embed\/|v\/|)(.{11})/', $url, $matches)) {
        $embed_url = 'https://www.youtube.com/embed/' . $matches[3] . '?autoplay=1&mute=1&rel=0';
    } elseif (preg_match('/(vimeo\.com)\/(video\/)?([0-9]+)/', $url, $matches)) {
        $embed_url = 'https://player.vimeo.com/video/' . $matches[3] . '?autoplay=1&muted=1';
    }
    return $embed_url;
}

