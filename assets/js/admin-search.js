document.addEventListener("DOMContentLoaded", function () {
  // Función para inicializar un campo de búsqueda AJAX
  function initializeAjaxSearch(container) {
    const searchInput = container.querySelector(".ajax-search-input");
    const resultsContainer = container.querySelector(".ajax-search-results");
    const hiddenIdInput = container.querySelector(".ajax-search-hidden-id");
    const ajaxAction = container.dataset.action || "viceunf_search_content";
    let searchTimer;

    if (!searchInput || !resultsContainer || !hiddenIdInput) {
      return;
    }

    searchInput.addEventListener("keyup", function () {
      clearTimeout(searchTimer);

      if (this.value.length < 3) {
        resultsContainer.style.display = "none";
        return;
      }

      resultsContainer.innerHTML = '<p class="loading-text">Buscando...</p>';
      resultsContainer.style.display = "block";

      searchTimer = setTimeout(() => {
        const formData = new FormData();
        formData.append("action", ajaxAction);
        formData.append("nonce", viceunf_ajax_obj.nonce);
        formData.append("search", this.value);

        fetch(viceunf_ajax_obj.ajax_url, { method: "POST", body: formData })
          .then((response) => response.json())
          .then((response) => {
            resultsContainer.innerHTML = "";
            if (response.success && response.data.length > 0) {
              const ul = document.createElement("ul");
              ul.className = "ajax-results-list";
              response.data.forEach((item) => {
                const li = document.createElement("li");
                li.innerHTML = `<strong>${item.title}</strong><br><small>Tipo: ${item.type}</small>`;
                li.dataset.id = item.id;
                li.dataset.title = item.title;

                li.addEventListener("click", () => {
                  hiddenIdInput.value = item.id;
                  searchInput.value = item.title;
                  resultsContainer.style.display = "none";
                });
                ul.appendChild(li);
              });
              resultsContainer.appendChild(ul);
            } else {
              resultsContainer.innerHTML =
                '<p class="no-results-text">No se encontraron resultados.</p>';
            }
          })
          .catch((error) => console.error("Error AJAX:", error));
      }, 500);
    });

    // Ocultar resultados al hacer clic fuera
    document.addEventListener("click", function (e) {
      if (!container.contains(e.target)) {
        resultsContainer.style.display = "none";
      }
    });
  }

  // Busca todos los contenedores de búsqueda y los inicializa
  const searchContainers = document.querySelectorAll(".ajax-search-container");
  searchContainers.forEach((container) => {
    initializeAjaxSearch(container);
  });
});