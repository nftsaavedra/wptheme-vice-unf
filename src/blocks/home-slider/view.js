/**
 * view.js — Swiper FSE encapsulado para vpinunf/home-slider
 * Solo se carga cuando el bloque está presente en la página.
 */
document.addEventListener('DOMContentLoaded', () => {
    const sliderEls = document.querySelectorAll('.dt_slider .dt_swiper_carousel.slider');
    if (!sliderEls.length) return;

    sliderEls.forEach(el => {
        let opts = {};
        try {
            opts = JSON.parse(el.dataset.swiperOptions || '{}');
        } catch (e) {}

        if (typeof Swiper !== 'undefined') {
            new Swiper(el, opts);
        }
    });
});
