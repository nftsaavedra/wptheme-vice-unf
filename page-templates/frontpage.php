<?php 
/**
Template Name: Frontpage
*/

get_header();

do_action( 'Desert_Companion_Softme_frontpage', false);

get_template_part('/template-parts/site','slider');

get_template_part('/template-parts/site','investigation');

get_template_part('/template-parts/site','about');

get_template_part('/template-parts/site','event');

get_template_part('/template-parts/site','production');

get_template_part('/template-parts/site','post');

get_template_part('/template-parts/site','partner');

get_footer();

?>