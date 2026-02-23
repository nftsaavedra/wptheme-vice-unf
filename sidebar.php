<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package ViceUnf
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<aside id="dt-sidebar" class="dt-col-lg-4 dt_widget-area">
    <?php if ( is_active_sidebar( 'viceunf-sidebar-primary' ) ) : ?>
        <?php dynamic_sidebar( 'viceunf-sidebar-primary' ); ?>
    <?php endif; ?>
</aside>
