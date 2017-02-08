<?php
/**
 * Create a javascript slideshow of each top level element in the
 * shortcode.  All attributes are optional, but may default to less than ideal
 * values.  Available attributes:
 *
 * height     => css height of the outputted slideshow, ex. height="100px"
 * width      => css width of the outputted slideshow, ex. width="100%"
 * transition => length of transition in milliseconds, ex. transition="1000"
 * cycle      => length of each cycle in milliseconds, ex cycle="5000"
 * animation  => The animation type, one of: 'slide' or 'fade'
 *
 * Example:
 * [slideshow height="500px" transition="500" cycle="2000"]
 * <img src="http://some.image.com" .../>
 * <div class="robots">Robots are coming!</div>
 * <p>I'm a slide!</p>
 * [/slideshow]
 **/
function sc_slideshow($attr, $content=null){
	$content = cleanup(str_replace('<br />', '', $content));
	$content = DOMDocument::loadHTML($content);
	$html    = $content->childNodes->item(1);
	$body    = $html->childNodes->item(0);
	$content = $body->childNodes;

	# Find top level elements and add appropriate class
	$items = array();
	foreach($content as $item){
		if ($item->nodeName != '#text'){
			$classes   = explode(' ', $item->getAttribute('class'));
			$classes[] = 'slide';
			$item->setAttribute('class', implode(' ', $classes));
			$items[] = $item->ownerDocument->saveXML($item);
		}
	}

	$animation = ($attr['animation']) ? $attr['animation'] : 'slide';
	$height    = ($attr['height']) ? $attr['height'] : '100px';
	$width     = ($attr['width']) ? $attr['width'] : '100%';
	$tran_len  = ($attr['transition']) ? $attr['transition'] : 1000;
	$cycle_len = ($attr['cycle']) ? $attr['cycle'] : 5000;

	ob_start();
	?>
	<div
		class="slideshow <?=$animation?>"
		data-tranlen="<?=$tran_len?>"
		data-cyclelen="<?=$cycle_len?>"
		style="height: <?=$height?>; width: <?=$width?>;"
	>
		<?php foreach($items as $item):?>
		<?=$item?>
		<?php endforeach;?>
	</div>
	<?php
	$html = ob_get_clean();

	return $html;
}
add_shortcode('slideshow', 'sc_slideshow');


function sc_search_form() {
	ob_start();
	?>
	<div class="search">
		<?get_search_form()?>
	</div>
	<?
	return ob_get_clean();
}
add_shortcode('search_form', 'sc_search_form');


/**
 * Include the defined publication, referenced by pub title:
 *
 *     [publication name="Where are the robots Magazine"]
 **/
function sc_publication($attr, $content=null){
	$pub      = @$attr['pub'];
	$pub_name = @$attr['name'];
	$pub_id   = @$attr['id'];

	// Get the post data
	if (!$pub and is_numeric($pub_id)){
		$pub = get_post($pub);
	}
	if (!$pub and $pub_name){
		$pub = get_page_by_title($pub_name, OBJECT, 'publication');
	}

	$url = get_post_meta($pub->ID, "publication_url", True);
	//$url = str_replace('https:', 'http:', $url); // Force http // y?

	// Get the Issuu DocumentID from the url provided
	$docID = json_decode(file_get_contents($url.'?issuu-data=docID'));
	$docID = $docID->docID;

	// If no docID is found, assume that the publication url is invalid
	if ($docID == NULL) { return 'DocID not found. Is the publication URL valid? Please use URLs from http://publications.ucf.edu.'; }

	// Output for an Issuu thumbnail, based on docID
	$issuu_thumb = "<img src='http://image.issuu.com/".$docID."/jpg/page_1_thumb_large.jpg' alt='".$pub->post_title."' title='".$pub->post_title."' />";

	// If a featured image is set, use it; otherwise, get the thumbnail from issuu
	$thumb = (get_the_post_thumbnail($pub->ID, 'publication_thumb', TRUE) !== '') ? get_the_post_thumbnail($pub->ID, 'publication_thumb', TRUE) : $issuu_thumb;

	ob_start(); ?>

	<div class="pub">
		<a class="track pub-track" title="<?=$pub->post_title?>" data-toggle="modal" href="#pub-modal-<?=$pub->ID?>">

			<?=$thumb?>
		</a>
		<p class="pub-desc"><?=$pub->post_content?></p>
		<div class="modal fade" id="pub-modal-<?php echo $pub->ID; ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $pub->post_title; ?>" aria-hidden="true" style="height:auto !important;">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body">
						<iframe src="<?php echo $url; ?>" style="width:100% !important; height:100% !important;" scrolling="no"></iframe>
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
	return ob_get_clean();
}
add_shortcode('publication', 'sc_publication');


/**
 * Include the defined YouTube video, referenced by video title.
 *
 *     [video name="Where are the robots? (VIDEO!)"]
 **/
function sc_video($attr, $content=null){
	$video_name = @$attr['name'];
	$video_id   = @$attr['id'];
	$display	= $attr['display'] ? $attr['display'] : 'modal';

	if (!$video and is_numeric($video_id)){
		$video = get_post($video_id);
	}
	if (!$video and $video_name){
		$video = get_page_by_title($video_name, 'OBJECT', 'video');
	}

	$video_url   		= get_post_meta($video->ID, "video_url", true);
	$video_yt_id		= get_youtube_id($video_url);
	$video_description  = $video->post_content;
	$video_thumbnail    = wp_get_attachment_image(get_post_thumbnail_id($video->ID, 'medium'));
	$embed_url			= 'http://www.youtube.com/embed/'.$video_yt_id.'?wmode=transparent';

	switch ($display) {
		default:
			ob_start(); ?>

				<div class="video">
					<div class="icon">
						<a title="Watch <?php echo $video->post_title; ?>" alt="Watch <?php echo $video->post_title; ?>" data-toggle="modal" class="video-link" href="#modal-vid<?php echo $video->ID; ?>">
							<?php echo $video_thumbnail; ?>
						</a>
					</div>
					<div class="modal video-modal fade" id="modal-vid<?php echo $video->ID; ?>" tabindex="-1" role="dialog">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<a class="close" data-dismiss="modal">×</a>
									<h3><?php echo $video->post_title; ?></h3>
								</div>
								<div class="modal-body" data-src="<?php echo $embed_url; ?>">
								</div>
							</div>
						</div>
					</div>
					<h4>
						<a title="Watch <?php echo $video->post_title; ?>" alt="Watch <?php echo $video->post_title; ?>" data-toggle="modal" class="video-link" href="#modal-vid<?php echo $video->ID; ?>">
							<?php echo $video->post_title; ?>
						</a>
					</h4>
					<div class="video-desc"><?php echo $video_description; ?></div>
				</div>
			<?php
			return ob_get_clean();
			break;
		case 'embed':
			ob_start(); ?>
				<iframe type="text/html" width="640" height="390" src="<?php echo $embed_url; ?>" frameborder="0"></iframe>
			<?php
			return ob_get_clean();
			break;
	}
}
add_shortcode('video', 'sc_video');


/**
 * Person picture lists
 **/
 // sort by job title deans -> directors -> coordinators
function sc_person_picture_list($atts) {
	$atts['type']	= ($atts['type']) ? $atts['type'] : null;
	$row_size 		= ($atts['row_size']) ? (intval($atts['row_size'])) : 5;
	$categories		= ($atts['categories']) ? $atts['categories'] : null;
	$org_groups		= ($atts['org_groups']) ? $atts['org_groups'] : null;
	$limit			= ($atts['limit']) ? (intval($atts['limit'])) : -1;
	$join			= ($atts['join']) ? $atts['join'] : 'or';
	$people 		= sc_object_list(
						array(
							'type' => 'person',
							'limit' => $limit,
							'join' => $join,
							'categories' => $categories,
							'org_groups' => $org_groups
						),
						array(
							'objects_only' => True,
						));

					
	ob_start();

	?><div class="person-picture-list"><?
	$count = 0;
	foreach($people as $person) {

		$image_url = get_featured_image_url($person->ID);

		$link = ($person->post_content != '') ? True : False;
		if( ($count % $row_size) == 0) {
			if($count > 0) {
				?></div><?
			}
			?><div class="row"><?
		}

		?>
		<div class="col-md-2 col-sm-2 person-picture-wrap">
			<? if($link) {?><a href="<?=get_permalink($person->ID)?>"><? } ?>
				<img src="<?=$image_url ? $image_url : get_bloginfo('stylesheet_directory').'/static/img/no-photo.jpg'?>" />
				<div class="name"><?=Person::get_name($person)?></div>
				<div class="title"><?=get_post_meta($person->ID, 'person_jobtitle', True)?></div>
			<? if($link) {?></a><?}?>
		</div>
		<?
		$count++;
	}
	?>	</div>
	</div>
	<?
	return ob_get_clean();
}
add_shortcode('person-picture-list', 'sc_person_picture_list');

function specCharEscCallback($buffer){
		return htmlentities($buffer);
	}

