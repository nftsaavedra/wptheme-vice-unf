document.addEventListener('DOMContentLoaded', function() {
    function initializeAjaxSearch(wrapper) {
        const searchInput = wrapper.querySelector('.ajax-search-input');
        const resultsContainer = wrapper.querySelector('.ajax-search-results');
        const hiddenIdInput = wrapper.querySelector('.ajax-search-hidden-id');
        const selectedView = wrapper.querySelector('.selected-item-view');
        const selectedTitle = wrapper.querySelector('.selected-item-title');
        const searchView = wrapper.querySelector('.search-input-view');
        const clearButton = wrapper.querySelector('.clear-selection-btn');
        const ajaxAction = wrapper.dataset.action || 'viceunf_search_content';
        let searchTimer;

        // --- EVENTO: Escribir en el campo de búsqueda ---
        searchInput.addEventListener('keyup', function() {
            clearTimeout(searchTimer);
            const searchTerm = this.value;

            if (searchTerm.length < 3) {
                resultsContainer.style.display = 'none';
                return;
            }

            resultsContainer.innerHTML = '<div class="spinner is-active"></div>';
            resultsContainer.style.display = 'block';

            searchTimer = setTimeout(() => {
                const formData = new FormData();
                formData.append('action', ajaxAction);
                formData.append('nonce', viceunf_ajax_obj.nonce);
                formData.append('search', searchTerm);

                fetch(viceunf_ajax_obj.ajax_url, { method: 'POST', body: formData })
                    .then(response => response.json())
                    .then(response => {
                        resultsContainer.innerHTML = '';
                        if (response.success && response.data.length > 0) {
                            const ul = document.createElement('ul');
                            response.data.forEach(item => {
                                const li = document.createElement('li');
                                li.dataset.id = item.id;
                                li.dataset.title = item.title;
                                li.innerHTML = `<strong>${item.title}</strong> <small>(${item.type})</small>`;
                                ul.appendChild(li);
                            });
                            resultsContainer.appendChild(ul);
                        } else {
                            resultsContainer.innerHTML = '<p class="no-results">No se encontraron resultados.</p>';
                        }
                    });
            }, 500);
        });

        // --- EVENTO: Clic en un resultado de la lista ---
        resultsContainer.addEventListener('click', function(e) {
            if (e.target && e.target.closest('li')) {
                const selectedItem = e.target.closest('li');
                const pageId = selectedItem.dataset.id;
                const pageTitle = selectedItem.dataset.title;

                // Actualizar valores
                hiddenIdInput.value = pageId;
                selectedTitle.textContent = pageTitle;

                // Cambiar a la vista "seleccionado"
                searchView.classList.remove('active');
                selectedView.classList.add('active');
                resultsContainer.style.display = 'none';
                searchInput.value = '';
            }
        });

        // --- EVENTO: Clic en el botón de limpiar (X) ---
        clearButton.addEventListener('click', function() {
            // Limpiar valores
            hiddenIdInput.value = '0';
            selectedTitle.textContent = '';
            searchInput.value = '';

            // Cambiar a la vista "búsqueda"
            selectedView.classList.remove('active');
            searchView.classList.add('active');
        });

        // Ocultar resultados si se hace clic fuera del contenedor
        document.addEventListener('click', function(e) {
            if (!wrapper.contains(e.target)) {
                resultsContainer.style.display = 'none';
            }
        });
    }

    // Inicializar todos los componentes de búsqueda en la página
    document.querySelectorAll('.ajax-search-wrapper').forEach(wrapper => {
        initializeAjaxSearch(wrapper);
    });
});