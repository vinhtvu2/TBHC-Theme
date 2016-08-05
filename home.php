<?php get_header();?>
<?php $options = get_option(THEME_OPTIONS_NAME);?>
<?php $page    = get_page_by_title('Home');?>
	<div class="row" id="home" data-template="home-nodescription" role="main">
		<div class="col-sm-15">
			<?php
				$args = array(
					'numberposts' => 1,
					'post_type' => 'centerpiece',
				);
				$latest_centerpiece = get_posts($args);
				echo do_shortcode('[centerpiece id="'.$latest_centerpiece[0]->ID.'"]');
				?>
		</div>
		<div class="col-sm-5 col-md-xpad col-sm-xpad" id="home_leftcol">
			<h2>Spotlight</h2>
			<?=frontpage_spotlights()?>
			<p class="screen-only"><a href="<?=get_permalink(get_page_by_title('Spotlight Archives', OBJECT, 'page')->ID);?>" class="home_col_morelink">Spotlight Archive</a></p>
		</div>
		<div class="col-sm-5 col-md-xpad col-sm-xpad" id="home_centercol">
			<h2>Opportunity</h2>
			<?=frontpage_opportunities()?>
			<p class="screen-only"><a href="<?=get_permalink(get_page_by_title('Opportunity Archives', OBJECT, 'page')->ID);?>" class="home_col_morelink">Opportunity Archive</a></p>
		</div>
		<div class="col-sm-5 col-md-xpad col-sm-xpad" id="home_rightcol">
			<h2>Upcoming Events</h2>
			<?php esi_include('do_shortcode','[events-widget]'); ?>
			<?php esi_include('output_weather_data'); ?>
		</div>
	</div>
<?php get_footer();?>
