<?php
/**
 * La plantilla para mostrar el footer.
 *
 * @package ViceUnf
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
    </div><!-- #content .site-content -->

    <footer id="dt_footer" class="dt_footer dt_footer--one">
        <?php do_action( 'viceunf_footer_widget' ); ?>
        <?php do_action( 'viceunf_footer_bottom' ); ?>
    </footer>

    <?php do_action( 'viceunf_top_scroller' ); ?>
</div><!-- #page .site -->
<?php wp_footer(); ?>
</body>
</html>
