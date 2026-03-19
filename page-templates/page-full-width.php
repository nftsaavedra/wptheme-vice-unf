<?php
/**
Template Name: Fullwidth Page
**/

get_header();
?>
<section class="dt-py-default page-full-width-section">
	<div class="dt-container">
		<main id="primary" class="site-main">
			<?php 		
				the_post(); the_content(); 
				
				if( $post->comment_status == 'open' ) { 
					 comments_template( '', true ); // show comments 
				}
			?>
		</main>
	</div>
</section>
<?php get_footer(); ?>

