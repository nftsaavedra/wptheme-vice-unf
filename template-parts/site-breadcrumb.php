<?php 
$softme_hs_site_breadcrumb    = get_theme_mod('softme_hs_site_breadcrumb','1');
// Prioridad: imagen destacada de la entrada/página
if ( is_singular() && has_post_thumbnail() ) {
    $softme_breadcrumb_bg_img = get_the_post_thumbnail_url( get_the_ID(), 'full' );
} else {
    $softme_breadcrumb_bg_img = get_theme_mod('softme_breadcrumb_bg_img',esc_url(get_template_directory_uri() .'/assets/images/background/page_title.jpg'));
}
$softme_breadcrumb_type    = get_theme_mod('softme_breadcrumb_type','theme');
if($softme_hs_site_breadcrumb == '1'):	
?>
<section id="dt_pagetitle" class="dt_pagetitle dt-text-center">
	<div class="dt_spotlight"></div>
	<div class="pattern-layer">
		<div class="pattern-1"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/shape/white_curved_line.png"></div>
		<div class="pattern-2"></div>
		<div class="pattern_lines">
			<div class="pattern_line pattern_line_1"></div>
			<div class="pattern_line pattern_line_2"></div>
		 </div>
	 </div>
	<div class="bg__ayer parallax_none parallax-bg" data-parallax='{"y": 100}' style="background-image: url(<?php echo esc_url($softme_breadcrumb_bg_img); ?>);"></div>
	<div class="dt-container">
		<div class="dt_pagetitle_content"> 
			<?php if($softme_breadcrumb_type == 'yoast' && (function_exists('yoast_breadcrumb'))):  yoast_breadcrumb(); ?>
			<?php elseif($softme_breadcrumb_type == 'rankmath' && (function_exists('rank_math_the_breadcrumbs'))):  rank_math_the_breadcrumbs(); ?>	
			<?php elseif($softme_breadcrumb_type == 'navxt' && (function_exists('bcn_display'))):  bcn_display(); else: ?>
				<ul class="dt_pagetitle_breadcrumb">
					<?php softme_page_header_breadcrumbs(); ?>
				</ul>
				<div class="title">
					<?php
						if(is_home() || is_front_page()) {
							echo '<h1>'; single_post_title(); echo '</h1>';
						} else {
							softme_theme_page_header_title();
						} 
					?>
				</div>
			<?php endif; ?>		
		</div>
	</div>
</section>
<?php endif; ?>