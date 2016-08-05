<?php
/**
 * Template Name: Two Column, Left Sidebar
 **/
?>
<?php get_header(); the_post();?>
	<div class="row page-content" id="<?=$post->post_name?>">
		<div class="col-sm-15">
			<div id="page-title">
				<div class="row">
					<div class="col-sm-12">
						<h1><?php the_title(); ?></h1>
					</div>
					<?php esi_include( 'output_weather_data', 'col-md-3 col-sm-3' ); ?>
				</div>
			</div>
		</div>

		<?=get_page_subheader($post)?>

		<div class="col-sm-12 col-sm-push-3" id="contentcol">
			<article role="main">
				<?php if (get_post_meta($post->ID, 'page_subheader', TRUE) !== '') { ?><div class="rightcol_subheader_fix"></div><?php } ?>
				<?php the_content();?>
			</article>
		</div>

		<div id="sidebar_left" class="col-sm-3 col-sm-pull-12" role="navigation">
			<?=get_sidebar('left');?>
		</div>
	</div>
<?php get_footer();?>
