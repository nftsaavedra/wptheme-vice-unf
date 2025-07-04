document.addEventListener("DOMContentLoaded", function () {
  /**
   * LÓGICA #1: Mostrar/Ocultar campos en el Meta Box del Slider.
   * Esta parte es específica para la página de edición de un Slider.
   */
  const linkTypeSelect = document.getElementById("slider_link_type");
  if (linkTypeSelect) {
    const urlField = document.getElementById("campo_url");
    const contentField = document.getElementById("campo_contenido");

    const toggleSliderFields = () => {
      if (urlField)
        urlField.style.display =
          linkTypeSelect.value === "url" ? "block" : "none";
      if (contentField)
        contentField.style.display =
          linkTypeSelect.value === "content" ? "block" : "none";
    };
    toggleSliderFields();
    linkTypeSelect.addEventListener("change", toggleSliderFields);
  }

  /**
   * LÓGICA #2: Funcionalidad de Búsqueda AJAX Reutilizable y Robusta.
   */
  function initializeAjaxSearch(wrapper) {
    const searchInput = wrapper.querySelector(".ajax-search-input");
    const resultsContainer = wrapper.querySelector(".ajax-search-results");
    const hiddenIdInput = wrapper.querySelector(".ajax-search-hidden-id");
    const selectedView = wrapper.querySelector(".selected-item-view");
    const selectedTitle = wrapper.querySelector(".selected-item-title");
    const searchView = wrapper.querySelector(".search-input-view");
    const ajaxAction = wrapper.dataset.action || "viceunf_search_content";
    let searchTimer;

    if (
      !searchInput ||
      !resultsContainer ||
      !hiddenIdInput ||
      !selectedView ||
      !searchView
    )
      return;

    // --- EVENTO: Escribir en el campo de búsqueda ---
    searchInput.addEventListener("keyup", function () {
      clearTimeout(searchTimer);
      if (this.value.length < 3) {
        resultsContainer.style.display = "none";
        return;
      }
      resultsContainer.innerHTML =
        '<div class="spinner is-active" style="margin:auto; float:none;"></div>';
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
                li.dataset.id = item.id;
                li.dataset.title = item.title;
                li.innerHTML = `<strong>${item.title}</strong><small> (${item.type})</small>`;
                ul.appendChild(li);
              });
              resultsContainer.appendChild(ul);
            } else {
              resultsContainer.innerHTML =
                '<p class="no-results">No se encontraron resultados.</p>';
            }
          });
      }, 500);
    });

    // --- EVENTO: Clics dentro del componente (Delegación de Eventos) ---
    wrapper.addEventListener("click", function (e) {
      // Caso 1: Clic en un resultado de la búsqueda
      const selectedItem = e.target.closest(".ajax-results-list li");
      if (selectedItem) {
        hiddenIdInput.value = selectedItem.dataset.id;
        selectedTitle.textContent = selectedItem.dataset.title;
        searchView.classList.remove("active");
        selectedView.classList.add("active");
        resultsContainer.style.display = "none";
        searchInput.value = "";
        return;
      }

      // Caso 2: Clic en el botón de limpiar (X)
      const clearButton = e.target.closest(".clear-selection-btn");
      if (clearButton) {
        e.preventDefault(); // Previene cualquier comportamiento por defecto del botón
        hiddenIdInput.value = "0";
        selectedTitle.textContent = "";
        searchInput.value = "";
        selectedView.classList.remove("active");
        searchView.classList.add("active");
        return;
      }
    });

    // Ocultar resultados si se hace clic fuera del componente
    document.addEventListener("click", function (e) {
      if (!wrapper.contains(e.target)) {
        resultsContainer.style.display = "none";
      }
    });
  }

  // Inicializar todos los componentes de búsqueda en la página
  document
    .querySelectorAll(".ajax-search-wrapper")
    .forEach(initializeAjaxSearch);
});
