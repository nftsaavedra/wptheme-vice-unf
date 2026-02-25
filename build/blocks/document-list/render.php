<?php
/**
 * Renderizador de servidor para el bloque viceunf/document-list.
 */

$post_type           = isset( $attributes['postType'] ) ? $attributes['postType'] : 'reglamento';
$taxonomy            = isset( $attributes['taxonomy'] ) ? $attributes['taxonomy'] : 'categoria_reglamento';
$selected_categories = isset( $attributes['selectedCategories'] ) ? $attributes['selectedCategories'] : array();

if ( ! class_exists( 'ViceUnf_Document_Service' ) ) {
    echo '<p>Error: ViceUnf Core plugin no está activo.</p>';
    return;
}

$categoria_slugs = array();
if ( ! empty( $selected_categories ) && is_array( $selected_categories ) ) {
    foreach ( $selected_categories as $term_id ) {
        $term = get_term( $term_id, $taxonomy );
        if ( ! is_wp_error( $term ) && $term ) {
            $categoria_slugs[] = $term->slug;
        }
    }
}

if ( empty( $categoria_slugs ) ) {
    $documents_data = ViceUnf_Document_Service::get_documents_tree( $post_type, $taxonomy );
} else {
    $documents_data = ViceUnf_Document_Service::get_documents( $post_type, $taxonomy, $categoria_slugs );
}

$wrapper_attributes = get_block_wrapper_attributes( array(
    'class' => 'viceunf-document-list-block'
) );

// Función recursiva aislada para este bloque
$render_tree_node = function( $node, $depth, $index ) use ( &$render_tree_node ) {
    $total = ViceUnf_Document_Service::count_all_documents( $node );
    $is_open = ( $depth === 0 && $index === 0 );
    $open_class = $is_open ? ' is-open' : '';
    $color = esc_attr( $node['color'] );
    $level_class = $depth > 0 ? 'viceunf-accordion--child' : 'viceunf-accordion--root';
    ?>
    <div class="viceunf-accordion <?php echo esc_attr( $level_class . $open_class ); ?>" style="--acc-color: <?php echo $color; ?>;" data-depth="<?php echo intval( $depth ); ?>" data-category="<?php echo esc_attr( $node['term_id'] ); ?>">
        <button type="button" class="viceunf-accordion__header" aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>">
            <span class="viceunf-accordion__icon">
                <i class="fas fa-folder<?php echo $is_open ? '-open' : ''; ?>" aria-hidden="true"></i>
            </span>
            <span class="viceunf-accordion__title"><?php echo esc_html( $node['term_name'] ); ?></span>
            <span class="viceunf-accordion__count"><?php echo intval( $total ); ?></span>
            <span class="viceunf-accordion__chevron" aria-hidden="true">
                <i class="fas fa-chevron-down"></i>
            </span>
        </button>

        <div class="viceunf-accordion__body">
            <div class="viceunf-accordion__body-inner">
                <?php if ( ! empty( $node['documents'] ) ) : ?>
                    <ul class="viceunf-accordion__list">
                        <?php foreach ( $node['documents'] as $doc ) : ?>
                            <li class="viceunf-accordion__item" data-title="<?php echo esc_attr( mb_strtolower( $doc['title'] ) ); ?>">
                                <a href="<?php echo esc_url( $doc['permalink'] ); ?>" class="viceunf-accordion__link">
                                    <div class="viceunf-accordion__link-icon-wrap">
                                        <i class="fas fa-file-pdf" aria-hidden="true"></i>
                                    </div>
                                    <span class="viceunf-accordion__link-text"><?php echo esc_html( $doc['title'] ); ?></span>
                                </a>
                                <?php if ( $doc['has_file'] ) : ?>
                                    <a href="<?php echo esc_url( $doc['file_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="viceunf-accordion__download" aria-label="<?php esc_attr_e( 'Descargar', 'viceunf' ); ?>">
                                        <i class="fas fa-arrow-down"></i>
                                        <span><?php esc_html_e( 'Descargar', 'viceunf' ); ?></span>
                                    </a>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <?php
                if ( ! empty( $node['children'] ) ) :
                    foreach ( $node['children'] as $child_index => $child ) :
                        $render_tree_node( $child, $depth + 1, $child_index );
                    endforeach;
                endif;
                ?>
            </div>
        </div>
    </div>
    <?php
};

