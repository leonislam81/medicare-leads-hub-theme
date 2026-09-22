<?php
/**
 * The custom theme header.
 *
 * @package Medicare_Leads_Hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$business_profile = medicare_leads_hub_get_business_profile();
$announcement    = medicare_leads_hub_profile_value( 'medicare_announcement_text', 'service_area', '' );
$announcement    = $announcement ? ( false !== stripos( $announcement, 'serving ' ) ? $announcement : sprintf( 'Serving %s', $announcement ) ) : $business_profile['tagline'];
$phone           = medicare_leads_hub_profile_value( 'medicare_header_phone', 'phone', '' );
$phone_href      = preg_replace( '/[^0-9+]/', '', $phone );
$cta_text        = medicare_leads_hub_profile_value( 'medicare_header_cta_text', 'cta_text', 'Get A Free Quote' );
$cta_url         = medicare_leads_hub_resolve_contact_cta_url( medicare_leads_hub_profile_value( 'medicare_header_cta_url', 'cta_url', '#contact' ) );
$show_announce   = get_theme_mod( 'medicare_show_announcement', true );
$sticky_class    = get_theme_mod( 'medicare_sticky_header', true ) ? ' is-sticky' : '';
$theme_version   = wp_get_theme()->get( 'Version' );
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text" href="#primary-content"><?php esc_html_e( 'Skip to content', 'medicare-leads-hub' ); ?></a>

<div class="site-header-stack<?php echo esc_attr( $sticky_class ); ?>">
	<?php if ( $show_announce && $announcement ) : ?>
		<div class="site-announcement" role="note">
			<div class="site-announcement__inner">
				<span class="site-announcement__icon" aria-hidden="true">
					<img class="site-announcement__icon-image" src="<?php echo esc_url( get_template_directory_uri() . '/assets/icons/location-pin-red.webp?ver=' . rawurlencode( $theme_version ) ); ?>" alt="" width="18" height="26" decoding="async" />
				</span>
				<span><?php echo esc_html( $announcement ); ?></span>
			</div>
		</div>
	<?php endif; ?>

<header class="site-header<?php echo esc_attr( $sticky_class ); ?>">
	<div class="site-header__inner">
		<div class="site-branding">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
			<?php endif; ?>
		</div>

		<nav class="site-navigation" aria-label="<?php esc_attr_e( 'Primary menu', 'medicare-leads-hub' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'menu_id'       => 'primary-menu',
					'container'      => false,
					'fallback_cb'    => 'medicare_leads_hub_fallback_menu',
				)
			);
			?>
		</nav>

		<div class="site-header__actions">
			<?php if ( $phone ) : ?>
				<a class="pro-call-button" href="tel:<?php echo esc_attr( $phone_href ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Call %s', 'medicare-leads-hub' ), $phone ) ); ?>">
					<svg class="pro-call-button__icon" viewBox="0 0 58 42" aria-hidden="true" focusable="false">
						<g class="pro-call-button__handset">
							<path d="M11 7.5 7.8 9.4c-.9.5-1.3 1.5-1 2.5 1.9 8.2 8.3 14.6 16.5 16.5 1 .2 2-.1 2.5-1l1.9-3.2c.5-.9.3-2-.5-2.6l-3.6-2.8c-.8-.6-1.9-.6-2.6.1l-1.9 1.8c-1.8-1-3.4-2.6-4.4-4.4l1.8-1.9c.7-.7.7-1.8.1-2.6l-2.8-3.6c-.7-.8-1.8-1-2.8-.5Z" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" />
						</g>
						<path class="pro-call-button__wave pro-call-button__wave--inner" d="M30 10c7 1.4 10.8 5.5 10.8 11.6" />
						<path class="pro-call-button__wave pro-call-button__wave--outer" d="M34 4.5c10 2.2 15 8.4 15 17" />
					</svg>
					<span><?php echo esc_html( $phone ); ?></span>
				</a>
			<?php endif; ?>
			<a class="header-cta" href="<?php echo esc_url( $cta_url ); ?>"><?php echo esc_html( $cta_text ); ?></a>
			<button class="menu-toggle" type="button" aria-controls="primary-menu" aria-expanded="false" aria-label="<?php esc_attr_e( 'Open menu', 'medicare-leads-hub' ); ?>">
				<span class="screen-reader-text"><?php esc_html_e( 'Toggle menu', 'medicare-leads-hub' ); ?></span>
				<svg class="menu-toggle__open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
				<svg class="menu-toggle__close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="m6 6 12 12M18 6 6 18"/></svg>
			</button>
		</div>
	</div>
</header>
</div>
