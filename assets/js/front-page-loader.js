// Usamos un listener para asegurar que el DOM esté completamente cargado antes de ejecutar el script.
document.addEventListener("DOMContentLoaded", function () {
  const sliderContainer = document.getElementById("slider-container");

  // Solo ejecuta el resto del código si el contenedor del slider existe en la página.
  if (!sliderContainer) {
    return;
  }

  const mainSliderSection = document.getElementById("dt_slider");
  // Obtenemos la URL de la API REST que nos pasa WordPress a través de wp_localize_script
  const restUrl = viceunf_front_obj.rest_url_slider;

  function getAutoplayEmbedUrl(url) {
    if (!url || typeof url !== "string") return "";
    let videoId;
    const youtubeRegex =
      /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
    const vimeoRegex = /vimeo\.com\/(?:video\/)?([0-9]+)/;
    if (url.match(youtubeRegex)) {
      videoId = url.match(youtubeRegex)[1];
      return `https://www.youtube.com/embed/${videoId}?autoplay=1&mute=1&rel=0`;
    }
    if (url.match(vimeoRegex)) {
      videoId = url.match(vimeoRegex)[1];
      return `https://player.vimeo.com/video/${videoId}?autoplay=1&muted=1`;
    }
    return url;
  }

  fetch(restUrl)
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then((slides) => {
      if (!slides || slides.length === 0) {
        if (mainSliderSection) mainSliderSection.style.display = "none";
        return;
      }

      let slidesHtml = "";
      slides.forEach((slide) => {
        const title = slide.title.rendered || "";
        const imageUrl = slide.featured_image_url || "";
        const subtitle = slide._slider_subtitle_key || "";
        const description = slide._slider_description_key || "";
        const textAlign = slide._slider_text_alignment_key || "dt-text-left";
        const btn1Text = slide._slider_btn1_text_key || "";
        const btn1Href = slide.btn1_final_href || "";
        const btn2Text = slide._slider_btn2_text_key || "";
        const btn2Href = slide._slider_btn2_link_key || "";
        const videoHref = getAutoplayEmbedUrl(
          slide._slider_video_link_key || ""
        );

        slidesHtml += `
                    <div class="dt_slider-item">
                        ${
                          imageUrl
                            ? `<img src="${imageUrl}" alt="${title}">`
                            : ""
                        }
                        <div class="dt_slider-wrapper"><div class="dt_slider-inner"><div class="dt_slider-innercell">
                            <div class="dt-container"><div class="dt-row ${textAlign}"><div class="dt-col-lg-12 dt-col-md-12 first dt-my-auto">
                                <div class="dt_slider-content">
                                    ${
                                      subtitle
                                        ? `<h5 class="subtitle">${subtitle}</h5>`
                                        : ""
                                    }
                                    <h2 class="title">${title}</h2>
                                    ${
                                      description
                                        ? `<p class="text">${description.replace(
                                            /\n/g,
                                            "<br>"
                                          )}</p>`
                                        : ""
                                    }
                                    <div class="dt_btn-group">
                                        ${
                                          btn1Text && btn1Href
                                            ? `<a href="${btn1Href}" class="dt-btn dt-btn-primary"><span class="dt-btn-text" data-text="${btn1Text}"><span>${btn1Text}</span></span></a>`
                                            : ""
                                        }
                                        ${
                                          btn2Text && btn2Href
                                            ? `<a href="${btn2Href}" class="dt-btn dt-btn-white"><span class="dt-btn-text" data-text="${btn2Text}"><span>${btn2Text}</span></span></a>`
                                            : ""
                                        }
                                        ${
                                          videoHref
                                            ? `<a href="${videoHref}" class="dt_lightbox_img dt-btn-play dt-btn-white" data-caption=""><i class="fas fa-play" aria-hidden="true"></i></a>`
                                            : ""
                                        }
                                    </div>
                                </div>
                            </div></div></div>
                        </div></div></div>
                    </div>`;
      });

      sliderContainer.innerHTML = slidesHtml;

      if (window.jQuery) {
        const $sliderElement = window.jQuery(sliderContainer);
        if (
          $sliderElement.length &&
          typeof $sliderElement.owlCarousel === "function"
        ) {
          const options = JSON.parse($sliderElement.attr("data-owl-options"));
          options.loop = slides.length > 1;
          $sliderElement.trigger("destroy.owl.carousel").owlCarousel(options);
        }
        const $lightboxButtons = $sliderElement.find(".dt_lightbox_img");
        if (
          $lightboxButtons.length &&
          typeof $lightboxButtons.magnificPopup === "function"
        ) {
          $lightboxButtons.magnificPopup({ type: "iframe" });
        }
      }
    })
    .catch((error) => {
      console.error("Error al cargar los sliders:", error);
      if (mainSliderSection) mainSliderSection.style.display = "none";
    });
});
