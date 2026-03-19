<?php 
/**
Template Name: Frontpage
*/

get_header();

get_template_part('/template-parts/site','slider');

get_template_part('/template-parts/site','investigation');

get_template_part('/template-parts/site','about');

get_template_part('/template-parts/site','event');

get_template_part('/template-parts/site','production');

get_template_part('/template-parts/site','post');

get_template_part('/template-parts/site','partner');

// Gutenberg Hybrid Content Area
$front_content = get_the_content();
if ( ! empty( trim( $front_content ) ) ) : ?>
	<div class="frontpage-gutenberg-content">
		<?php the_post(); the_content(); ?>
	</div>
<?php endif;

get_footer();

?>