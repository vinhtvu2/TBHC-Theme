<!DOCTYPE html>
<html lang="en-US">
	<head>
		<?="\n".header_()."\n"?>
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<?php echo google_tag_manager_dl(); ?>

		<?php if ( CB_UID ): ?>
		<script type="text/javascript">
			var _sf_startpt = (new Date()).getTime();

			var CB_UID      = '<?php echo CB_UID; ?>';
			var CB_DOMAIN   = '<?php echo CB_DOMAIN; ?>';
		</script>
		<?php endif;?>

		<!--[if IE]>
		<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

		<?php
		// Always load webfont css for Degree posts and the 404 template.
		if ( $post->post_type == 'degree' || is_404() ) {
			webfont_stylesheet();
		}

		// Load webfonts if enabled by page.
		if ( is_page() ) {
			page_specific_webfonts( $post->ID );
		}

		// Load page-specific css.
		if ( is_page() || ( is_404() && $post = get_page_by_title( '404' ) ) ) {
			esi_include( 'page_specific_stylesheet', $post->ID ); // Wrap in ESI to prevent caching of .css file
		}
		?>
		<script type="text/javascript">
			var PostTypeSearchDataManager = {
				'searches' : [],
				'register' : function(search) {
					this.searches.push(search);
				}
			}
			var PostTypeSearchData = function(column_count, column_width, data) {
				this.column_count = column_count;
				this.column_width = column_width;
				this.data         = data;
			}

			var ALERT_RSS_URL				= '<?php echo get_theme_option('alert_feed_url'); ?>';
			var SITE_DOMAIN					= '<?php echo WP_SITE_DOMAIN; ?>';
			var SITE_PATH					= '<?php echo WP_SITE_PATH; ?>';
			var PRINT_HEADER_IMG			= '<?php echo THEME_IMG_URL.'/ucflogo-print.png'; ?>';

		</script>
		<script type="text/javascript" src="http://malsup.github.io/min/jquery.cycle.all.min.js"></script>
		<script type="text/javascript" src="http://code.jquery.com/jquery-migrate-1.4.1.min.js"></script>
		<!--[if IE]>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js">"</script>			
		<![endif]-->
		</head>
	<body <?php echo body_class(); ?>>

		<?php echo google_tag_manager(); ?>
		
		<nav id="site-nav-xs" class="visible-xs-block navbar navbar-inverse">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#header-menu-xs-collapse" aria-expanded="false">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<span class="navbar-brand">Navigation</span>
			</div>
			<div class="collapse navbar-collapse" id="header-menu-xs-collapse">
				<?php
					wp_nav_menu( array(
					'theme_location' => 'header',
					'container' => false,
					'menu_class' => 'menu nav navbar-nav',
					'menu_id' => 'header-menu-xs',
					'walker' => new Bootstrap_Walker_Nav_Menu()
					) );
				?>
			</div>
		</nav>
		
		<div id="jumbotron-logo">
			<a href="http://tbhccmsdev.smca.ucf.edu">
				<img id="tbhcLogo" src="<?php bloginfo('stylesheet_directory'); ?>/static/img/TbhcLogo.png" alt="The Burnett Honors College">
				</img>
			</a>
		</div>
		<nav id="header-nav-wrap" role="navigation" class="screen-only hidden-xs">
			<?php 
				wp_nav_menu(array(
					'theme_location' => 'header',
					'container' => 'false',
					'menu_class' => 'menu list-unstyled list-inline text-center '.get_header_styles(),
					'menu_id' => 'header-menu',
					'walker' => new Bootstrap_Walker_Nav_Menu(),
					'before' => '<strong>',
					'after' => '</strong>',
					)
				);
			?>
		</nav>
		
		<div class="container">
			<div class="row status-alert" id="status-alert-template" data-alert-id="">
				<div class="col-md-12 col-sm-12 alert-wrap">
					<div class="alert alert-danger alert-block">
						<div class="row">
							<div class="col-md-2 col-sm-2 alert-icon-wrap">
								<div class="alert-icon general"></div>
							</div>
							<div class="col-md-10 col-sm-10 alert-inner-wrap">
								<button type="button" class="close" data-dismiss="alert">&times;</button>
								<h2>
									<a href="<?php echo get_theme_option('alert_more_information_url'); ?>">
										<span class="title"></span>
									</a>
								</h2>
								<p class="alert-body">
									<a href="<?php echo get_theme_option('alert_more_information_url'); ?>">
										<span class="content"></span>
									</a>
								</p>
								<p class="alert-action">
									<a class="more-information" href="<?php echo get_theme_option('alert_more_information_url'); ?>"></a>
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php if ( is_front_page() ): ?>
			<div id="header" class="sr-only" role="banner">
				<h1><?php echo bloginfo( 'name' ); ?></h1>
			</div>
			<?php endif; ?>