/**
 * Custom Person List by Erik
 **/
function sc_person_profile_grid($atts) {
	$atts = array_map('specCharEscCallback', $atts);
	//remove_filter('the_content','wpautop');
	$atts['type']	= ($atts['type']) ? $atts['type'] : null;
	$row_size 		= ($atts['row_size']) ? (intval($atts['row_size'])) : 5;
	$mobile_row_size 	= ($atts['mobile_row_size']) ? (intval($atts['mobile_row_size'])) : 3;
	$categories		= ($atts['categories']) ? $atts['categories'] : null;
	$org_groups		= ($atts['org_groups']) ? $atts['org_groups'] : null;
	$org_groups2		= ($atts['org_groups2']) ? $atts['org_groups2'] : null;	
	$limit			= ($atts['limit']) ? (intval($atts['limit'])) : -1;
	$join			= ($atts['join']) ? $atts['join'] : 'or';
	$dropdown		= ($atts['dropdown']) ? $atts['dropdown'] : false;
	$dd_org_groups	= ($atts['dd_org_groups']) ? $atts['dd_org_groups'] : $org_groups;
	$dropdown2		= ($atts['dropdown2']) ? $atts['dropdown2'] : false;
	$dd2_org_groups	= ($atts['dd2_org_groups']) ? $atts['dd2_org_groups'] : NULL;	
	$show_org_groups	= ($atts['show_org_groups']) ? $atts['show_org_groups'] : false;
	$OGID			= get_term_by('slug', $dd_org_groups, 'org_groups')->term_id;
	$OGID2			= get_term_by('slug', $dd2_org_groups, 'org_groups');
	$OGID2			= $OGID2 ? $OGID2->term_id : false;
	$show_option_all	= ($atts['show_option_all']) ? $atts['show_option_all'] : null;
	$show_option_all2	= ($atts['show_option_all2']) ? $atts['show_option_all2'] : null;		
	$operator		= ($atts['operator']) ? $atts['operator'] : NULL;
	$people 		= sc_object_list(
		array(
			'type' => 'person',
			'limit' => $limit,
			'join' => $join,
			'categories' => $categories,
			'org_groups' => $org_groups2 ? $org_groups.' '.$org_groups2 : $org_groups,
			'orderby' => 'person_orderby_name',
			'order' => 'ASC',
			'operator' => $operator
		),
	array(
		'objects_only' => True,
	));
	if(strpos($org_groups, "dist") > -1){
		usort($people, function($a, $b){
			$a_date = new DateTime(get_post_meta($a->ID, 'dist_speaker_date', true));
			$b_date = new DateTime(get_post_meta($b->ID, 'dist_speaker_date', true));
			$a_title = get_post_meta($a->ID, 'person_jobtitle', true);
			$b_title = get_post_meta($b->ID, 'person_jobtitle', true);			
			$a_date = $a_date->getTimestamp();
			$b_date = $b_date->getTimestamp();
			if($a_date == $b_date){
				return strcmp($a_title, $b_title);
			}
			return $a_date < $b_date ? -1 : 1;
		});
	}else if(strpos($org_groups, "ambass") > -1){
		usort($people, function($a, $b){ // tentative peer-ambassador name sort
			$res = strcmp($a->post_title, $b->post_title);
			//echo "\n".$a->post_title." is ".$res." than ".$b->post_title;
			return $res;
		});
	}else{
		usort($people, function($a, $b){
			$a_title = get_post_meta($a->ID, 'person_jobtitle', true);
			$b_title = get_post_meta($b->ID, 'person_jobtitle', true);
			$haystack = ["Dean", "Director", "Coordinator"];
			$res = 0;
			if(preg_match('/Dean|Director|Coordinator/', $a_title) || preg_match('/Dean|Director|Coordinator/', $b_title)){
				foreach ($haystack as $item)	{
					$a_r = strpos($a_title, $item);
					$b_r = strpos($b_title, $item);
					if($a_r >= 0 && $a_r !== false){
						if($b_r >= 0 && $b_r !== false){
							//print($a_title." and ".$b_title." contain ".$item.".\n");
							$res = $a_r < $b_r ? -1 : $a_r == $b_r ? 0 : 1; // both contain
							break;
						}else{
							//print("Only ".$a_title." contains ".$item.".\n");					
							$res = -1; // only a contains
							break;
						}
					}else{
						if($b_r >= 0 && $b_r !== false){
							//print("Only ".$b_title." contains ".$item.".\n");										
							$res = 1; // only b contains
							break;
						}
					}
				}
			}else{
				// neither contains
				//print("Neither ".$a_title." nor ".$b_title." contain ".$item.".\n");										
				$res = $a_title < $b_title ? -1 : $a_title == $b_title ? 0 : 1;
			}
			return $res;
		});
	}
	ob_start("specCharEscCallback");
	// Added row_size attribute to end of line below (omj it's soooo long...)
	?><div class="person-profile-grid" data-url="<?=admin_url( 'admin-ajax.php' )?>" data-group="<?=esc_attr($dd_org_groups)?>" data-group2="<?=esc_attr($dd2_org_groups)?>" data-shwgrp="<?=esc_attr($show_org_groups)?>" data-jn="<?=esc_attr($join)?>" data-oprtr="<?=esc_attr($operator)?>" data-allopt="<?=esc_attr($show_option_all)?>" data-allopt2="<?=esc_attr($show_option_all2)?>" data-rowsize="<?=esc_attr($row_size)?>">
		<? if($dropdown){ 
			$args = array(
				'taxonomy'	=>	'org_groups',
				'value_field'	=>	'slug',
				'class'	=>	'person-profile-grid-dropdown form-control',
				'id'	=>	'dd_org_groups',
				'name'	=>	'dd_org_groups',
				'echo'	=> false,
				'selected'	=>	$org_groups,
				'child_of'	=>	$OGID,
			);
			if(!empty($show_option_all)){
				$args['show_option_all'] = $show_option_all;
			}			
			echo str_replace(
				'<select',
				'<select onchange="getProfilesForGrid(this.value'.($dropdown2 ? ', $(\'#dd2_org_groups\').val()' : '').')"',
				wp_dropdown_categories($args)
			);
		} 
		if($dropdown2 && $OGID2){ 
			$args2 = array(
				'taxonomy'	=>	'org_groups',
				'value_field'	=>	'slug',
				'class'	=>	'person-profile-grid-dropdown form-control',
				'id'	=>	'dd2_org_groups',
				'name'	=>	'dd2_org_groups',			
				'echo'	=> false,
				'selected'	=>	$org_groups2,
				'child_of'	=>	$OGID2,
			);	
			if(!empty($show_option_all2)){
				$args['show_option_all2'] = $show_option_all2;
			}						
			echo str_replace(
				'<select',
				'<select onchange="getProfilesForGrid($(\'#dd_org_groups\').val(), this.value)"',
				wp_dropdown_categories($args2)
			);
		} 
		$count = 0;
		foreach($people as $person) {
			if(strtolower($show_org_groups) == "true"){
				$term_list = wp_get_post_terms($person->ID, 'org_groups');
																									
				$terms = array_filter($term_list, function($thng) use($OGID, $OGID2) {			
					return !empty($thng->parent) && ($thng->parent == $OGID || ($OGID2 != false && $thng->parent == $OGID2));
				});
				$terms = implode(", ", array_map(function($blrp){
					return $blrp->name;
				}, $terms));
			}
			$imageT = get_image_tag(get_post_thumbnail_id($person->ID), 'alt text', 'title text', 'None', 'profile-grid-image');
			$imageT = preg_replace( '/(width)=\"\d*\"\s/', "width=\"100%\"", $imageT );
			$imageT = preg_replace( '/(height)=\"\d*\"\s/', "", $imageT );
			$image = wp_get_attachment_image_src(get_post_thumbnail_id($person->ID), 'profile-grid-image', false);
			$image_url = get_featured_image_url($person->ID);
			$link = ($person->post_content != '') ? True : False;
			$wdth = round((float)1 / $row_size, 3) * 100;
			if( ($count % $row_size) == 0) {
				if($count > 0) {
				?></div><?
			}
			?><div class="row"><?
			}
		?>
			<div class="person-profile-wrap" style="width: <?= $wdth ?>%; padding-bottom: <?= $wdth ?>%; position: relative; float: left; overflow: hidden;">
				<div class="person-inner-wrap" style="position: absolute; left: 0; top: 0; max-width: 100%; max-height: 100%;">
					<? if($link) {?><a href="<?=esc_attr(get_permalink($person->ID))?>"><? } ?>
						<?= $imageT ?>	
						<div class="profile-short">
							<h4 class="title">
								<?=Person::get_name($person);?>
								<br/>
								<!-- this whole bit is for majors and hometown for peer ambassador check-->
								<?if(has_term('peer-ambassador','org_groups',$person->ID)){
									echo '<small>'.get_post_meta($person->ID, 'peer_ambassador_major', True).'</small><br/><small>'.get_post_meta($person->ID, 'peer_ambassador_hometown', True).'</small>';
								}else{
									echo '<small>'.get_post_meta($person->ID, 'person_jobtitle', True).'</small>';	
								}?>
							</h4>		
						</div>
						<div class="group">
							<span class="group-inner">
								<?php if(strtolower($show_org_groups) == "true"){ print($terms); } ?>
							</span>
						</div>
						<div class="overlay"></div>						
					<? if($link) {?></a><?}?>
				</div>
			</div>
			<?
			$count++;
		}
	?>		<!--</div> attempt to get left sidebar back onto grid pages-->
		</div>
	</div>
	<?
	return ob_get_clean();
	//add_filter('the_content','wpautop');		
}
add_shortcode('person-profile-grid', 'sc_person_profile_grid');

