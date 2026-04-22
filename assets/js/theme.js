'use strict';

/**
 * @description Módulo principal de UI del header para wptheme-vice-unf.
 * Gestiona sticky header, navegación mobile, búsqueda, sidebar y accesibilidad.
 * Migrado de jQuery a Vanilla JS.
 */
const VpinUnfTheme = {
    eventID: 'DtThemeJs',
    body: document.body,
    classes: {
        toggled: 'active',
        isOverlay: 'overlay--enabled',
        mobileMainMenuActive: 'dt_mobilenav-mainmenu--active',
        headerSearchActive: 'dt_header-search--active',
        headerSidebarActive: 'sidebar--active',
    },

    init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.onReady());
        } else {
            this.onReady();
        }
    },

    onReady() {
        this.headerHeight();
        this.topbarMobile();
        this.mobileNavRight();
        this.setupMobileSubmenus();
        this.menuFocusAccessibility();
        this.bindEvents();
    },

    setupMobileSubmenus() {
        const hasChildrenItems = document.querySelectorAll('.dt_mobilenav-mainmenu .dropdown, .dt_mobilenav-mainmenu .menu-item-has-children');
        hasChildrenItems.forEach(item => {
            if (!item.querySelector(':scope > .dt_mobilenav-dropdown-toggle')) {
                const btn = document.createElement('button');
                btn.className = 'dt_mobilenav-dropdown-toggle';
                btn.setAttribute('aria-expanded', 'false');
                btn.setAttribute('aria-label', 'Toggle submenu');
                
                const submenu = item.querySelector(':scope > .dropdown-menu, :scope > .sub-menu');
                if (submenu) {
                    submenu.setAttribute('aria-hidden', 'true');
                    item.insertBefore(btn, submenu);
                } else {
                    item.appendChild(btn);
                }
            }
        });
    },

    bindEvents() {
        document.addEventListener('click', (e) => {
            const target = e.target;

            if (target.closest('.dt_mobilenav-mainmenu-toggle') || target.closest('.dt_header-closemenu')) {
                this.menuToggleHandler(e);
            }

            if (target.closest('.dt_mobilenav-dropdown-toggle')) {
                this.verticalMobileSubMenuLinkHandle(e);
            }

            if (target.closest('.dt_header-closemenu')) {
                this.resetVerticalMobileMenu();
            }

            if (target.closest('.dt_navbar-search-toggle') || target.closest('.dt_search-close')) {
                this.searchPopupHandler(e);
            }

            if (target.closest('.dt_navbar-sidebar-toggle') || target.closest('.dt_sidebar-close')) {
                this.sidebarPopupHandler(e);
            }

            // Cerrar popup mobile al hacer click fuera
            this.hideHeaderMobilePopup(e);
        });

        window.addEventListener('scroll', () => this.scrollToSticky(), { passive: true });
        window.addEventListener('resize', () => this.headerHeight());
    },

    scrollToSticky() {
        const stickyEl = document.querySelector('.is--sticky');
        if (!stickyEl) return;
        if (window.scrollY >= 220) {
            stickyEl.classList.add('on');
        } else {
            stickyEl.classList.remove('on');
        }
    },

    headerHeight() {
        const wrapper = document.querySelector('.dt_header-navwrapper');
        const inners = document.querySelectorAll('.dt_header-navwrapperinner');
        if (!wrapper) return;

        const hasStickyChild = document.body.querySelector('div.is--sticky');
        if (!hasStickyChild) return;

        let maxHeight = 0;
        inners.forEach((inner) => {
            const h = inner.clientHeight;
            if (h > maxHeight) maxHeight = h;
        });
        wrapper.style.minHeight = maxHeight + 'px';
    },

    topbarMobile() {
        const content = document.querySelector('.dt_mobilenav-topbar-content');
        const widget = document.querySelector('.dt_header-widget');
        const toggle = document.querySelector('.dt_mobilenav-topbar-toggle');
        if (!content || !widget || !toggle) return;

        if (!widget.children.length) {
            toggle.style.display = 'none';
            return;
        }

        toggle.style.display = '';
        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            // slideToggle equivalente con max-height
            if (content.classList.contains('is-expanded')) {
                content.style.maxHeight = '0';
                content.classList.remove('is-expanded');
            } else {
                content.style.maxHeight = content.scrollHeight + 'px';
                content.classList.add('is-expanded');
            }
            toggle.classList.toggle('active');
        });

        this.topbarAccessibility();
    },

    topbarAccessibility() {
        const container = document.querySelector('.dt_mobilenav-topbar');
        const toggle = document.querySelector('.dt_mobilenav-topbar-toggle');
        if (!container || !toggle) return;

        const focusables = container.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
        const lastFocusable = focusables[focusables.length - 1];

        const links = container.getElementsByTagName('a');
        for (let i = 0; i < links.length; i++) {
            links[i].addEventListener('focus', handleTopbarFocus, true);
            links[i].addEventListener('blur', handleTopbarFocus, true);
        }

        function handleTopbarFocus() {
            let el = this;
            while (el && !el.classList.contains('dt_mobilenav-topbar')) {
                if (el.tagName.toLowerCase() === '*') {
                    el.classList.toggle('focus');
                }
                el = el.parentElement;
            }
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab' && toggle.classList.contains('active')) {
                if (e.shiftKey) {
                    if (document.activeElement === toggle) {
                        lastFocusable.focus();
                        e.preventDefault();
                    }
                } else {
                    if (document.activeElement === lastFocusable) {
                        toggle.focus();
                        e.preventDefault();
                    }
                }
            }
        });
    },

    mobileNavRight() {
        const source = document.querySelector('.dt_navbar-right .dt_navbar-cart-item');
        const target = document.querySelector('.dt_mobilenav-right .dt_navbar-list-right');
        if (source && target) {
            target.prepend(source.cloneNode(true));
        }
    },

    menuFocusAccessibility() {
        const navs = document.querySelectorAll('.dt_navbar-nav, .widget_nav_menu');
        navs.forEach((nav) => {
            const links = nav.querySelectorAll('a');
            links.forEach((link) => {
                link.addEventListener('focus', toggleParentFocus);
                link.addEventListener('blur', toggleParentFocus);
            });
        });

        function toggleParentFocus() {
            let el = this.parentElement;
            while (el) {
                if (el.tagName === 'UL' || el.tagName === 'LI') {
                    el.classList.toggle('focus');
                }
                el = el.parentElement;
                // Detener al llegar al contenedor de navegación
                if (el && (el.classList.contains('dt_navbar-nav') || el.classList.contains('widget_nav_menu'))) {
                    break;
                }
            }
        }
    },

    menuToggleHandler(e) {
        const menuContent = document.querySelector('.dt_mobilenav-mainmenu-content');
        const menuToggle = document.querySelector('.dt_mobilenav-mainmenu-toggle');
        if (!menuContent || !menuToggle) return;

        this.body.classList.toggle(this.classes.mobileMainMenuActive);
        this.body.classList.toggle(this.classes.isOverlay);
        menuToggle.classList.toggle(this.classes.toggled);

        const isActive = this.body.classList.contains(this.classes.mobileMainMenuActive);
        menuToggle.setAttribute('aria-expanded', isActive ? 'true' : 'false');
        menuContent.setAttribute('aria-hidden', isActive ? 'false' : 'true');

        if (isActive) {
            const closeBtn = document.querySelector('.dt_header-closemenu');
            if (closeBtn) closeBtn.focus();
        } else {
            menuToggle.focus();
        }

        this.menuAccessibility();
    },

    hideHeaderMobilePopup(e) {
        const menuToggle = document.querySelector('.dt_mobilenav-mainmenu-toggle');
        const mobileNav = document.querySelector('.dt_mobilenav-mainmenu');
        const menuContent = document.querySelector('.dt_mobilenav-mainmenu-content');
        if (!menuToggle || !mobileNav || !menuContent) return;

        if (!e.target.closest('.dt_mobilenav-mainmenu-toggle') &&
            !e.target.closest('.dt_mobilenav-mainmenu') &&
            this.body.classList.contains(this.classes.mobileMainMenuActive)) {

            this.body.classList.remove(this.classes.mobileMainMenuActive);
            this.body.classList.remove(this.classes.isOverlay);
            menuToggle.classList.remove(this.classes.toggled);
            
            menuToggle.setAttribute('aria-expanded', 'false');
            menuContent.setAttribute('aria-hidden', 'true');

            this.resetVerticalMobileMenu();
            e.stopPropagation();
        }
    },

    verticalMobileSubMenuLinkHandle(e) {
        e.preventDefault();
        const toggle = e.target.closest('.dt_mobilenav-dropdown-toggle');
        if (!toggle) return;

        // Eliminar timeout para ganar responsivity natural en mobile.
        const parent = toggle.parentElement;
        const submenu = toggle.nextElementSibling;
        
        if (!submenu) return;

        // SlideToggle Logic Native
        if (submenu.classList.contains('is-expanded')) {
            submenu.style.maxHeight = '0';
            submenu.classList.remove('is-expanded');
            if (parent) parent.classList.remove('current');
            toggle.setAttribute('aria-expanded', 'false');
            submenu.setAttribute('aria-hidden', 'true');
        } else {
            if (parent) parent.classList.add('current');
            submenu.style.display = 'block'; // Fallback por seguridad
            submenu.style.maxHeight = submenu.scrollHeight + 'px';
            submenu.classList.add('is-expanded');
            toggle.setAttribute('aria-expanded', 'true');
            submenu.setAttribute('aria-hidden', 'false');
            
            // Re-ejecutar altura si hay sub-sub menus anidados
            setTimeout(() => {
                submenu.style.maxHeight = submenu.scrollHeight + 100 + 'px';
            }, 300);
        }
    },

    resetVerticalMobileMenu() {
        const items = document.querySelectorAll('.dt_mobilenav-mainmenu .menu-item');
        const submenus = document.querySelectorAll('.dt_mobilenav-mainmenu .dropdown-menu');

        setTimeout(() => {
            items.forEach((item) => item.classList.remove('current'));
            submenus.forEach((menu) => {
                menu.style.maxHeight = '0';
                menu.classList.remove('is-expanded');
            });
        }, 250);
    },

    menuAccessibility() {
        const container = document.querySelector('.dt_mobilenav-mainmenu-content');
        const closeBtn = document.querySelector('.dt_header-closemenu:not(.off--layer)');
        if (!container || !closeBtn) return;

        const focusables = container.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
        const lastFocusable = focusables[focusables.length - 1];

        const links = container.getElementsByTagName('a');
        for (let i = 0; i < links.length; i++) {
            links[i].addEventListener('focus', handleMenuFocus, true);
            links[i].addEventListener('blur', handleMenuFocus, true);
        }

        function handleMenuFocus() {
            let el = this;
            while (el && !el.classList.contains('dt_mobilenav-mainmenu-inner')) {
                if (el.tagName.toLowerCase() === 'li') {
                    el.classList.toggle('focus');
                }
                el = el.parentElement;
            }
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                if (e.shiftKey) {
                    if (document.activeElement === closeBtn) {
                        lastFocusable.focus();
                        e.preventDefault();
                    }
                } else {
                    if (document.activeElement === lastFocusable) {
                        closeBtn.focus();
                        e.preventDefault();
                    }
                }
            }
        });
    },

    searchPopupHandler(e) {
        const toggle = document.querySelector('.dt_navbar-search-toggle');
        const field = document.querySelector('.dt_search-field');

        this.body.classList.toggle(this.classes.headerSearchActive);
        this.body.classList.toggle(this.classes.isOverlay);

        if (this.body.classList.contains(this.classes.headerSearchActive)) {
            if (field) field.focus();
        } else {
            if (toggle) toggle.focus();
        }

        this.searchPopupAccessibility();
    },

    searchPopupAccessibility() {
        const headers = document.querySelectorAll('.search--header');
        headers.forEach((container) => {
            const searchField = container.querySelector('.dt_search-field');
            const focusables = container.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            const lastFocusable = focusables[focusables.length - 1];
            if (!searchField || !lastFocusable) return;

            const buttons = container.getElementsByTagName('button');
            for (let i = 0; i < buttons.length; i++) {
                buttons[i].addEventListener('focus', handleSearchFocus, true);
                buttons[i].addEventListener('blur', handleSearchFocus, true);
            }

            function handleSearchFocus() {
                let el = this;
                while (el && !el.classList.contains('search--header')) {
                    if (el.tagName.toLowerCase() === 'input') {
                        el.classList.toggle('focus');
                    }
                    el = el.parentElement;
                }
            }

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Tab') {
                    if (e.shiftKey) {
                        if (document.activeElement === searchField) {
                            lastFocusable.focus();
                            e.preventDefault();
                        }
                    } else {
                        if (document.activeElement === lastFocusable) {
                            searchField.focus();
                            e.preventDefault();
                        }
                    }
                }
            });
        });
    },

    sidebarPopupHandler(e) {
        const toggle = document.querySelector('.dt_navbar-sidebar-toggle');
        const closeBtn = document.querySelector('.dt_sidebar-close');

        this.body.classList.toggle(this.classes.headerSidebarActive);
        this.body.classList.toggle(this.classes.isOverlay);
        if (toggle) toggle.classList.toggle(this.classes.toggled);

        if (this.body.classList.contains(this.classes.headerSidebarActive)) {
            if (closeBtn) closeBtn.focus();
        } else {
            if (toggle) toggle.focus();
        }

        this.sidebarPopupAccessibility();
    },

    sidebarPopupAccessibility() {
        const container = document.querySelector('.dt_sidebar');
        const closeBtn = document.querySelector('.dt_sidebar-close:not(.off--layer)');
        if (!container || !closeBtn) return;

        const focusables = container.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
        const lastFocusable = focusables[focusables.length - 1];

        const buttons = container.getElementsByTagName('button');
        for (let i = 0; i < buttons.length; i++) {
            buttons[i].addEventListener('focus', handleSidebarFocus, true);
            buttons[i].addEventListener('blur', handleSidebarFocus, true);
        }

        function handleSidebarFocus() {
            let el = this;
            while (el && !el.classList.contains('dt_sidebar-inner')) {
                if (el.tagName.toLowerCase() === 'input') {
                    el.classList.toggle('focus');
                }
                el = el.parentElement;
            }
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                if (e.shiftKey) {
                    if (document.activeElement === closeBtn) {
                        lastFocusable.focus();
                        e.preventDefault();
                    }
                } else {
                    if (document.activeElement === lastFocusable) {
                        closeBtn.focus();
                        e.preventDefault();
                    }
                }
            }
        });
    },
};

