<?php

/**
 * Renderizador del bloque viceunf/document-list.
 * Interactivity API (WP 6.5+).
 *
 * ARQUITECTURA:
 * - wp_interactivity_state()           → estado GLOBAL reactivo del store
 * - wp_interactivity_data_wp_context() → datos LOCALES por elemento del DOM
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
$first_item_id = $is_tree && ! empty($documents_data['data'][0]['term_id'])
    ? (string) $documents_data['data'][0]['term_id']
    : '';

// ─── Estado GLOBAL del store (accesible como state.X en view.js) ─────────────
wp_interactivity_state('viceunf/document-list', [
    'searchQuery'  => '',
    'activeFilter' => 'all',
    'openItems'    => $first_item_id ? array_values([$first_item_id]) : [],
    'visibleCount' => -1,
]);
// ─────────────────────────────────────────────────────────────────────────────

$wrapper_attributes = get_block_wrapper_attributes([
    'class'               => 'viceunf-document-list-block',
    'data-wp-interactive' => 'viceunf/document-list',
    // context vacío en el root para que el runtime inicialice correctamente
    'data-wp-context'     => '{}',
]);

// ─── Árbol de acordeones ─────────────────────────────────────────────────────
$render_tree_node = function ($node, $depth) use (&$render_tree_node, $documentService) {
    $total       = $documentService->count_all_documents($node);
    $color       = esc_attr($node['color']);
    $item_id     = (string) $node['term_id'];
    $level_class = $depth > 0 ? 'viceunf-accordion--child' : 'viceunf-accordion--root';
    // Contexto LOCAL: solo el dato que el state getter necesita de este elemento
    $item_ctx    = esc_attr(wp_json_encode(['itemId' => $item_id]));
?>
    <div
        class="viceunf-accordion <?php echo esc_attr($level_class); ?>"
        style="--acc-color: <?php echo $color; ?>;"
        data-depth="<?php echo intval($depth); ?>"
        data-category="<?php echo esc_attr($item_id); ?>"
        data-wp-context="<?php echo $item_ctx; ?>"
        data-wp-class--is-open="state.isOpen">
        <button
            type="button"
            class="viceunf-accordion__header"
            data-wp-on--click="actions.toggleAccordion"
            data-wp-bind--aria-expanded="state.isOpen">
            <span class="viceunf-accordion__icon" aria-hidden="true">
                <i class="fas"
                    data-wp-class--fa-folder-open="state.isOpen"
                    data-wp-class--fa-folder="!state.isOpen"></i>
            </span>
            <span class="viceunf-accordion__title"><?php echo esc_html($node['term_name']); ?></span>
            <span
                class="viceunf-accordion__badge"
                aria-label="<?php echo esc_attr(sprintf(__('%d documentos', 'viceunf'), $total)); ?>">
                <?php echo intval($total); ?>
            </span>
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
                            $doc_ctx         = esc_attr(wp_json_encode(['docTitle' => $doc_title_lower]));
                        ?>
                            <li
                                class="viceunf-accordion__item"
                                data-title="<?php echo esc_attr($doc_title_lower); ?>"
                                data-wp-context="<?php echo $doc_ctx; ?>"
                                data-wp-class--is-hidden="state.isDocHidden">
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

// ─── Filtros de categoría ────────────────────────────────────────────────────
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

                <!-- Campo de búsqueda -->
                <?php $search_id = wp_unique_id('viceunf-doc-search-'); ?>
                <div class="viceunf-doc-search" role="search" aria-label="<?php esc_attr_e('Buscar documentos', 'viceunf'); ?>">
                    <label for="<?php echo esc_attr($search_id); ?>" class="sr-only">
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
                        autocomplete="off"
                        data-wp-on--input="actions.onSearch">
                    <button
                        type="button"
                        class="viceunf-doc-search__clear"
                        aria-label="<?php esc_attr_e('Limpiar búsqueda', 'viceunf'); ?>"
                        data-wp-class--is-visible="state.hasQuery"
                        data-wp-on--click="actions.clearSearch">
                        <i class="fas fa-times" aria-hidden="true"></i>
                    </button>
                </div>

                <!-- Filtros de categoría -->
                <nav class="viceunf-doc-filters" aria-label="<?php esc_attr_e('Filtrar por categoría', 'viceunf'); ?>">
                    <button
                        type="button"
                        class="viceunf-doc-filter-btn"
                        data-wp-context="<?php echo esc_attr(wp_json_encode(['filterId' => 'all'])); ?>"
                        data-wp-on--click="actions.setFilter"
                        data-wp-class--is-active="state.isFilterActive">
                        <i class="fas fa-layer-group" aria-hidden="true"></i>
                        <?php esc_html_e('Todas', 'viceunf'); ?>
                    </button>
                    <?php foreach ($filter_categories as $cat) :
                        $cat_ctx = esc_attr(wp_json_encode(['filterId' => (string) $cat['id']]));
                    ?>
                        <button
                            type="button"
                            class="viceunf-doc-filter-btn <?php echo $cat['depth'] > 0 ? 'viceunf-doc-filter-btn--sub' : ''; ?>"
                            data-wp-context="<?php echo $cat_ctx; ?>"
                            data-wp-on--click="actions.setFilter"
                            data-wp-class--is-active="state.isFilterActive">
                            <?php echo esc_html($cat['name']); ?>
                        </button>
                    <?php endforeach; ?>
                </nav>
            </div>
        <?php endif; ?>

        <!-- Estado sin resultados -->
        <div
            class="viceunf-doc-no-results"
            role="status"
            aria-live="polite"
            data-wp-class--is-visible="state.hasNoResults">
            <i class="fas fa-search-minus" aria-hidden="true"></i>
            <p><?php esc_html_e('Sin resultados para esa búsqueda.', 'viceunf'); ?></p>
        </div>

        <!-- Árbol de acordeones -->
        <div class="viceunf-doc-tree" data-wp-class--is-hidden="state.hasNoResults">
            <?php foreach ($documents_data['data'] as $node) : ?>
                <?php $render_tree_node($node, 0); ?>
            <?php endforeach; ?>
        </div>

    <?php else : ?>
        <!-- Vista plana (sin categorías jerárquicas) -->
        <ul class="viceunf-accordion__list viceunf-accordion__list--flat" role="list">
            <?php foreach ($documents_data['data'] as $doc) : ?>
                <li class="viceunf-accordion__item">
                    <a href="<?php echo esc_url($doc['permalink']); ?>" class="viceunf-accordion__link">
                        <span class="viceunf-accordion__link-icon" aria-hidden="true">
                            <i class="fas fa-file-alt"></i>
                        </span>
                        <span class="viceunf-accordion__link-text"><?php echo esc_html($doc['title']); ?></span>
                    </a>
                    <?php if ($doc['has_file']) : ?>
                        <a
                            href="<?php echo esc_url($doc['file_url']); ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="viceunf-accordion__download"
                            aria-label="<?php esc_attr_e('Descargar', 'viceunf'); ?>">
                            <i class="fas fa-arrow-down" aria-hidden="true"></i>
                            <span><?php esc_html_e('Descargar', 'viceunf'); ?></span>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>