// Función para recolectar filtros
$collect_filters = function( $nodes, $depth = 0 ) use ( &$collect_filters ) {
    $cats = array();
    foreach ( $nodes as $node ) {
        $cats[] = array(
            'id'    => $node['term_id'],
            'name'  => $node['term_name'],
            'color' => $node['color'],
            'depth' => $depth,
        );
        if ( ! empty( $node['children'] ) && $depth < 1 ) {
            $cats = array_merge( $cats, $collect_filters( $node['children'], $depth + 1 ) );
        }
    }
    return $cats;
};
?>
<div <?php echo $wrapper_attributes; ?>>
    <?php if ( empty( $documents_data['data'] ) ) : ?>
        <p><?php esc_html_e( 'No se encontraron documentos.', 'viceunf' ); ?></p>
    <?php elseif ( ! empty( $documents_data['is_tree'] ) ) : 
        $filter_categories = $collect_filters( $documents_data['data'] );
    ?>
        <div class="viceunf-reglamentos-tree">
            <!-- Barra de Búsqueda + Filtros -->
            <div class="viceunf-reglamentos-toolbar">
                <div class="viceunf-reglamentos-search">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    <input
                        type="text"
                        class="viceunf-reglamentos-search-input"
                        placeholder="<?php esc_attr_e( 'Buscar documento...', 'viceunf' ); ?>"
                        autocomplete="off"
                    >
                    <button type="button" class="viceunf-reglamentos-search-clear" style="display:none;" aria-label="<?php esc_attr_e( 'Limpiar búsqueda', 'viceunf' ); ?>">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="viceunf-reglamentos-filters">
                    <button type="button" class="viceunf-filter-btn is-active" data-filter="all">
                        <?php esc_html_e( 'Todas', 'viceunf' ); ?>
                    </button>
                    <?php foreach ( $filter_categories as $cat ) : ?>
                        <button type="button" class="viceunf-filter-btn <?php echo $cat['depth'] > 0 ? 'viceunf-filter-btn--sub' : ''; ?>" data-filter="<?php echo intval( $cat['id'] ); ?>">
                            <?php echo esc_html( $cat['name'] ); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Mensaje sin resultados -->
            <div class="viceunf-reglamentos-empty" style="display:none;">
                <i class="fas fa-search"></i>
                <p><?php esc_html_e( 'No se encontraron documentos con esa búsqueda.', 'viceunf' ); ?></p>
            </div>

            <!-- Árbol de Acordeones -->
            <div class="viceunf-reglamentos-content">
                <?php foreach ( $documents_data['data'] as $index => $node ) : ?>
                    <?php $render_tree_node( $node, 0, $index ); ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else : ?>
        <div class="viceunf-reglamentos-tree">
            <ul class="viceunf-accordion__list">
                <?php foreach ( $documents_data['data'] as $doc ) : ?>
                    <li class="viceunf-accordion__item" data-title="<?php echo esc_attr( mb_strtolower( $doc['title'] ) ); ?>">
                        <a href="<?php echo esc_url( $doc['permalink'] ); ?>" class="viceunf-accordion__link">
                            <i class="fas fa-file-alt" aria-hidden="true"></i>
                            <span><?php echo esc_html( $doc['title'] ); ?></span>
                        </a>
                        <?php if ( $doc['has_file'] ) : ?>
                            <a href="<?php echo esc_url( $doc['file_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="viceunf-accordion__download" title="<?php esc_attr_e( 'Descargar', 'viceunf' ); ?>">
                                <i class="fas fa-download"></i>
                                <span><?php esc_html_e( 'Descargar', 'viceunf' ); ?></span>
                            </a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
