<?php disallow_direct_load('single.php');?>
<?php get_header(); the_post();?>

	<div class="row page-content" id="<?=$post->post_name?>">
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
		<div id="contentcol" class="col-md-15 col-sm-15">
			<article role="main">
				<?=the_content();?>
			</article>
		</div>
	</div>
	<div class="container-shadow">
		<span></span>
	</div>
</div>
<?php get_footer();?>
