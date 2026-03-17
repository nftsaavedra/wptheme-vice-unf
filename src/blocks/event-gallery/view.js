/**
 * Frontend initialization for Event Gallery Swiper
 */
document.addEventListener('DOMContentLoaded', () => {
    // Buscamos todas las galerías del tema
    const galleryContainers = document.querySelectorAll('.viceunf-event-gallery');

    galleryContainers.forEach(container => {
        const mainSwiperEl = container.querySelector('.viceunf-event-gallery__main');
        const thumbsSwiperEl = container.querySelector('.viceunf-event-gallery__thumbs');

        if (!mainSwiperEl || !thumbsSwiperEl || typeof Swiper === 'undefined') {
            return;
        }

        // Obtener opciones desde data attributes (patrón del tema)
        const mainOptions = JSON.parse(mainSwiperEl.getAttribute('data-swiper-options') || '{}');
        const thumbsOptions = JSON.parse(thumbsSwiperEl.getAttribute('data-swiper-options') || '{}');

        // 1. Inicializar Thumbs Swiper
        const thumbsSwiper = new Swiper(thumbsSwiperEl, thumbsOptions);

        // 2. Inicializar Main Swiper conectándolo con el anterior
        mainOptions.thumbs = {
            swiper: thumbsSwiper
        };

        new Swiper(mainSwiperEl, mainOptions);
    });
});
