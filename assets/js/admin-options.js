document.addEventListener("DOMContentLoaded", function () {
  const tabs = document.querySelectorAll(".nav-tab-wrapper .nav-tab");
  const sections = document.querySelectorAll(
    ".viceunf-options-wrap .form-table"
  );

  tabs.forEach((tab) => {
    tab.addEventListener("click", function (e) {
      e.preventDefault();
      tabs.forEach((t) => t.classList.remove("nav-tab-active"));
      sections.forEach((s) => s.classList.remove("active"));

      this.classList.add("nav-tab-active");
      const targetId = this.getAttribute("href").split("tab=")[1];
      document
        .querySelector("#" + targetId + "-section")
        .classList.add("active");

      // Actualiza la URL sin recargar la página
      window.history.pushState({}, "", this.href);
    });
  });

  // Muestra la pestaña correcta al cargar la página
  const activeTab =
    new URLSearchParams(window.location.search).get("tab") || "homepage";
  document.querySelector('.nav-tab[href*="tab=' + activeTab + '"]').click();
});
