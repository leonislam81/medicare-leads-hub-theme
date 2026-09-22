<?php
/**
 * The custom theme footer.
 *
 * @package Medicare_Leads_Hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$business_profile  = medicare_leads_hub_get_business_profile();
$footer_phone      = medicare_leads_hub_profile_value( 'medicare_header_phone', 'phone', '' );
$mobile_action_call_number = trim( (string) get_theme_mod( 'medicare_mobile_action_bar_call_number', $footer_phone ) );
if ( ! $mobile_action_call_number ) {
	$mobile_action_call_number = $footer_phone;
}
$footer_phone_href       = preg_replace( '/[^0-9+]/', '', $footer_phone );
$mobile_call_phone_href  = preg_replace( '/[^0-9+]/', '', $mobile_action_call_number );
$mobile_action_show       = get_theme_mod( 'medicare_mobile_action_bar_show', true );
$mobile_action_call_text  = get_theme_mod( 'medicare_mobile_action_bar_call_text', 'Call Now' );
$mobile_action_quote_text = get_theme_mod( 'medicare_mobile_action_bar_quote_text', 'Get a Quote' );
$mobile_quote_default_url = medicare_leads_hub_contact_page_url();
$mobile_quote_url         = get_theme_mod( 'medicare_mobile_action_bar_quote_url', $mobile_quote_default_url );
if ( ! $mobile_quote_url || '#contact' === trim( $mobile_quote_url ) ) {
	$mobile_quote_url = $mobile_quote_default_url;
}
$mobile_action_background = get_theme_mod( 'medicare_mobile_action_bar_background', '#062A4A' );
$mobile_action_text_color = get_theme_mod( 'medicare_mobile_action_bar_text_color', '#ffffff' );
$footer_contact_label = get_theme_mod( 'medicare_footer_contact_label', medicare_leads_hub_is_portable_starter() ? 'Contact' : 'Service Area & Contact' );
$footer_service_area  = medicare_leads_hub_profile_value( 'medicare_footer_service_area', 'service_area', '' );
$footer_email         = medicare_leads_hub_profile_value( 'medicare_footer_email', 'email', '' );
$footer_copyright     = get_theme_mod( 'medicare_footer_copyright', medicare_leads_hub_is_portable_starter() ? ( $business_profile['name'] ? $business_profile['name'] . ' All rights reserved.' : '' ) : 'Locksmith Centennial Co. All rights reserved.' );
$footer_logo          = get_custom_logo();
$footer_social_links  = array(
	'facebook'  => array(
		'label' => __( 'Facebook', 'medicare-leads-hub' ),
		'url'   => get_theme_mod( 'medicare_footer_facebook_url', '' ),
	),
	'instagram' => array(
		'label' => __( 'Instagram', 'medicare-leads-hub' ),
		'url'   => get_theme_mod( 'medicare_footer_instagram_url', '' ),
	),
	'linkedin'  => array(
		'label' => __( 'LinkedIn', 'medicare-leads-hub' ),
		'url'   => get_theme_mod( 'medicare_footer_linkedin_url', '' ),
	),
);
$footer_menu = wp_nav_menu(
	array(
		'theme_location' => 'footer',
		'menu_id'       => 'footer-menu',
		'menu_class'    => 'site-footer__menu',
		'container'     => false,
		'fallback_cb'   => 'medicare_leads_hub_footer_fallback_menu',
		'echo'          => false,
	)
);

?>
	<footer class="site-footer">
		<div id="contact" class="site-footer__main">
			<div class="site-footer__inner">
				<div class="site-footer__contact">
					<p class="site-footer__eyebrow"><?php echo esc_html( $footer_contact_label ); ?></p>
					<?php if ( $footer_service_area ) : ?>
						<p class="site-footer__detail site-footer__detail--area">
							<span class="site-footer__detail-icon" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
							</span>
							<span><?php echo esc_html( $footer_service_area ); ?></span>
						</p>
					<?php endif; ?>
					<?php if ( $footer_phone ) : ?>
						<a class="site-footer__detail" href="tel:<?php echo esc_attr( $footer_phone_href ); ?>">
							<span class="site-footer__detail-icon" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M7.3 3.8 5.7 4.7c-.7.4-1 1.2-.8 2 1.5 6.5 6.6 11.6 13.1 13.1.8.2 1.6-.1 2-.8l.9-1.6c.4-.7.2-1.6-.4-2.1l-2.1-1.7c-.6-.5-1.5-.5-2.1.1l-1.1 1.1a13.4 13.4 0 0 1-5.3-5.3L11 8.4c.6-.6.6-1.5.1-2.1L9.4 4.2c-.5-.6-1.4-.8-2.1-.4Z"/></svg>
							</span>
							<span><?php echo esc_html( $footer_phone ); ?></span>
						</a>
					<?php endif; ?>
					<?php if ( $footer_email ) : ?>
						<a class="site-footer__detail" href="mailto:<?php echo esc_attr( antispambot( $footer_email ) ); ?>">
							<span class="site-footer__detail-icon" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg>
							</span>
							<span><?php echo esc_html( antispambot( $footer_email ) ); ?></span>
						</a>
					<?php endif; ?>
				</div>

				<div class="site-footer__brand">
					<div class="site-footer__logo">
						<?php if ( $footer_logo ) : ?>
							<?php echo $footer_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress returns escaped custom-logo markup. ?>
						<?php else : ?>
							<a class="site-footer__fallback-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
						<?php endif; ?>
					</div>
					<div class="site-footer__social" aria-label="<?php esc_attr_e( 'Social links', 'medicare-leads-hub' ); ?>">
						<?php foreach ( $footer_social_links as $network => $social ) : ?>
							<?php if ( $social['url'] ) : ?>
								<a class="site-footer__social-link site-footer__social-link--<?php echo esc_attr( $network ); ?>" href="<?php echo esc_url( $social['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $social['label'] ); ?>">
									<?php if ( 'facebook' === $network ) : ?>
										<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14.2 8.2h2.9V5h-2.9c-2.5 0-4.2 1.8-4.2 4.4v2H7v3.2h3v6.2h3.4v-6.2h2.9l.6-3.2h-3.5v-1.7c0-.9.3-1.5.8-1.5Z"/></svg>
									<?php elseif ( 'instagram' === $network ) : ?>
										<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3.5" y="3.5" width="17" height="17" rx="4"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r=".8" fill="currentColor" stroke="none"/></svg>
									<?php else : ?>
										<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 8.2A2.2 2.2 0 1 1 5 3.8a2.2 2.2 0 0 1 0 4.4ZM3.2 20.2V9.8h3.6v10.4H3.2Zm5.8 0V9.8h3.5v1.4h.1c.5-.9 1.7-1.9 3.5-1.9 3.8 0 4.5 2.5 4.5 5.8v5.1H17v-4.5c0-1.1 0-2.6-1.6-2.6s-1.9 1.2-1.9 2.5v4.6H9Z"/></svg>
									<?php endif; ?>
								</a>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>

		<div class="site-footer__bottom">
			<div class="site-footer__bottom-inner">
				<small>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( $footer_copyright ); ?></small>
				<?php if ( $footer_menu ) : ?>
					<nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Footer menu', 'medicare-leads-hub' ); ?>">
						<?php echo $footer_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_nav_menu() returns escaped menu markup. ?>
					</nav>
				<?php endif; ?>
			</div>
		</div>
	</footer>

	<?php if ( $mobile_action_show ) : ?>
	<nav class="mobile-action-bar" style="--mlh-mobile-action-background: <?php echo esc_attr( $mobile_action_background ); ?>; --mlh-mobile-action-text: <?php echo esc_attr( $mobile_action_text_color ); ?>;" aria-label="<?php esc_attr_e( 'Quick actions', 'medicare-leads-hub' ); ?>">
		<?php if ( $mobile_action_call_number && $mobile_call_phone_href ) : ?>
			<a class="mobile-action-bar__button" href="tel:<?php echo esc_attr( $mobile_call_phone_href ); ?>">
				<svg class="mobile-action-bar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<path d="M7.3 3.8 5.7 4.7c-.7.4-1 1.2-.8 2 1.5 6.5 6.6 11.6 13.1 13.1.8.2 1.6-.1 2-.8l.9-1.6c.4-.7.2-1.6-.4-2.1l-2.1-1.7c-.6-.5-1.5-.5-2.1.1l-1.1 1.1a13.4 13.4 0 0 1-5.3-5.3L11 8.4c.6-.6.6-1.5.1-2.1L9.4 4.2c-.5-.6-1.4-.8-2.1-.4Z" />
				</svg>
				<span><?php echo esc_html( $mobile_action_call_text ); ?></span>
			</a>
		<?php endif; ?>
		<a class="mobile-action-bar__button mobile-action-bar__button--quote" href="<?php echo esc_url( $mobile_quote_url ); ?>">
			<svg class="mobile-action-bar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<rect x="5" y="3" width="14" height="18" rx="2" />
				<path d="M8 7h8M8 11h8M8 15h5" />
			</svg>
			<span><?php echo esc_html( $mobile_action_quote_text ); ?></span>
		</a>
	</nav>
	<?php endif; ?>
	<?php wp_footer(); ?>
</body>
</html>