/**
 * Custom Opp List by Erik
 **/
function sc_opportunity_grid($atts) {
	$atts = array_map('specCharEscCallback', $atts);	
	//remove_filter('the_content','wpautop');
	$atts['type']	= ($atts['type']) ? $atts['type'] : null;
	$categories		= ($atts['categories']) ? $atts['categories'] : null;	
	$event_groups		= ($atts['event_groups']) ? $atts['event_groups'] : null;
	$event_groups2		= ($atts['event_groups2']) ? $atts['event_groups2'] : null;	
	$limit			= ($atts['limit']) ? (intval($atts['limit'])) : -1;
	$join			= ($atts['join']) ? $atts['join'] : 'or';
	$dropdown		= ($atts['dropdown']) ? $atts['dropdown'] : false;
	$dd_event_groups	= ($atts['dd_event_groups']) ? $atts['dd_event_groups'] : $event_groups;
	$dropdown2		= ($atts['dropdown2']) ? $atts['dropdown2'] : false;
	$dd2_event_groups	= ($atts['dd2_event_groups']) ? $atts['dd2_event_groups'] : NULL;	
	$show_option_all	= ($atts['show_option_all']) ? $atts['show_option_all'] : null;
	$show_option_all2	= ($atts['show_option_all2']) ? $atts['show_option_all2'] : null;	
	$EGID			= get_term_by('slug', $dd_event_groups, 'event_groups')->term_id;
	$EGID2			= get_term_by('slug', $dd2_event_groups, 'event_groups');
	$EGID2			= $EGID2 ? $EGID2->term_id : false;
	$operator		= ($atts['operator']) ? $atts['operator'] : NULL;
	$opps 		= sc_object_list(
		array(
			'type' => 'opportunity',
			'limit' => $limit,
			'join' => $join,
			//'categories' => $categories,
			'event_groups' => $event_groups2 ? $event_groups.' '.$event_groups2 : $event_groups,
			//'orderby' => 'meta_value_num',
			//'order' => 'DESC',
			//'meta_key'	=> 'opportunity_end',
			'operator' => $operator,
			'meta_query'	=> array(
				array(
					'key'	=>	'opportunity_start',
					'value'	=>	date('Ymd', mktime(23,59,59)), // this might work? set time as 23:59:59?
					'compare'	=>	'<=',
				),
				array(
					'key'	=>	'opportunity_end',
					'value'	=>	date('Ymd', mktime(0,0,0)),
					'compare'	=>	'>=',
				),
			),
		),
	array(
		'objects_only' => True,
	));
	
	usort($opps, function($a, $b){
		$a_dt = new DateTime(get_post_meta($a->ID, 'opportunity_end', TRUE));
		$b_dt = new DateTime(get_post_meta($b->ID, 'opportunity_end', TRUE));
		$a_dt = $a_dt->getTimestamp();
		$b_dt = $b_dt->getTimestamp();
		if ($a_dt == $b_dt){
			// If they have the same depth, compare titles
			return strcmp($a->post_title, $b->post_title);
		}
		// If depth_a is smaller than depth_b, return -1; otherwise return 1
		$res = ($a_dt > $b_dt) ? -1 : 1;
		return $res;
	});
	
	ob_start();
	?><div class="opportunity-grid" data-url="<?=admin_url( 'admin-ajax.php' )?>" data-group="<?=esc_attr($dd_event_groups)?>" data-group2="<?=esc_attr($dd2_event_groups)?>" data-jn="<?=esc_attr($join)?>" data-oprtr="<?=esc_attr($operator)?>" data-allopt="<?=esc_attr($show_option_all)?>" data-allopt2="<?=esc_attr($show_option_all2)?>">
		<? if($dropdown){ 
			$prntTrm = get_term_by('slug', 'event-category','event_groups');
			$ids = array_map(function($blrp)use($prntTrm){ 
				$trms = wp_get_post_terms($blrp->ID, 'event_groups');
				$otpt = "";
				foreach($trms as $trm){
					if($trm->parent && $trm->parent == $prntTrm->term_id){
						$otpt .= $trm->term_id;
					}
				}
				return $otpt; 
			}, $opps);
			$args = array(
				'taxonomy'	=>	'event_groups',
				'value_field'	=>	'slug',
				'class'	=>	'opportunity-grid-dropdown form-control',
				'id'	=>	'dd_event_groups',
				'name'	=>	'dd_event_groups',
				'echo'	=> false,
				'selected'	=>	$event_groups,
				'child_of'	=>	$EGID,
				'include'	=>	implode(",", $ids),
			);
			if(!empty($show_option_all)){
				$args['show_option_all'] = $show_option_all;
			}
			echo str_replace(
				'<select',
				'<select onchange="getOppsForGrid(this.value'.($dropdown2 ? ', $(\'#dd2_event_groups\').val()' : '').')"',
				wp_dropdown_categories($args)
			);
		} 
		if($dropdown2 && $EGID2){ 
			$args2 = array(
				'taxonomy'	=>	'event_groups',
				'value_field'	=>	'slug',
				'class'	=>	'opportunity-grid-dropdown form-control',
				'id'	=>	'dd2_event_groups',
				'name'	=>	'dd2_event_groups',			
				'echo'	=> false,
				'selected'	=>	$event_groups2,
				'child_of'	=>	$EGID2,
			);
			if(!empty($show_option_all2)){
				$args2['show_option_all2'] = $show_option_all2;
			}			
			echo str_replace(
				'<select',
				'<select onchange="getOppsForGrid($(\'#dd_event_groups\').val(), this.value)"',
				wp_dropdown_categories($args2)
			);
		} 
		?>	
		<ul class="opportunity-list">
			<?php
				//rsort($opps);
				$matches = "";
				foreach ($opps as $opportunity) { 
					$start_date; //= get_post_meta($opportunity->ID, 'opportunity_start', TRUE);
					$end_date = get_post_meta($opportunity->ID, 'opportunity_end', TRUE);
					$cPost = get_post_meta($opportunity->ID, 'opportunity_url_redirect', true);
					preg_match('/(?:http|https):\/\/tbhccmsdev.smca.ucf.edu\/(?<url>\S*)(?:\/*)/', $cPost, $matches);
					$cPost = $matches['url'];					
					$cPost = get_page_by_path($cPost, OBJECT, 'post');
					$cPost = wp_trim_words($cPost->post_content, 50);	// was 75, dropped to 50
					$time = '';
					$location = '';
					if($ext_link){
						$link = $ext_link; 
					}			
					if($start_date){
						$start_date = new DateTime($start_date);
					}
					if($end_date){
						$end_date = new DateTime($end_date);
					}
					$link = get_post_meta($opportunity->ID, 'opportunity_url_redirect', TRUE);		
					// added these lines to retrieve taxonomy terms instead of using the meta field we had
					$parntCat = get_term_by('slug', 'event-category','event_groups');
					$postCats = wp_get_post_terms($opportunity->ID, 'event_groups');
					$catTerms = '';
					foreach($postCats as $cat){
						$catTerms.= $cat->parent == $parntCat->term_id ? $cat->name : '';
					}
				?>
				<li>
					<a href="<?=$link?>">
						<?=$opportunity->post_title?>
					</a>
					<br/>
					<?=$cPost?>
					<? if($end_date){ ?>
						<div class="opportunity_info">
							<b>Date Close: <?=$end_date->format('l, F jS, Y')?></b> <!-- Added these here b tags -->
						</div>
					<? } ?>
					<? if($time){ ?>
						<div class="opportunity_info">
							Time: <?=get_post_meta($opportunity->ID, 'opportunity_time', true)?>
						</div>
					<? } ?>
					<? if($location){ ?>
						<div class="opportunity_info">
							Location: <?=get_post_meta($opportunity->ID, 'opportunity_location', true)?>
						</div>
					<? } ?>
					<div class="text-right opportunity_category">
						Category:&nbsp;<?=$catTerms?> <!-- switched this bugger out -->
					</div>
				</li>
				<?php
				}
			?>
		</ul>
	</div>
	
	<?
	return ob_get_clean();
	//add_filter('the_content','wpautop');		
}
add_shortcode('opportunity-grid', 'sc_opportunity_grid');

