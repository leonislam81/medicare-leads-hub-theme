<?php
/**
 * Template Name: Contact Us
 *
 * @package Medicare_Leads_Hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$contact_page_show  = get_theme_mod( 'medicare_contact_page_show', true );
$contact_heading    = get_theme_mod( 'medicare_contact_page_heading', 'Ready to get help from our team?' );
$contact_intro      = get_theme_mod( 'medicare_contact_page_intro', 'Tell us what you need, where you are located, and how we can help. We will respond with clear next steps.' );
$form_heading       = get_theme_mod( 'medicare_contact_page_form_heading', 'Request a free quote' );
$service_area       = trim( (string) medicare_leads_hub_profile_value( 'medicare_contact_page_address', 'service_area', '' ) );
$business_profile   = medicare_leads_hub_get_business_profile();
if ( ! $service_area && ! empty( $business_profile['service_area'] ) ) {
	$service_area = trim( (string) $business_profile['service_area'] );
}
$phone              = medicare_leads_hub_profile_value( 'medicare_header_phone', 'phone', '' );
$phone_href         = preg_replace( '/[^0-9+]/', '', $phone );
$recipient_email    = medicare_leads_hub_contact_recipient_email();
$footer_email       = sanitize_email( get_theme_mod( 'medicare_footer_email', '' ) );
$contact_email      = $footer_email ? $footer_email : $recipient_email;
$map_configured_url = medicare_leads_hub_sanitize_map_embed_url( get_theme_mod( 'medicare_contact_page_map_embed_url', '' ) );
$map_open_url       = $map_configured_url;
$map_embed_url      = '';
$map_card_title     = $service_area ? $service_area : __( 'Our service area', 'medicare-leads-hub' );

// Google share/short links (for example maps.app.goo.gl) cannot be rendered
// inside an iframe. Keep those links usable for the button and fall back to
// the service-area map for the visual embed unless an actual Embed URL was
// provided.
if ( $map_configured_url && ( false !== stripos( $map_configured_url, '/maps/embed' ) || false !== stripos( $map_configured_url, 'output=embed' ) ) ) {
	$map_embed_url = $map_configured_url;
}

if ( ! $map_embed_url && $service_area ) {
	$map_embed_url = add_query_arg(
		array(
			'q'      => $service_area,
			'output' => 'embed',
		),
		'https://www.google.com/maps'
	);
}
$cf7_shortcode      = trim( (string) get_theme_mod( 'medicare_contact_page_cf7_shortcode', '' ) );
$cf7_ready          = $cf7_shortcode && shortcode_exists( 'contact-form-7' );

get_header();
?>
<main id="primary-content" class="site-main contact-page">
	<?php if ( $contact_page_show ) : ?>
		<?php if ( $map_embed_url ) : ?>
		<section class="contact-page__map" aria-label="Service area map">
			<iframe src="<?php echo esc_url( $map_embed_url ); ?>" title="Map showing our service area" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
			<div class="contact-page__map-card">
				<span class="contact-page__map-card-eyebrow"><?php esc_html_e( 'Serving locally', 'medicare-leads-hub' ); ?></span>
				<strong><?php echo esc_html( $map_card_title ); ?></strong>
				<a href="<?php echo esc_url( $map_open_url ? $map_open_url : 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $service_area ) ); ?>">
					<span><?php esc_html_e( 'Open in Google Maps', 'medicare-leads-hub' ); ?></span>
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 17 17 7"/><path d="M8 7h9v9"/></svg>
				</a>
			</div>
		</section>
		<?php endif; ?>

		<section class="contact-page__main" aria-labelledby="contact-page-title">
			<div class="contact-page__inner">
				<div class="contact-page__copy">
					<h1 id="contact-page-title"><?php echo esc_html( $contact_heading ); ?></h1>
					<span class="contact-page__accent-rule" aria-hidden="true"></span>
					<p><?php echo nl2br( esc_html( $contact_intro ) ); ?></p>
					<div class="contact-page__details">
						<?php if ( $service_area ) : ?>
							<div class="contact-page__detail">
								<span class="contact-page__detail-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg></span>
								<span><?php echo esc_html( $service_area ); ?></span>
							</div>
						<?php endif; ?>
						<?php if ( $phone ) : ?>
							<a class="contact-page__detail" href="tel:<?php echo esc_attr( $phone_href ); ?>">
								<span class="contact-page__detail-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7.3 3.8 5.7 4.7c-.7.4-1 1.2-.8 2 1.5 6.5 6.6 11.6 13.1 13.1.8.2 1.6-.1 2-.8l.9-1.6c.4-.7.2-1.6-.4-2.1l-2.1-1.7c-.6-.5-1.5-.5-2.1.1l-1.1 1.1a13.4 13.4 0 0 1-5.3-5.3L11 8.4c.6-.6.6-1.5.1-2.1L9.4 4.2c-.5-.6-1.4-.8-2.1-.4Z"/></svg></span>
								<span><?php echo esc_html( $phone ); ?></span>
							</a>
						<?php endif; ?>
						<?php if ( $contact_email ) : ?>
							<a class="contact-page__detail" href="mailto:<?php echo esc_attr( antispambot( $contact_email ) ); ?>">
								<span class="contact-page__detail-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg></span>
								<span><?php echo esc_html( antispambot( $contact_email ) ); ?></span>
							</a>
						<?php endif; ?>
					</div>
				</div>

				<div class="contact-page__form-panel">
					<h2><?php echo esc_html( $form_heading ); ?></h2>
					<?php if ( $cf7_ready ) : ?>
						<div class="contact-page__cf7">
							<?php echo do_shortcode( $cf7_shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Contact Form 7 returns its own form markup. ?>
						</div>
					<?php else : ?>
						<div class="contact-page__notice contact-page__notice--error" role="alert">
							<?php esc_html_e( 'This form is not configured yet. Please create a Contact Form 7 form and add its shortcode in the Contact Us page settings.', 'medicare-leads-hub' ); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
