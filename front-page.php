<?php
/**
 * Homepage hero section.
 *
 * @package Medicare_Leads_Hub
 */

get_header();

$business_profile = medicare_leads_hub_get_business_profile();
$hero_image_id   = absint( get_theme_mod( 'medicare_hero_image_id', 0 ) );
$hero_image_html = $hero_image_id ? wp_get_attachment_image(
	$hero_image_id,
	'full',
	false,
	array(
		'class'        => 'hero-section__image',
		'alt'          => '',
		'loading'      => 'eager',
		'fetchpriority' => 'high',
		'decoding'     => 'async',
		'sizes'        => '100vw',
	)
) : '';
$hero_title      = get_theme_mod( 'medicare_hero_title', $business_profile['name'] ? $business_profile['name'] . ' services you can count on' : 'Reliable service for your home or business' );
$hero_intro      = get_theme_mod( 'medicare_hero_intro', $business_profile['tagline'] );
$hero_supporting = get_theme_mod( 'medicare_hero_supporting', 'Clear recommendations, professional workmanship, and dependable support from first contact to final check.' );
$primary_text    = get_theme_mod( 'medicare_hero_primary_text', 'Get A Free Estimate' );
$primary_url     = medicare_leads_hub_resolve_contact_cta_url( get_theme_mod( 'medicare_hero_primary_url', '#contact' ) );
$show_phone      = get_theme_mod( 'medicare_hero_show_phone', true );
$phone           = medicare_leads_hub_profile_value( 'medicare_header_phone', 'phone', '' );
$phone_href      = preg_replace( '/[^0-9+]/', '', $phone );
$show_why        = get_theme_mod( 'medicare_why_show', true );
$why_heading     = get_theme_mod( 'medicare_why_heading', 'Why Choose Us' );
$theme_version   = wp_get_theme()->get( 'Version' );
$show_process    = get_theme_mod( 'medicare_process_show', true );
$process_heading = get_theme_mod( 'medicare_process_heading', 'Get Your Service Done in 3 Easy Steps' );
$process_image_id  = absint( get_theme_mod( 'medicare_process_image_id', 0 ) );
$process_steps     = array( 1, 2, 3 );
$show_services     = get_theme_mod( 'medicare_services_show', true );
$services_heading  = get_theme_mod( 'medicare_services_heading', 'Services designed around your needs.' );
$show_specialty_services      = get_theme_mod( 'medicare_specialty_services_show', true );
$specialty_services_heading   = get_theme_mod( 'medicare_specialty_services_heading', 'Specialty Services' );
$specialty_services_background = get_theme_mod( 'medicare_specialty_services_background', '#ffffff' );
$show_commercial_services      = get_theme_mod( 'medicare_commercial_services_show', true );
$commercial_services_heading   = get_theme_mod( 'medicare_commercial_services_heading', 'Commercial Services' );
$commercial_services_background = get_theme_mod( 'medicare_commercial_services_background', '#F3F7FA' );
$show_locksmith_faq            = get_theme_mod( 'medicare_locksmith_faq_show', true );
$locksmith_faq_eyebrow         = get_theme_mod( 'medicare_locksmith_faq_eyebrow', 'Service Support' );
$locksmith_faq_background      = get_theme_mod( 'medicare_locksmith_faq_background', '#F3F7FA' );
$locksmith_faq_image_id        = absint( get_theme_mod( 'medicare_locksmith_faq_image_id', 0 ) );
$show_locksmith_pricing        = get_theme_mod( 'medicare_locksmith_pricing_show', true );
$locksmith_pricing_background   = get_theme_mod( 'medicare_locksmith_pricing_background', '#F3F7FA' );
$locksmith_pricing_left_heading = get_theme_mod( 'medicare_locksmith_pricing_left_heading', 'Reliable services at fair prices' );
$locksmith_pricing_left_content = get_theme_mod( 'medicare_locksmith_pricing_left_content', medicare_leads_hub_locksmith_pricing_content_default() );
$show_locksmith_testimonials    = get_theme_mod( 'medicare_locksmith_testimonials_show', true );
$locksmith_testimonials_background = get_theme_mod( 'medicare_locksmith_testimonials_background', '#ffffff' );
$locksmith_testimonials_eyebrow = get_theme_mod( 'medicare_locksmith_testimonials_eyebrow', 'Client Testimonials' );
$locksmith_testimonials_heading = get_theme_mod( 'medicare_locksmith_testimonials_heading', 'Hear it from our happy clients!' );
$locksmith_testimonial_items = medicare_leads_hub_get_testimonial_items();
$show_expectations              = get_theme_mod( 'medicare_expectations_show', true );
$expectations_heading           = get_theme_mod( 'medicare_expectations_heading', 'What to expect when you hire our locksmith team' );
$expectations_background        = get_theme_mod( 'medicare_expectations_background', '#062A4A' );
$expectations_overlay           = absint( get_theme_mod( 'medicare_expectations_overlay', 76 ) );
$expectations_background_id     = absint( get_theme_mod( 'medicare_expectations_background_image_id', 0 ) );
$expectations_background_url     = $expectations_background_id ? wp_get_attachment_image_url( $expectations_background_id, 'full' ) : '';
$expectations_cards             = array(
	1 => array( 'Share the Problem', 'Tell us what happened, where you are, and what kind of lock or access issue you are facing.', 'navy' ),
	2 => array( 'Get Clear Recommendations', 'We assess the situation, explain your options, and recommend the right repair, rekey, or upgrade.', 'gold' ),
	3 => array( 'Approve the Plan', 'You receive a straightforward estimate before work begins, with no pressure and no hidden surprises.', 'navy' ),
	4 => array( 'Professional Service', 'Our locksmith completes the work carefully, tests the result, and keeps your property protected.', 'gold' ),
	5 => array( 'Secure the Finish', 'We confirm everything works smoothly and show you what to expect from your new hardware.', 'navy' ),
	6 => array( 'Stay Protected', 'Leave with dependable access and practical next steps for your home, vehicle, or business.', 'gold' ),
);
$show_booking_faq             = get_theme_mod( 'medicare_booking_faq_show', true );
$booking_faq_background       = get_theme_mod( 'medicare_booking_faq_background', '#ffffff' );
$booking_faq_eyebrow          = get_theme_mod( 'medicare_booking_faq_eyebrow', 'FAQ' );
$booking_faq_heading          = get_theme_mod( 'medicare_booking_faq_heading', 'Questions before you book?' );
$booking_faq_intro            = get_theme_mod( 'medicare_booking_faq_intro', 'Here are answers to common service questions.' );
	$show_about        = get_theme_mod( 'medicare_about_show', true );
	$about_heading     = get_theme_mod( 'medicare_about_heading', 'Experience you can count on' );
	$about_content_default = implode(
		"\n\n",
		array_filter(
			array(
				get_theme_mod( 'medicare_about_paragraph_1', 'Tell visitors who you help, what you do, and why your team is a dependable choice.' ),
				get_theme_mod( 'medicare_about_paragraph_2', 'Use this space to explain your service area, process, and the practical results customers can expect.' ),
				get_theme_mod( 'medicare_about_paragraph_3', 'Add your experience, values, guarantees, and the details that make your business different.' ),
			)
		)
	);
	$about_content = get_theme_mod( 'medicare_about_content', $about_content_default );