function sc_spotlight_grid($atts) {
	$atts = array_map('specCharEscCallback', $atts);	
	//remove_filter('the_content','wpautop');
	$atts['type']	= ($atts['type']) ? $atts['type'] : null;
	$categories		= ($atts['categories']) ? $atts['categories'] : null;	
	$event_groups		= ($atts['event_groups']) ? $atts['event_groups'] : null;
	$event_groups2		= ($atts['event_groups2']) ? $atts['event_groups2'] : null;	
	$limit			= ($atts['limit']) ? (intval($atts['limit'])) : -1;
	$join			= ($atts['join']) ? $atts['join'] : 'or';
	$dropdown		= ($atts['dropdown']) ? $atts['dropdown'] : false;
	$dd_event_groups	= ($atts['dd_event_groups']) ? $atts['dd_event_groups'] : $event_groups;
	$dropdown2		= ($atts['dropdown2']) ? $atts['dropdown2'] : false;
	$dd2_event_groups	= ($atts['dd2_event_groups']) ? $atts['dd2_event_groups'] : NULL;	
	$show_option_all	= ($atts['show_option_all']) ? $atts['show_option_all'] : null;
	$show_option_all2	= ($atts['show_option_all2']) ? $atts['show_option_all2'] : null;	
	$EGID			= get_term_by('slug', $dd_event_groups, 'event_groups')->term_id;
	$EGID2			= get_term_by('slug', $dd2_event_groups, 'event_groups');
	$EGID2			= $EGID2 ? $EGID2->term_id : false;
	$operator		= ($atts['operator']) ? $atts['operator'] : NULL;
	$spots 		= sc_object_list(
		array(
			'type' => 'spotlight',
			'limit' => $limit,
			'join' => $join,
			'categories' => $categories,
			'event_groups' => $event_groups2 ? $event_groups.' '.$event_groups2 : $event_groups,
			'orderby' => 'meta_value_num',
			'order' => 'DESC',
			'meta_key'	=> 'spotlight_end',
			'operator' => $operator,
			'meta_query'	=> array(
				array(
					'key'	=>	'spotlight_start',
					'value'	=>	date('Ymd'),
					'compare'	=>	'<=',				
				),
			),
		),
		array(
		'objects_only' => True,
		)
	);
	usort($spots, function($a, $b){
		$a_dt = new DateTime(get_post_meta($a->ID, 'spotlight_end', TRUE));
		$b_dt = new DateTime(get_post_meta($b->ID, 'spotlight_end', TRUE));
		$a_dt = $a_dt->getTimestamp();
		$b_dt = $b_dt->getTimestamp();
		if ($a_dt == $b_dt){
			// If they have the same depth, compare titles
			return strcmp($a->post_title, $b->post_title) * -1;
		}
		// If depth_a is smaller than depth_b, return -1; otherwise return 1
		$res = ($a_dt > $b_dt) ? -1 : 1;
		return $res;
	});
	//var_dump($spots);	
	
	ob_start();
	?><div class="spotlight-grid" data-url="<?=admin_url( 'admin-ajax.php' )?>" data-group="<?=esc_attr($dd_event_groups)?>" data-group2="<?=esc_attr($dd2_event_groups)?>" data-jn="<?=esc_attr($join)?>" data-oprtr="<?=esc_attr($operator)?>" data-allopt="<?=esc_attr($show_option_all)?>" data-allopt2="<?=esc_attr($show_option_all2)?>">
		<? if($dropdown){ 
			$args = array(
				'taxonomy'	=>	'event_groups',
				'value_field'	=>	'slug',
				'class'	=>	'spotlight-grid-dropdown form-control',
				'id'	=>	'dd_event_groups',
				'name'	=>	'dd_event_groups',
				'echo'	=> false,
				'selected'	=>	$event_groups,
				'child_of'	=>	$EGID,
			);
			if(!empty($show_option_all)){
				$args['show_option_all'] = $show_option_all;
			}
			// filter hooks from http://wordpress.stackexchange.com/a/72562, get_terms_orderby_semester_year function exists in functions.php
			add_filter('get_terms_orderby', 'get_terms_orderby_semester_year',10,2);
			$wp1Args = wp_dropdown_categories($args);
			remove_filter('get_terms_orderby', 'get_terms_orderby_semester_year');
			//
			rsort($wp1Args);
			echo str_replace(
				'<select',
				'<select onchange="getSpotsForGrid(this.value'.($dropdown2 ? ', $(\'#dd2_event_groups\').val()' : '').')"',
				$wp1Args
			);
		} 
		if($dropdown2 && $EGID2){ 
			$args2 = array(
				'taxonomy'	=>	'event_groups',
				'value_field'	=>	'slug',
				'class'	=>	'spotlight-grid-dropdown form-control',
				'id'	=>	'dd2_event_groups',
				'name'	=>	'dd2_event_groups',			
				'echo'	=> false,
				'selected'	=>	$event_groups2,
				'child_of'	=>	$EGID2,
			);
			if(!empty($show_option_all2)){
				$args2['show_option_all2'] = $show_option_all2;
			}			
			echo str_replace(
				'<select',
				'<select onchange="getSpotsForGrid($(\'#dd_event_groups\').val(), this.value)"',
				wp_dropdown_categories($args2)
			);
		} 
		?>	
		<ul class="spotlight-list">
			<?php
				//rsort($opps);
				foreach ($spots as $spotlight) { 
					$start_date; //= get_post_meta($spotlight->ID, 'spotlight_start', TRUE);
					$end_date; //= get_post_meta($spotlight->ID, 'spotlight_end', TRUE);
					$link = get_post_meta($spotlight->ID, 'spotlight_url_redirect', TRUE);
					$time = '';
					$location = '';
					if($start_date){
						$start_date = new DateTime($start_date);
					}
					if($end_date){
						$end_date = new DateTime($end_date);
					}
					// added these lines to retrieve taxonomy terms instead of using the meta field we had
					$parntCat = get_term_by('slug', 'event-category','event_groups');
					$postCats = wp_get_post_terms($spotlight->ID, 'event_groups');
					$catTerms = '';
					foreach($postCats as $cat){
						$catTerms.= $cat->parent == $parntCat->term_id ? $cat->name : '';
					}
				?>
				<li>
					<a href="<?=$link?>">
						<?=$spotlight->post_title?>
					</a>
					<? if($end_date){ ?>
						<div class="spotlight_info">
							Date Available: <?=$start_date->format('l, F jS, Y')?>
							<br/>
							Date Close: <?=$end_date->format('l, F jS, Y')?>
						</div>
					<? } ?>
					<? if($time){ ?>
						<div class="spotlight_info">
							Time: <?=get_post_meta($spotlight->ID, 'spotlight_time', true)?>
						</div>
					<? } ?>
					<? if($location){ ?>
						<div class="spotlight_info">
							Location: <?=get_post_meta($spotlight->ID, 'spotlight_location', true)?>
						</div>
					<? } ?>
					<div class="text-right spotlight_category">
						Category:&nbsp;<?=$catTerms?> <!-- silly bugger -->
					</div>
				</li>
				<?php
				}
			?>
		</ul>
	</div>
	
	<?
	return ob_get_clean();
	//add_filter('the_content','wpautop');		
}
add_shortcode('spotlight-grid', 'sc_spotlight_grid');


