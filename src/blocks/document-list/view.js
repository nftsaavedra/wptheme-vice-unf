/**
 * Frontend Vanilla JS para el bloque viceunf/document-list.
 * Gestiona acordeones, búsqueda en tiempo real y filtros de categoría.
 *
 * Compatible WP 6.x / 7.0 — sin dependencias externas.
 */
document.addEventListener( 'DOMContentLoaded', () => {
	document.querySelectorAll( '.viceunf-document-list-block' ).forEach( initBlock );
} );

function initBlock( block ) {
	initAccordions( block );
	initSearch( block );
	initFilters( block );
}

// ─── Acordeones ───────────────────────────────────────────────────────────────
function initAccordions( block ) {
	block.querySelectorAll( '.viceunf-accordion__header' ).forEach( ( btn ) => {
		btn.addEventListener( 'click', () => {
			const acc    = btn.closest( '.viceunf-accordion' );
			const isOpen = acc.classList.contains( 'is-open' );
			const icon   = btn.querySelector( '.viceunf-accordion__icon i' );

			acc.classList.toggle( 'is-open' );
			btn.setAttribute( 'aria-expanded', String( ! isOpen ) );

			if ( icon ) {
				icon.className = isOpen ? 'fas fa-folder' : 'fas fa-folder-open';
			}
		} );
	} );
}

// ─── Búsqueda en tiempo real ─────────────────────────────────────────────────
function initSearch( block ) {
	const input    = block.querySelector( '.viceunf-doc-search__input' );
	const clearBtn = block.querySelector( '.viceunf-doc-search__clear' );
	const emptyMsg = block.querySelector( '.viceunf-doc-no-results' );
	const tree     = block.querySelector( '.viceunf-doc-tree' );

	if ( ! input ) return;

	const allItems      = block.querySelectorAll( '.viceunf-accordion__item[data-title]' );
	const allAccordions = block.querySelectorAll( '.viceunf-accordion' );

	input.addEventListener( 'input', () => {
		const query = input.value.toLowerCase().trim();

		// Mostrar/ocultar botón limpiar
		clearBtn.classList.toggle( 'is-visible', !! query );

		if ( ! query ) {
			restoreAll( allItems, allAccordions, emptyMsg, tree );
			return;
		}

		filterByQuery( query, allItems, allAccordions, emptyMsg, tree );
	} );

	clearBtn.addEventListener( 'click', () => {
		input.value = '';
		clearBtn.classList.remove( 'is-visible' );
		restoreAll( allItems, allAccordions, emptyMsg, tree );
		input.focus();
	} );
}

function filterByQuery( query, allItems, allAccordions, emptyMsg, tree ) {
	let visible = 0;

	// 1. Clasificar todos los items por coincidencia
	allItems.forEach( ( item ) => {
		const match = ( item.dataset.title || '' ).includes( query );
		item.classList.toggle( 'is-hidden', ! match );
		if ( match ) visible++;
	} );

	// 2. Procesar acordeones: ocultar todos primero, luego mostrar
	//    solo los que tienen al menos un item visible DIRECTO o un
	//    hijo cascaded. Procesamos en orden reverso para hacer bottom-up.
	const accordionList = Array.from( allAccordions );

	// Primero ocultar todos
	accordionList.forEach( ( acc ) => {
		acc.classList.add( 'is-hidden' );
		acc.classList.remove( 'is-open' );
		const btn = acc.querySelector( ':scope > .viceunf-accordion__header' );
		if ( btn ) btn.setAttribute( 'aria-expanded', 'false' );
	} );

	// Luego mostrar los que tienen contenido directo visible
	accordionList.forEach( ( acc ) => {
		// Buscar items DIRECTOS (excluyendo los de sub-acordeones)
		// usando scope > body-inner > list > item
		const directItems = acc.querySelectorAll(
			':scope > .viceunf-accordion__body > .viceunf-accordion__body-inner > .viceunf-accordion__list > .viceunf-accordion__item[data-title]'
		);
		const hasDirectMatch = Array.from( directItems ).some(
			( item ) => ! item.classList.contains( 'is-hidden' )
		);

		if ( hasDirectMatch ) {
			showAccordionAndAncestors( acc );
		}
	} );

	// 3. Estados visuales
	const noResults = visible === 0;
	emptyMsg?.classList.toggle( 'is-visible', noResults );
	tree?.classList.toggle( 'is-hidden', noResults );
}