VpinUnfTheme.init();

/**
 * @description Plugin Load More — muestra items progresivamente.
 * Reescrito de jQuery $.fn.btnloadmore a clase ES6 Vanilla JS.
 */
class LoadMore {
    /**
     * @param {HTMLElement} container - Contenedor padre de los items.
     * @param {object} [options] - Opciones de configuración.
     */
    constructor(container, options = {}) {
        this.container = container;
        const colAttr = container.getAttribute('data-col');
        const defaultShow = colAttr ? parseInt(colAttr, 10) : 4;

        /** @type {{ showItem: number, whenClickBtn: number, textBtn: string, classBtn: string, delayToScroll: number }} */
        this.options = {
            showItem: defaultShow,
            whenClickBtn: defaultShow,
            textBtn: 'See More',
            classBtn: '',
            delayToScroll: 2000,
            ...options,
        };

        this.items = Array.from(container.children);
        this.init();
    }

    init() {
        // Ocultar todos los items primero
        this.items.forEach((item) => (item.style.display = 'none'));

        // Mostrar los primeros N items
        this.items.slice(0, this.options.showItem).forEach((item) => (item.style.display = ''));

        // Crear botón "Ver más" si hay items ocultos
        const hiddenCount = this.items.filter((item) => item.style.display === 'none').length;
        if (hiddenCount > 0) {
            this.createButton();
        }
    }

