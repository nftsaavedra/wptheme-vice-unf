'use strict';

/**
 * @description Módulo de efectos visuales y componentes frontend para wptheme-vice-unf.
 * Migrado de jQuery a Vanilla JS. Integra Swiper.js, GLightbox, y efectos nativos.
 */

// ============================================================
// Button Split Effect
// ============================================================
if (document.body.classList.contains('btn--effect-two') || document.body.classList.contains('btn--effect-three')) {
    document.querySelectorAll('.btn--effect-two .dt-btn .dt-btn-text, .btn--effect-three .dt-btn .dt-btn-text')
        .forEach((button) => {
            button.innerHTML = '<span>' + button.textContent.trim().split('').join('</span><span>') + '</span>';
        });
}

// ============================================================
// Preloader
// ============================================================
function sitePreloader() {
    const preloader = document.querySelector('.dt_preloader');
    if (!preloader) return;

    setTimeout(() => {
        preloader.style.transition = 'opacity 0.5s ease';
        preloader.style.opacity = '0';
        preloader.addEventListener('transitionend', () => preloader.remove(), { once: true });
    }, 1000);
}

const preloaderClose = document.querySelector('.dt_preloader-close');
if (preloaderClose) {
    preloaderClose.addEventListener('click', () => {
        const preloader = document.querySelector('.dt_preloader');
        if (!preloader) return;
        setTimeout(() => {
            preloader.style.transition = 'opacity 0.5s ease';
            preloader.style.opacity = '0';
            preloader.addEventListener('transitionend', () => preloader.remove(), { once: true });
        }, 200);
    });
}

// ============================================================
// Animated Headlines
// ============================================================
const animationDelay = 2500;
const barAnimationDelay = 3800;
const barWaiting = barAnimationDelay - 3000;
const lettersDelay = 50;
const typeLettersDelay = 150;
const selectionDuration = 500;
const typeAnimationDelay = selectionDuration + 800;
const revealDuration = 600;
const revealAnimationDelay = 1500;

function initHeadline() {
    const letterClasses = ['.dt_heading.dt_heading_2', '.dt_heading.dt_heading_3', '.dt_heading.dt_heading_8', '.dt_heading.dt_heading_9'];
    letterClasses.forEach((cls) => {
        document.querySelectorAll(cls + ' b').forEach((word) => singleLetters(word));
    });

    document.querySelectorAll('.dt_heading').forEach((headline) => animateHeadline(headline));
}

function singleLetters(word) {
    const letters = word.textContent.split('');
    const selected = word.classList.contains('is_on');

    const isHeading3 = word.closest('.dt_heading_3') !== null;

    let newLetters = '';
    for (let i = 0; i < letters.length; i++) {
        let letter = letters[i];
        if (isHeading3) letter = '<em>' + letter + '</em>';
        letter = selected ? '<i class="in">' + letter + '</i>' : '<i>' + letter + '</i>';
        newLetters += letter;
    }
    word.innerHTML = newLetters;
    word.style.opacity = '1';
}

function animateHeadline(headline) {
    let duration = animationDelay;

    if (headline.classList.contains('dt_heading_4')) {
        duration = barAnimationDelay;
        setTimeout(() => {
            const inner = headline.querySelector('.dt_heading_inner');
            if (inner) inner.classList.add('is-loading');
        }, barWaiting);
    } else if (headline.classList.contains('dt_heading_6')) {
        const spanWrapper = headline.querySelector('.dt_heading_inner');
        if (spanWrapper) {
            const newWidth = spanWrapper.offsetWidth + 10;
            spanWrapper.style.width = newWidth + 'px';
        }
    } else if (!headline.classList.contains('dt_heading_2')) {
        const words = headline.querySelectorAll('.dt_heading_inner b');
        let maxWidth = 0;
        words.forEach((w) => {
            const ww = w.offsetWidth;
            if (ww > maxWidth) maxWidth = ww;
        });
        const inner = headline.querySelector('.dt_heading_inner');
        if (inner) inner.style.width = maxWidth + 'px';
    }

    const activeWord = headline.querySelector('.is_on');
    if (activeWord) {
        setTimeout(() => hideWord(activeWord), duration);
    }
}

