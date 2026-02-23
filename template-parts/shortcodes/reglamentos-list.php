<?php
// Salir si se accede directamente.
if ( ! defined( 'ABSPATH' ) ) exit;

$reglamentos_data = isset( $args['reglamentos_data'] ) ? $args['reglamentos_data'] : array();

if ( empty( $reglamentos_data['data'] ) ) {
    echo '<p>' . esc_html__( 'No se encontraron reglamentos en las categorías especificadas.', 'viceunf' ) . '</p>';
    return;
}

// Función recursiva para renderizar nodos del árbol como acordeones.
if ( ! function_exists( 'viceunf_render_tree_node' ) ) :
function viceunf_render_tree_node( $node, $depth = 0, $index = 0 ) {
    $total = ViceUnf_Reglamentos_Service::count_all_reglamentos( $node );
    $is_open = ( $depth === 0 && $index === 0 ); // Primer nodo raíz abierto por defecto.
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

        <div class="viceunf-accordion__body" <?php echo $is_open ? '' : 'style="display:none;"'; ?>>
            <?php
            // ===========================================
            // 1. Renderizar REGLAMENTOS DIRECTOS primero.
            // ===========================================
            if ( ! empty( $node['reglamentos'] ) ) : ?>
                <ul class="viceunf-accordion__list">
                    <?php foreach ( $node['reglamentos'] as $reg ) : ?>
                        <li class="viceunf-accordion__item" data-title="<?php echo esc_attr( mb_strtolower( $reg['title'] ) ); ?>">
                            <a href="<?php echo esc_url( $reg['permalink'] ); ?>" class="viceunf-accordion__link">
                                <i class="fas fa-file-alt" aria-hidden="true"></i>
                                <span><?php echo esc_html( $reg['title'] ); ?></span>
                            </a>
                            <?php if ( $reg['has_file'] ) : ?>
                                <a href="<?php echo esc_url( $reg['file_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="viceunf-accordion__download" title="<?php esc_attr_e( 'Descargar', 'viceunf' ); ?>">
                                    <i class="fas fa-download"></i>
                                    <span><?php esc_html_e( 'Descargar', 'viceunf' ); ?></span>
                                </a>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif;

            // ============================================
            // 2. Luego renderizar subcategorías (children).
            // ============================================
            if ( ! empty( $node['children'] ) ) :
                foreach ( $node['children'] as $child_index => $child ) :
                    viceunf_render_tree_node( $child, $depth + 1, $child_index );
                endforeach;
            endif;
            ?>
        </div>
    </div>
    <?php
}
endif;

// Recolectar categorías para los filtros (solo nodos raíz + hijos de primer nivel).
if ( ! function_exists( 'viceunf_collect_filter_categories' ) ) :
function viceunf_collect_filter_categories( $nodes, $depth = 0 ) {
    $cats = array();
    foreach ( $nodes as $node ) {
        $cats[] = array(
            'id'    => $node['term_id'],
            'name'  => $node['term_name'],
            'color' => $node['color'],
            'depth' => $depth,
        );
        if ( ! empty( $node['children'] ) && $depth < 1 ) {
            $cats = array_merge( $cats, viceunf_collect_filter_categories( $node['children'], $depth + 1 ) );
        }
    }
    return $cats;
}
endif;

