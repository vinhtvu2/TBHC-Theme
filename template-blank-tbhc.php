<?php
/**
 * Template Name: Blank Tbhc (PageBuilder)
 **/
?>
<?php get_header(); the_post();?>
<?php 
	if(is_page() && get_the_title() == 'staff-profiles'){
		remove_filter( 'the_content', 'wpautop' );
		add_filter( 'the_content', 'wpautop' , 99);
	}
	the_content();
?>
	<div class="container-shadow">
		<span></span>
	</div>
</div><!--.container [hidden rage]-->
<?php get_footer();?>