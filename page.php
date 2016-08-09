<?php get_header(); the_post();?>
	<div class="row page-content" id="<?php echo $post->post_name; ?>">
		<div class="col-md-15 col-sm-15">
			<div id="page-title">
				<div class="row">
					<div class="col-md-12 col-sm-12">
						<h1><?php the_title(); ?></h1>
					</div>
					<?php esi_include( 'output_weather_data', 'col-md-3 col-sm-3' ); ?>
				</div>
			</div>
		</div>

		<?=get_page_subheader($post)?>

		<div class="col-sm-12 col-sm-push-3" id="contentcol">
			<article role="main">
				<?php the_content();?>
				&nbsp;
			</article>
		</div>

		<div id="sidebar_left" class="col-sm-3 col-sm-pull-12" role="navigation">
			<?=get_sidebar('left');?>
		</div>
		
		<?php
			$theSubHeaderId = get_post_meta($post->ID, 'page_subheader', TRUE);
			$theSubHeaderPush = get_post_meta($theSubHeaderId, 'subheader_push_right_sidebar', TRUE);
			$pushRightSidebar = $theSubHeaderId != '' && $theSubHeaderPush == 'on';
		?>
		<div id="sidebar_right" class="col-md-3 col-sm-3 <?php if (!($pushRightSidebar)) { ?>notoppad<?php } ?>" role="complementary">
			<?=get_sidebar('right');?>
		</div>
	</div>
</div>
<?php get_footer();?>