/**
 * Centerpiece Slider
 **/
	function sc_centerpiece_slider( $atts, $content = null ) {
		$atts = array_map('specCharEscCallback', $atts);		
		extract( shortcode_atts( array(
			'id' => '',
		), $atts ) );

		global $post;

		$args = array('p'              => esc_attr( $id ),
					  'post_type'      => 'centerpiece',
					  'posts_per_page' => '1'
				  );

		query_posts( $args );

		if( have_posts() ) while ( have_posts() ) : the_post();

			$slide_order 			= trim(get_post_meta($post->ID, 'ss_slider_slideorder', TRUE), ',');
			$slide_order			= explode("," , $slide_order);
			$slide_count			= count($slide_order);
			$slide_title			= get_post_meta($post->ID, 'ss_slide_title', TRUE);
			$slide_content_type 	= get_post_meta($post->ID, 'ss_type_of_content', TRUE);
			$slide_image			= get_post_meta($post->ID, 'ss_slide_image', TRUE);
			$slide_video			= get_post_meta($post->ID, 'ss_slide_video', TRUE);
			$slide_video_thumb_def	= THEME_IMG_URL.'/video_thumb_default.jpg';
			$slide_video_thumb		= get_post_meta($post->ID, 'ss_slide_video_thumb', TRUE);
			$slide_content			= get_post_meta($post->ID, 'ss_slide_content', TRUE);
			$slide_links_to			= get_post_meta($post->ID, 'ss_slide_links_to', TRUE);
			$slide_newtab			= get_post_meta($post->ID, 'ss_slide_link_newtab', TRUE);
			$slide_duration			= get_post_meta($post->ID, 'ss_slide_duration', TRUE);
			$slide_display_tit		= get_post_meta($post->ID, 'ss_display_title', TRUE);
			$slide_tit_off_top		= get_post_meta($post->ID, 'ss_title_top_offset', TRUE);	
			$slide_tit_off_left		= get_post_meta($post->ID, 'ss_title_left_offset', TRUE);	
			$slide_tit_font_sz		= get_post_meta($post->ID, 'ss_title_font_size', TRUE);	
			$slide_tit_font_col		= get_post_meta($post->ID, 'ss_title_font_color', TRUE);	
			$slide_tit_bg_color		= get_post_meta($post->ID, 'ss_title_background_color', TRUE);	
			$slide_tit_opacity		= get_post_meta($post->ID, 'ss_title_opacity', TRUE);	
		
			//$slide_mob_height		= get_
			
			// id have made a param array (literals in js), debug gets ezier
			if(DEBUG){
				$a = array($slide_display_tit,$slide_tit_off_top,$slide_tit_off_left,$slide_tit_font_sz,$slide_tit_font_col,$slide_tit_bg_color,$slide_tit_opacity);
				print_r($a);
			}
		
			// #centerpiece_slider must contain an image placeholder set to the max
			// slide width in order to trigger responsive styles properly--
			// http://www.bluebit.co.uk/blog/Using_jQuery_Cycle_in_a_Responsive_Layout
			$output .= '<div id="centerpiece_slider">
						  <ul>
						  	<img src="'.get_bloginfo('stylesheet_directory').'/static/img/blank_slide.png" style="max-width: 100%; height: auto;">';


			foreach ($slide_order as $s) {

				if ( ($s !== '') && ($s !== NULL) ) {
					$s = (int)$s;

					$slide_image_url = wp_get_attachment_image_src($slide_image[$s], 'centerpiece-image-wide');
					$slide_video_thumb_url = wp_get_attachment_image_src($slide_video_thumb[$s], 'centerpiece-image-wide');

					$slide_single_duration = (!empty($slide_duration[$s]) ? $slide_duration[$s] : '6');

					// Start <li>
					$output .= '<li class="centerpiece_single" id="centerpiece_single_'.$s.'" data-duration="'.$slide_single_duration.'">';

					// Add <a> tag and target="_blank" if applicable:
					if ($slide_links_to[$s] !== '' && $slide_content_type[$s] == 'image') {
						$output .= '<a href="'.$slide_links_to[$s];
						if ($slide_newtab == 'on') {
							$output .= ' target="_blank"';
						}
						$output .= '">';
					}

					// Image output:
					if ($slide_content_type[$s] == 'image') {
						//$output .= '<img class="centerpiece_single_img" src="'.$slide_image_url[0].'" title="'.$slide_title[$s].'" alt="'.$slide_title[$s].'"';
						$output .= '<div class="centerpiece_single)img" style="background-image:url(\''.$slide_image_url[0].'\');background-size:cover;"';
						$output .= '/>';

						if($slide_display_tit[$s] == 'on'){
							$rgba = hex_and_opacity_to_rgba($slide_tit_bg_color[$s], $slide_tit_opacity[$s]);
							$output .= '<div style="position:absolute;top:'.$slide_tit_off_top[$s].';left:'.$slide_tit_off_left[$s].';font-size:'.$slide_tit_font_sz[$s].';color:'.$slide_tit_font_col[$s].';background-color:rgba('.$rgba.');">'.$slide_title[$s].'</div>';
						}
						
						if ($slide_links_to[$s] !== '' && $slide_content_type[$s] == 'image') {
							$output .= '</a>';
						}

						if ($slide_content[$s] !== '') {
							$output .= '<div class="slide_contents">'.apply_filters('the_content', $slide_content[$s]).'</div>';
						}						

					}

					// Video output:
					if ($slide_content_type[$s] == 'video') {

						// if a video thumbnail is not set and this is not a
						// single slide centerpiece, use the default video thumb
						// (single slide centerpieces w/video should have an
						// optional thumbnail for autoplay purposes)
						if ($slide_count > 1) {
							if (!$slide_video_thumb[$s]) {
								$slide_video_thumb_url[0] = $slide_video_thumb_def;
							}
						}

						$filtered_video_metadata = strip_tags(apply_filters('the_content', $slide_video[$s]), '<iframe><object><embed>');

						if ($slide_video_thumb_url[0] !== NULL) {
							$output .= '<img class="centerpiece_single_vid_thumb" src="'.$slide_video_thumb_url[0].'" alt="Click to Watch" title="Click to Watch" />';
							$output .= '<div class="centerpiece_single_vid_hidden">'.$filtered_video_metadata.'</div>';
						}
						else {
							$output .= $filtered_video_metadata;
						}
					}

					// End <li>
					$output .= '</li>';
				}
			}


			$output .= '</ul>';

			// Apply rounded corners:
			if ($rounded_corners == 'on') {
				$output .= '<div class="thumb_corner_tl"></div><div class="thumb_corner_tr"></div><div class="thumb_corner_bl"></div><div class="thumb_corner_br"></div>';
			}

			$output .= '
						<div id="centerpiece_control"></div>
					</div>';

		endwhile;

		wp_reset_query();

		return $output;

	}
	add_shortcode('centerpiece', 'sc_centerpiece_slider');


/**
 * Output Upcoming Events via shortcode.
 **/
function sc_events_widget() {
	display_events();
	print /*'<p class="events_icons"><a class="icsbtn" href="http://events.ucf.edu/upcoming/feed.ics">ICS Format for upcoming events</a><a class="rssbtn" href="http://events.ucf.edu/upcoming/feed.rss">RSS Format for upcoming events</a></p>*/
	'<div class="moreBtnPad"><div class="screen-only moreBtn"><a href="https://events.ucf.edu/calendar/2862/the-burnett-honors-college/upcoming/" class="home_col_morelink">More Events</a>'.output_weather_data().'</div></div>';
}
add_shortcode('events-widget', 'sc_events_widget');


/**
 * Post search
 *
 * @return string
 * @author Chris Conover
 * */