function hideWord(word) {
    const nextWord = takeNext(word);
    const heading = word.closest('.dt_heading');
    if (!heading) return;

    if (heading.classList.contains('dt_heading_2')) {
        const parentSpan = word.closest('.dt_heading_inner');
        if (parentSpan) {
            parentSpan.classList.add('selected');
            parentSpan.classList.remove('waiting');
        }
        setTimeout(() => {
            if (parentSpan) parentSpan.classList.remove('selected');
            word.classList.remove('is_on');
            word.classList.add('is_off');
            word.querySelectorAll('i').forEach((i) => {
                i.classList.remove('in');
                i.classList.add('out');
            });
        }, selectionDuration);
        setTimeout(() => showWord(nextWord, typeLettersDelay), typeAnimationDelay);

    } else if (heading.classList.contains('dt_heading_3') || heading.classList.contains('dt_heading_8') || heading.classList.contains('dt_heading_9')) {
        const wordLetters = word.querySelectorAll('i');
        const nextLetters = nextWord.querySelectorAll('i');
        const bool = wordLetters.length >= nextLetters.length;
        hideLetter(wordLetters[0], word, bool, lettersDelay);
        showLetter(nextLetters[0], nextWord, bool, lettersDelay);

    } else if (heading.classList.contains('dt_heading_6')) {
        const inner = word.closest('.dt_heading_inner');
        if (inner) {
            inner.style.transition = 'width ' + (revealDuration / 1000) + 's ease';
            inner.style.width = '2px';
            inner.addEventListener('transitionend', function handler() {
                inner.removeEventListener('transitionend', handler);
                switchWord(word, nextWord);
                showWord(nextWord);
            });
        }

    } else if (heading.classList.contains('dt_heading_4')) {
        const inner = word.closest('.dt_heading_inner');
        if (inner) inner.classList.remove('is-loading');
        switchWord(word, nextWord);
        setTimeout(() => hideWord(nextWord), barAnimationDelay);
        setTimeout(() => {
            if (inner) inner.classList.add('is-loading');
        }, barWaiting);

    } else {
        switchWord(word, nextWord);
        setTimeout(() => hideWord(nextWord), animationDelay);
    }
}

function showWord(word, duration) {
    const heading = word.closest('.dt_heading');
    if (!heading) return;

    if (heading.classList.contains('dt_heading_2')) {
        const letters = word.querySelectorAll('i');
        if (letters.length) showLetter(letters[0], word, false, duration);
        word.classList.add('is_on');
        word.classList.remove('is_off');

    } else if (heading.classList.contains('dt_heading_6')) {
        const inner = word.closest('.dt_heading_inner');
        if (inner) {
            inner.style.transition = 'width ' + (revealDuration / 1000) + 's ease';
            inner.style.width = (word.offsetWidth + 10) + 'px';
            inner.addEventListener('transitionend', function handler() {
                inner.removeEventListener('transitionend', handler);
                setTimeout(() => hideWord(word), revealAnimationDelay);
            });
        }
    }
}

function hideLetter(letter, word, bool, duration) {
    if (!letter) return;
    letter.classList.remove('in');
    letter.classList.add('out');

    const next = letter.nextElementSibling;
    if (next) {
        setTimeout(() => hideLetter(next, word, bool, duration), duration);
    } else if (bool) {
        setTimeout(() => hideWord(takeNext(word)), animationDelay);
    }

    if (!next && document.documentElement.classList.contains('no-csstransitions')) {
        const nextWord = takeNext(word);
        switchWord(word, nextWord);
    }
}

function showLetter(letter, word, bool, duration) {
    if (!letter) return;
    letter.classList.add('in');
    letter.classList.remove('out');

    const next = letter.nextElementSibling;
    if (next) {
        setTimeout(() => showLetter(next, word, bool, duration), duration);
    } else {
        const heading = word.closest('.dt_heading');
        if (heading && heading.classList.contains('dt_heading_2')) {
            setTimeout(() => {
                const inner = word.closest('.dt_heading_inner');
                if (inner) inner.classList.add('waiting');
            }, 200);
        }
        if (!bool) {
            setTimeout(() => hideWord(word), animationDelay);
        }
    }
}

function takeNext(word) {
    return word.nextElementSibling || word.parentElement.children[0];
}

function switchWord(oldWord, newWord) {
    oldWord.classList.remove('is_on');
    oldWord.classList.add('is_off');
    newWord.classList.remove('is_off');
    newWord.classList.add('is_on');
}

// ============================================================
// Swiper Carousels (reemplaza OwlCarousel)
// ============================================================
function initSwiperCarousels() {
    document.querySelectorAll('.dt_swiper_carousel').forEach((el) => {
        const optionsAttr = el.getAttribute('data-swiper-options');
        if (!optionsAttr) return;

        try {
            const options = JSON.parse(optionsAttr);
            const swiperInstance = new Swiper(el, options);

            // Thumbnav para slider principal
            if (el.closest('.dt_slider--thumbnav')) {
                updateSliderThumbnav(el);
                swiperInstance.on('slideChange', () => updateSliderThumbnav(el));
            }
        } catch (e) {
            console.error('[ViceUnf] Error al inicializar Swiper:', e);
        }
    });
}

