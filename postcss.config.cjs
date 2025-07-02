const purgecss = require("@fullhuman/postcss-purgecss");

module.exports = {
  plugins: [
    purgecss({
      // Rutas a tus archivos
      content: [
        "./**/*.php",
        "../softme/**/*.php", // Ruta al tema padre
        "./**/*.js",
        "../softme/**/*.js", // Ruta al tema padre
      ],

      // Lista de clases a proteger
      safelist: {
        standard: [
          "admin-bar",
          "blog",
          "body",
          "comment-author",
          "comment-body",
          "comment-list",
          "current-menu-item",
          "error404",
          "gallery",
          "home",
          "logged-in",
          "menu-item",
          "page",
          "pagination",
          "post",
          "sticky",
          "widget",
          "wp-caption",
          "woocommerce",
          "woocommerce-page",
          "product",
          "aligncenter",
          "alignleft",
          "alignright",
          "alignwide",
          "alignfull",
          "wp-block-button__link",
        ],
        deep: [
          /^(wp-block-)/,
          /^(has-)/,
          /^(is-)/,
          /^(search-)/,
          /^(wc-)/,
          /^(swiper-)/,
        ],
        greedy: [
          /^(page-id-)/,
          /^(postid-)/,
          /^(archive-)/,
          /^(category-)/,
          /^(tag-)/,
        ],
      },
    }),
  ],
};