// === RENDER ===
if ( ! empty( $reglamentos_data['is_tree'] ) ) :
    $filter_categories = viceunf_collect_filter_categories( $reglamentos_data['data'] );
    ?>
    <div class="viceunf-reglamentos-tree" id="viceunf-reglamentos-tree">

        <!-- Barra de Búsqueda + Filtros -->
        <div class="viceunf-reglamentos-toolbar">
            <div class="viceunf-reglamentos-search">
                <i class="fas fa-search" aria-hidden="true"></i>
                <input
                    type="text"
                    id="viceunf-reglamentos-search-input"
                    placeholder="<?php esc_attr_e( 'Buscar reglamento...', 'viceunf' ); ?>"
                    autocomplete="off"
                >
                <button type="button" id="viceunf-reglamentos-search-clear" class="viceunf-reglamentos-search__clear" style="display:none;" aria-label="<?php esc_attr_e( 'Limpiar búsqueda', 'viceunf' ); ?>">
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
        <div class="viceunf-reglamentos-empty" id="viceunf-reglamentos-empty" style="display:none;">
            <i class="fas fa-search"></i>
            <p><?php esc_html_e( 'No se encontraron reglamentos con esa búsqueda.', 'viceunf' ); ?></p>
        </div>

        <!-- Árbol de Acordeones -->
        <div id="viceunf-reglamentos-content">
            <?php foreach ( $reglamentos_data['data'] as $index => $node ) : ?>
                <?php viceunf_render_tree_node( $node, 0, $index ); ?>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
    (function() {
        // === Acordeones ===
        document.querySelectorAll('.viceunf-accordion__header').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var acc = this.closest('.viceunf-accordion');
                var body = acc.querySelector(':scope > .viceunf-accordion__body');
                var isOpen = acc.classList.contains('is-open');
                var icon = this.querySelector('.viceunf-accordion__icon i');

                acc.classList.toggle('is-open');
                this.setAttribute('aria-expanded', !isOpen);
                body.style.display = isOpen ? 'none' : '';
                if (icon) icon.className = 'fas fa-folder' + (isOpen ? '' : '-open');
            });
        });

        // === Búsqueda en tiempo real ===
        var searchInput = document.getElementById('viceunf-reglamentos-search-input');
        var clearBtn = document.getElementById('viceunf-reglamentos-search-clear');
        var emptyMsg = document.getElementById('viceunf-reglamentos-empty');
        var content = document.getElementById('viceunf-reglamentos-content');
        var allItems = content.querySelectorAll('.viceunf-accordion__item');
        var allAccordions = content.querySelectorAll('.viceunf-accordion');

        searchInput.addEventListener('input', function() {
            var query = this.value.toLowerCase().trim();
            clearBtn.style.display = query ? '' : 'none';

            if (!query) {
                // Restaurar estado original.
                allItems.forEach(function(item) { item.style.display = ''; });
                allAccordions.forEach(function(acc) {
                    acc.style.display = '';
                    // Restaurar estado colapsado excepto el primero.
                    if (acc.dataset.depth === '0') {
                        var isFirst = acc === content.querySelector('.viceunf-accordion--root');
                        if (!isFirst) {
                            acc.classList.remove('is-open');
                            var body = acc.querySelector(':scope > .viceunf-accordion__body');
                            if (body) body.style.display = 'none';
                        }
                    }
                });
                emptyMsg.style.display = 'none';
                content.style.display = '';
                return;
            }

            var visibleCount = 0;

            // Ocultar todos los ítems que no coinciden.
            allItems.forEach(function(item) {
                var title = item.getAttribute('data-title') || '';
                if (title.indexOf(query) !== -1) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Mostrar/ocultar acordeones según si tienen ítems visibles.
            allAccordions.forEach(function(acc) {
                var visItems = acc.querySelectorAll('.viceunf-accordion__item[style=""],.viceunf-accordion__item:not([style])');
                var hasVisible = false;
                visItems.forEach(function(item) {
                    if (item.style.display !== 'none') hasVisible = true;
                });

                if (hasVisible) {
                    acc.style.display = '';
                    acc.classList.add('is-open');
                    var body = acc.querySelector(':scope > .viceunf-accordion__body');
                    if (body) body.style.display = '';
                } else {
                    // Solo ocultar si no tiene hijos con resultados visibles.
                    var childAccs = acc.querySelectorAll('.viceunf-accordion');
                    var childHasVisible = false;
                    childAccs.forEach(function(c) { if (c.style.display !== 'none') childHasVisible = true; });
                    acc.style.display = childHasVisible ? '' : 'none';
                    if (childHasVisible) {
                        acc.classList.add('is-open');
                        var body = acc.querySelector(':scope > .viceunf-accordion__body');
                        if (body) body.style.display = '';
                    }
                }
            });

            emptyMsg.style.display = visibleCount === 0 ? '' : 'none';
            content.style.display = visibleCount === 0 ? 'none' : '';
        });

        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
            searchInput.focus();
        });

        // === Filtros de Categoría ===
        var filterBtns = document.querySelectorAll('.viceunf-filter-btn');
        filterBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                var filter = this.getAttribute('data-filter');

                // Limpiar búsqueda primero.
                searchInput.value = '';
                clearBtn.style.display = 'none';
                allItems.forEach(function(item) { item.style.display = ''; });
                emptyMsg.style.display = 'none';
                content.style.display = '';

                // Actualizar estado activo.
                filterBtns.forEach(function(b) { b.classList.remove('is-active'); });
                this.classList.add('is-active');

                if (filter === 'all') {
                    // Mostrar todas las categorías.
                    allAccordions.forEach(function(acc) { acc.style.display = ''; });
                    // Abrir solo el primer nodo raíz.
                    var rootAccs = content.querySelectorAll(':scope > .viceunf-accordion--root');
                    rootAccs.forEach(function(acc, i) {
                        var body = acc.querySelector(':scope > .viceunf-accordion__body');
                        if (i === 0) {
                            acc.classList.add('is-open');
                            if (body) body.style.display = '';
                        } else {
                            acc.classList.remove('is-open');
                            if (body) body.style.display = 'none';
                        }
                    });
                } else {
                    var catId = filter;
                    allAccordions.forEach(function(acc) {
                        var accCat = acc.getAttribute('data-category');
                        var isTarget = accCat === catId;
                        var containsTarget = acc.querySelector('[data-category="' + catId + '"]');

                        if (isTarget) {
                            acc.style.display = '';
                            acc.classList.add('is-open');
                            var body = acc.querySelector(':scope > .viceunf-accordion__body');
                            if (body) body.style.display = '';
                        } else if (containsTarget) {
                            acc.style.display = '';
                            acc.classList.add('is-open');
                            var body = acc.querySelector(':scope > .viceunf-accordion__body');
                            if (body) body.style.display = '';
                        } else {
                            // Ocultar si no es un ancestro ni el objetivo.
                            var isAncestorOfTarget = false;
                            var parent = acc.parentElement;
                            while (parent) {
                                if (parent.getAttribute && parent.getAttribute('data-category') === catId) {
                                    isAncestorOfTarget = true;
                                    break;
                                }
                                parent = parent.parentElement;
                            }
                            if (!isAncestorOfTarget) {
                                acc.style.display = 'none';
                            }
                        }
                    });
                }
            });
        });
    })();
    </script>
    <?php
else :
    // Vista plana (filtrada por categoría específica).
    ?>
    <div class="viceunf-reglamentos-tree">
        <ul class="viceunf-accordion__list">
            <?php foreach ( $reglamentos_data['data'] as $reg ) : ?>
                <li class="viceunf-accordion__item" data-title="<?php echo esc_attr( mb_strtolower( $reg['title'] ) ); ?>">
                    <a href="<?php echo esc_url( $reg['permalink'] ); ?>" class="viceunf-accordion__link">
                        <i class="fas fa-file-alt" aria-hidden="true"></i>
                        <span><?php echo esc_html( $reg['title'] ); ?></span>
                    </a>
                    <?php if ( $reg['has_file'] ) : ?>
                        <a href="<?php echo esc_url( $reg['file_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="viceunf-accordion__download" title="<?php esc_attr_e( 'Descargar', 'viceunf' ); ?>">
                            <i class="fas fa-download"></i>
                            <span><?php esc_html_e( 'Descargar', 'viceunf' ); ?></span>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
endif;
