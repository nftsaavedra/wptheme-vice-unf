(function($) {
    // Se asegura de que el código se ejecute solo cuando el documento esté listo.
    $(document).ready(function() {

        // --- Lógica para mostrar/ocultar campos condicionales ---
        const linkTypeSelect = $('#slider_link_type');
        
        // Solo ejecuta esta parte si el selector existe en la página
        if (linkTypeSelect.length) {
            const urlField = $('#campo_url');
            const contentField = $('#campo_contenido');

            function toggleFields() {
                const selectedType = linkTypeSelect.val();
                urlField.toggle(selectedType === 'url');
                contentField.toggle(selectedType === 'content');
            }

            // Muestra/oculta los campos al cargar la página y cuando se cambia la selección
            toggleFields();
            linkTypeSelect.on('change', toggleFields);
        }

        // --- Lógica para la búsqueda AJAX de contenido ---
        let searchTimer;
        const searchInput = $('#content_search_input');

        // Solo ejecuta si el campo de búsqueda existe
        if (searchInput.length) {
            const searchResultsContainer = $('#search_results_container');
            const contentIdInput = $('#slider_link_content_id');

            searchInput.on('keyup', function() {
                // Limpia el temporizador anterior para evitar múltiples peticiones
                clearTimeout(searchTimer);
                const searchTerm = $(this).val();

                // Si el término de búsqueda es muy corto, oculta los resultados y no busca
                if (searchTerm.length < 3) {
                    searchResultsContainer.hide();
                    return;
                }
                
                // Muestra un mensaje de "Buscando..."
                searchResultsContainer.html('<p style="padding: 8px 12px;">Buscando...</p>').show();

                // Inicia un temporizador para buscar solo cuando el usuario deja de teclear
                searchTimer = setTimeout(function() {
                    $.ajax({
                        url: viceunf_ajax_obj.ajax_url, // URL del AJAX de WordPress (pasada desde PHP)
                        type: 'POST',
                        data: {
                            action: 'viceunf_search_content', // Nuestra acción AJAX
                            nonce: viceunf_ajax_obj.nonce,     // Nonce de seguridad
                            search: searchTerm
                        },
                        success: function(response) {
                            searchResultsContainer.empty(); // Limpia resultados anteriores
                            if (response.success && response.data.length > 0) {
                                const ul = $('<ul></ul>');
                                $.each(response.data, function(index, item) {
                                    // Crea cada elemento de la lista de resultados
                                    const li = $('<li></li>')
                                        .html(`<strong>${item.title}</strong><br><small>Tipo: ${item.type}</small>`)
                                        .attr('data-id', item.id)
                                        .attr('data-title', item.title);
                                    ul.append(li);
                                });
                                searchResultsContainer.append(ul);
                            } else {
                                searchResultsContainer.html('<p style="padding: 8px 12px;">No se encontraron resultados.</p>');
                            }
                        }
                    });
                }, 500); // Espera de 500ms antes de enviar la petición
            });

            // Maneja el clic en un resultado de la búsqueda
            searchResultsContainer.on('click', 'li', function() {
                const postId = $(this).data('id');
                const postTitle = $(this).data('title');

                // Rellena el campo oculto con el ID y el campo visible con el título
                contentIdInput.val(postId);
                searchInput.val(postTitle);

                // Oculta la lista de resultados
                searchResultsContainer.hide();
            });

            // Oculta la lista de resultados si se hace clic en cualquier otra parte de la página
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#campo_contenido').length) {
                    searchResultsContainer.hide();
                }
            });
        }
    });
})(jQuery);