$why_icons       = array(
	'shield-check'    => get_template_directory_uri() . '/assets/icons/shield-check.svg?ver=' . rawurlencode( $theme_version ),
	'emergency-clock' => get_template_directory_uri() . '/assets/icons/emergency-clock.svg?ver=' . rawurlencode( $theme_version ),
	'location-pin'    => get_template_directory_uri() . '/assets/icons/location-pin.svg?ver=' . rawurlencode( $theme_version ),
);
$why_cards        = array( 1, 2, 3 );
?>
<main id="primary-content" class="site-main">
	<section class="hero-section" aria-labelledby="hero-title">
		<?php if ( $hero_image_html ) : ?>
			<div class="hero-section__media" aria-hidden="true">
				<?php echo $hero_image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() returns safe, escaped markup. ?>
			</div>
		<?php endif; ?>
		<div class="hero-section__overlay" aria-hidden="true"></div>
		<div class="hero-section__inner">
			<div class="hero-section__card">
				<h1 id="hero-title"><?php echo esc_html( $hero_title ); ?></h1>
				<?php if ( $hero_intro ) : ?>
					<p class="hero-section__intro"><?php echo nl2br( esc_html( $hero_intro ) ); ?></p>
				<?php endif; ?>
				<?php if ( $hero_supporting ) : ?>
					<p class="hero-section__supporting"><?php echo nl2br( esc_html( $hero_supporting ) ); ?></p>
				<?php endif; ?>
				<div class="hero-section__actions">
					<a class="hero-button hero-button--primary" href="<?php echo esc_url( $primary_url ); ?>"><?php echo esc_html( $primary_text ); ?></a>
					<?php if ( $show_phone && $phone ) : ?>
						<a class="hero-button hero-button--phone" href="tel:<?php echo esc_attr( $phone_href ); ?>">
							<svg class="hero-button__icon" viewBox="0 0 58 42" aria-hidden="true" focusable="false">
								<g class="pro-call-button__handset">
									<path d="M11 7.5 7.8 9.4c-.9.5-1.3 1.5-1 2.5 1.9 8.2 8.3 14.6 16.5 16.5 1 .2 2-.1 2.5-1l1.9-3.2c.5-.9.3-2-.5-2.6l-3.6-2.8c-.8-.6-1.9-.6-2.6.1l-1.9 1.8c-1.8-1-3.4-2.6-4.4-4.4l1.8-1.9c.7-.7.7-1.8.1-2.6l-2.8-3.6c-.7-.8-1.8-1-2.8-.5Z" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" />
								</g>
								<path class="pro-call-button__wave pro-call-button__wave--inner" d="M30 10c7 1.4 10.8 5.5 10.8 11.6" />
								<path class="pro-call-button__wave pro-call-button__wave--outer" d="M34 4.5c10 2.2 15 8.4 15 17" />
							</svg>
							<span><?php echo esc_html( $phone ); ?></span>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>

	<?php if ( $show_why ) : ?>
		<section id="why-choose-us" class="why-section" aria-labelledby="why-title">
			<div class="why-section__inner">
				<div class="why-section__heading">
					<div class="why-section__eyebrow">
						<span class="why-section__eyebrow-line" aria-hidden="true"></span>
						<h2 id="why-title"><?php echo esc_html( $why_heading ); ?></h2>
						<span class="why-section__eyebrow-line" aria-hidden="true"></span>
					</div>
				</div>

				<div class="why-section__grid">
					<?php foreach ( $why_cards as $card_index ) : ?>
						<?php
						$icon_key = sanitize_key( get_theme_mod( 'medicare_why_card_' . $card_index . '_icon', array( 1 => 'shield-check', 2 => 'emergency-clock', 3 => 'location-pin' )[ $card_index ] ) );
						$icon_url = isset( $why_icons[ $icon_key ] ) ? $why_icons[ $icon_key ] : $why_icons['shield-check'];
						$card_title = get_theme_mod( 'medicare_why_card_' . $card_index . '_title', array( 1 => 'Licensed & Insured', 2 => '24/7 Emergency Response', 3 => 'Local & Trusted' )[ $card_index ] );
						$card_body  = get_theme_mod( 'medicare_why_card_' . $card_index . '_body', array( 1 => 'Show customers the training, care, or standards your team brings to every project.', 2 => 'Explain how customers can reach you and what they can expect after contacting your team.', 3 => 'Add the service area and trust signal that matter most to your customers.' )[ $card_index ] );
						?>
						<article class="why-card">
							<div class="why-card__icon-wrap">
								<img class="why-card__icon" src="<?php echo esc_url( $icon_url ); ?>" alt="" loading="lazy" width="88" height="88" />
							</div>
							<h3><?php echo esc_html( $card_title ); ?></h3>
							<span class="why-card__rule" aria-hidden="true"></span>
							<p><?php echo nl2br( esc_html( $card_body ) ); ?></p>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $show_about ) : ?>
		<section id="about-experience" class="about-section" aria-labelledby="about-title">
			<div class="about-section__inner">
				<div class="about-section__visual">
					<h2 id="about-title"><?php echo esc_html( $about_heading ); ?></h2>
					<span class="about-section__rule" aria-hidden="true"></span>
				</div>

				<div class="about-section__copy">
					<?php echo wp_kses_post( wpautop( $about_content ) ); ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $show_process ) : ?>
		<?php
		$step_defaults = array(
			1 => array( 'Call Us', 'Tell our team what you need—lockout help, rekeying, installation, or security support.' ),
			2 => array( 'Get A Free Quote', 'After we understand the scope and location, we provide a clear, no-obligation estimate.' ),
			3 => array( 'Schedule Service', 'Choose a convenient time and our certified crew will arrive ready to work safely.' ),
		);
		$process_image_html = $process_image_id ? wp_get_attachment_image(
			$process_image_id,
			'full',
			false,
			array(
				'class'        => 'process-single-image__image',
				'loading'      => 'lazy',
				'decoding'     => 'async',
				'sizes'        => '(max-width: 760px) 82vw, 540px',
			)
		) : '';
		?>
		<section id="how-it-works" class="process-section" aria-labelledby="process-title">
			<div class="process-section__inner">
				<div class="process-section__content">
					<h2 id="process-title"><?php echo esc_html( $process_heading ); ?></h2>
					<ol class="process-steps">
						<?php foreach ( $process_steps as $step_index ) : ?>
							<?php
							$step_title = get_theme_mod( 'medicare_process_step_' . $step_index . '_title', $step_defaults[ $step_index ][0] );
							$step_body  = get_theme_mod( 'medicare_process_step_' . $step_index . '_body', $step_defaults[ $step_index ][1] );
							?>
							<li class="process-step">
								<span class="process-step__number" aria-hidden="true"><?php echo esc_html( $step_index ); ?></span>
								<div class="process-step__body">
									<h3>
										<?php echo esc_html( $step_title ); ?>
										<?php if ( 1 === $step_index ) : ?>
											<span class="process-step__phone"><?php echo esc_html( $phone ); ?></span>
										<?php endif; ?>
									</h3>
									<p><?php echo nl2br( esc_html( $step_body ) ); ?></p>
								</div>
							</li>
						<?php endforeach; ?>
					</ol>
				</div>

				<div class="process-section__aside">
					<?php if ( $process_image_html ) : ?>
						<div class="process-single-image">
							<?php echo $process_image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() returns safe, escaped markup. ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $show_services ) : ?>
		<?php
		$service_cards = array(
			1 => array( 'Residential Lock Repair', 'Repair, adjust, and restore reliable locks for doors, gates, bedrooms, and entry points around your home.', 33 ),
			2 => array( 'Rekeying & Key Replacement', 'Restore control after a move, lost key, tenant change, or any time you want a fresh set of working keys.', 34 ),
			3 => array( 'Emergency Lockout Service', 'Fast, professional assistance when you are locked out of your home, office, or vehicle.', 35 ),
			4 => array( 'Smart Lock Installation', 'Upgrade everyday entry with practical smart locks, keypad systems, and connected access options.', 36 ),
			5 => array( 'Car Lockout & Key Service', 'Get dependable help with vehicle lockouts, replacement keys, and common automotive access problems.', 37 ),
			6 => array( 'High-Security Lock Upgrades', 'Strengthen doors and entry points with dependable hardware selected for your property and security needs.', 32 ),
		);
		$services_heading_words = preg_split( '/\s+/', trim( $services_heading ) );
		$services_heading_last  = array_pop( $services_heading_words );
		$services_heading_base  = implode( ' ', $services_heading_words );
		?>
		<section id="services-grid" class="services-section" aria-labelledby="services-title">
			<div class="services-section__background" aria-hidden="true"></div>
			<div class="services-section__heading-band">
				<header class="services-section__heading">
					<h2 id="services-title">
						<?php echo esc_html( $services_heading_base ); ?>
						<?php if ( $services_heading_last ) : ?>
							<span><?php echo esc_html( $services_heading_last ); ?></span>
						<?php endif; ?>
					</h2>
				</header>
			</div>

			<div class="services-section__inner">
				<div class="services-grid">
			<?php foreach ( $service_cards as $card_index => $card ) : ?>
				<?php
				$card_title    = get_theme_mod( 'medicare_service_card_' . $card_index . '_title', $card[0] );
				$card_body     = get_theme_mod( 'medicare_service_card_' . $card_index . '_body', $card[1] );
				$card_image_id = absint( get_theme_mod( 'medicare_service_card_' . $card_index . '_image_id', $card[2] ) );
						$card_image     = $card_image_id ? wp_get_attachment_image(
							$card_image_id,
							'full',
							false,
							array(
								'class'        => 'service-card__image',
								'loading'      => 'lazy',
								'decoding'     => 'async',
								'sizes'        => '(max-width: 760px) 100vw, (max-width: 980px) 50vw, 33vw',
							)
						) : '';
						?>
						<article class="service-card">
							<div class="service-card__media">
								<?php if ( $card_image ) : ?>
									<?php echo $card_image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() returns safe, escaped markup. ?>
								<?php endif; ?>
				</div>
				<div class="service-card__body">
					<h3><?php echo esc_html( $card_title ); ?></h3>
					<?php if ( $card_body ) : ?>
						<p><?php echo nl2br( esc_html( $card_body ) ); ?></p>
					<?php endif; ?>
				</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $show_commercial_services ) : ?>
		<?php
		$commercial_service_cards = array(
			1 => array( 'Master Key Systems', 'Organize access across offices, suites, and facilities with a dependable master key plan.', 32 ),
			2 => array( 'High-Security Locks', 'Upgrade doors and entry points with commercial-grade locks selected for your property.', 33 ),
			3 => array( 'Commercial Rekeying', 'Restore control after staff changes, tenant turnover, or a lost key without replacing every lock.', 34 ),
			4 => array( 'Business Lockout Service', 'Get fast, professional help when your team is locked out and your business needs to stay moving.', 35 ),
			5 => array( 'Access Control Installation', 'Improve convenience and accountability with practical keypad, card, and electronic entry solutions.', 36 ),
			6 => array( 'Safe & Vault Services', 'Get reliable opening, repair, and security support for safes and vaults used by your business.', 37 ),
		);
		$commercial_heading_words = preg_split( '/\s+/', trim( $commercial_services_heading ) );
		$commercial_heading_last  = array_pop( $commercial_heading_words );
		$commercial_heading_base  = implode( ' ', $commercial_heading_words );
		?>
		<section id="commercial-services-grid" class="services-section services-section--commercial" aria-labelledby="commercial-services-title" style="--mlh-services-background: <?php echo esc_attr( $commercial_services_background ); ?>;">
			<div class="services-section__background" aria-hidden="true"></div>
			<div class="services-section__heading-band">
				<header class="services-section__heading">
					<h2 id="commercial-services-title">
						<?php echo esc_html( $commercial_heading_base ); ?>
						<?php if ( $commercial_heading_last ) : ?>
							<span><?php echo esc_html( $commercial_heading_last ); ?></span>
						<?php endif; ?>
					</h2>
				</header>
			</div>

			<div class="services-section__inner">
				<div class="services-grid">
			<?php foreach ( $commercial_service_cards as $card_index => $card ) : ?>
				<?php
				$card_title    = get_theme_mod( 'medicare_commercial_service_card_' . $card_index . '_title', $card[0] );
				$card_body     = get_theme_mod( 'medicare_commercial_service_card_' . $card_index . '_body', $card[1] );
				$card_image_id = absint( get_theme_mod( 'medicare_commercial_service_card_' . $card_index . '_image_id', $card[2] ) );
						$card_image     = $card_image_id ? wp_get_attachment_image(
							$card_image_id,
							'full',
							false,
							array(
								'class'        => 'service-card__image',
								'loading'      => 'lazy',
								'decoding'     => 'async',
								'sizes'        => '(max-width: 760px) 100vw, (max-width: 980px) 50vw, 33vw',
							)
						) : '';
						?>
						<article class="service-card">
							<div class="service-card__media">
								<?php if ( $card_image ) : ?>
									<?php echo $card_image; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() returns safe, escaped markup. ?>
								<?php endif; ?>
				</div>
				<div class="service-card__body">
					<h3><?php echo esc_html( $card_title ); ?></h3>
					<?php if ( $card_body ) : ?>
						<p><?php echo nl2br( esc_html( $card_body ) ); ?></p>
					<?php endif; ?>
				</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $show_specialty_services ) : ?>
		<?php
		$specialty_service_cards = array(
			1 => array( 'Emergency Lockout Service', 'Fast, professional assistance when you are locked out of your home, office, or vehicle.' ),
			2 => array( 'Key Duplication & Replacement', 'Accurate replacement keys and practical solutions for everyday access needs.' ),
			3 => array( 'High-Security Lock Upgrades', 'Upgrade vulnerable entry points with dependable hardware and stronger protection.' ),
			4 => array( 'Access Control Systems', 'Modern keypad, card, and electronic access solutions for easier entry management.' ),
			5 => array( 'Master Key Systems', 'Organize access across multiple doors with a clear, convenient key system.' ),
			6 => array( 'Safe & Vault Services', 'Professional support for business and residential safes, including opening and maintenance.' ),
		);
		?>
		<section id="specialty-services-grid" class="services-section services-section--specialty" aria-labelledby="specialty-services-title" style="--mlh-services-background: <?php echo esc_attr( $specialty_services_background ); ?>;">
			<div class="services-section__inner">
				<header class="specialty-section__heading">
					<h2 id="specialty-services-title"><?php echo esc_html( $specialty_services_heading ); ?></h2>
				</header>

				<div class="specialty-services-grid">
					<?php foreach ( $specialty_service_cards as $card_index => $card ) : ?>
						<?php
						$card_title = get_theme_mod( 'medicare_specialty_service_card_' . $card_index . '_title', $card[0] );
						$card_body  = get_theme_mod( 'medicare_specialty_service_card_' . $card_index . '_body', $card[1] );
						?>
						<article class="specialty-service-card">
							<span class="specialty-service-card__marker" aria-hidden="true"></span>
							<div class="specialty-service-card__body">
								<h3><?php echo esc_html( $card_title ); ?></h3>
								<?php if ( $card_body ) : ?>
									<p><?php echo nl2br( esc_html( $card_body ) ); ?></p>
								<?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $show_locksmith_faq ) : ?>
		<?php
		$locksmith_process_info_content = get_theme_mod( 'medicare_locksmith_process_content', medicare_leads_hub_locksmith_process_content_default() );
		$locksmith_process_info_image_html = '';
		if ( $locksmith_faq_image_id ) {
			$locksmith_process_info_image_html = wp_get_attachment_image(
				$locksmith_faq_image_id,
				'full',
				false,
				array(
					'class'        => 'locksmith-process-info-section__image',
					'loading'      => 'lazy',
					'decoding'     => 'async',
					'alt'          => __( 'Professional service technician', 'medicare-leads-hub' ),
					'sizes'        => '(max-width: 980px) 82vw, 44vw',
				)
			);
		}
		?>
		<section id="locksmith-support-process" class="locksmith-process-info-section" aria-label="<?php esc_attr_e( 'Locksmith Support Process', 'medicare-leads-hub' ); ?>" style="--mlh-locksmith-process-background: <?php echo esc_attr( $locksmith_faq_background ); ?>;">
			<div class="locksmith-process-info-section__inner">
				<div class="locksmith-process-info-section__visual">
					<?php echo $locksmith_process_info_image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() returns safe, escaped markup. ?>
					</div>

				<div class="locksmith-process-info-section__panel">
					<div class="locksmith-process-info-section__eyebrow">
						<span class="locksmith-process-info-section__eyebrow-line" aria-hidden="true"></span>
						<span><?php echo esc_html( $locksmith_faq_eyebrow ); ?></span>
					</div>
					<div class="locksmith-process-info-section__content">
						<article class="locksmith-process-info-block locksmith-process-info-block--rich">
							<?php echo wp_kses_post( wpautop( $locksmith_process_info_content ) ); ?>
						</article>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $show_locksmith_pricing ) : ?>
		<?php
		$locksmith_pricing_rows = medicare_leads_hub_get_locksmith_pricing_rows();
		$pricing_header_service = get_theme_mod( 'medicare_locksmith_pricing_header_service', 'Service Type' );
		$pricing_header_typical = get_theme_mod( 'medicare_locksmith_pricing_header_typical', 'Typical Service Cost' );
		$pricing_header_project = get_theme_mod( 'medicare_locksmith_pricing_header_project', 'Average Project Cost' );
		?>
		<section id="locksmith-pricing" class="locksmith-pricing-section" aria-label="Service pricing" style="--mlh-pricing-background: <?php echo esc_attr( $locksmith_pricing_background ); ?>;">
			<div class="locksmith-pricing-section__decor locksmith-pricing-section__decor--top" aria-hidden="true"></div>
			<div class="locksmith-pricing-section__decor locksmith-pricing-section__decor--bottom" aria-hidden="true"></div>
			<div class="locksmith-pricing-section__inner">
				<div class="locksmith-pricing-section__content">
				<div class="locksmith-pricing-section__copy">
					<h3><?php echo esc_html( $locksmith_pricing_left_heading ); ?></h3>
					<?php echo wp_kses_post( wpautop( $locksmith_pricing_left_content ) ); ?>
				</div>

					<div class="locksmith-pricing-table-wrap">
						<table class="locksmith-pricing-table">
							<thead>
								<tr>
									<th scope="col"><?php echo esc_html( $pricing_header_service ); ?></th>
									<th scope="col"><?php echo esc_html( $pricing_header_typical ); ?></th>
									<th scope="col"><?php echo esc_html( $pricing_header_project ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $locksmith_pricing_rows as $row ) : ?>
									<tr>
										<th scope="row"><?php echo esc_html( $row['service'] ); ?></th>
										<td><?php echo esc_html( $row['typical'] ); ?></td>
										<td><?php echo esc_html( $row['project'] ); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $show_expectations ) : ?>
		<section id="what-to-expect" class="expectations-section" aria-labelledby="expectations-title" style="--mlh-expectations-background: <?php echo esc_attr( $expectations_background ); ?>; --mlh-expectations-overlay: <?php echo esc_attr( max( 35, min( 90, $expectations_overlay ) ) / 100 ); ?>;<?php echo $expectations_background_url ? ' --mlh-expectations-image: url(\'' . esc_url( $expectations_background_url ) . '\');' : ''; ?>">
			<div class="expectations-section__overlay" aria-hidden="true"></div>
			<div class="expectations-section__inner">
				<header class="expectations-section__heading">
					<h2 id="expectations-title"><?php echo nl2br( esc_html( $expectations_heading ) ); ?></h2>
				</header>

				<div class="expectations-grid">
					<?php foreach ( $expectations_cards as $card_index => $card ) : ?>
						<?php
						$card_title = get_theme_mod( 'medicare_expectations_card_' . $card_index . '_title', $card[0] );
						$card_body  = get_theme_mod( 'medicare_expectations_card_' . $card_index . '_body', $card[1] );
						$card_tone  = get_theme_mod( 'medicare_expectations_card_' . $card_index . '_tone', $card[2] );
						$card_tone  = medicare_leads_hub_sanitize_expectations_tone( $card_tone );
						?>
						<article class="expectations-card expectations-card--<?php echo esc_attr( $card_tone ); ?>">
							<h3><?php echo esc_html( $card_title ); ?></h3>
							<?php if ( $card_body ) : ?>
								<p><?php echo nl2br( esc_html( $card_body ) ); ?></p>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $show_locksmith_testimonials && $locksmith_testimonial_items ) : ?>
		<section id="locksmith-testimonials" class="locksmith-testimonials-section" aria-labelledby="locksmith-testimonials-title" style="--mlh-testimonials-background: <?php echo esc_attr( $locksmith_testimonials_background ); ?>;">
			<div class="locksmith-testimonials-section__inner">
				<header class="locksmith-testimonials-section__heading">
					<div class="locksmith-testimonials-section__eyebrow">
						<span class="locksmith-testimonials-section__eyebrow-line" aria-hidden="true"></span>
						<span><?php echo esc_html( $locksmith_testimonials_eyebrow ); ?></span>
						<span class="locksmith-testimonials-section__eyebrow-line" aria-hidden="true"></span>
					</div>
					<h2 id="locksmith-testimonials-title"><?php echo esc_html( $locksmith_testimonials_heading ); ?></h2>
				</header>

				<div class="locksmith-testimonials-widget">
					<div class="locksmith-testimonials-slider" data-testimonials-slider>
						<button class="locksmith-testimonials-slider__arrow locksmith-testimonials-slider__arrow--prev" type="button" aria-label="<?php esc_attr_e( 'Previous reviews', 'medicare-leads-hub' ); ?>">‹</button>
						<div class="locksmith-testimonials-slider__viewport">
							<div class="locksmith-testimonials-fallback locksmith-testimonials-slider__track" aria-label="<?php esc_attr_e( 'Customer testimonials', 'medicare-leads-hub' ); ?>">
						<?php foreach ( $locksmith_testimonial_items as $testimonial_index => $testimonial ) : ?>
							<?php
							$name       = isset( $testimonial['name'] ) ? $testimonial['name'] : '';
							$initial    = function_exists( 'mb_substr' ) ? mb_substr( $name, 0, 1 ) : substr( $name, 0, 1 );
							$rating     = max( 1, min( 5, absint( isset( $testimonial['rating'] ) ? $testimonial['rating'] : 5 ) ) );
							$avatar_tones = array( '', ' locksmith-testimonials-fallback__avatar--orange', ' locksmith-testimonials-fallback__avatar--purple' );
							$avatar_tone  = $avatar_tones[ $testimonial_index % count( $avatar_tones ) ];
							$meta         = implode( ' · ', array_filter( array( $testimonial['date'], $testimonial['source'] ) ) );
							?>
							<article class="locksmith-testimonials-fallback__card">
								<div class="locksmith-testimonials-fallback__person">
									<span class="locksmith-testimonials-fallback__avatar<?php echo esc_attr( $avatar_tone ); ?>"><?php echo esc_html( strtoupper( $initial ) ); ?></span>
									<strong><?php echo esc_html( $name ); ?></strong>
									<?php if ( ! empty( $testimonial['verified'] ) ) : ?>
										<span class="locksmith-testimonials-fallback__verified" aria-label="<?php esc_attr_e( 'Verified review', 'medicare-leads-hub' ); ?>">✓</span>
									<?php endif; ?>
								</div>
								<?php if ( $meta ) : ?><div class="locksmith-testimonials-fallback__meta"><?php echo esc_html( $meta ); ?></div><?php endif; ?>
								<div class="locksmith-testimonials-fallback__stars" aria-label="<?php echo esc_attr( sprintf( __( '%d out of 5 stars', 'medicare-leads-hub' ), $rating ) ); ?>"><?php echo esc_html( str_repeat( '★', $rating ) . str_repeat( '☆', 5 - $rating ) ); ?></div>
								<?php if ( ! empty( $testimonial['body'] ) ) : ?><p><?php echo esc_html( $testimonial['body'] ); ?></p><?php endif; ?>
							</article>
						<?php endforeach; ?>
							</div>
						</div>
						<button class="locksmith-testimonials-slider__arrow locksmith-testimonials-slider__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Next reviews', 'medicare-leads-hub' ); ?>">›</button>
						<div class="locksmith-testimonials-slider__dots" role="tablist" aria-label="<?php esc_attr_e( 'Review slides', 'medicare-leads-hub' ); ?>"></div>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $show_booking_faq ) : ?>
		<?php
		$booking_faq_items = medicare_leads_hub_get_booking_faq_items();
		?>
		<?php if ( $booking_faq_items ) : ?>
		<section id="booking-faq" class="booking-faq-section" aria-labelledby="booking-faq-title" style="--mlh-booking-faq-background: <?php echo esc_attr( $booking_faq_background ); ?>;">
			<div class="booking-faq-section__inner">
				<header class="booking-faq-section__heading">
					<div class="booking-faq-section__eyebrow"><?php echo esc_html( $booking_faq_eyebrow ); ?></div>
					<h2 id="booking-faq-title"><?php echo esc_html( $booking_faq_heading ); ?></h2>
					<?php if ( $booking_faq_intro ) : ?>
						<p><?php echo nl2br( esc_html( $booking_faq_intro ) ); ?></p>
					<?php endif; ?>
				</header>

				<div class="booking-faq-list">
					<?php foreach ( $booking_faq_items as $faq_index => $faq_item ) : ?>
						<article class="booking-faq-item<?php echo 0 === $faq_index ? ' is-open' : ''; ?>">
							<button class="booking-faq-item__trigger" type="button" aria-expanded="<?php echo 0 === $faq_index ? 'true' : 'false'; ?>" aria-controls="booking-faq-answer-<?php echo absint( $faq_index ); ?>">
								<span><?php echo esc_html( $faq_item['question'] ); ?></span>
								<span class="booking-faq-item__icon" aria-hidden="true"><?php echo 0 === $faq_index ? '−' : '+'; ?></span>
							</button>
							<div id="booking-faq-answer-<?php echo absint( $faq_index ); ?>" class="booking-faq-item__answer" role="region">
								<div class="booking-faq-item__answer-inner">
									<?php if ( $faq_item['answer'] ) : ?>
										<p><?php echo nl2br( esc_html( $faq_item['answer'] ) ); ?></p>
									<?php endif; ?>
								</div>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php endif; ?>
	<?php endif; ?>

</main>
<?php get_footer(); ?>
