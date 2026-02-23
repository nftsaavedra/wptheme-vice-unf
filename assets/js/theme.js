!(function ($, b) {
    "use strict";
    var a = {
        eventID: "DtThemeJs",
        document: $(document),
        window: $(window),
        body: $("body"),
        classes: {
            toggled: "active",
            isOverlay: "overlay--enabled",
            mobileMainMenuActive: "dt_mobilenav-mainmenu--active",
            headerSearchActive: "dt_header-search--active",
            headerSidebarActive: "sidebar--active",
        },
        init: function () {
            this.document.on("ready", this.documentReadyRender.bind(this)),
                this.document.on("ready", this.menuFocusAccessibility.bind(this)),
                this.document.on("ready", this.headerHeight.bind(this)),
                this.document.on("ready", this.topbarMobile.bind(this)),
                this.document.on("ready", this.mobileNavRight.bind(this)),
                this.window.on("ready", this.documentReadyRender.bind(this));
        },
        documentReadyRender: function () {
            this.document
                .on("click." + this.eventID, ".dt_mobilenav-mainmenu-toggle", this.menuToggleHandler.bind(this))
                .on("click." + this.eventID, ".dt_header-closemenu", this.menuToggleHandler.bind(this))
                .on("click." + this.eventID, this.hideHeaderMobilePopup.bind(this))
                .on("click." + this.eventID, ".dt_mobilenav-dropdown-toggle", this.verticalMobileSubMenuLinkHandle.bind(this))
                .on("click." + this.eventID, ".dt_header-closemenu", this.resetVerticalMobileMenu.bind(this))
                .on("hideHeaderMobilePopup." + this.eventID, this.resetVerticalMobileMenu.bind(this))
                .on("click." + this.eventID, ".dt_navbar-search-toggle", this.searchPopupHandler.bind(this))
                .on("click." + this.eventID, ".dt_search-close", this.searchPopupHandler.bind(this))
                .on("click." + this.eventID, ".dt_navbar-sidebar-toggle", this.sidebarPopupHandler.bind(this))
                .on("click." + this.eventID, ".dt_sidebar-close", this.sidebarPopupHandler.bind(this)),
                this.window.on("scroll." + this.eventID, this.scrollToSticky.bind(this)).on("resize." + this.eventID, this.headerHeight.bind(this));
        },
        scrollToSticky: function (b) {
            var a = $(".is--sticky");
            this.window.scrollTop() >= 220 ? a.addClass("on") : a.removeClass("on");
        },
        headerHeight: function (d) {
            var a = $(".dt_header-navwrapper"),
                b = $(".dt_header-navwrapperinner"),
                c = 0;
            $("body").find("div").hasClass("is--sticky") &&
                (b.each(function () {
                    var a = this.clientHeight;
                    a > c && (c = a);
                }),
                    a.css("min-height", c));
        },
        topbarAccessibility: function () {
            var b,
                a,
                d,
                c = document.querySelector(".dt_mobilenav-topbar");
            var f = document.querySelector(".dt_mobilenav-topbar-toggle"),
                e = c.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'),
                g = e[e.length - 1];
            if (!c) return !1;
            for (a = 0, d = (b = c.getElementsByTagName("a")).length; a < d; a++) b[a].addEventListener("focus", h, !0), b[a].addEventListener("blur", h, !0);

            function h() {
                for (var a = this; -1 === a.className.indexOf("dt_mobilenav-topbar");)
                    "*" === a.tagName.toLowerCase() && (-1 !== a.className.indexOf("focus") ? (a.className = a.className.replace(" focus", "")) : (a.className += " focus")), (a = a.parentElement);
            }
            document.addEventListener("keydown", function (a) {
                ("Tab" === a.key || 9 === a.keyCode) && f.classList.contains("active") && (a.shiftKey ? document.activeElement === f && (g.focus(), a.preventDefault()) : document.activeElement === g && (f.focus(), a.preventDefault()));
            });
        },
        topbarMobile: function () {
            var c = $(".dt_mobilenav-topbar-content"),
                b = $(".dt_header-widget"),
                a = $(".dt_mobilenav-topbar-toggle");
            !b.children().length > 0
                ? a.hide()
                : (a.show(),
                    a.on("click", function (b) {
                        c.slideToggle(), a.toggleClass("active"), b.preventDefault();
                    }),
                    this.topbarAccessibility());
        },
        mobileNavRight: function () {
            $(".dt_navbar-right .dt_navbar-cart-item").clone().prependTo(".dt_mobilenav-right .dt_navbar-list-right");
        },
        menuFocusAccessibility: function (a) {
            $(".dt_navbar-nav, .widget_nav_menu")
                .find("a")
                .on("focus blur", function () {
                    $(this).parents("ul, li").toggleClass("focus");
                });
        },
        menuToggleHandler: function (c) {
            var b = $(".dt_mobilenav-mainmenu-content"),
                a = $(".dt_mobilenav-mainmenu-toggle");
            this.body.toggleClass(this.classes.mobileMainMenuActive),
                this.body.toggleClass(this.classes.isOverlay),
                a.toggleClass(this.classes.toggled),
                b.fadeToggle(),
                this.body.hasClass(this.classes.mobileMainMenuActive) ? $(".dt_header-closemenu").focus() : a.focus(),
                this.menuAccessibility();
        },
        hideHeaderMobilePopup: function (a) {
            var b = $(".dt_mobilenav-mainmenu-toggle"),
                c = $(".dt_mobilenav-mainmenu");
            !$(a.target).closest(b).length &&
                !$(a.target).closest(c).length &&
                this.body.hasClass(this.classes.mobileMainMenuActive) &&
                (this.body.removeClass(this.classes.mobileMainMenuActive),
                    this.body.removeClass(this.classes.isOverlay),
                    b.removeClass(this.classes.toggled),
                    mobileMainmenuContent.fadeOut(),
                    this.document.trigger("hideHeaderMobilePopup." + this.eventID),
                    a.stopPropagation());
        },
        verticalMobileSubMenuLinkHandle: function (a) {
            a.preventDefault();
            var b = $(a.currentTarget);
            b.closest(".dt_mobilenav-mainmenu .dt_navbar-mainmenu"),
                b.parents(".dropdown-menu").length,
                this.isRTL,
                setTimeout(function () {
                    b.parent().toggleClass("current"), b.next().slideToggle();
                }, 250);
        },
        resetVerticalMobileMenu: function (a) {
            $(".dt_mobilenav-mainmenu .dt_navbar-mainmenu");
            var b = $(".dt_mobilenav-mainmenu  .menu-item"),
                c = $(".dt_mobilenav-mainmenu .dropdown-menu");
            setTimeout(function () {
                b.removeClass("current"), c.hide();
            }, 250);
        },
        menuAccessibility: function () {
            var b,
                a,
                d,
                c = document.querySelector(".dt_mobilenav-mainmenu-content");
            var f = document.querySelector(".dt_header-closemenu:not(.off--layer)"),
                e = c.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'),
                g = e[e.length - 1];
            if (!c) return !1;
            for (a = 0, d = (b = c.getElementsByTagName("a")).length; a < d; a++) b[a].addEventListener("focus", h, !0), b[a].addEventListener("blur", h, !0);

            function h() {
                for (var a = this; -1 === a.className.indexOf("dt_mobilenav-mainmenu-inner");)
                    "li" === a.tagName.toLowerCase() && (-1 !== a.className.indexOf("focus") ? (a.className = a.className.replace(" focus", "")) : (a.className += " focus")), (a = a.parentElement);
            }
            document.addEventListener("keydown", function (a) {
                ("Tab" === a.key || 9 === a.keyCode) && (a.shiftKey ? document.activeElement === f && (g.focus(), a.preventDefault()) : document.activeElement === g && (f.focus(), a.preventDefault()));
            });
        },
        searchPopupHandler: function (c) {
            var a = $(".dt_navbar-search-toggle"),
                b = $(".dt_search-field");
            this.body.toggleClass(this.classes.headerSearchActive), this.body.toggleClass(this.classes.isOverlay), this.body.hasClass(this.classes.headerSearchActive) ? b.focus() : a.focus(), this.searchPopupAccessibility();
        },
        searchPopupAccessibility: function () {
            var headers = document.querySelectorAll(".search--header");
            headers.forEach(function (c) {
                var f = c.querySelector(".dt_search-field");
                var e = c.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
                var g = e[e.length - 1];
                if (!f || !g) return;
                var buttons = c.getElementsByTagName("button");
                for (var i = 0; i < buttons.length; i++) {
                    buttons[i].addEventListener("focus", handleFocus, true);
                    buttons[i].addEventListener("blur", handleBlur, true);
                }
                function handleFocus() {
                    var el = this;
                    while (el && !el.classList.contains("search--header")) {
                        if (el.tagName.toLowerCase() === "input") {
                            if (el.classList.contains("focus")) {
                                el.classList.remove("focus");
                            } else {
                                el.classList.add("focus");
                            }
                        }
                        el = el.parentElement;
                    }
                }
                function handleBlur() {
                    handleFocus.call(this);
                }
                document.addEventListener("keydown", function (a) {
                    if (a.key === "Tab" || a.keyCode === 9) {
                        if (a.shiftKey) {
                            if (document.activeElement === f) {
                                g.focus();
                                a.preventDefault();
                            }
                        } else {
                            if (document.activeElement === g) {
                                f.focus();
                                a.preventDefault();
                            }
                        }
                    }
                });
            });
        },
        sidebarPopupHandler: function (d) {
            var a = $(".dt_navbar-sidebar-toggle"),
                b = $(".dt_sidebar"),
                c = $(".dt_sidebar-close");
            this.body.toggleClass(this.classes.headerSidebarActive),
                this.body.toggleClass(this.classes.isOverlay),
                a.toggleClass(this.classes.toggled),
                this.body.hasClass(this.classes.headerSidebarActive) ? /*b.addClass('1e3'),*/ c.focus() : /*b.fadeOut(1e3),*/ a.focus(),
                this.sidebarPopupAccessibility();
        },
        sidebarPopupAccessibility: function () {
            var b,
                a,
                d,
                c = document.querySelector(".dt_sidebar");
            var f = document.querySelector(".dt_sidebar-close:not(.off--layer)"),
                e = c.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'),
                g = e[e.length - 1];
            if (!c) return !1;
            for (a = 0, d = (b = c.getElementsByTagName("button")).length; a < d; a++) b[a].addEventListener("focus", h, !0), b[a].addEventListener("blur", h, !0);

            function h() {
                for (var a = this; -1 === a.className.indexOf("dt_sidebar-inner");)
                    "input" === a.tagName.toLowerCase() && (-1 !== a.className.indexOf("focus") ? (a.className = a.className.replace("focus", "")) : (a.className += " focus")), (a = a.parentElement);
            }
            document.addEventListener("keydown", function (a) {
                ("Tab" === a.key || 9 === a.keyCode) && (a.shiftKey ? document.activeElement === f && (g.focus(), a.preventDefault()) : document.activeElement === g && (f.focus(), a.preventDefault()));
            });
        },
    };
    a.init();
})(jQuery, window.asConfig),
    (function ($) {
        $.fn.btnloadmore = function (a) {
            var cl = $(this).attr("data-col");
            var b = {
                showItem: cl,
                whenClickBtn: cl,
                textBtn: "See More",
                classBtn: "",
                setCookies: !1,
                delayToScroll: 2e3,
            },
                a = $.extend(b, a);
            this.each(function () {
                var c = $(this),
                    a = $(c.children());
                a.hide(),
                    a.slice(0, b.showItem).show(),
                    a.filter(":hidden").length > 0 &&
                    c.after(
                        '<div class="dt-row dt-text-center dt-mt-5" style="align-items: center;"><div class="dt-col-12"><a href="javascript:void(0);" class="dt-btn dt-btn-primary dt-btn-loadmore ' +
                        b.classBtn +
                        '"><i class="fa fa-rotate-right dt-mr-1"></i> ' +
                        b.textBtn +
                        "</a></div></div>"
                    ),
                    $(document).on("click", ".dt-btn-loadmore", function (c) {
                        c.preventDefault(),
                            a.filter(":hidden").slice(0, b.whenClickBtn).slideDown(),
                            0 == a.filter(":hidden").length && $(".dt-btn-loadmore").parent().parent().fadeOut("slow"),
                            $("html, body").animate(
                                {
                                    scrollTop: a.filter(":visible").last().offset().top,
                                },
                                b.delayToScroll
                            );
                    });
            });
        };
    })(jQuery);

