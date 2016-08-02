			<div id="footer">

				<div id="footer-navwrap" role="navigation" class="screen-only">
					<?=wp_nav_menu(array(
						'theme_location' => 'footer-menu',
						'container' => 'false',
						'menu_class' => 'menu list-unstyled list-inline text-center',
						'menu_id' => 'footer-menu',
						'fallback_cb' => false,
						'depth' => 1,
						'walker' => new Bootstrap_Walker_Nav_Menu()
						));
					?>
				</div>
				<div id="footer-logo">
					<a href="http://tbhccmsdev.smca.ucf.edu">
						<img id="tbhcFooterLogo" src="<?php bloginfo('stylesheet_directory'); ?>/static/img/WebsiteFooter.png" alt="The Burnett Honors College">
						</img>
					</a>
				</div>
				<?=wp_nav_menu(array(
					'theme_location' => 'social-links',
					'container' => 'div',
					'container_id' => 'social-menu-wrap',
					'menu_class' => 'menu list-unstyled list-inline text-center screen-only',
					'menu_id' => 'social-menu',
					'depth' => 1,
					));
				?>

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
				</p>

			</div>
		</div><!-- .container -->
	</body>
	<?="\n".footer_()."\n"?>
</html>
