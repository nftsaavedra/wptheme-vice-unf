// assets/js/admin-main.js

/**
 * Módulo para gestionar la interactividad de los meta-boxes de ViceUnf.
 */
function viceunfAdminMetaboxes() {
  /**
   * Gestiona la lógica para el meta-box de Reglamentos.
   */
  function initReglamentoMetabox() {
    const selector = document.getElementById("reglamento-source-selector");
    if (!selector) return;

    const radios = selector.querySelectorAll(
      'input[name="reglamento_source_type"]'
    );
    const uploadSection = document.getElementById("reglamento-upload-section");
    const externalSection = document.getElementById(
      "reglamento-external-section"
    );
    const fileIdInput = document.getElementById("reglamento_file_id");
    const externalUrlInput = document.getElementById("reglamento_external_url");
    const postForm = document.getElementById("post");

    const toggleSections = () => {
      const selectedValue = selector.querySelector(
        'input[name="reglamento_source_type"]:checked'
      ).value;
      uploadSection.style.display =
        selectedValue === "upload" ? "block" : "none";
      externalSection.style.display =
        selectedValue === "external" ? "block" : "none";

      // Habilitar/deshabilitar el atributo 'required' para validación del navegador
      fileIdInput.required = selectedValue === "upload";
      externalUrlInput.required = selectedValue === "external";
    };

    radios.forEach((radio) => radio.addEventListener("change", toggleSections));
    toggleSections(); // Ejecutar al cargar la página

    // Validación antes de guardar
    if (postForm) {
      postForm.addEventListener("submit", function (event) {
        const selectedValue = selector.querySelector(
          'input[name="reglamento_source_type"]:checked'
        ).value;
        let isValid = true;
        if (selectedValue === "upload" && !fileIdInput.value) {
          isValid = false;
          alert("Por favor, seleccione un archivo para el reglamento.");
        }
        if (selectedValue === "external" && !externalUrlInput.value) {
          isValid = false;
          alert("Por favor, ingrese una URL para el reglamento.");
        }
        if (!isValid) {
          event.preventDefault();
        }
      });
    }

    if (typeof wp !== "undefined" && wp.media) {
      initMediaUploader(selector);
    }
  }

  /**
   * Inicializa el cargador de medios de WordPress.
   */
  function initMediaUploader(selector) {
    const uploadButton = selector.querySelector("#upload_reglamento_button");
    const removeButton = selector.querySelector("#remove_reglamento_button");
    const fileIdInput = selector.querySelector("#reglamento_file_id");
    const fileInfo = selector.querySelector(
      "#reglamento-upload-section .file-info"
    );
    let mediaUploader;

    uploadButton.addEventListener("click", (e) => {
      e.preventDefault();
      if (mediaUploader) {
        mediaUploader.open();
        return;
      }
      mediaUploader = wp.media({
        title: "Seleccionar Archivo de Reglamento",
        button: { text: "Usar este archivo" },
        multiple: false,
        library: {
          type: [
            "application/pdf",
            "application/msword",
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
            "application/vnd.ms-excel",
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
          ],
        },
      });
      mediaUploader.on("select", () => {
        const attachment = mediaUploader
          .state()
          .get("selection")
          .first()
          .toJSON();
        fileIdInput.value = attachment.id;
        fileInfo.innerHTML = `Archivo actual: <a href="${attachment.url}" target="_blank">${attachment.filename}</a>`;
        removeButton.style.display = "inline-block";
      });
      mediaUploader.open();
    });

    removeButton.addEventListener("click", (e) => {
      e.preventDefault();
      fileIdInput.value = "";
      fileInfo.innerHTML = "No se ha seleccionado ningún archivo.";
      e.currentTarget.style.display = "none";
    });
  }

  /**
   * Activa el selector de color de WordPress.
   */
  function initColorPicker() {
    if (
      typeof jQuery === "function" &&
      typeof jQuery.fn.wpColorPicker === "function"
    ) {
      (function ($) {
        $(function () {
          $(".viceunf-color-picker").wpColorPicker();
        });
      })(jQuery);
    }
  }

  initReglamentoMetabox();
  initColorPicker();
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", viceunfAdminMetaboxes);
} else {
  viceunfAdminMetaboxes();
}
