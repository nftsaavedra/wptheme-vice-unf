<?php

/**
 * Renderizador del bloque viceunf/document-list.
 */

$post_type           = $attributes['postType'] ?? 'reglamento';
$taxonomy            = $attributes['taxonomy'] ?? 'categoria_reglamento';
$selected_categories = $attributes['selectedCategories'] ?? [];

if (! class_exists('ViceUnf\\Core\\Service\\DocumentService')) {
    echo '<div class="viceunf-doc-notice"><p>' . esc_html__('Error: Plugin ViceUnf Core no está activo.', 'viceunf') . '</p></div>';
    return;
}

$documentService = new \ViceUnf\Core\Service\DocumentService();

$categoria_slugs = [];
if (! empty($selected_categories) && is_array($selected_categories)) {
    foreach ($selected_categories as $term_id) {
        $term = get_term($term_id, $taxonomy);
        if (! is_wp_error($term) && $term) {
            $categoria_slugs[] = $term->slug;
        }
    }
}

if (empty($categoria_slugs)) {
    $documents_data = $documentService->get_documents_tree($post_type, $taxonomy);
} else {
    $documents_data = $documentService->get_documents($post_type, $taxonomy, $categoria_slugs);
}

$is_tree       = ! empty($documents_data['is_tree']);
$wrapper_attributes = get_block_wrapper_attributes(['class' => 'viceunf-document-list-block']);

// ─── Función recursiva para el árbol de acordeones ───────────────────────────
$render_tree_node = function ($node, $depth) use (&$render_tree_node, $documentService) {
    $total       = $documentService->count_all_documents($node);
    $color       = esc_attr($node['color']);
    $item_id     = (string) $node['term_id'];
    $level_class = $depth > 0 ? 'viceunf-accordion--child' : 'viceunf-accordion--root';
    $is_open     = $depth === 0;
?>
    <div
        class="viceunf-accordion <?php echo esc_attr($level_class . ($is_open ? ' is-open' : '')); ?>"
        style="--acc-color: <?php echo $color; ?>;"
        data-depth="<?php echo intval($depth); ?>"
        data-category="<?php echo esc_attr($item_id); ?>">
        <button
            type="button"
            class="viceunf-accordion__header"
            aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>">
            <span class="viceunf-accordion__icon" aria-hidden="true">
                <i class="fas <?php echo $is_open ? 'fa-folder-open' : 'fa-folder'; ?>"></i>
            </span>
            <span class="viceunf-accordion__title"><?php echo esc_html($node['term_name']); ?></span>
            <span class="viceunf-accordion__badge"><?php echo intval($total); ?></span>
            <span class="viceunf-accordion__chevron" aria-hidden="true">
                <i class="fas fa-chevron-down"></i>
            </span>
        </button>

        <div class="viceunf-accordion__body">
            <div class="viceunf-accordion__body-inner">
                <?php if (! empty($node['documents'])) : ?>
                    <ul class="viceunf-accordion__list" role="list">
                        <?php foreach ($node['documents'] as $doc) :
                            $doc_title_lower = mb_strtolower($doc['title']);
                        ?>
                            <li
                                class="viceunf-accordion__item"
                                data-title="<?php echo esc_attr($doc_title_lower); ?>">
                                <a href="<?php echo esc_url($doc['permalink']); ?>" class="viceunf-accordion__link">
                                    <span class="viceunf-accordion__link-icon" aria-hidden="true">
                                        <i class="fas fa-file-pdf"></i>
                                    </span>
                                    <span class="viceunf-accordion__link-text"><?php echo esc_html($doc['title']); ?></span>
                                </a>
                                <?php if ($doc['has_file']) : ?>
                                    <a
                                        href="<?php echo esc_url($doc['file_url']); ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="viceunf-accordion__download"
                                        aria-label="<?php echo esc_attr(sprintf(__('Descargar: %s', 'viceunf'), $doc['title'])); ?>">
                                        <i class="fas fa-arrow-down" aria-hidden="true"></i>
                                        <span><?php esc_html_e('Descargar', 'viceunf'); ?></span>
                                    </a>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php
                if (! empty($node['children'])) :
                    foreach ($node['children'] as $child) :
                        $render_tree_node($child, $depth + 1);
                    endforeach;
                endif;
                ?>
            </div>
        </div>
    </div>
<?php
};

