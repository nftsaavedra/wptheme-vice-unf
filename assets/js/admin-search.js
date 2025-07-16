/**
 * Script Unificado para Componentes de Búsqueda AJAX en el Panel de Administración de ViceUnf.
 * @version 1.4.0
 */

/**
 * LÓGICA DE BÚSQUEDA AJAX REUTILIZABLE.
 * Se aplica a cualquier elemento con la clase '.ajax-search-wrapper'.
 * Hacemos esta función global (la adjuntamos al objeto 'window') para que otros scripts
 * como el gestor del repetidor, puedan llamarla para inicializar nuevos componentes.
 *
 * @param {HTMLElement} wrapper El elemento contenedor del componente de búsqueda.
 */
window.initializeAjaxSearch = function (wrapper) {
  const searchInput = wrapper.querySelector(".ajax-search-input");
  const resultsContainer = wrapper.querySelector(".ajax-search-results");
  const hiddenIdInput = wrapper.querySelector(".ajax-search-hidden-id");
  const selectedView = wrapper.querySelector(".selected-item-view");
  const selectedTitle = wrapper.querySelector(".selected-item-title");
  const searchView = wrapper.querySelector(".search-input-view");
  const ajaxAction = wrapper.dataset.action;
  let searchTimer;
  let abortController;

  if (
    !searchInput ||
    !resultsContainer ||
    !hiddenIdInput ||
    !selectedView ||
    !searchView
  ) {
    return;
  }

  // --- EVENTO: Escribir en el campo de búsqueda ---
  searchInput.addEventListener("keyup", function () {
    clearTimeout(searchTimer);
    if (abortController) {
      abortController.abort();
    }
    if (this.value.length < 2 && ajaxAction !== "viceunf_search_icons") {
      resultsContainer.style.display = "none";
      return;
    }

    resultsContainer.innerHTML =
      '<div class="spinner is-active" style="margin: auto; float: none;"></div>';
    resultsContainer.style.display = "block";

    searchTimer = setTimeout(() => {
      abortController = new AbortController();
      const signal = abortController.signal;
      const formData = new FormData();
      formData.append("action", ajaxAction);
      formData.append("nonce", viceunf_ajax_obj.nonce);
      formData.append("search", this.value);

      fetch(viceunf_ajax_obj.ajax_url, {
        method: "POST",
        body: formData,
        signal,
      })
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
              let iconHTML =
                ajaxAction === "viceunf_search_icons"
                  ? `<i class="result-icon ${item.id}"></i>`
                  : "";
              let typeHTML = item.type ? `<small>(${item.type})</small>` : "";
              li.innerHTML = `${iconHTML}<strong>${item.title}</strong> ${typeHTML}`;
              ul.appendChild(li);
            });
            resultsContainer.appendChild(ul);
          } else {
            resultsContainer.innerHTML =
              '<p class="no-results">No se encontraron resultados.</p>';
          }
        })
        .catch((error) => {
          if (error.name !== "AbortError") {
            console.error("Error en la búsqueda:", error);
          }
        });
    }, 500);
  });

  // --- EVENTO: Clics dentro del componente (seleccionar o limpiar) ---
  wrapper.addEventListener("click", function (e) {
    const selectedItem = e.target.closest(".ajax-results-list li");
    const clearButton = e.target.closest(".clear-selection-btn");

    if (selectedItem) {
      hiddenIdInput.value = selectedItem.dataset.id;
      selectedTitle.textContent = selectedItem.dataset.title;

      if (wrapper.classList.contains("icon-search-wrapper")) {
        const previewIcon = wrapper.querySelector(".icon-preview i");
        if (previewIcon) previewIcon.className = selectedItem.dataset.id;
      }
      searchView.classList.remove("active");
      selectedView.classList.add("active");
      resultsContainer.style.display = "none";
      searchInput.value = "";
    }

    if (clearButton) {
      e.preventDefault();
      hiddenIdInput.value = wrapper.classList.contains("icon-search-wrapper")
        ? ""
        : "0";
      selectedTitle.textContent = "";
      searchInput.value = "";
      if (wrapper.classList.contains("icon-search-wrapper")) {
        const previewIcon = wrapper.querySelector(".icon-preview i");
        if (previewIcon) previewIcon.className = "";
      }
      selectedView.classList.remove("active");
      searchView.classList.add("active");
    }
  });

  // Ocultar resultados si se hace clic fuera del componente
  document.addEventListener("click", (e) => {
    if (!wrapper.contains(e.target)) {
      resultsContainer.style.display = "none";
    }
  });
};

document.addEventListener("DOMContentLoaded", function () {
  /**
   * LÓGICA #1: Mostrar/Ocultar campos en el Meta Box del Slider.
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
   * LÓGICA #2: Inicializar todos los componentes de búsqueda en la página al cargar.
   */
  document
    .querySelectorAll(".ajax-search-wrapper")
    .forEach(window.initializeAjaxSearch);
});
