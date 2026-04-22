<?php

/**
 * Renderizador del bloque vpinunf/document-list.
 */

$post_type           = $attributes['postType'] ?? 'reglamento';
$taxonomy            = $attributes['taxonomy'] ?? 'categoria_reglamento';
$selected_categories = $attributes['selectedCategories'] ?? [];

if (! class_exists('VpinUnf\\Core\\Service\\DocumentService')) {
    echo '<div class="vpinunf-doc-notice"><p>' . esc_html__('Error: Plugin VpinUnf Core no está activo.', 'vpinunf') . '</p></div>';
    return;
}

$documentService = new \VpinUnf\Core\Service\DocumentService();

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
$wrapper_attributes = get_block_wrapper_attributes(['class' => 'vpinunf-document-list-block']);

// ─── Función recursiva para el árbol de acordeones ───────────────────────────
$render_tree_node = function ($node, $depth) use (&$render_tree_node, $documentService) {
    $total       = $documentService->count_all_documents($node);
    $color       = esc_attr($node['color']);
    $item_id     = (string) $node['term_id'];
    $level_class = $depth > 0 ? 'vpinunf-accordion--child' : 'vpinunf-accordion--root';
    $is_open     = $depth === 0;
?>
    <div
        class="vpinunf-accordion <?php echo esc_attr($level_class . ($is_open ? ' is-open' : '')); ?>"
        style="--acc-color: <?php echo $color; ?>;"
        data-depth="<?php echo intval($depth); ?>"
        data-category="<?php echo esc_attr($item_id); ?>">
        <button
            type="button"
            class="vpinunf-accordion__header"
            aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>">
            <span class="vpinunf-accordion__icon" aria-hidden="true">
                <i class="fas <?php echo $is_open ? 'fa-folder-open' : 'fa-folder'; ?>"></i>
            </span>
            <span class="vpinunf-accordion__title"><?php echo esc_html($node['term_name']); ?></span>
            <span class="vpinunf-accordion__badge"><?php echo intval($total); ?></span>
            <span class="vpinunf-accordion__chevron" aria-hidden="true">
                <i class="fas fa-chevron-down"></i>
            </span>
        </button>

        <div class="vpinunf-accordion__body">
            <div class="vpinunf-accordion__body-inner">
                <?php if (! empty($node['documents'])) : ?>
                    <ul class="vpinunf-accordion__list" role="list">
                        <?php foreach ($node['documents'] as $doc) :
                            $doc_title_lower = mb_strtolower($doc['title']);
                        ?>
                            <li
                                class="vpinunf-accordion__item"
                                data-title="<?php echo esc_attr($doc_title_lower); ?>">
                                <a href="<?php echo esc_url($doc['permalink']); ?>" class="vpinunf-accordion__link">
                                    <span class="vpinunf-accordion__link-icon" aria-hidden="true">
                                        <i class="fas fa-file-pdf"></i>
                                    </span>
                                    <span class="vpinunf-accordion__link-text"><?php echo esc_html($doc['title']); ?></span>
                                </a>
                                <?php if ($doc['has_file']) : ?>
                                    <a
                                        href="<?php echo esc_url($doc['file_url']); ?>"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="vpinunf-accordion__download"
                                        aria-label="<?php echo esc_attr(sprintf(__('Descargar: %s', 'vpinunf'), $doc['title'])); ?>">
                                        <i class="fas fa-arrow-down" aria-hidden="true"></i>
                                        <span><?php esc_html_e('Descargar', 'vpinunf'); ?></span>
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
        <div class="vpinunf-doc-empty-state">
            <i class="fas fa-folder-open" aria-hidden="true"></i>
            <p><?php esc_html_e('No se encontraron documentos.', 'vpinunf'); ?></p>
        </div>

    <?php elseif ($is_tree) :
        $filter_categories = $collect_filters($documents_data['data']);
    ?>
        <?php if (! empty($filter_categories)) : ?>
            <div class="vpinunf-doc-toolbar">

                <!-- Búsqueda -->
                <?php $search_id = wp_unique_id('vpinunf-search-'); ?>
                <div class="vpinunf-doc-search" role="search" aria-label="<?php esc_attr_e('Buscar documentos', 'vpinunf'); ?>">
                    <label for="<?php echo esc_attr($search_id); ?>" class="vpinunf-sr-only">
                        <?php esc_html_e('Buscar documento', 'vpinunf'); ?>
                    </label>
                    <span class="vpinunf-doc-search__icon" aria-hidden="true">
                        <i class="fas fa-search"></i>
                    </span>
                    <input
                        type="search"
                        id="<?php echo esc_attr($search_id); ?>"
                        class="vpinunf-doc-search__input"
                        placeholder="<?php esc_attr_e('Buscar documento...', 'vpinunf'); ?>"
                        autocomplete="off">
                    <button
                        type="button"
                        class="vpinunf-doc-search__clear"
                        aria-label="<?php esc_attr_e('Limpiar búsqueda', 'vpinunf'); ?>">
                        <i class="fas fa-times" aria-hidden="true"></i>
                    </button>
                </div>

                <!-- Filtros de categoría -->
                <nav class="vpinunf-doc-filters" aria-label="<?php esc_attr_e('Filtrar por categoría', 'vpinunf'); ?>">
                    <button type="button" class="vpinunf-doc-filter-btn is-active" data-filter="all">
                        <i class="fas fa-layer-group" aria-hidden="true"></i>
                        <?php esc_html_e('Todas', 'vpinunf'); ?>
                    </button>
                    <?php foreach ($filter_categories as $cat) : ?>
                        <button
                            type="button"
                            class="vpinunf-doc-filter-btn <?php echo $cat['depth'] > 0 ? 'vpinunf-doc-filter-btn--sub' : ''; ?>"
                            data-filter="<?php echo intval($cat['id']); ?>">
                            <?php echo esc_html($cat['name']); ?>
                        </button>
                    <?php endforeach; ?>
                </nav>
            </div>
        <?php endif; ?>

        <!-- Sin resultados de búsqueda -->
        <div class="vpinunf-doc-no-results" role="status" aria-live="polite">
            <i class="fas fa-search-minus" aria-hidden="true"></i>
            <p><?php esc_html_e('Sin resultados para esa búsqueda.', 'vpinunf'); ?></p>
        </div>

        <!-- Árbol de acordeones -->
        <div class="vpinunf-doc-tree">
            <?php foreach ($documents_data['data'] as $node) : ?>
                <?php $render_tree_node($node, 0); ?>
            <?php endforeach; ?>
        </div>

    <?php else : ?>
        <!-- Vista plana -->
        <ul class="vpinunf-accordion__list vpinunf-accordion__list--flat" role="list">
            <?php foreach ($documents_data['data'] as $doc) : ?>
                <li class="vpinunf-accordion__item">
                    <a href="<?php echo esc_url($doc['permalink']); ?>" class="vpinunf-accordion__link">
                        <span class="vpinunf-accordion__link-icon" aria-hidden="true"><i class="fas fa-file-alt"></i></span>
                        <span class="vpinunf-accordion__link-text"><?php echo esc_html($doc['title']); ?></span>
                    </a>
                    <?php if ($doc['has_file']) : ?>
                        <a href="<?php echo esc_url($doc['file_url']); ?>" target="_blank" rel="noopener noreferrer"
                            class="vpinunf-accordion__download" aria-label="<?php esc_attr_e('Descargar', 'vpinunf'); ?>">
                            <i class="fas fa-arrow-down" aria-hidden="true"></i>
                            <span><?php esc_html_e('Descargar', 'vpinunf'); ?></span>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>