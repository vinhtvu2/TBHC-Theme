			<div id="footer">
				<div id="footer-navwrap" class="row">
					<div class="hidden-sm hidden-xs footerCol col-md-3">
						<?=wp_nav_menu(array(
							'theme_location' => 'footer-outer-left-collapse',
							//'menu' => 'footer-audience',
							'menu_id' => 'footer-outer-left-collapse',
							'container' => 'false',
							'menu_class' => 'menu list-unstyled screen-only',
							'fallback_cb' => false,
							'depth' => 1,
							'walker' => new Bootstrap_Walker_Nav_Menu()
							));
						?>				
					</div>
					<div class="hidden-sm hidden-xs footerCol col-md-3">
						<?=wp_nav_menu(array(
							'theme_location' => 'footer-inner-left-collapse',
							//'menu' => 'footer-quick-links',
							'container' => 'false',
							'menu_class' => 'menu list-unstyled',
							'menu_id' => 'footer-inner-left-collapse',
							'fallback_cb' => false,
							'depth' => 1,
							'walker' => new Bootstrap_Walker_Nav_Menu()
							));
						?>
					</div>
					<div id="footer-logo" class="col-xs-15 col-sm-5 col-sm-push-5 col-md-push-0 col-md-3">
						<a href="http://tbhccmsdev.smca.ucf.edu">
							<img id="tbhcFooterLogo" src="<?php bloginfo('stylesheet_directory'); ?>/static/img/WebsiteFooterShort.png" alt="The Burnett Honors College">
							</img>
						</a>
						<?=wp_nav_menu(array(
							'theme_location' => 'social-links',
							'container' => 'div',
							'container_id' => 'social-menu-wrap',
							'menu_class' => 'menu list-unstyled list-inline screen-only',
							'menu_id' => 'social-menu',
							'depth' => 1,
							));
						?>
					</div>				
					<div class="footerCol col-xs-half col-sm-5 col-sm-pull-5 col-md-pull-0 col-md-3">
						<?=wp_nav_menu(array(
							'theme_location' => 'footer-inner-right',
							//'menu' => 7,
							'menu_id' => 'footer-inner-right',
							'container' => 'div',
							'menu_class' => 'menu list-unstyled screen-only',
							//'menu_id' => 'department-sites',
							'depth' => 1,
							));
						?>
					</div>	
					<div class="footerCol col-xs-half col-sm-5 col-md-3">
						<div id="contactInfo" role="contentinfo" class="vcard">
							<a href="<?=site_url()?>" class="print-noexpand fn org url">
								<span class="organization-name">The Burnett Honors College</span>
							</a>
							<div class="adr">
								<span class="street-address">P.O. Box 161800</span>
								<div>
									<span class="locality">Orlando,</span>
									<span class="region">FL</span>
									<span class="postal-code">32816</span>
								</div>
								<div class="tel">
									<a href="tel:4078232076">407.823.2076</a>
								</div>
								<div class="email">
									<a href="mailto:honors@ucf.edu">honors@ucf.edu</a>
								</div>
							</div>
							<!--<br/>
								<a href="<?=site_url()?>/feedback/">Comments and Feedback</a> | &copy;
								<a href="<?=site_url()?>" class="print-noexpand fn org url">
								<span class="organization-name">The Burnett Honors College</span>
							</a>-->
						</div>
					</div>
				</div>
			</div>
			<div id="subfooter">
				<?=
					$crumbs = explode("/",$_SERVER["REQUEST_URI"]);
					print_r($crumbs);
					foreach($crumbs as $crumb){
						echo ucfirst(str_replace(array(".php","_"),array(""," "),$crumb) . ' ');
					}
				?>
			</div>
		</div><!-- .container -->
		<!--[if IE]>
			<script src="https://cdn.jsdelivr.net/css3-mediaqueries/0.1/css3-mediaqueries.min.js"></script>			
		<![endif]-->
	</body>
	<?="\n".footer_()."\n"?>
</html>