$collect_filters = function ($nodes, $depth = 0) use (&$collect_filters) {
    $cats = [];
    foreach ($nodes as $node) {
        $cats[] = ['id' => $node['term_id'], 'name' => $node['term_name'], 'depth' => $depth];
        if (! empty($node['children']) && $depth < 1) {
            $cats = array_merge($cats, $collect_filters($node['children'], $depth + 1));
        }
    }
    return $cats;
};
?>
<div <?php echo $wrapper_attributes; ?>>

    <?php if (empty($documents_data['data'])) : ?>
        <div class="viceunf-doc-empty-state">
            <i class="fas fa-folder-open" aria-hidden="true"></i>
            <p><?php esc_html_e('No se encontraron documentos.', 'viceunf'); ?></p>
        </div>

    <?php elseif ($is_tree) :
        $filter_categories = $collect_filters($documents_data['data']);
    ?>
        <?php if (! empty($filter_categories)) : ?>
            <div class="viceunf-doc-toolbar">

                <!-- Búsqueda -->
                <?php $search_id = wp_unique_id('viceunf-search-'); ?>
                <div class="viceunf-doc-search" role="search" aria-label="<?php esc_attr_e('Buscar documentos', 'viceunf'); ?>">
                    <label for="<?php echo esc_attr($search_id); ?>" class="viceunf-sr-only">
                        <?php esc_html_e('Buscar documento', 'viceunf'); ?>
                    </label>
                    <span class="viceunf-doc-search__icon" aria-hidden="true">
                        <i class="fas fa-search"></i>
                    </span>
                    <input
                        type="search"
                        id="<?php echo esc_attr($search_id); ?>"
                        class="viceunf-doc-search__input"
                        placeholder="<?php esc_attr_e('Buscar documento...', 'viceunf'); ?>"
                        autocomplete="off">
                    <button
                        type="button"
                        class="viceunf-doc-search__clear"
                        aria-label="<?php esc_attr_e('Limpiar búsqueda', 'viceunf'); ?>">
                        <i class="fas fa-times" aria-hidden="true"></i>
                    </button>
                </div>

                <!-- Filtros de categoría -->
                <nav class="viceunf-doc-filters" aria-label="<?php esc_attr_e('Filtrar por categoría', 'viceunf'); ?>">
                    <button type="button" class="viceunf-doc-filter-btn is-active" data-filter="all">
                        <i class="fas fa-layer-group" aria-hidden="true"></i>
                        <?php esc_html_e('Todas', 'viceunf'); ?>
                    </button>
                    <?php foreach ($filter_categories as $cat) : ?>
                        <button
                            type="button"
                            class="viceunf-doc-filter-btn <?php echo $cat['depth'] > 0 ? 'viceunf-doc-filter-btn--sub' : ''; ?>"
                            data-filter="<?php echo intval($cat['id']); ?>">
                            <?php echo esc_html($cat['name']); ?>
                        </button>
                    <?php endforeach; ?>
                </nav>
            </div>
        <?php endif; ?>

        <!-- Sin resultados de búsqueda -->
        <div class="viceunf-doc-no-results" role="status" aria-live="polite">
            <i class="fas fa-search-minus" aria-hidden="true"></i>
            <p><?php esc_html_e('Sin resultados para esa búsqueda.', 'viceunf'); ?></p>
        </div>

        <!-- Árbol de acordeones -->
        <div class="viceunf-doc-tree">
            <?php foreach ($documents_data['data'] as $node) : ?>
                <?php $render_tree_node($node, 0); ?>
            <?php endforeach; ?>
        </div>

    <?php else : ?>
        <!-- Vista plana -->
        <ul class="viceunf-accordion__list viceunf-accordion__list--flat" role="list">
            <?php foreach ($documents_data['data'] as $doc) : ?>
                <li class="viceunf-accordion__item">
                    <a href="<?php echo esc_url($doc['permalink']); ?>" class="viceunf-accordion__link">
                        <span class="viceunf-accordion__link-icon" aria-hidden="true"><i class="fas fa-file-alt"></i></span>
                        <span class="viceunf-accordion__link-text"><?php echo esc_html($doc['title']); ?></span>
                    </a>
                    <?php if ($doc['has_file']) : ?>
                        <a href="<?php echo esc_url($doc['file_url']); ?>" target="_blank" rel="noopener noreferrer"
                            class="viceunf-accordion__download" aria-label="<?php esc_attr_e('Descargar', 'viceunf'); ?>">
                            <i class="fas fa-arrow-down" aria-hidden="true"></i>
                            <span><?php esc_html_e('Descargar', 'viceunf'); ?></span>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>