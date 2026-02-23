<?php 
$viceunf_hs_breadcrumb = get_theme_mod( 'viceunf_hs_breadcrumb', '1' );
// Prioridad: imagen destacada de la entrada/página
if ( is_singular() && has_post_thumbnail() ) {
    $viceunf_breadcrumb_bg_img = get_the_post_thumbnail_url( get_the_ID(), 'full' );
} else {
    $viceunf_breadcrumb_bg_img = get_theme_mod( 'viceunf_breadcrumb_bg_img', esc_url( get_stylesheet_directory_uri() . '/assets/images/background/page_title.jpg' ) );
}
$viceunf_breadcrumb_type = get_theme_mod( 'viceunf_breadcrumb_type', 'theme' );
if ( '1' === $viceunf_hs_breadcrumb ) :	
?>
<section id="dt_pagetitle" class="dt_pagetitle dt-text-center">
	<div class="dt_spotlight"></div>
	<div class="pattern-layer">
		<div class="pattern-1"><img src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/assets/images/shape/white_curved_line.png"></div>
		<div class="pattern-2"></div>
		<div class="pattern_lines">
			<div class="pattern_line pattern_line_1"></div>
			<div class="pattern_line pattern_line_2"></div>
		 </div>
	 </div>
	<div class="bg__ayer parallax_none parallax-bg" data-parallax='{"y": 100}' style="background-image: url(<?php echo esc_url( $viceunf_breadcrumb_bg_img ); ?>);"></div>
	<div class="dt-container">
		<div class="dt_pagetitle_content"> 
			<?php if ( 'yoast' === $viceunf_breadcrumb_type && function_exists( 'yoast_breadcrumb' ) ) :  yoast_breadcrumb(); ?>
			<?php elseif ( 'rankmath' === $viceunf_breadcrumb_type && function_exists( 'rank_math_the_breadcrumbs' ) ) :  rank_math_the_breadcrumbs(); ?>	
			<?php elseif ( 'navxt' === $viceunf_breadcrumb_type && function_exists( 'bcn_display' ) ) :  bcn_display(); else: ?>
				<ul class="dt_pagetitle_breadcrumb">
					<?php viceunf_page_header_breadcrumbs(); ?>
				</ul>
				<div class="title">
					<?php
					if ( is_home() || is_front_page() ) {
						echo '<h1>'; single_post_title(); echo '</h1>';
					} else {
						viceunf_page_header_title();
					} 
					?>
				</div>
			<?php endif; ?>		
		</div>
	</div>
</section>
<?php endif; ?>