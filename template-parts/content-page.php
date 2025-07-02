<?php
/**
 * Template part for displaying page content in page.php.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package SoftMe
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('dt_post_item dt_posts--one dt-mb-4'); ?>>
	<?php /* Eliminado el thumbnail porque ya se usa como fondo en breadcrumb */ ?>
	<div class="inner">
		<div class="meta">
			<ul>
				<li>
					<div class="date">
						<i class="far fa-calendar-alt dt-mr-2" aria-hidden="true"></i> 
						<?php echo esc_html(get_the_date('j \d\e F \d\e Y')); ?>
					</div>
				</li>
				<li>
					<div class="catetag">
						<i class="fas fa-folder dt-mr-1" aria-hidden="true"></i>
						<?php the_category(' , '); ?>
					</div>
				</li>
			</ul>
		</div>
		<?php     
			if ( is_single() ) :
				the_title('<h4 class="title">', '</h4>' );
			else:
				the_title( sprintf( '<h4 class="title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' );
			endif; 
		?> 
		<div class="content">
			<?php 
				the_content( 
						sprintf( 
							__( 'Read More', 'softme' ), 
							'<span class="screen-reader-text">  '.esc_html(get_the_title()).'</span>' 
						) 
					);
			?>
		</div>
		<?php if ( is_single() ) : ?>
			<div class="meta_bottom">
				<div class="tags">
					<?php the_tags('', ' ', ''); ?>
				</div>
				<div class="comments_count"><a href="#respond" rel="bookmark" class="count"><?php esc_html_e('Comments','softme'); ?> <?php echo esc_html(get_comments_number($post->ID)); ?></a></div>
			</div>
		<?php endif;  ?>
	</div>
</article>