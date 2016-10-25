<?php disallow_direct_load('sidebar-left.php');?>

<?php if(!function_exists('dynamic_sidebar') or !dynamic_sidebar('Left Sidebar')):?>
<?php endif;?>

<!-- Hard-written sidebar components go here: -->

<?php

	$show_facebook	 		= get_post_meta($post->ID, 'page_widget_l_showfacebook', TRUE);	
	$more_info_nav_val 			= get_post_meta($post->ID, 'page_widget_l_moreinfo', TRUE);
	$more_info_nav_val_title 	= get_post_meta($post->ID, 'page_widget_l_moreinfo_title', TRUE);
	$secondary_nav_val 			= get_post_meta($post->ID, 'page_widget_l_secinfo', TRUE);
	$secondary_nav_val_title 	= get_post_meta($post->ID, 'page_widget_l_secinfo_title', TRUE);
	$show_colleges_val 			= get_post_meta($post->ID, 'page_widget_l_showcolleges', TRUE);
	
	if ($more_info_nav_val) {
		$moreinfo_title = $more_info_nav_val_title !== '' ? $more_info_nav_val_title : 'More Information';
		//print '<h3 id="sidebar_l_moreinfo" class="sidebar_title">'.$moreinfo_title.':</h3>';

		$args = array(
			'menu' => $more_info_nav_val,
			'container' => 'false',
			'menu_class' => 'sidebar_nav list-unstyled furtherInfo',
			'before' => '<strong>',
			'after' => '</strong>'
		);
		wp_nav_menu($args);
	}
	if ($secondary_nav_val) {
		$nav_title = $secondary_nav_val_title !== '' ? $secondary_nav_val_title : 'Useful Links';
		print '<h3 id="sidebar_l_secinfo" class="sidebar_title">'.$nav_title.':</h3>';

		$args = array(
			'menu' => $secondary_nav_val,
			'container' => 'false',
			'menu_class' => 'sidebar_nav list-unstyled',
			'before' => '<strong>',
			'after' => '</strong>'
		);
		wp_nav_menu($args);
	}
	if ($show_colleges_val) {
		echo sprintf(
			'<h3 id="sidebar_l_colleges" class="sidebar_title%s">UCF Colleges</h3>',
			($more_info_nav_val || $secondary_nav_val ? '' : ' notoppad')
		);

		$args = array(
			'theme_location' => 'ucf-colleges',
			'container' => 'false',
			'menu_class' => 'sidebar_nav list-unstyled',
			'before' => '<strong>',
			'after' => '</strong>'
		);
		wp_nav_menu($args);
	}

	// Facebook Link
	if ($show_facebook == 'on') {	
		print '<iframe src="https://www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2FTheBurnettHonorsCollege%2F&width=151&layout=button_count&action=like&size=large&show_faces=false&share=true&height=46&appId=966428133474292" width="151" height="46" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>';
	}	
	// Embed Widget 1
	$embed1_title	 		= get_post_meta($post->ID, 'page_widget_l_embed1_title', TRUE);
	$embed1			 		= get_post_meta($post->ID, 'page_widget_l_embed1', TRUE);	
	$embed1					= do_shortcode($embed1);

	if ($embed1) {	
		if ($embed1_title !== '') {
			print '<h3 id="sidebar_l_embed1" class="sidebar_title">'.$embed1_title.'</h3>';
		}
		print '<div id="sidebar_l_embed1_wrap" class="sidebar_l_wrap">';
		print $embed1;
		print '</div>';
	}
?>
