import purgecssPkg from '@fullhuman/postcss-purgecss';
const purgecss = purgecssPkg.default || purgecssPkg.purgeCSSPlugin || purgecssPkg;
import autoprefixer from 'autoprefixer';

export default {
  plugins: [
    autoprefixer,
    purgecss({
      // Rutas a tus archivos
      // Rutas a tus archivos
      content: [
        './*.php',
        './**/*.php',
        './*.js',
        './**/*.js',
      ],

      // Tu lista segura de clases
      safelist: {
        standard: [
          'admin-bar', 'blog', 'body', 'comment-author', 'comment-body',
          'comment-list', 'current-menu-item', 'error404', 'gallery', 'home',
          'logged-in', 'menu-item', 'page', 'pagination', 'post', 'sticky',
          'widget', 'wp-caption', 'woocommerce', 'woocommerce-page',
          'product', 'aligncenter', 'alignleft', 'alignright', 'alignwide', 'alignfull',
          'wp-block-button__link', 'nav-links', 'page-numbers', 'current', 'dots', 'navigation',
          'vpinunf-post-nav__card', 'vpinunf-post-nav__card--prev', 'vpinunf-post-nav__img', 'vpinunf-post-nav__content', 'vpinunf-post-nav__label', 'vpinunf-post-nav__title', 'vpinunf-post-nav__card--next',
          'vpinunf-related-card', 'vpinunf-related-card__thumb', 'vpinunf-related-card__info', 'vpinunf-related-card__meta', 'vpinunf-related-card__title', 'vpinunf-related-card__excerpt'
        ],
        deep: [
          /^(wp-block-)/, /^(has-)/, /^(is-)/, /^(search-)/,
          /^(wc-)/, /^(swiper-)/, /^(vpinunf-single-doc)/,
          /^(vpinunf-card-)/, /^(vpinunf-bg-)/, /^(dt-text-)/,
          /^(dt_mobilenav)/, /^(overlay--enabled)/, /^(active)/,
        ],
        greedy: [
          /^(page-id-)/, /^(postid-)/, /^(archive-)/, /^(category-)/, /^(tag-)/
        ]
      }
    })
  ]
};
