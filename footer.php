			<div id="footer">
				<div id="footer-navwrap" class="row">
					<div class="hidden-sm hidden-xs footerCol col-md-2">
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
					<div class="hidden-sm hidden-xs footerCol col-md-2 col-half-offset">
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
					<div class="footerCol col-xs-4 col-md-2 col-half-offset">
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
					<div id="footer-logo" class="footerCol col-xs-4 col-md-2 col-half-offset">
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
					<div class="footerCol col-xs-4 col-md-2 col-half-offset">
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
									<span>p.<a href="tel:4078232076">407.823.2076</a></span>
								</div>
								<div class="fax">
									<span>f.<a href="tel:4078236583">407.823.6583</a></span>
								</div>
								<div class="email">
									<span>e.<a href="mailto:honors@ucf.edu">honors@ucf.edu</a></span>
								</div>
							</div>
							<!--<br/>
								<a href="<?=site_url()?>/feedback/">Comments and Feedback</a> | &copy;
								<a href="<?=site_url()?>" class="print-noexpand fn org url">
								<span class="organization-name">The Burnett Honors College</span>
							</a>-->
						</p>
					</div>
				</div>
			</div>
		</div><!-- .container -->
	</body>
	<?="\n".footer_()."\n"?>
</html>
