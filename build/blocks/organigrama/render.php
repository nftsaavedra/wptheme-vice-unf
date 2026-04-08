<?php
/**
 * Renderizado en Frontend Dinámico del Organigrama.
 * Consumiendo Servicio de Capa Data del plugin vpinunf-core.
 */

declare(strict_types=1);

if (! class_exists('\VpinUnf\Core\Service\DependenciaService')) {
    echo '<p>Error: El motor de datos VpinUnf Core no está activado.</p>';
    return;
}

$parent_id = isset($attributes['parentId']) ? (int) $attributes['parentId'] : 0;
$service   = new \VpinUnf\Core\Service\DependenciaService();
$tree      = $service->get_dependencia_tree($parent_id);

$wrapper_attributes = get_block_wrapper_attributes(['class' => 'viceunf-organigrama']);

/**
 * Función recursiva para dibujar el HTML del árbol.
 */
if (! function_exists('viceunf_render_org_node')) {
    function viceunf_render_org_node(array $node) {
        $html = '<li class="org-node-item">';
        $html .= '<div class="org-card">';
        
        if (!empty($node['title'])) {
            $html .= '<h3 class="org-title">' . esc_html($node['title']) . '</h3>';
        }
        
        $has_meta = !empty($node['siglas']) || !empty($node['autoridad']);
        
        if ($has_meta) {
            $html .= '<div class="org-meta">';
            if (!empty($node['siglas'])) {
                $html .= '<span class="org-badge">' . esc_html($node['siglas']) . '</span>';
            }
            if (!empty($node['autoridad'])) {
                $html .= '<p class="org-autoridad"><strong>Jefatura:</strong> ' . esc_html($node['autoridad']) . '</p>';
            }
            $html .= '</div>';
        }
        
        if (!empty($node['permalink']) && $node['id'] > 0) {
            $html .= '<a href="' . esc_url($node['permalink']) . '" class="org-link">Ver Más</a>';
        }
        
        $html .= '</div>';
        
        if (!empty($node['children'])) {
            $html .= '<ul class="org-children">';
            foreach ($node['children'] as $child) {
                $html .= viceunf_render_org_node($child);
            }
            $html .= '</ul>';
        }
        
        $html .= '</li>';
        return $html;
    }
}

?>

<div <?php echo $wrapper_attributes; ?>>
    <?php if (empty($tree)) : ?>
        <p class="dt-text-center">No se encontraron dependencias para renderizar.</p>
    <?php else : ?>
        <div class="org-chart-wrapper">
            <ul class="org-root">
                <?php echo viceunf_render_org_node($tree); ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
