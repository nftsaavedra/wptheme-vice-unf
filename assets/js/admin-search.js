document.addEventListener("DOMContentLoaded", function () {
  const linkTypeSelect = document.getElementById("slider_link_type");
  if (linkTypeSelect) {
    const urlField = document.getElementById("campo_url");
    const contentField = document.getElementById("campo_contenido");
    const toggleFields = () => {
      urlField.style.display =
        linkTypeSelect.value === "url" ? "block" : "none";
      contentField.style.display =
        linkTypeSelect.value === "content" ? "block" : "none";
    };
    toggleFields();
    linkTypeSelect.addEventListener("change", toggleFields);
  }

  let searchTimer;
  const searchInput = document.getElementById("content_search_input");
  if (searchInput) {
    const searchResultsContainer = document.getElementById(
      "search_results_container"
    );
    const contentIdInput = document.getElementById("slider_link_content_id");
    searchInput.addEventListener("keyup", function () {
      clearTimeout(searchTimer);
      if (this.value.length < 3) {
        searchResultsContainer.style.display = "none";
        return;
      }
      searchResultsContainer.innerHTML =
        '<p style="padding: 8px 12px;">Buscando...</p>';
      searchResultsContainer.style.display = "block";
      searchTimer = setTimeout(() => {
        const formData = new FormData();
        formData.append("action", "viceunf_search_content");
        formData.append("nonce", viceunf_ajax_obj.nonce);
        formData.append("search", this.value);
        fetch(viceunf_ajax_obj.ajax_url, { method: "POST", body: formData })
          .then((response) => response.json())
          .then((response) => {
            searchResultsContainer.innerHTML = "";
            if (response.success && response.data.length > 0) {
              const ul = document.createElement("ul");
              response.data.forEach((item) => {
                const li = document.createElement("li");
                li.innerHTML = `<strong>${item.title}</strong><br><small>Tipo: ${item.type}</small>`;
                li.dataset.id = item.id;
                li.dataset.title = item.title;
                li.addEventListener("click", () => {
                  contentIdInput.value = item.id;
                  searchInput.value = item.title;
                  searchResultsContainer.style.display = "none";
                });
                ul.appendChild(li);
              });
              searchResultsContainer.appendChild(ul);
            } else {
              searchResultsContainer.innerHTML =
                '<p style="padding: 8px 12px;">No se encontraron resultados.</p>';
            }
          })
          .catch((error) => console.error("Error AJAX:", error));
      }, 500);
    });
    document.addEventListener("click", (e) => {
      const container = document.getElementById("campo_contenido");
      if (container && !container.contains(e.target)) {
        searchResultsContainer.style.display = "none";
      }
    });
  }
});
