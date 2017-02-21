<?php get_header();?>
<?php $options = get_option(THEME_OPTIONS_NAME);?>
<?php $page    = get_page_by_title('Home');?>
	<div class="row" id="home" data-template="home-nodescription" role="main">
		<?=frontpage_opportunities()?>
		<?=frontpage_interests()?>
		<section id="spotlights">
			<div class="spotlights_title_wrap">
				<h2>Spotlights<hr/></h2>
				<a href="<?=get_permalink(get_page_by_title('Spotlight Archives', OBJECT, 'page')->ID)?>" class="home_col_morelink">Spotlight Archive</a>				
			</div>
			<?=frontpage_spotlights()?>
		</section>
		<section id="events">
			<h2>Upcoming Events</h2>
			<?php do_shortcode('[events-widget]'); ?>
			<?php //esi_include('output_weather_data'); ?>
		</section>
	</div>
	<!--<div class="container-shadow">
		<span></span>
	</div>-->
</div><!--[container]-->
<?php get_footer();?>
