<?php
/**
 * Template Name: About Us
 *
 * @package Medicare_Leads_Hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$about_page_show = get_theme_mod( 'medicare_about_page_show', true );
$theme_version   = wp_get_theme()->get( 'Version' );
$hero_image_id   = absint( get_theme_mod( 'medicare_about_page_hero_image_id', 0 ) );
$hero_image      = $hero_image_id ? wp_get_attachment_image_url( $hero_image_id, 'full' ) : '';

if ( $hero_image && function_exists( 'medicare_leads_hub_preferred_image_url' ) ) {
	$hero_image = medicare_leads_hub_preferred_image_url( $hero_image );
}

if ( ! $hero_image && ! medicare_leads_hub_is_portable_starter() ) {
	$hero_image = get_template_directory_uri() . '/assets/images/about-locksmith-hero.webp';
}

$hero_image = add_query_arg( 'ver', rawurlencode( $theme_version ), $hero_image );
$overlay     = medicare_leads_hub_sanitize_range( get_theme_mod( 'medicare_about_page_hero_overlay', 68 ), 35, 90, 68 ) / 100;

$values = array();
for ( $index = 1; $index <= 4; $index++ ) {
	$values[] = array(
		'title' => get_theme_mod( 'medicare_about_page_value_' . $index . '_title', array( '', 'Integrity', 'Quality', 'Customer Oriented', 'Reliable' )[ $index ] ),
		'body'  => get_theme_mod( 'medicare_about_page_value_' . $index . '_body', array( '', 'We show up, follow through, and deliver exactly what we promise—every time.', 'We treat every project like it’s our own, ensuring smooth finishes and durable results.', 'We prioritize honest communication, transparent pricing, and a seamless experience from start to finish.', 'We respect your time, property, and trust—always showing up on schedule and cleaning up after ourselves.' )[ $index ] ),
		'icon'  => get_theme_mod( 'medicare_about_page_value_' . $index . '_icon', array( '', 'shield-check', 'medal-star', 'people-check', 'lock' )[ $index ] ),
	);
}

get_header();
?>
<main id="primary-content" class="site-main about-page">
	<?php if ( $about_page_show ) : ?>
		<section class="about-page__hero" aria-labelledby="about-page-title" style="--mlh-about-page-hero-image: url('<?php echo esc_url( $hero_image ); ?>'); --mlh-about-page-hero-overlay: <?php echo esc_attr( $overlay ); ?>;">
			<div class="about-page__hero-inner">
				<h1 id="about-page-title"><?php echo esc_html( get_theme_mod( 'medicare_about_page_hero_title', 'About our business' ) ); ?></h1>
				<p><?php echo esc_html( get_theme_mod( 'medicare_about_page_hero_intro', 'We provide dependable locksmith service with clear recommendations, careful workmanship, and respect for your property.' ) ); ?></p>
			</div>
		</section>

		<?php if ( get_theme_mod( 'medicare_about_page_values_show', true ) ) : ?>
			<section class="about-page__values" aria-labelledby="about-values-title">
				<div class="about-page__inner">
					<h2 id="about-values-title" class="screen-reader-text"><?php esc_html_e( 'Our values', 'medicare-leads-hub' ); ?></h2>
					<div class="about-page__values-grid">
						<?php foreach ( $values as $value ) : ?>
							<article class="about-page__value-card">
								<div class="about-page__value-icon-wrap">
									<?php echo medicare_leads_hub_about_value_icon_svg( $value['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
								<h3><?php echo esc_html( $value['title'] ); ?></h3>
								<p><?php echo esc_html( $value['body'] ); ?></p>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<section class="about-page__mission-vision" aria-labelledby="about-mission-title">
			<div class="about-page__inner about-page__mission-grid">
				<article class="about-page__mission-column">
					<h2 id="about-mission-title"><?php echo esc_html( get_theme_mod( 'medicare_about_page_mission_title', 'Mission' ) ); ?></h2>
					<span class="about-page__accent-rule" aria-hidden="true"></span>
					<p><?php echo esc_html( get_theme_mod( 'medicare_about_page_mission_body', 'Explain the standard of service, care, and communication your business promises to every customer.' ) ); ?></p>
				</article>
				<article class="about-page__mission-column">
					<h2><?php echo esc_html( get_theme_mod( 'medicare_about_page_vision_title', 'Vision' ) ); ?></h2>
					<span class="about-page__accent-rule" aria-hidden="true"></span>
					<p><?php echo esc_html( get_theme_mod( 'medicare_about_page_vision_body', 'Describe the future you are building and the experience you want customers to have when they choose your team.' ) ); ?></p>
				</article>
			</div>
		</section>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
