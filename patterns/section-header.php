<?php
/**
 * Title: Cabecera de Sección con Título
 * Slug: vpinunf/section-header
 * Description: Encabezado de sección con título h2, línea decorativa y descripción opcional.
 * Categories: vpinunf-blocks, text
 * Keywords: título, sección, cabecera, encabezado
 * Inserter: true
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|30"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull">

    <!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontSize":"var:preset|font-size|xl"}},"textColor":"vpinunf-primary"} -->
    <h2 class="wp-block-heading has-text-align-center has-vpinunf-primary-color has-text-color">Título de la Sección</h2>
    <!-- /wp:heading -->

    <!-- wp:separator {"backgroundColor":"vpinunf-secondary","className":"is-style-wide","style":{"layout":{"selfStretch":"fixed","flexSize":"80px"}}} -->
    <hr class="wp-block-separator has-text-color has-vpinunf-secondary-color has-alpha-channel-opacity has-vpinunf-secondary-background-color has-background is-style-wide"/>
    <!-- /wp:separator -->

    <!-- wp:paragraph {"align":"center","textColor":"vpinunf-legacy-sec","style":{"typography":{"fontSize":"var:preset|font-size|md"}}} -->
    <p class="has-text-align-center has-vpinunf-legacy-sec-color has-text-color">Descripción breve de la sección. Edita este texto para añadir contexto relevante.</p>
    <!-- /wp:paragraph -->

</div>
<!-- /wp:group -->
