document.addEventListener("DOMContentLoaded", function () {
  /**
   * Inicializa un selector de iconos con búsqueda y vista previa en tiempo real.
   * @param {HTMLElement} wrapper - El contenedor principal del componente.
   */
  function initializeIconPicker(wrapper) {
    const input = wrapper.querySelector(".icon-picker-input");
    const previewIcon = wrapper.querySelector(".icon-preview i");
    const resultsContainer = wrapper.querySelector(".icon-picker-results");
    const allIcons = resultsContainer.querySelectorAll("li");

    if (!input || !previewIcon || !resultsContainer) return;

    // Muestra la lista de resultados al hacer clic o enfocarse en el input
    input.addEventListener("focus", function () {
      resultsContainer.style.display = "block";
    });

    // Filtra los iconos de la lista mientras el usuario escribe
    input.addEventListener("keyup", function () {
      const searchTerm = this.value.toLowerCase();
      allIcons.forEach((li) => {
        const iconClass = li.dataset.value.toLowerCase();
        li.style.display = iconClass.includes(searchTerm) ? "flex" : "none";
      });
    });

    // Actualiza la vista previa del icono en tiempo real mientras se escribe
    input.addEventListener("input", function () {
      previewIcon.className = this.value;
    });

    // Asigna el valor final al hacer clic en un icono de la lista
    resultsContainer.addEventListener("click", function (e) {
      const targetLi = e.target.closest("li");
      if (targetLi) {
        const selectedValue = targetLi.dataset.value;
        input.value = selectedValue;
        previewIcon.className = selectedValue;
        resultsContainer.style.display = "none"; // Oculta la lista
      }
    });
  }

  // Aplica la funcionalidad a todos los selectores de icono en la página
  document
    .querySelectorAll(".icon-picker-wrapper")
    .forEach(initializeIconPicker);

  // Cierra los resultados si se hace clic fuera del componente
  document.addEventListener("click", function (e) {
    document
      .querySelectorAll(".icon-picker-wrapper")
      .forEach(function (wrapper) {
        if (!wrapper.contains(e.target)) {
          const results = wrapper.querySelector(".icon-picker-results");
          if (results) {
            results.style.display = "none";
          }
        }
      });
  });
});
