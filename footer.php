			<div id="footer">
				
				<div class="collapse col">
					<?=wp_nav_menu(array(
						//'theme_location' => 'footer-menu',
						'menu' => 31,
						'container' => 'false',
						'menu_class' => 'menu list-unstyled text-left',
						'fallback_cb' => false,
						'depth' => 1,
						'walker' => new Bootstrap_Walker_Nav_Menu()
						));
					?>				
				</div>
				<div class="collapse col">
					<?=wp_nav_menu(array(
						//'theme_location' => 'footer-menu',
						'menu' => 6,
						'container' => 'false',
						'menu_class' => 'menu list-unstyled text-left',
						//'menu_id' => 'quick-links',
						'fallback_cb' => false,
						'depth' => 1,
						'walker' => new Bootstrap_Walker_Nav_Menu()
						));
					?>
				</div>
				<div id="footerLogo">
						<a href="http://tbhccmsdev.smca.ucf.edu">
							<img id="tbhcFooterLogo" src="<?php bloginfo('stylesheet_directory'); ?>/static/img/WebsiteFooterShort.png" alt="The Burnett Honors College">
							</img>
						</a>
					<?=wp_nav_menu(array(
						'theme_location' => 'social-links',
						'container' => 'div',
						'container_id' => 'social-menu-wrap',
						'menu_class' => 'menu list-unstyled list-inline text-left screen-only',
						'menu_id' => 'social-menu',
						'depth' => 1,
						));
					?>
				</div>
				<div class="col">
					<?=wp_nav_menu(array(
						//'theme_location' => 'department-sites',
						'menu' => 7,
						'container' => 'div',
						'menu_class' => 'menu list-unstyled text-left screen-only',
						//'menu_id' => 'department-sites',
						'depth' => 1,
						));
					?>
				</div>
				<div class="col">
					<p id="" role="contentinfo" class="vcard">
						<div class="adr">
							<div class="street-address">P.O. Box 161800 </div>
							<div class="locality">Orlando</div>,
							<div class="region">Florida</div>,
							<div class="postal-code">32816-1800</div> |
							<div class="tel"><a href="tel:4078232076">407.823.2076</a></div>
						</div>
						<!--<br/>
						<a href="<?=site_url()?>/feedback/">Comments and Feedback</a> | &copy;
						<a href="<?=site_url()?>" class="print-noexpand fn org url">
							<span class="organization-name">The Burnett Honors College</span>
						</a>-->
					</p>
				</div>
			
			
			
			
			
			
<!--				<div id="footer-navwrap" role="navigation" class="screen-only">
					
				</div>


				<p id="subfooter" role="contentinfo" class="vcard">
					<span class="adr">
						<span class="street-address">P.O. Box 161800 </span>
						<span class="locality">Orlando</span>,
						<span class="region">Florida</span>,
						<span class="postal-code">32816-1800</span> |
						<span class="tel"><a href="tel:4078232076">407.823.2076</a></span>
					</span>
					<br/>
					<a href="<?=site_url()?>/feedback/">Comments and Feedback</a> | &copy;
					<a href="<?=site_url()?>" class="print-noexpand fn org url">
						<span class="organization-name">The Burnett Honors College</span>
					</a>
				</p>-->

			</div>
		</div><!-- .container -->
	</body>
	<?="\n".footer_()."\n"?>
</html>
