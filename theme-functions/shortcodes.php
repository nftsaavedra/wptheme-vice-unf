<?php

/**
 * Archivo de shortcodes del tema ViceUnf.
 *
 * El shortcode `listar_reglamentos` fue eliminado en favor del bloque
 * Gutenberg nativo `document-list` (src/blocks/document-list).
 *
 * Si alguna página todavía usa [listar_reglamentos], mostrará un aviso
 * de migración descriptivo en lugar de contenido roto.
 *
 * @package ViceUnf
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Stub de compatibilidad: muestra un aviso de migración si el shortcode
 * obsoleto todavía se encuentra en contenido publicado.
 *
 * @deprecated Usar el bloque Gutenberg `document-list` en su lugar.
 */
add_shortcode('listar_reglamentos', function (): string {
    // Solo visible para administradores en el frontend
    if (! current_user_can('manage_options')) {
        return '';
    }

    return '<div style="border:2px solid #d63638;background:#fff5f5;padding:16px 20px;border-radius:4px;font-family:sans-serif;font-size:13px;">'
        . '<strong>⚠️ Shortcode obsoleto detectado: <code>[listar_reglamentos]</code></strong><br>'
        . 'Este shortcode fue eliminado. Reemplázalo por el bloque <strong>"Lista de Documentos"</strong> disponible en el editor de bloques de Gutenberg.'
        . '</div>';
});
