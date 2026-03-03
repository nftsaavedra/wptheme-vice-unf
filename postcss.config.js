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
          'viceunf-post-nav__card', 'viceunf-post-nav__card--prev', 'viceunf-post-nav__img', 'viceunf-post-nav__content', 'viceunf-post-nav__label', 'viceunf-post-nav__title', 'viceunf-post-nav__card--next',
          'viceunf-related-card', 'viceunf-related-card__thumb', 'viceunf-related-card__info', 'viceunf-related-card__meta', 'viceunf-related-card__title', 'viceunf-related-card__excerpt'
        ],
        deep: [
          /^(wp-block-)/, /^(has-)/, /^(is-)/, /^(search-)/,
          /^(wc-)/, /^(swiper-)/, /^(viceunf-single-doc)/,
          /^(viceunf-card-)/, /^(viceunf-bg-)/,
        ],
        greedy: [
          /^(page-id-)/, /^(postid-)/, /^(archive-)/, /^(category-)/, /^(tag-)/
        ]
      }
    })
  ]
};
