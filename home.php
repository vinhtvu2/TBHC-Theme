<?php get_header();?>
<?php $options = get_option(THEME_OPTIONS_NAME);?>
<?php $page    = get_page_by_title('Home');?>
	<div class="row" id="home" data-template="home-nodescription" role="main">
		<div class="col-sm-5" id="home_leftcol">
			<h2>Spotlights<hr/></h2>
			<?=frontpage_spotlights()?>
			<div class="moreBtnPad"><div class="screen-only moreBtn"><a href="<?=get_permalink(get_page_by_title('Spotlight Archives', OBJECT, 'page')->ID);?>" class="home_col_morelink">Spotlight Archive</a></div></div>
		</div>
		<div class="col-sm-5" id="home_centercol">
			<h2>Opportunities<hr/></h2>
			<?=frontpage_opportunities()?>
			<div class="moreBtnPad"><div class="screen-only moreBtn"><a href="<?=get_permalink(get_page_by_title('Opportunities', OBJECT, 'page')->ID);?>" class="home_col_morelink">More Opportunities</a></div></div>
		</div>
		<div class="col-sm-5" id="home_rightcol">
			<h2>Upcoming Events<hr/></h2>
			<?php do_shortcode('[events-widget]'); ?>
			<?php //esi_include('output_weather_data'); ?>
		</div>
	</div>
	<!--<div class="container-shadow">
		<span></span>
	</div>-->
</div><!--[container]-->
<?php get_footer();?>
