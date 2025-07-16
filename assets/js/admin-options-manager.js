/**
 * =================================================================
 * Gestor de la Página de Opciones del Tema ViceUnf (Vanilla JS)
 * =================================================================
 * Gestiona la funcionalidad del selector de imágenes y del campo
 * repetidor para la sección "Sobre Nosotros".
 * Cero dependencias de jQuery.
 * @version 1.1.0
 */
document.addEventListener("DOMContentLoaded", () => {
  /**
   * Función principal que se ejecuta al cargar la página.
   */
  function initializeOptionsPage() {
    // Inicializa todos los selectores de imagen.
    document
      .querySelectorAll(".viceunf-image-uploader")
      .forEach(initImageUploader);

    // Inicializa el repetidor.
    const repeater = document.getElementById("about-repeater-container");
    if (repeater) {
      initRepeater(repeater);
    }
  }

  /**
   * Inicializa la lógica para un selector de imágenes.
   * @param {HTMLElement} uploaderEl - El elemento contenedor del selector.
   */
  function initImageUploader(uploaderEl) {
    const uploadButton = uploaderEl.querySelector(".upload-image-button");
    const removeButton = uploaderEl.querySelector(".remove-image-button");
    const imageIdInput = uploaderEl.querySelector(".image-attachment-id");
    const previewWrapper = uploaderEl.querySelector(".image-preview-wrapper");
    const previewImg = uploaderEl.querySelector(".image-preview");

    if (typeof wp === "undefined" || typeof wp.media === "undefined") {
      return;
    }

    let mediaFrame;

    uploadButton.addEventListener("click", (event) => {
      event.preventDefault();

      if (mediaFrame) {
        mediaFrame.open();
        return;
      }

      mediaFrame = wp.media({
        title: "Elegir una imagen",
        button: {
          text: "Usar esta imagen",
        },
        multiple: false,
      });

      mediaFrame.on("select", () => {
        const attachment = mediaFrame.state().get("selection").first().toJSON();
        imageIdInput.value = attachment.id;
        previewImg.src = attachment.sizes.medium
          ? attachment.sizes.medium.url
          : attachment.url;
        previewWrapper.style.display = "block";
        removeButton.style.display = "inline-block";
      });

      mediaFrame.open();
    });

    removeButton.addEventListener("click", (event) => {
      event.preventDefault();
      imageIdInput.value = "";
      previewWrapper.style.display = "none";
      previewImg.src = "";
      removeButton.style.display = "none";
    });
  }

  /**
   * Inicializa la lógica para un campo repetidor.
   * @param {HTMLElement} repeaterEl - El elemento contenedor del repetidor.
   */
  function initRepeater(repeaterEl) {
    const itemsWrapper = repeaterEl.querySelector(".repeater-items-wrapper");
    const addButton = repeaterEl.querySelector(".add-repeater-item");
    const template = repeaterEl.querySelector("template");

    if (!itemsWrapper || !addButton || !template) return;

    // --- Evento para Añadir un Nuevo Ítem ---
    addButton.addEventListener("click", () => {
      const templateContent = template.content.cloneNode(true);
      const newItem = templateContent.firstElementChild;
      const uniqueIndex = Date.now();

      newItem.querySelectorAll('[name*="__INDEX__"]').forEach((input) => {
        input.name = input.name.replace("__INDEX__", uniqueIndex);
      });

      itemsWrapper.appendChild(newItem);

      // --- CORRECCIÓN CLAVE ---
      // Ahora llamamos a la función GLOBAL que definimos en admin-search.js
      if (typeof window.initializeAjaxSearch === "function") {
        newItem.querySelectorAll(".ajax-search-wrapper").forEach((wrapper) => {
          // La magia sucede aquí: conectamos la lógica de búsqueda al nuevo ítem.
          window.initializeAjaxSearch(wrapper);
        });
      }
    });

    // --- Delegación de Eventos para los Controles de los Ítems ---
    itemsWrapper.addEventListener("click", (e) => {
      const currentItem = e.target.closest(".repeater-item");
      if (!currentItem) return;

      // Botón de Eliminar
      if (e.target.matches(".remove-repeater-item")) {
        e.preventDefault();
        if (
          window.confirm("¿Estás seguro de que quieres eliminar este ítem?")
        ) {
          currentItem.remove();
        }
      }

      // Botón de Mover Arriba
      if (e.target.matches(".move-repeater-item-up")) {
        e.preventDefault();
        const prevItem = currentItem.previousElementSibling;
        if (prevItem) {
          itemsWrapper.insertBefore(currentItem, prevItem);
        }
      }

      // Botón de Mover Abajo
      if (e.target.matches(".move-repeater-item-down")) {
        e.preventDefault();
        const nextItem = currentItem.nextElementSibling;
        if (nextItem) {
          itemsWrapper.insertBefore(nextItem, currentItem);
        }
      }
    });
  }

  // Inicia todo el gestor.
  initializeOptionsPage();
});