/**
 * @description Actualiza las imágenes de navegación prev/next del slider thumbnav.
 */
function updateSliderThumbnav(swiperEl) {
    const activeSlide = swiperEl.querySelector('.swiper-slide-active');
    if (!activeSlide) return;

    const nextSlide = swiperEl.querySelector('.swiper-slide-next');
    const prevSlide = swiperEl.querySelector('.swiper-slide-prev');

    const prevHolder = swiperEl.querySelector('.swiper-button-prev .imgholder');
    const nextHolder = swiperEl.querySelector('.swiper-button-next .imgholder');

    if (prevSlide && prevHolder) {
        const prevImg = prevSlide.querySelector('.dt_slider-item > img, .dt_slider-item img.dt-slider-bg');
        if (prevImg) prevHolder.style.backgroundImage = 'url(' + prevImg.getAttribute('src') + ')';
    }

    if (nextSlide && nextHolder) {
        const nextImg = nextSlide.querySelector('.dt_slider-item > img, .dt_slider-item img.dt-slider-bg');
        if (nextImg) nextHolder.style.backgroundImage = 'url(' + nextImg.getAttribute('src') + ')';
    }
}

// ============================================================
// Scroll Animations (reemplaza WOW.js + scrollAnimations plugin)
// ============================================================
function initScrollAnimations() {
    const elements = document.querySelectorAll('[data-animation]:not([data-animation-text]), [data-animation-box]');
    if (!elements.length) return;

    // Configurar animation-delay antes de observar
    elements.forEach((el) => {
        const textEls = el.querySelectorAll('[data-animation-text]');
        if (textEls.length) {
            textEls.forEach((textEl) => {
                const delay = textEl.getAttribute('data-animation-delay');
                if (delay) textEl.style.animationDelay = delay;
            });
        } else {
            const delay = el.getAttribute('data-animation-delay');
            if (delay) el.style.animationDelay = delay;
        }
    });

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting || entry.target.classList.contains('animated')) return;

            const el = entry.target;
            const textEls = el.querySelectorAll('[data-animation-text]');

            if (textEls.length) {
                el.classList.add('animated');
                textEls.forEach((textEl) => {
                    textEl.classList.add('animated');
                    const anim = textEl.getAttribute('data-animation');
                    if (anim) textEl.classList.add(anim);
                });
            } else {
                el.classList.add('animated');
                const anim = el.getAttribute('data-animation');
                if (anim) el.classList.add(anim);
            }

            observer.unobserve(el);
        });
    }, { threshold: 0, rootMargin: '-30% 0px' });

    elements.forEach((el) => observer.observe(el));
}

// ============================================================
// Scroll-to-Top (dt_uptop)
// ============================================================
function initScrollToTop() {
    const uptop = document.querySelector('.dt_uptop');
    if (!uptop) return;

    const progressPath = uptop.querySelector('path');
    if (!progressPath) return;

    const pathLength = progressPath.getTotalLength();
    progressPath.style.transition = progressPath.style.WebkitTransition = 'none';
    progressPath.style.strokeDasharray = pathLength + ' ' + pathLength;
    progressPath.style.strokeDashoffset = pathLength;
    progressPath.getBoundingClientRect();
    progressPath.style.transition = progressPath.style.WebkitTransition = 'stroke-dashoffset 10ms linear';

    function updateProgress() {
        const scroll = window.scrollY;
        const height = document.documentElement.scrollHeight - window.innerHeight;
        const progress = pathLength - (scroll * pathLength / height);
        progressPath.style.strokeDashoffset = progress;
    }

    updateProgress();

    window.addEventListener('scroll', () => {
        updateProgress();
        if (window.scrollY > 50) {
            uptop.classList.add('active');
        } else {
            uptop.classList.remove('active');
        }
    }, { passive: true });

    uptop.addEventListener('click', (e) => {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

// ============================================================
// GLightbox (reemplaza Fancybox)
// ============================================================
function initLightbox() {
    if (typeof GLightbox === 'undefined') return;
    if (!document.querySelector('.dt_lightbox_img')) return;

    GLightbox({
        selector: '.dt_lightbox_img',
        openEffect: 'fade',
        closeEffect: 'fade',
    });
}

// ============================================================
// Counter Animation (reemplaza jQuery Appear + $.animate)
// ============================================================
function initCounters() {
    const boxes = document.querySelectorAll('.dt_count_box');
    if (!boxes.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            const box = entry.target;
            if (box.classList.contains('counted')) return;

            box.classList.add('counted');
            const countEl = box.querySelector('.dt_count_text');
            if (!countEl) return;

            const targetVal = parseFloat(countEl.getAttribute('data-stop'));
            const duration = parseInt(countEl.getAttribute('data-speed'), 10) || 1500;
            const startVal = parseFloat(countEl.textContent) || 0;
            const startTime = performance.now();

            function animate(now) {
                const elapsed = now - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const current = startVal + (targetVal - startVal) * progress;

                countEl.textContent = progress < 1 ? Math.floor(current) : targetVal;

                if (progress < 1) {
                    requestAnimationFrame(animate);
                }
            }

            requestAnimationFrame(animate);
            observer.unobserve(box);
        });
    }, { threshold: 0.5 });

    boxes.forEach((box) => observer.observe(box));
}

