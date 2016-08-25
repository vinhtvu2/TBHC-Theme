<?php
/**
 * Template Name: Blank Tbhc (PageBuilder)
 **/
?>
<?php get_header(); the_post();?>
<?php 
	if(is_page() && $post = get_page_by_title( 'staff-profiles')){
		
	}
	the_content();
?>
</div><!--.container [hidden rage]-->
<?php get_footer();?>