function sc_post_type_search( $params=array(), $content='' ) {
	$defaults = array(
		'post_type_name'          => 'post',
		'taxonomy'                => 'category',
		'meta_key'                => '',
		'meta_value'              => '',
		'show_empty_sections'     => false,
		'non_alpha_section_name'  => 'Other',
		'column_width'            => 'col-md-4 col-sm-4',
		'column_count'            => '3',
		'order_by'                => 'title',
		'order'                   => 'ASC',
		'show_sorting'            => true,
		'default_sorting'         => 'term',
		'show_sorting'            => true,
		'show_uncategorized'      => false,
		'uncategorized_term_name' => 'Uncategorized'
	);

	$params = ( $params === '' ) ? $defaults : array_merge( $defaults, $params );

	$params['show_empty_sections'] = filter_var( $params['show_empty_sections'], FILTER_VALIDATE_BOOLEAN );
	$params['column_count']        = is_numeric( $params['column_count'] ) ? (int)$params['column_count'] : $defaults['column_count'];
	$params['show_sorting']        = filter_var( $params['show_sorting'], FILTER_VALIDATE_BOOLEAN );

	if ( !in_array( $params['default_sorting'], array( 'term', 'alpha' ) ) ) {
		$params['default_sorting'] = $default['default_sorting'];
	}

	// Resolve the post type class
	if ( is_null( $post_type_class = get_custom_post_type( $params['post_type_name'] ) ) ) {
		return '<p>Invalid post type.</p>';
	}
	$post_type = new $post_type_class;

	// Set default search text if the user didn't
	if ( !isset( $params['default_search_text'] ) ) {
		$params['default_search_text'] = 'Find a '.$post_type->singular_name;
	}

	// Set default search label if the user didn't
	if ( !isset( $params['default_search_label'] ) ) {
		$params['default_search_label'] = 'Find a '.$post_type->singular_name;
	}

	// Register the search data with the JS PostTypeSearchDataManager.
	// Format is array(post->ID=>terms) where terms include the post title
	// as well as all associated tag names
	$search_data = array();
	foreach ( get_posts( array( 'numberposts' => -1, 'post_type' => $params['post_type_name'] ) ) as $post ) {
		$search_data[$post->ID] = array( $post->post_title );
		foreach ( wp_get_object_terms( $post->ID, 'post_tag' ) as $term ) {
			$search_data[$post->ID][] = $term->name;
		}
	}
?>
	<script type="text/javascript">
		if(typeof PostTypeSearchDataManager != 'undefined') {
			PostTypeSearchDataManager.register(new PostTypeSearchData(
				<?php echo json_encode( $params['column_count'] ); ?>,
				<?php echo json_encode( $params['column_width'] ); ?>,
				<?php echo json_encode( $search_data ); ?>
			));
		}
	</script>
	<?php

	// Set up a post query
	$args = array(
		'numberposts' => -1,
		'post_type'   => $params['post_type_name'],
		'tax_query'   => array(
			array(
				'taxonomy' => $params['taxonomy'],
				'field'    => 'id',
				'terms'    => '',
			)
		),
		'orderby'     => $params['order_by'],
		'order'       => $params['order'],
	);

	// Handle meta key and value query
	if ($params['meta_key'] && $params['meta_value']) {
		$args['meta_key'] = $params['meta_key'];
		$args['meta_value'] = $params['meta_value'];
	}

	// Split up this post type's posts by term
	$by_term = array();
	foreach ( get_terms( $params['taxonomy'] ) as $term ) { // get_terms defaults to an orderby=name, order=asc value
		$args['tax_query'][0]['terms'] = $term->term_id;
		$posts = get_posts( $args );

		if ( count( $posts ) == 0 && $params['show_empty_sections'] ) {
			$by_term[$term->name] = array();
		} else {
			$by_term[$term->name] = $posts;
		}
	}

	// Add uncategorized items to posts by term if parameter is set.
	if ( $params['show_uncategorized'] ) {
		$terms = get_terms( $params['taxonomy'], array( 'fields' => 'ids', 'hide_empty' => false ) );
		$args['tax_query'][0]['terms'] = $terms;
		$args['tax_query'][0]['operator'] = 'NOT IN';
		$uncat_posts = get_posts( $args );
		if ( count( $uncat_posts == 0 ) && $params['show_empty_sections'] ) {
			$by_term[$params['uncategorized_term_name']] = array();
		} else {
			$by_term[$params['uncategorized_term_name']] = $uncat_posts;
		}
	}

	// Split up this post type's posts by the first alpha character
	$args['orderby'] = 'title';
	$args['order'] = 'ASC';
	$args['tax_query'] = '';
	$by_alpha_posts = get_posts( $args );
	foreach( $by_alpha_posts as $post ) {
		if ( preg_match( '/([a-zA-Z])/', $post->post_title, $matches ) == 1 ) {
			$by_alpha[strtoupper($matches[1])][] = $post;
		} else {
			$by_alpha[$params['non_alpha_section_name']][] = $post;
		}
	}
	if( $params['show_empty_sections'] ) {
		foreach( range( 'a', 'z' ) as $letter ) {
			if ( !isset( $by_alpha[strtoupper( $letter )] ) ) {
				$by_alpha[strtoupper( $letter )] = array();
			}
		}
	}
	ksort( $by_alpha );

	$sections = array(
		'post-type-search-term'  => $by_term,
		'post-type-search-alpha' => $by_alpha,
	);

	ob_start();
?>
	<div class="post-type-search">
		<div class="post-type-search-header">
			<form class="post-type-search-form form-inline" action="." method="get">
				<label><?php echo $params['default_search_label']; ?></label>
				<input type="text" class="form-control" placeholder="<?php echo $params['default_search_text']; ?>">
			</form>
		</div>
		<div class="post-type-search-results"></div>
		<?php if ( $params['show_sorting'] ) { ?>
		<div class="btn-group post-type-search-sorting">
			<button class="btn btn-default<?php if ( $params['default_sorting'] == 'term' ) echo ' active'; ?>">
				<span class="glyphicon glyphicon-list-alt"></span>
			</button>
			<button class="btn btn-default<?php if ( $params['default_sorting'] == 'alpha' ) echo ' active'; ?>">
				<span class="glyphicon glyphicon-font"></span>
			</button>
		</div>
		<?php } ?>
	<?php

	foreach ( $sections as $id => $section ):
		$hide = false;
		switch ( $id ) {
			case 'post-type-search-alpha':
				if ( $params['default_sorting'] == 'term' ) {
					$hide = True;
				}
				break;
			case 'post-type-search-term':
				if ( $params['default_sorting'] == 'alpha' ) {
					$hide = True;
				}
				break;
		}
?>
		<div class="<?php echo $id; ?>"<?php if ( $hide ) { echo ' style="display:none;"'; } ?>>
			<div class="row">
			<?php
			$count = 0;
			foreach ( $section as $section_title => $section_posts ):
				if ( count( $section_posts ) > 0 || $params['show_empty_sections'] ):
			?>

				<?php if ( $section_title == $params['uncategorized_term_name'] ): ?>
					</div>
						<div class="row">
							<div class="<?php echo $params['column_width']; ?>">
								<h3><?php echo esc_html( $section_title ); ?></h3>
							</div>
						</div>

						<div class="row">
						<?php
						// $split_size must be at least 1
						$split_size = max( floor( count( $section_posts ) / $params['column_count'] ), 1 );
						$split_posts = array_chunk( $section_posts, $split_size );
						foreach ( $split_posts as $index => $column_posts ):
						?>
							<div class="<?php echo $params['column_width']; ?>">
								<ul>
								<?php foreach( $column_posts as $key => $post ): ?>
									<li data-post-id="<?php echo $post->ID; ?>">
										<?php echo $post_type->toHTML( $post ); ?><span class="search-post-pgsection"><?php echo $section_title; ?></span>
									</li>
								<?php endforeach; ?>
								</ul>
							</div>
						<?php endforeach; ?>

				<?php else: ?>

					<?php if ( $count % $params['column_count'] == 0 && $count !== 0 ): ?>
						</div><div class="row">
					<?php endif; ?>

					<div class="<?php echo $params['column_width']; ?>">
						<h3><?php echo esc_html( $section_title ); ?></h3>
						<ul>
						<?php foreach( $section_posts as $post ):  ?>
							<li data-post-id="<?php echo $post->ID; ?>">
								<?php echo $post_type->toHTML( $post ); ?><span class="search-post-pgsection"><?php echo $section_title; ?></span>
							</li>
						<?php endforeach; ?>
						</ul>
					</div>

			<?php
					endif;

				$count++;
				endif;

			endforeach;
			?>
			</div><!-- .row -->
		</div><!-- term/alpha section -->

	<?php endforeach; ?>

	</div><!-- .post-type-search -->

<?php
	return ob_get_clean();
}
add_shortcode( 'post-type-search', 'sc_post_type_search' );



/**
 * Handles the form output and input for the phonebook search.
 *
 * @return string
 * @author Chris Conover
 **/