// ============================================================
// Paroller (reemplaza jQuery Paroller — ~30 líneas Vanilla)
// ============================================================
function initParallaxScroll() {
    const parollerElements = document.querySelectorAll('.paroller .image, .paroller-2 .image');
    if (!parollerElements.length) return;

    function updateParallax() {
        const scrollTop = window.scrollY;
        const windowHeight = window.innerHeight;

        parollerElements.forEach((el) => {
            const container = el.closest('.paroller, .paroller-2');
            if (!container) return;

            const rect = container.getBoundingClientRect();
            const isVisible = rect.top < windowHeight && rect.bottom > 0;
            if (!isVisible) return;

            const isReverse = container.classList.contains('paroller-2');
            const factor = isReverse ? -0.1 : 0.1;
            const center = rect.top + rect.height / 2 - windowHeight / 2;
            const offset = center * factor;

            el.style.transform = 'translateY(' + offset + 'px)';
        });
    }

    window.addEventListener('scroll', updateParallax, { passive: true });
    updateParallax();
}

// ============================================================
// Parallax Scene (reemplaza Parallax.js — mousemove nativo)
// ============================================================
function initParallaxScene() {
    const scene = document.querySelector('.parallax-scene-1');
    if (!scene) return;

    // Si Parallax global existe (vendor cargado), usarlo directamente
    if (typeof Parallax !== 'undefined') {
        new Parallax(scene);
        return;
    }

    // Fallback: efecto parallax con mousemove nativo
    const layers = scene.querySelectorAll('[data-depth]');
    if (!layers.length) return;

    window.addEventListener('mousemove', (e) => {
        const centerX = window.innerWidth / 2;
        const centerY = window.innerHeight / 2;
        const moveX = (e.clientX - centerX) / centerX;
        const moveY = (e.clientY - centerY) / centerY;

        layers.forEach((layer) => {
            const depth = parseFloat(layer.getAttribute('data-depth')) || 0;
            const translateX = moveX * depth * 30;
            const translateY = moveY * depth * 30;
            layer.style.transform = 'translate(' + translateX + 'px, ' + translateY + 'px)';
        });
    });
}

// ============================================================
// Spotlight
// ============================================================
function initSpotlight() {
    const spotlight = document.querySelector('.dt_spotlight');
    if (!spotlight) return;

    let spotlightSize = 'transparent 10px, rgba(3, 4, 21, 0.78) 650px)';

    function updateSpotlight(e) {
        spotlight.style.backgroundImage =
            'radial-gradient(circle at ' +
            (e.pageX / window.innerWidth * 100) + '% ' +
            (e.pageY / window.innerHeight * 15) + '%, ' +
            spotlightSize;
    }

    window.addEventListener('mousemove', updateSpotlight);
    window.addEventListener('mousedown', (e) => {
        spotlightSize = 'transparent 10px, rgba(3, 4, 21, 0.78) 500px)';
        updateSpotlight(e);
    });
    window.addEventListener('mouseup', (e) => {
        spotlightSize = 'transparent 10px, rgba(3, 4, 21, 0.78) 650px)';
        updateSpotlight(e);
    });
}

// ============================================================
// Inicialización global
// ============================================================
window.addEventListener('load', () => {
    sitePreloader();
    initHeadline();
});

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', onDOMReady);
} else {
    onDOMReady();
}

function onDOMReady() {
    initSwiperCarousels();
    initScrollAnimations();
    initScrollToTop();
    initLightbox();
    initCounters();
    initParallaxScroll();
    initParallaxScene();
    initSpotlight();
}