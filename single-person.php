<?php disallow_direct_load('single-person.php');?>
<?php get_header(); the_post();?>

	<div class="row page-content person-profile" id="<?=$post->post_name?>">
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
		<div class="col-md-3 col-sm-3 details">
			<?
				global $wp;
				$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
				$title = get_post_meta($post->ID, 'person_jobtitle', True);
				$time = get_post_meta($post->ID, 'dist_speaker_time', True);
				$date = get_post_meta($post->ID, 'dist_speaker_date', True);
				$location = get_post_meta($post->ID, 'dist_speaker_location', True);
				$image_url = get_featured_image_url($post->ID, 'person-grid-image');
				$email = get_post_meta($post->ID, 'person_email', True);
				$phones = Person::get_phones($post);
				$office = get_post_meta($post->ID, 'person_office', True);
			?>
			<img src="<?=$image_url ? $image_url : get_bloginfo('stylesheet_directory').'/static/img/no-photo.jpg'?>" />
		</div>
		<div class="col-md-12 col-sm-12">
			<article role="main">
				<h2><?=$post->post_title?><?=($title == '') ?: ' - '.$title ?></h2>
				<div class="contact">
				<?if(strpos(wp_get_referer(), "distinguished-speaker") === false){ //and type = staff?>
					<ul class="list-unstyled">	
					<?
					if(count($phones)) {
						foreach($phones as $phone) { ?>
							<li><i class="glyphicon glyphicon-earphone"></i><a href="tel:<?=$phone?>" class="phones"><?=$phone?></a></li>
						<? }
						} ?>
						<? if($email != '') { ?>
							<li><i class="glyphicon glyphicon-envelope"></i><a class="email" href="mailto:<?=$email?>"><?=$email?></a></li>
						<? } ?>
						<? if($office != '') { ?>
							<li><i class="glyphicon glyphicon-map-marker"></i><span class="office"><?=$office?></span></li>
						<? } ?>
					</ul>
				<? }
				if(strpos(wp_get_referer(), "distinguished-speaker") > 0){ //or type = dist speaker ?> 
					<ul class="list-unstyled">
						<? //if($title != '') { ?>
							<!--<li><i class="glyphicon glyphicon-info-sign"></i><span class="title"><?=//$title?></span></li> Removed all this to get rid of second title-->
						<? //} ?>
						<? if($time != '') { ?>
							<li><i class="glyphicon glyphicon-time"></i><span class="time"><?=$time?></span></li>
						<? } ?>
						<? if($date != '') { ?>
							<li><i class="glyphicon glyphicon-calendar"></i><span class="date"><?=$date->format('l, F jS, Y')?></span></li>
						<? } ?>
						<? if($location != '') { ?>
							<li><i class="glyphicon glyphicon-map-marker"><!--glyphicon-globe"> replaced with a gps marker --></i><span class="location"><?=$location?></span></li>
						<? } ?>
					</ul>	
				<? } ?>
				</div>
				<?=$content = str_replace(']]>', ']]>', apply_filters('the_content', $post->post_content))?>
			</article>
		</div>
	</div>
</div>

<?php get_footer();?>