function sc_phonebook_search($attrs) {
	$show_label = isset($attrs['show_label']) && (bool)$attrs['show_label'] ? '' : ' hidden';
	$input_size = isset($attrs['input_size']) && $attrs['input_size'] != '' ? $attrs['input_size'] : 'col-md-9 col-sm-9';

	# Looks up search term in the search service
	$phonebook_search_query = '';
	$results                = array();
	if(isset($_GET['phonebook-search-query'])) {
		$phonebook_search_query = $_GET['phonebook-search-query'];
		$results                = query_search_service(array('search'=>$phonebook_search_query));
	}

	# Filter out the result types that we don't understand
	# We only understand organizations, departments, and staff
	$results = array_filter(
		$results,
		create_function('$r', 'return in_array($r->from_table, array(\'organizations\', \'departments\', \'staff\'));')
	);

	foreach ( $results as $result ) {
		$result->email = trim( $result->email );
	}

	# Filter out records with Fax in the name
	$results = array_filter($results, create_function('$r', '
			return (preg_match("/^fax\s/i", $r->name) ||
						preg_match("/\sfax\s/i", $r->name) ||
							preg_match("/\sfax$/i", $r->name)) ? False : True;')
	);

	# Limit results to 300 entries
	$additional_results = (count($results) > 300);
	if($additional_results) {
		$results = array_slice($results, 0, 299);
	}

	$organizations = array();
	$departments   = array();

	# Attach staff to organizations and departments;
	# only use alpha person results to avoid duplicates
	foreach($results as $key => $result) {
		$is_organization = ($result->from_table == 'organizations');
		$is_department   = ($result->from_table == 'departments');
		if($is_organization || $is_department) {
			$result->staff = array();
			$emails = array();
			foreach($results as $_result) {
				if($_result->from_table == 'staff') {
					if(
						( $is_organization ) &&
						( $result->name == $_result->organization ) &&
						( ! in_array( $_result->email, $emails ) ) )
						{
						$emails[] = $_result->email;
						$result->staff[$_result->last_name.'-'.$result->first_name.'-'.$_result->id] = $_result;
					} else if(
						( $is_department ) &&
						( $result->name == $_result->department ) &&
						( ! in_array( $_result->email, $emails ) ) )
						{
						$emails[] = $_result->email;
						$result->staff[$_result->last_name.'-'.$result->first_name.'-'.$_result->id] = $_result;
					}
				}
				# Make sure that $result->staff[] is alphabetized
				ksort($result->staff);
			}
		}
		# Separate organizations and departments so we can
		# reorder them later
		if ($is_organization) {
			$organizations[] = $result;
			unset($results[$key]);
		}
		if ($is_department) {
			$departments[] = $result;
			unset($results[$key]);
		}
	}

	# Lump duplicate person data under that person's alpha info
	foreach($results as $key => $result) {
		$staff = ($result->from_table == 'staff');
		if( $staff ) {
			foreach ($results as $_key=>$_result) {
				# If two email addresses match and are not null,
				# lump the secondary listing under the alpha listing
				# array (generated on the fly)
				if (
					( $result->email !== null ) &&
					( $_result->email !== null ) &&
					( $result != $_result ) &&
					( $_result->email == $result->email ) )
					{
					$_result->secondary[] = $result;
					unset($results[$key]);
				}
			}
		}
	}

	# Reorder results: Organizations, then Departments, then Staff
	$results = array_merge($organizations, $departments, $results);


	# Helper function for naming consistencies
	function fix_name_case($name) {
		$name = ucwords(strtolower($name));
		$name = str_replace('Ucf', 'UCF', $name);
		$name = str_replace('dr.', 'Dr.', $name);
		$name = str_replace('alumni', 'Alumni', $name);
		$name = str_replace(' And ', ' and ', $name);
		$name = str_replace('Cosas ', ' COSAS ', $name);
		$name = str_replace('Creol', 'CREOL', $name);
		$name = str_replace('Lead Scholars', 'LEAD Scholars', $name);
		$name = str_replace('Rotc', 'ROTC', $name);
		$name = preg_replace('/\bSdes\b/', 'SDES', $name);
		$name = str_replace(' Of ', ' of ', $name);
		$name = preg_replace('/\sOf$/', ' of', $name);
		$name = str_replace(' For ', ' for ', $name);
		$name = preg_replace('/\sFor$/', ' for', $name);
		$name = str_replace('&public', '&amp; Public', $name);
		$name = str_replace('Student-athletes', 'Student Athletes', $name);
		$name = str_replace('Wucf', 'WUCF', $name);
		$name = str_replace('WUCF Tv', 'WUCF TV', $name);
		$name = str_replace('WUCF-fm', 'WUCF-FM', $name);
		$name = preg_replace_callback('/\([a-z]+\)/', create_function('$m', 'return strtoupper($m[0]);'), $name);
		$name = preg_replace_callback('/\([a-z]{1}/', create_function('$m', 'return strtoupper($m[0]);'), $name);
		return $name;
	}

	# Display single result name, position, dept, and org
	function display_primary_info($result) {
		ob_start(); ?>

		<span class="name">
			<strong><?php echo ($result->from_table == 'organizations') ? fix_name_case($result->name) : $result->name; ?></strong>
		</span>
		<?php if ($result->from_table == 'staff' && $result->job_position) { ?>
		<span class="job-title">
			<?php echo $result->job_position; ?>
		</span>
		<?php } ?>
		<?php if($result->from_table == 'departments' && $result->organization) { ?>
		<span class="division">
			A division of: <a href="?phonebook-search-query=<?php echo urlencode($result->organization); ?>"><?php echo fix_name_case($result->organization); ?></a>
		</span>
		<?php } ?>
		<?php if($result->from_table == 'staff' && $result->department) { ?>
		<span class="department">
			<a href="?phonebook-search-query=<?php echo urlencode($result->department); ?>"><?php echo $result->department; ?></a>
		</span>
		<?php } ?>
		<?php if($result->from_table == 'staff' && $result->organization) { ?>
		<span class="organization">
			<a href="?phonebook-search-query=<?php echo urlencode($result->organization); ?>"><?php echo fix_name_case($result->organization); ?></a>
		</span>
		<?php }

		return ob_get_clean();
	}

	# Display single result location information
	function display_location_info($result) {
		ob_start(); ?>

		<?php if($result->from_table == 'staff' && $result->email) { ?>
		<span class="email">
			<a href="mailto:<?php echo $result->email; ?>"><?php echo $result->email; ?></a>
		</span>
		<?php } ?>
		<?php if ($result->building) { ?>
		<span class="location">
			<a href="http://map.ucf.edu/?show=<?php echo $result->bldg_id ?>">
				<?php echo fix_name_case($result->building); ?>
				<?php if($result->room) {
					echo ' - '.$result->room;
				} ?>
			</a>
		</span>
		<?php } ?>
		<?php if ($result->postal) { ?>
			<span class="postal">Zip: <?=$result->postal; ?></span>
		<?php }

		return ob_get_clean();
	}

	# Display single result phone/fax information
	function display_contact_info($result) {
		ob_start(); ?>

		<?php if($result->phone) { ?>
		<span class="phone">Phone: <a href="tel:<?= str_replace("-", "", $result->phone); ?>"><?= $result->phone; ?></a></span>
		<?php } ?>
		<?php if($result->from_table !== 'staff' && $result->fax) { ?>
		<span class="fax">Fax: <?=$result->fax; ?></span>
		<?php }

		return ob_get_clean();
	}


	ob_start();?>
	<form id="phonebook-search">
		<div class="row">
			<div class="col-md-6 col-sm-10">
				<label class="<?php echo $show_label ?>" for="phonebook-search-query">Search Term</label>
				<input type="text" id="phonebook-search-query" name="phonebook-search-query" class="search-query form-control"
					value="<?php echo stripslashes(htmlentities($phonebook_search_query)); ?>">
				<span class="help-block">Organization, Department, or Person (Name, Email, Phone)</span>
			</div>
			<div class="col-md-2 col-sm-2">
				<button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span> Search</button>
			</div>
		</div>
	</form>
	<?php
	if($phonebook_search_query != '') {
		?>
		<?php if(count($results) == 0) { ?>
		<p><strong><big>No results were found.</big></strong></p>
		<?php
		} else {
			if($additional_results) { ?>
				<p id="additional_results">First 300 results returned. Try narrowing your search.</p>
			<?php } ?>
		<ul id="phonebook-search-results">
			<?php foreach($results as $i => $result) { ?>
				<li class="result<?php if ($result->from_table == 'departments' || $result->from_table == 'organizations') { ?> group-result<?php } ?>">
					<table class="table">
						<tbody>
							<?php
								switch($result->from_table) {
									case 'staff':
										?>
										<tr>
											<td class="col-md-6 col-sm-6">
												<?=display_primary_info($result);?>
											</td>
											<td class="col-md-3 col-sm-3">
												<?=display_contact_info($result);?>
											</td>
											<td class="col-md-3 col-sm-3">
												<?=display_location_info($result);?>
											</td>
										<?php if (!empty($result->secondary)) { ?>
										</tr>
										<tr class="person-secondary-list">
											<td class="col-md-12 col-sm-12" colspan="3">
												<a class="toggle person-secondary"><span class="glyphicon glyphicon-plus"></span> More Results</a>
												<ul>
													<?php foreach ($result->secondary as $secondary) { ?>
													<li>
														<table class="table">
															<tbody>
																<tr>
																	<td class="col-md-6 col-sm-6">
																		<?=display_primary_info($secondary);?>
																	</td>
																	<td class="col-md-3 col-sm-3">
																		<?=display_contact_info($secondary);?>
																	</td>
																	<td class="col-md-3 col-sm-3">
																		<?=display_location_info($secondary);?>
																	</td>
																</tr>
															</tbody>
														</table>
													</li>
													<?php } ?>
												</ul>
											</td>
										<?php } ?>
										</tr>

							<?php
								break;
								case 'departments':
								case 'organizations':
									?>
									<tr>
										<td class="col-md-6 col-sm-6">
											<?=display_primary_info($result);?>
										</td>
										<td class="col-md-3 col-sm-3">
											<?=display_contact_info($result);?>
										</td>
										<td class="col-md-3 col-sm-3">
											<?=display_location_info($result);?>
										</td>
									<?php if(count($result->staff) > 0) { ?>
									</tr>
									<tr>
										<td colspan="3" class="show_staff col-md-12 col-sm-12">
											<a class="toggle"><span class="glyphicon glyphicon-plus"></span> Show Staff</a>
											<div class="show-staff-wrap">
												<ul class="staff-list">
													<?php
														$staff_per_column = ceil(count($result->staff) / 3);
														$count = 0;
													?>
													<?php foreach($result->staff as $person) { ?>
														<li>
															<?php if($person->email) { ?>
																<span class="email"><a href="mailto:<?php echo $person->email; ?>"><?php echo $person->name; ?></a></span>
															<?php } else { ?>
																<span class="name"><?php echo $person->name; ?></span>
															<?php } ?>
															<?php if($person->phone) { ?>
																<span class="phone"><a href="tel:<?= str_replace("-", "", $person->phone); ?>"><?= $person->phone; ?></a></span>
															<?php } ?>
														</li>
														<?php if( ((($count + 1) % $staff_per_column) == 0) && ($count + 1 !== count($result->staff))) {
															echo '</ul><ul class="staff-list">';
														}
														$count++;
													} ?>
												</ul>
											</div>
										</td>
										<?php } ?>
									</tr>
							<?php
								break;
							}
							?>
							</tbody>
						</table>
				</li>
			<?php } ?>
		<?php } ?>
	</ul>
	<?php }
	return ob_get_clean();
}
add_shortcode('phonebook-search', 'sc_phonebook_search');

/**
 * Authenticates the username/password combination with LDAP.
 *
 * @param string $username The username to authenticate.
 * @param string $password The password to authenticate.
 * @return bool True if username/password was authenticated, otherwise false
 *
 * @author Brandon T. Groves
 */
function ldap_auth($username, $password) {
	$ldapbind = false;
	$ldap = ldap_connect(LDAP_HOST);
	if ($ldap) {
		$ldapbind = ldap_bind($ldap, $username . '@' . LDAP_HOST, $password);
	} else {
		echo "could not connect.";
	}

	return $ldapbind;
}

/**
 * Sets the session data for gravity forms authentication.
 *
 * @author Brandon T. Groves
 */
function gf_set_session_data($user) {
	$timeout = 15 * 60;
	$_SESSION['timeout'] = time() + $timeout;
	$_SESSION['user'] = $user;
	$_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
}

/**
 * Destroys the session data for gravity forms authentication.
 *
 * @author Brandon T. Groves
 */
function gf_destroy_session() {
	$_SESSION = array();
	session_destroy();
}

/**
 * Retrieves the login HTML.
 *
 * @error bool display error message
 * @return string html login
 *
 * @author Brandon T. Groves
 */
function gf_login_html($error = false) {
	ob_start();
	gf_destroy_session();
	// Force HTTPS
	$pageURL = "https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
	?>

	<div id="ann-login-wrapper">
		<h2>Login</h2>
		<p>To post a new announcement, please log in using your NID and NID password below.<br/></p>
		<form method="post" id="auth-form" action="<?=$pageURL; ?>">
			<div class="wrapper">
				<?php if ($error):?>
				<div class="alert alert-danger" id="login_error">
					<strong>Error:</strong>
					<p>Your NID or password is invalid or the authentication service was unavailable.</p>
					<p>To verify your NID, go to <a href="http://my.ucf.edu/">myUCF</a> and select "What are my PID and NID?"<br/>
					To reset your password, go to the <a href="http://mynid.ucf.edu/">Change Your NID Password</a> page.<br/>
					For further help, contact the Service Desk at 407-823-5117, Monday-Friday 8am-5pm.</p>
				</div>
				<?php endif; ?>
				<div id="auth-form-items">
					<div class="form-group">
						<label for="username">NID (Network ID)</label>
						<input name="username" class="form-control" id="username" type="text">
					</div>
					<div class="form-group">
						<label for="password">Password</label>
						<input name="password" id="password" class="form-control" type="password">
					</div>
					<input name="submit-auth" class="btn btn-default" id="submit-auth" type="submit" value="Submit">
				</div>
			</div>
		</form>
	</div>

	<?php
	return ob_get_clean();
}

/**
 * Displays LDAP authentication unless already authenicated,
 * which displays the gravity form.
 *
 * @param array $username .
 * @param string $password The password to authenticate.
 * @return bool True if username/password was authenticated, otherwise false
 *
 * @author Brandon T. Groves
 */
function gravity_ldap($attr, $content = null) {

	if (isset($_SESSION['timeout']) && $_SESSION['timeout'] < time()) {
		gf_destroy_session();
	}

	require_once(WP_CONTENT_DIR . '/plugins/gravityforms/gravityforms.php');

	if (isset($_SESSION['user']) && isset($_SESSION['ip']) && $_SESSION['ip'] == $_SERVER['REMOTE_ADDR']) {
		gf_set_session_data($_SESSION['user']);
		return RGForms::parse_shortcode($attr, $content);
	} elseif (isset($_POST["submit-auth"]) && isset($_POST['username']) && strlen($_POST['username']) != 0 && isset($_POST['password']) && strlen($_POST['password']) != 0) {
		if (ldap_auth($_POST['username'], $_POST['password'])) {
			gf_set_session_data($_POST['username']);
			return RGForms::parse_shortcode($attr, $content);
		} else {
			return gf_login_html(True);
		}
	} else {
		return gf_login_html();
	}
}
add_shortcode('gravity-with-ldap', 'gravity_ldap');


/**
 * Output a list of A-Z Index Links with their Web Administrator
 * information.
 * (This is a separate shortcode only so that we aren't
 * modifying the azindexlink objectsToHtml or toHTML methods.)
 **/
function azindexlink_webadmins($attr) {
	$args = array(
		'post_type' => 'azindexlink',
		'numberposts' => -1,
		'orderby' => 'post_title',
		'order' => 'ASC',
	);
	$links = get_posts($args);

	$output = '<ul id="azindexlink-webadmins">';

	foreach ($links as $link) {
		$url = get_post_meta($link->ID, 'azindexlink_url', true);
		$webadmins = apply_filters('the_content', get_post_meta($link->ID, 'azindexlink_webadmins', true));
		$output .= '<li><a href="'.$url.'">'.$link->post_title.'</a>';
		if ($webadmins) {
			$output .= '<br/><p>'.$webadmins.'</p>';
		}
		$output .= '</li>';
	}

	$output .= '</ul>';
	return $output;
}
add_shortcode('azindexlinks-webadmins', 'azindexlink_webadmins');

function sc_remarketing_tag($attr) {
	$conversion_id = '';
	$img_src = '';

	if ( isset( $attr[ 'conversion_id' ] ) ) {
		$conversion_id = str_replace( array( '"', "'" ), '', $attr[ 'conversion_id' ] );
	} else {
		return '';
	}

	if ( isset( $attr[ 'img_src' ] ) ) {
		$img_src = str_replace( array( '"', "'" ), '', $attr[ 'img_src' ] );
	} else {
		return '';
	}

	ob_start();

	?>
	<script type="text/javascript">
		// <![CDATA[
		var google_conversion_id = <?php echo $conversion_id; ?>;
		var google_custom_params = window.google_tag_params;
		var google_remarketing_only = true;
		// ]]>
	</script>
	<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
	<noscript>
		<div style="display:inline;">
			<img height="1" width="1" style="border-style:none;" alt="" src="<?php echo $img_src; ?>" />
		</div>
	</noscript>
	<?php

	return ob_get_clean();
}

add_shortcode( 'google-remarketing', 'sc_remarketing_tag' );


function sc_undergrad_catalog_url( $attr ) {
	return UNDERGRAD_CATALOG_URL;
}
add_shortcode( 'undergraduate-catalog-url', 'sc_undergrad_catalog_url' );


function sc_grad_catalog_url( $attr ) {
	return GRAD_CATALOG_URL;
}
add_shortcode( 'graduate-catalog-url', 'sc_grad_catalog_url' );


function sc_chart( $attr ) {
	$id = $attr['id'] ? $attr['id'] : 'custom-chart';
	$type = $attr['type'] ? $attr['type'] : 'bar';
	$json = $attr['data'] ? $attr['data'] : '';
	$options = $attr['options'] ? $attr['options'] : '';

	if ( empty( $json ) ) {
		return;
	}

	$class = $attr['class'] ? 'custom-chart ' . $class : 'custom-chart';

	ob_start();

	?>
		<div id="<?php echo $id; ?>" class="<?php echo $class; ?>" data-chart-type="<?php echo $type; ?>" data-chart-data="<?php echo $json; ?>" <?php echo $options ? 'data-chart-options="' . $options . '"' : ''; ?>></div>
	<?php

	return ob_get_clean();
}
add_shortcode( 'chart', 'sc_chart' );


/**
 * Displays affixed navigation for the A-Z Index.
 **/
function sc_azindex_navbar( $attr ) {
	ob_start();
?>
	<div id="top"></div>

	<div id="azIndexList" data-spy="affix" data-offset-top="200">
		<span id="azIndexList-label">Jump To:</span>
		<div class="navbar navbar-default">
			<ul class="nav navbar-nav">
			<?php foreach ( range( 'A', 'Z' ) as $index=>$alpha ): ?>
				<li <?php echo $index === 0 ? 'class="active"' : ''; ?>>
					<a href="#az-<?php echo strtolower( $alpha ); ?>">
						<?php echo $alpha; ?>
					</a>
				</li>
			<?php endforeach; ?>
			</ul>
		</div>
	</div>
<?php
	return ob_get_clean();
}
add_shortcode( 'azindex-navbar', 'sc_azindex_navbar' );


/**
 * Displays social media buttons for sharing a post.
 **/
function sc_social_share_buttons( $atts, $content='' ) {
	global $post;
	$url = get_permalink( $post->ID );
	$title = $post->post_title;

	$atts = shortcode_atts(
		array(
			'subject_line' => '',
			'email_body' => ''
		),
		$atts,
		'social-share-buttons'
	);

	return display_social( $url, $title, $atts['subject_line'], $atts['email_body'] );
}

add_shortcode( 'social-share-buttons', 'sc_social_share_buttons' );

// Nabbed from STEPHANIE LEARY http://stephanieleary.com/2010/07/call-a-navigation-menu-using-a-shortcode/
// props where props are due (coulda done it, but lazy)	-E
function print_menu_shortcode($atts, $content = null) {
    extract(shortcode_atts(array( 'name' => null, ), $atts));
    return wp_nav_menu( array( 'menu' => $name, 'echo' => false ) );
}
add_shortcode('menu', 'print_menu_shortcode');

?>
