<?php get_header();?>
<?php $options = get_option(THEME_OPTIONS_NAME);?>
<?php $page    = get_page_by_title('Home');?>
	<div class="row" id="home" data-template="home-nodescription" role="main">
		<?=frontpage_opportunities()?>
		<?=frontpage_interests()?>
 		<?=frontpage_spotlights()?>
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