    createButton() {
        const wrapper = document.createElement('div');
        wrapper.className = 'dt-row dt-text-center dt-mt-5';
        wrapper.style.alignItems = 'center';
        wrapper.innerHTML = `
            <div class="dt-col-12">
                <a href="javascript:void(0);" class="dt-btn dt-btn-primary dt-btn-loadmore ${this.options.classBtn}">
                    <i class="fa fa-rotate-right dt-mr-1"></i> ${this.options.textBtn}
                </a>
            </div>`;

        this.container.insertAdjacentElement('afterend', wrapper);
        this.btnWrapper = wrapper;
        this.btn = wrapper.querySelector('.dt-btn-loadmore');

        this.btn.addEventListener('click', (e) => {
            e.preventDefault();
            this.showNext();
        });
    }

    showNext() {
        const hidden = this.items.filter((item) => item.style.display === 'none');
        const toShow = hidden.slice(0, this.options.whenClickBtn);

        toShow.forEach((item) => {
            item.style.display = '';
            item.style.opacity = '0';
            item.style.transition = 'opacity 0.4s ease';
            requestAnimationFrame(() => (item.style.opacity = '1'));
        });

        // Ocultar botón si no quedan items
        const remaining = this.items.filter((item) => item.style.display === 'none').length;
        if (remaining === 0 && this.btnWrapper) {
            this.btnWrapper.style.transition = 'opacity 0.5s ease';
            this.btnWrapper.style.opacity = '0';
            this.btnWrapper.addEventListener('transitionend', () => this.btnWrapper.remove(), { once: true });
        }

        // Scroll suave al último item visible
        const lastVisible = this.items.filter((item) => item.style.display !== 'none').pop();
        if (lastVisible) {
            lastVisible.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
}

// Auto-inicializar LoadMore en contenedores con data-col
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-col]').forEach((container) => {
        new LoadMore(container);
    });
});