/**
 * Muestra un acordeón y todos sus ancestros acordeones.
 */
function showAccordionAndAncestors( acc ) {
	let current = acc;
	while ( current && current.classList.contains( 'viceunf-accordion' ) ) {
		current.classList.remove( 'is-hidden' );
		current.classList.add( 'is-open' );
		const btn = current.querySelector( ':scope > .viceunf-accordion__header' );
		if ( btn ) btn.setAttribute( 'aria-expanded', 'true' );

		// Subir al siguiente accordeón padre en el árbol
		const parent = current.parentElement?.closest( '.viceunf-accordion' );
		current = parent ?? null;
	}
}


function restoreAll( allItems, allAccordions, emptyMsg, tree ) {
	allItems.forEach( ( item ) => item.classList.remove( 'is-hidden' ) );
	allAccordions.forEach( ( acc ) => acc.classList.remove( 'is-hidden' ) );
	emptyMsg?.classList.remove( 'is-visible' );
	tree?.classList.remove( 'is-hidden' );
}

// ─── Filtros de categoría ────────────────────────────────────────────────────
function initFilters( block ) {
	const filterBtns = block.querySelectorAll( '.viceunf-doc-filter-btn' );
	const input      = block.querySelector( '.viceunf-doc-search__input' );
	const clearBtn   = block.querySelector( '.viceunf-doc-search__clear' );
	const emptyMsg   = block.querySelector( '.viceunf-doc-no-results' );
	const tree       = block.querySelector( '.viceunf-doc-tree' );

	if ( ! filterBtns.length ) return;

	const allItems      = block.querySelectorAll( '.viceunf-accordion__item[data-title]' );
	const allAccordions = block.querySelectorAll( '.viceunf-accordion' );

	filterBtns.forEach( ( btn ) => {
		btn.addEventListener( 'click', () => {
			const filter = btn.dataset.filter;

			// Limpiar búsqueda
			if ( input ) {
				input.value = '';
				clearBtn?.classList.remove( 'is-visible' );
			}
			allItems.forEach( ( item ) => item.classList.remove( 'is-hidden' ) );
			emptyMsg?.classList.remove( 'is-visible' );
			tree?.classList.remove( 'is-hidden' );

			// Activar botón seleccionado
			filterBtns.forEach( ( b ) => b.classList.remove( 'is-active' ) );
			btn.classList.add( 'is-active' );

			if ( filter === 'all' ) {
				// Mostrar todos los acordeones, solo el primero abierto
				allAccordions.forEach( ( acc, i ) => {
					acc.classList.remove( 'is-hidden' );
					const header = acc.querySelector( '.viceunf-accordion__header' );
					if ( acc.classList.contains( 'viceunf-accordion--root' ) ) {
						const shouldOpen = i === 0;
						acc.classList.toggle( 'is-open', shouldOpen );
						header?.setAttribute( 'aria-expanded', String( shouldOpen ) );
					}
				} );
				return;
			}

			// Mostrar solo la categoría seleccionada y sus descendientes
			allAccordions.forEach( ( acc ) => {
				const cat       = acc.dataset.category;
				const isTarget  = cat === filter;
				const hasTarget = !! acc.querySelector( `[data-category="${ filter }"]` );

				if ( isTarget || hasTarget ) {
					acc.classList.remove( 'is-hidden' );
					acc.classList.add( 'is-open' );
					acc.querySelector( '.viceunf-accordion__header' )
						?.setAttribute( 'aria-expanded', 'true' );
				} else {
					acc.classList.add( 'is-hidden' );
				}
			} );
		} );
	} );
}
