<?php
/**
 * Title: Sidebar — Entradas Recientes
 * Slug: viceunf/sidebar-recent-posts
 * Description: Alternativa FSE-nativa al sidebar clásico. Muestra entradas recientes con imagen, título y fecha.
 * Categories: viceunf-blocks, query
 * Keywords: sidebar, entradas, recientes, blog, fse
 * Inserter: true
 */
?>
<!-- wp:group {"style":{"spacing":{"padding":{"all":"var:preset|spacing|40"}}},"backgroundColor":"white","layout":{"type":"constrained"}} -->
<div class="wp-block-group has-white-background-color has-background">

    <!-- wp:heading {"level":4,"style":{"typography":{"fontSize":"var:preset|font-size|md"}},"textColor":"viceunf-primary"} -->
    <h4 class="wp-block-heading has-viceunf-primary-color has-text-color">Entradas Recientes</h4>
    <!-- /wp:heading -->

    <!-- wp:separator {"backgroundColor":"viceunf-secondary","style":{"layout":{"selfStretch":"fixed","flexSize":"48px"}}} -->
    <hr class="wp-block-separator has-text-color has-viceunf-secondary-color has-alpha-channel-opacity has-viceunf-secondary-background-color has-background"/>
    <!-- /wp:separator -->

    <!-- wp:query {"query":{"perPage":5,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":false},"displayLayout":{"type":"list"}} -->
    <div class="wp-block-query">
        <!-- wp:post-template -->
            <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20","padding":{"bottom":"var:preset|spacing|30"}}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
            <div class="wp-block-group">
                <!-- wp:post-featured-image {"isLink":true,"width":"72px","height":"72px","style":{"border":{"radius":"6px"}}} /-->
                <!-- wp:group {"style":{"spacing":{"blockGap":"4px"}},"layout":{"type":"constrained"}} -->
                <div class="wp-block-group">
                    <!-- wp:post-title {"isLink":true,"level":5,"style":{"typography":{"fontSize":"var:preset|font-size|sm"}}} /-->
                    <!-- wp:post-date {"style":{"typography":{"fontSize":"var:preset|font-size|xs"}},"textColor":"viceunf-legacy-pri2"} /-->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->
        <!-- /wp:post-template -->
    </div>
    <!-- /wp:query -->

</div>
<!-- /wp:group -->
