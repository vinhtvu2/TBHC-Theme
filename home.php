<?php get_header();?>
<?php $options = get_option(THEME_OPTIONS_NAME);?>
<?php $page    = get_page_by_title('Home');?>
	<div class="row" id="home" data-template="home-nodescription" role="main">
		<section id="opportunities">
			<h2>Opportunities</h2>
			<?=frontpage_opportunities()?>
		</section>
		<?if(function_exists("Tbhc_Interests_Plugin_Post_Type_render")){
			Tbhc_Interests_Plugin_Post_Type_render();
		}?>
		<section id="spotlights">
			<h2>Spotlights<hr/></h2>
			<?=frontpage_spotlights()?>
			<div class="moreBtnPad"><div class="screen-only moreBtn"><a href="<?=get_permalink(get_page_by_title('Spotlight Archives', OBJECT, 'page')->ID);?>" class="home_col_morelink">Spotlight Archive</a></div></div>
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
