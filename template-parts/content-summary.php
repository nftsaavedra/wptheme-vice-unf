<?php

/**
 * =================================================================
 * Template part for displaying post summaries as cards.
 * =================================================================
 *
 * Used by archive.php, home.php, and search.php to display a consistent
 * summary card for each post in a list.
 *
 * @package ViceUnf
 */

// Definimos las clases de diseño para la tarjeta.
$post_classes = ['dt_post_item', 'dt_posts--one', 'dt-mb-4'];
?>

<article id="post-<?php the_ID(); ?>" <?php post_class($post_classes); ?>>

  <?php if (has_post_thumbnail()) : ?>
    <a href="<?php the_permalink(); ?>">
      <div class="image">
        <?php
        // Usamos 'large' para una buena calidad de imagen.
        // Esta función genera srcset para imágenes adaptables, mejorando el rendimiento.
        the_post_thumbnail('large', ['alt' => the_title_attribute('echo=0')]);
        ?>
      </div>
    </a>
  <?php endif; ?>

  <div class="inner">
    <div class="meta">
      <ul>
        <li>
          <div class="date">
            <i class="far fa-calendar-alt dt-mr-2" aria-hidden="true"></i>
            <?php echo get_the_date(); ?>
          </div>
        </li>
      </ul>
    </div>
    <div class="catetag">
      <i class="fas fa-folder dt-mr-1" aria-hidden="true"></i>
      <?php the_category(', '); ?>
    </div>

    <h4 class="title">
      <a href="<?php the_permalink(); ?>" rel="bookmark">
        <?php the_title(); ?>
      </a>
    </h4>

    <div class="content">
      <?php
      // Extracto controlado a 35 palabras. Puedes ajustar este número.
      echo '<p>' . wp_kses_post(wp_trim_words(get_the_content(), 35, '...')) . '</p>';
      ?>
      <a href="<?php the_permalink(); ?>" class="more-link">Leer más</a>
    </div>
  </div>
</article>