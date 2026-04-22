<?php
/**
 * Title: Footer Institucional con Widgets
 * Slug: vpinunf/footer-widgets-grid
 * Description: Pie de página institucional en 4 columnas — equivalente FSE-nativo a los footer widgets clásicos.
 * Categories: vpinunf-blocks, footer
 * Keywords: footer, pie, institucional, widgets, columnas, vpinunf
 * Block Types: core/template-part
 * Inserter: true
 */
?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|50"}}},"backgroundColor":"vpinunf-primary","textColor":"white","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-vpinunf-primary-background-color has-white-color has-text-color has-background">

    <!-- wp:columns {"style":{"spacing":{"blockGap":"var:preset|spacing|50"}}} -->
    <div class="wp-block-columns">

        <!-- wp:column {"width":"30%"} -->
        <div class="wp-block-column" style="flex-basis:30%">
            <!-- wp:site-logo {"width":120} /-->
            <!-- wp:paragraph {"style":{"typography":{"fontSize":"var:preset|font-size|sm"}},"textColor":"white"} -->
            <p class="has-white-color has-text-color">Vicepresidencia de Investigación — Universidad Nacional de Frontera.</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"width":"23.33%"} -->
        <div class="wp-block-column" style="flex-basis:23.33%">
            <!-- wp:heading {"level":5,"style":{"typography":{"fontSize":"var:preset|font-size|base"}},"textColor":"vpinunf-secondary"} -->
            <h5 class="wp-block-heading has-vpinunf-secondary-color has-text-color">Navegación</h5>
            <!-- /wp:heading -->
            <!-- wp:navigation {"overlayMenu":"never","style":{"typography":{"fontSize":"var:preset|font-size|sm"}},"layout":{"type":"flex","orientation":"vertical"}} /-->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"width":"23.33%"} -->
        <div class="wp-block-column" style="flex-basis:23.33%">
            <!-- wp:heading {"level":5,"style":{"typography":{"fontSize":"var:preset|font-size|base"}},"textColor":"vpinunf-secondary"} -->
            <h5 class="wp-block-heading has-vpinunf-secondary-color has-text-color">Investigación</h5>
            <!-- /wp:heading -->
            <!-- wp:paragraph {"style":{"typography":{"fontSize":"var:preset|font-size|sm"}},"textColor":"white"} -->
            <p class="has-white-color has-text-color">Proyectos de investigación, convocatorias y publicaciones de la UNF.</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->

        <!-- wp:column {"width":"23.33%"} -->
        <div class="wp-block-column" style="flex-basis:23.33%">
            <!-- wp:heading {"level":5,"style":{"typography":{"fontSize":"var:preset|font-size|base"}},"textColor":"vpinunf-secondary"} -->
            <h5 class="wp-block-heading has-vpinunf-secondary-color has-text-color">Contacto</h5>
            <!-- /wp:heading -->
            <!-- wp:paragraph {"style":{"typography":{"fontSize":"var:preset|font-size|sm"}},"textColor":"white"} -->
            <p class="has-white-color has-text-color">📍 Sullana, Piura — Perú<br>📧 investigacion@unf.edu.pe</p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:column -->

    </div>
    <!-- /wp:columns -->

    <!-- wp:separator {"backgroundColor":"vpinunf-legacy-pri2","style":{"spacing":{"margin":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|40"}}}} -->
    <hr class="wp-block-separator has-text-color has-vpinunf-legacy-pri2-color has-alpha-channel-opacity has-vpinunf-legacy-pri2-background-color has-background"/>
    <!-- /wp:separator -->

    <!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"var:preset|font-size|xs"}},"textColor":"white"} -->
    <p class="has-text-align-center has-white-color has-text-color">© <?php echo esc_html(date('Y')); ?> Vicepresidencia de Investigación — Universidad Nacional de Frontera. Todos los derechos reservados.</p>
    <!-- /wp:paragraph -->

</div>
<!-- /wp:group -->