/* 
    ScrollAnimations
*/
(function (t, i, a, n) {
    var e = "scrollAnimations",
        s = { offset: 0.7 };
    var o;
    function m(a, n) {
        if (a) {
            this.element = a;
            this.animationElements = [];
            this.triggerPoint = null;
            this.lastScrollPos = -1;
            this.options = t.extend({}, s, n);
            this._defaults = s;
            this._name = e;
            i.onload = this.init();
        }
    }
    m.prototype = {
        init: function () {
            var a = this;
            var n = t(this.element);
            i.requestAnimationFrame =
                i.requestAnimationFrame ||
                i.mozRequestAnimationFrame ||
                i.webkitRequestAnimationFrame ||
                i.msRequestAnimationFrame ||
                function (t) {
                    setTimeout(t, 1e3 / 60);
                };
            a.setup(n);
            a.updatePage();
            t(i).on("resize", function () {
                a.resize();
            });
        },
        resize: function () {
            var t = this;
            clearTimeout(o);
            o = setTimeout(function () {
                t.setTriggerpoint();
            }, 50);
        },
        setTriggerpoint: function () {
            this.triggerPoint = i.innerHeight * this.options.offset;
        },
        setup: function (i) {
            this.setTriggerpoint();
            var a = t(i),
                n = a.find("[data-animation-text]");
            if (n.length) {
                n.each(function () {
                    var i = t(this);
                    var a = i.attr("data-animation-delay");
                    i.css({ "-webkit-animation-delay": a, "-moz-animation-delay": a, "-ms-animation-delay": a, "-o-animation-delay": a, "animation-delay": a });
                });
            } else {
                var e = a.attr("data-animation-delay");
                a.css({ "-webkit-animation-delay": e, "-moz-animation-delay": e, "-ms-animation-delay": e, "-o-animation-delay": e, "animation-delay": e });
            }
            this.animationElements.push(a);
        },
        updatePage: function () {
            var t = this;
            this.animateElements();
            requestAnimationFrame(this.updatePage.bind(this));
        },
        animateElements: function () {
            var a = this;
            var n = i.pageYOffset;
            if (n === this.lastScrollPos) return;
            this.lastScrollPos = n;
            t(a.animationElements).each(function () {
                var i = t(this),
                    e = i.find("[data-animation-text]");
                if (i.hasClass("animated") || n < i.offset().top - a.triggerPoint) return;
                if (e.length) {
                    i.addClass("animated");
                    e.each(function () {
                        t(this).addClass("animated").addClass(t(this).attr("data-animation"));
                    });
                } else {
                    i.addClass("animated").addClass(i.attr("data-animation"));
                }
            });
        },
    };
    t.fn[e] = function (i) {
        return this.each(function () {
            if (!t.data(this, "plugin_" + e)) {
                t.data(this, "plugin_" + e, new m(this, i));
            }
        });
    };
    if (typeof define === "function" && define.amd) {
        define(function () {
            return m;
        });
    }
})(jQuery, window, document);