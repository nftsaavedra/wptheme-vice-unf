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
        </div><!-- .site-content -->
    </div><!-- .site-content-inner -->

    <footer id="dt_footer" class="dt_footer dt_footer--one">
        <?php do_action( 'viceunf_footer_widget' ); ?>
        <?php do_action( 'viceunf_footer_bottom' ); ?>
    </footer>

    <?php do_action( 'viceunf_top_scroller' ); ?>
</div><!-- .dt-site-wrapper -->
<?php wp_footer(); ?>
</body>
</html>
