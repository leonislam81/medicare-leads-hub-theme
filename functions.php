<?php
/**
 * Medicare Leads Hub custom theme functions.
 *
 * @package Medicare_Leads_Hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function medicare_leads_hub_setup() {
	load_theme_textdomain( 'medicare-leads-hub', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 72,
			'width'       => 260,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Header Menu', 'medicare-leads-hub' ),
			'footer'  => __( 'Footer Menu', 'medicare-leads-hub' ),
		)
	);
}
add_action( 'after_setup_theme', 'medicare_leads_hub_setup' );

function medicare_leads_hub_font_stack( $font ) {
	$stacks = array(
		'plus-jakarta' => '"Plus Jakarta Sans", Arial, sans-serif',
		'inter'       => 'Inter, Arial, sans-serif',
		'roboto'      => 'Roboto, Arial, sans-serif',
		'system'      => '-apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif',
		'georgia'     => 'Georgia, "Times New Roman", serif',
	);

	return isset( $stacks[ $font ] ) ? $stacks[ $font ] : $stacks['plus-jakarta'];
}

function medicare_leads_hub_sanitize_font( $value ) {
	$allowed = array( 'plus-jakarta', 'inter', 'roboto', 'system', 'georgia' );
	return in_array( $value, $allowed, true ) ? $value : 'plus-jakarta';
}

function medicare_leads_hub_sanitize_hex( $value ) {
	$color = sanitize_hex_color( $value );
	return $color ? $color : '#0B3B66';
}

/**
 * Accept either a Google Maps embed URL or the complete iframe snippet copied
 * from Google Maps, while storing only the iframe's safe src URL.
 *
 * @param mixed $value URL or iframe HTML.
 * @return string Sanitized embed URL.
 */
function medicare_leads_hub_sanitize_map_embed_url( $value ) {
	$value = trim( wp_unslash( (string) $value ) );

	if ( preg_match( '/<iframe\b[^>]*\bsrc\s*=\s*(?:"([^"]+)"|\'([^\']+)\')/i', $value, $matches ) ) {
		$value = ! empty( $matches[1] ) ? $matches[1] : $matches[2];
		$value = html_entity_decode( $value, ENT_QUOTES, get_bloginfo( 'charset' ) ? get_bloginfo( 'charset' ) : 'UTF-8' );
	}

	return esc_url_raw( $value );
}

function medicare_leads_hub_sanitize_range( $value, $min, $max, $fallback ) {
	$value = absint( $value );
	return ( $value >= $min && $value <= $max ) ? $value : $fallback;
}

function medicare_leads_hub_sanitize_icon( $value ) {
	$allowed = array( 'shield-check', 'emergency-clock', 'location-pin', 'emergency', 'keys', 'shield-lock', 'keypad', 'master-key', 'safe' );
	return in_array( $value, $allowed, true ) ? $value : 'shield-check';
}

function medicare_leads_hub_sanitize_expectations_tone( $value ) {
	$allowed = array( 'navy', 'gold' );
	return in_array( $value, $allowed, true ) ? $value : 'navy';
}

/**
 * Return true when this theme is running with its neutral, reusable starter
 * profile. Existing sites are marked as "site" so their saved Customizer
 * values continue to win over starter defaults.
 *
 * @return bool
 */
function medicare_leads_hub_is_portable_starter() {
	return 'starter' === get_option( 'medicare_leads_hub_profile_mode', '' );
}

/**
 * Central business-profile defaults used by shared theme chrome.
 *
 * @return array<string,string>
 */
function medicare_leads_hub_business_profile_defaults() {
	return array(
		'name'         => get_bloginfo( 'name' ),
		'tagline'      => 'Reliable service for your home or business.',
		'service_type' => 'Local service business',
		'service_area' => '',
		'phone'        => '',
		'email'        => '',
		'cta_text'     => 'Get a Free Quote',
		'cta_url'      => '#contact',
	);
}

/**
 * Get the reusable business profile from Customizer settings.
 *
 * @return array<string,string>
 */
function medicare_leads_hub_get_business_profile() {
	$profile  = medicare_leads_hub_business_profile_defaults();
	$settings = array(
		'name'         => 'medicare_business_name',
		'tagline'      => 'medicare_business_tagline',
		'service_type' => 'medicare_business_service_type',
		'service_area' => 'medicare_business_service_area',
		'phone'        => 'medicare_business_phone',
		'email'        => 'medicare_business_email',
		'cta_text'     => 'medicare_business_primary_cta',
		'cta_url'      => 'medicare_business_primary_url',
	);

	foreach ( $settings as $key => $setting ) {
		$profile[ $key ] = (string) get_theme_mod( $setting, $profile[ $key ] );
	}

	return $profile;
}

/**
 * Read a legacy setting when an existing site has explicitly saved it;
 * otherwise fall back to the central business profile. This keeps old
 * Customizer exports backward-compatible while making new sites portable.
 *
 * @param string $legacy_setting Legacy Customizer setting ID.
 * @param string $profile_key    Business profile key.
 * @param mixed  $fallback       Fallback when neither exists.
 * @return mixed
 */
function medicare_leads_hub_profile_value( $legacy_setting, $profile_key, $fallback = '' ) {
	$mods = get_theme_mods();
	if ( is_array( $mods ) && array_key_exists( $legacy_setting, $mods ) ) {
		return get_theme_mod( $legacy_setting, $fallback );
	}

	$profile = medicare_leads_hub_get_business_profile();
	return array_key_exists( $profile_key, $profile ) ? $profile[ $profile_key ] : $fallback;
}

/**
 * Return the Contact Us page URL used by conversion-focused buttons.
 *
 * Keeping this lookup dynamic makes the starter theme portable: a migrated
 * site can keep its saved CTA settings while every default quote button still
 * follows the destination site's Contact Us page.
 *
 * @return string
 */
function medicare_leads_hub_contact_page_url() {
	$contact_page = get_page_by_path( 'contact-us', OBJECT, 'page' );

	return $contact_page ? get_permalink( $contact_page ) : home_url( '/contact-us/' );
}

/**
 * Resolve the legacy homepage contact anchor used by quote CTAs.
 *
 * @param mixed $url Saved CTA URL.
 * @return string
 */
function medicare_leads_hub_resolve_contact_cta_url( $url ) {
	$url = trim( (string) $url );

	return ! $url || '#contact' === $url ? medicare_leads_hub_contact_page_url() : $url;
}

/**
 * Populate the central profile on an existing site without changing its
 * visible output. Section-specific settings remain the source of truth until
 * an editor chooses to use the new Business Profile controls.
 */
function medicare_leads_hub_seed_profile_from_legacy() {
	$legacy_service_area = medicare_leads_hub_is_portable_starter() ? '' : 'Centennial, CO & Surrounding Areas';
	$legacy_phone        = medicare_leads_hub_is_portable_starter() ? '' : '(508) 290-9514';
	$profile = array(
		'medicare_business_name'         => get_bloginfo( 'name' ),
		'medicare_business_service_type' => 'Local service business',
		'medicare_business_tagline'      => get_theme_mod( 'medicare_hero_intro', medicare_leads_hub_is_portable_starter() ? 'Reliable service for your home or business.' : 'Locksmith Centennial Co. provides dependable residential, commercial, and automotive locksmith services throughout Centennial, Colorado.' ),
		'medicare_business_service_area' => get_theme_mod( 'medicare_footer_service_area', $legacy_service_area ),
		'medicare_business_phone'        => get_theme_mod( 'medicare_header_phone', $legacy_phone ),
		'medicare_business_email'        => get_theme_mod( 'medicare_footer_email', '' ),
		'medicare_business_primary_cta'  => get_theme_mod( 'medicare_header_cta_text', 'Get A Free Quote' ),
		'medicare_business_primary_url'  => get_theme_mod( 'medicare_header_cta_url', '#contact' ),
	);

	foreach ( $profile as $setting => $value ) {
		if ( '__missing__' === get_theme_mod( $setting, '__missing__' ) ) {
			set_theme_mod( $setting, $value );
		}
	}

	// Repair the first-run profile seed if it was created before the legacy
	// defaults were restored. This runs only once on the existing site.
	if ( ! medicare_leads_hub_is_portable_starter() && '1.1' !== get_option( 'medicare_leads_hub_profile_sync_version', '' ) ) {
		if ( '__missing__' === get_theme_mod( 'medicare_header_phone', '__missing__' ) && '' === get_theme_mod( 'medicare_business_phone', '' ) ) {
			set_theme_mod( 'medicare_business_phone', '(508) 290-9514' );
		}
		if ( '__missing__' === get_theme_mod( 'medicare_footer_service_area', '__missing__' ) && '' === get_theme_mod( 'medicare_business_service_area', '' ) ) {
			set_theme_mod( 'medicare_business_service_area', 'Centennial, CO & Surrounding Areas' );
		}
		update_option( 'medicare_leads_hub_profile_sync_version', '1.1', false );
	}
}

function medicare_leads_hub_default_booking_faq_items() {
	return array(
		array( 'question' => 'What should I do if I am locked out?', 'answer' => 'Stay calm and contact our team. We will confirm your location, send a qualified locksmith, and help restore access safely.' ),
		array( 'question' => 'What items can you secure or install?', 'answer' => 'We can install and service residential locks, high-security hardware, smart locks, keypads, access control systems, safes, and more.' ),
		array( 'question' => 'Do you offer residential and commercial service?', 'answer' => 'Yes. We help homeowners, property managers, offices, retail locations, and commercial facilities with practical security and access solutions.' ),
		array( 'question' => 'Can you help with smart locks and access control?', 'answer' => 'Yes. Our team can recommend, install, configure, and support smart locks, keypad entry, card access, and other modern systems.' ),
		array( 'question' => 'How quickly can I schedule service?', 'answer' => 'Emergency help may be available the same day. For planned work, we will coordinate a convenient appointment based on your needs.' ),
	);
}

function medicare_leads_hub_sanitize_booking_faq_items( $value ) {
	if ( ! is_array( $value ) ) {
		return array();
	}

	$items = array();
	foreach ( $value as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		$question = isset( $item['question'] ) ? sanitize_text_field( $item['question'] ) : '';
		$answer   = isset( $item['answer'] ) ? sanitize_textarea_field( $item['answer'] ) : '';

		if ( '' === trim( $question ) ) {
			continue;
		}

		$items[] = array(
			'question' => $question,
			'answer'   => $answer,
		);
	}

	return $items;
}

function medicare_leads_hub_get_booking_faq_items() {
	$defaults = medicare_leads_hub_default_booking_faq_items();
	$saved    = get_theme_mod( 'medicare_booking_faq_items', null );

	if ( is_array( $saved ) ) {
		return medicare_leads_hub_sanitize_booking_faq_items( $saved );
	}

	$items = array();
	foreach ( $defaults as $index => $default ) {
		$legacy_index = $index + 1;
		$items[]      = array(
			'question' => get_theme_mod( 'medicare_booking_faq_item_' . $legacy_index . '_question', $default['question'] ),
			'answer'   => get_theme_mod( 'medicare_booking_faq_item_' . $legacy_index . '_answer', $default['answer'] ),
		);
	}

	return medicare_leads_hub_sanitize_booking_faq_items( $items );
}

function medicare_leads_hub_default_locksmith_pricing_rows() {
	return array(
		array( 'service' => 'House Lockout', 'typical' => '$75 – $150', 'project' => '$75 – $150' ),
		array( 'service' => 'Rekeying Locks', 'typical' => '$20 – $50', 'project' => '$60 – $150' ),
		array( 'service' => 'Lock Installation', 'typical' => '$80 – $150', 'project' => '$100 – $300' ),
		array( 'service' => 'Smart Lock Installation', 'typical' => '$100 – $200', 'project' => '$150 – $400' ),
		array( 'service' => 'Car Lockout', 'typical' => '$75 – $150', 'project' => '$75 – $150' ),
		array( 'service' => 'Ignition Repair / Replacement', 'typical' => '$150 – $300', 'project' => '$200 – $500' ),
		array( 'service' => 'Commercial Lockout', 'typical' => '$100 – $250', 'project' => '$100 – $250' ),
	);
}

function medicare_leads_hub_sanitize_locksmith_pricing_rows( $value ) {
	if ( ! is_array( $value ) ) {
		return array();
	}

	$rows = array();
	foreach ( $value as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$service = isset( $row['service'] ) ? sanitize_text_field( $row['service'] ) : '';
		$typical = isset( $row['typical'] ) ? sanitize_text_field( $row['typical'] ) : '';
		$project = isset( $row['project'] ) ? sanitize_text_field( $row['project'] ) : '';

		if ( '' === trim( $service ) ) {
			continue;
		}

		$rows[] = array(
			'service' => $service,
			'typical' => $typical,
			'project' => $project,
		);
	}

	return $rows;
}

function medicare_leads_hub_get_locksmith_pricing_rows() {
	$defaults = medicare_leads_hub_default_locksmith_pricing_rows();
	$saved    = get_theme_mod( 'medicare_locksmith_pricing_rows', null );

	if ( is_array( $saved ) ) {
		return medicare_leads_hub_sanitize_locksmith_pricing_rows( $saved );
	}

	$rows = array();
	foreach ( $defaults as $index => $default ) {
		$legacy_index = $index + 1;
		$rows[] = array(
			'service' => get_theme_mod( 'medicare_locksmith_pricing_row_' . $legacy_index . '_service', $default['service'] ),
			'typical' => get_theme_mod( 'medicare_locksmith_pricing_row_' . $legacy_index . '_typical', $default['typical'] ),
			'project' => get_theme_mod( 'medicare_locksmith_pricing_row_' . $legacy_index . '_project', $default['project'] ),
		);
	}

	return medicare_leads_hub_sanitize_locksmith_pricing_rows( $rows );
}

function medicare_leads_hub_default_testimonial_items() {
	return array(
		array( 'name' => 'Jennifer F.', 'date' => '1 month ago', 'source' => 'Google', 'rating' => 5, 'verified' => true, 'body' => 'Great communication and quality workmanship. They answered all our questions and completed the work without any problems.' ),
		array( 'name' => 'Michael R.', 'date' => '2 months ago', 'source' => 'Google', 'rating' => 5, 'verified' => true, 'body' => 'The locksmith handled everything quickly and professionally. The work was clean and the new locks look great.' ),
		array( 'name' => 'Jonathan T.', 'date' => '5 months ago', 'source' => 'Google', 'rating' => 5, 'verified' => true, 'body' => 'Very professional service. The whole process was smooth, and we are extremely happy with the result.' ),
		array( 'name' => 'Janelle R.', 'date' => '7 months ago', 'source' => 'Google', 'rating' => 5, 'verified' => true, 'body' => 'Excellent experience from start to finish. They explained everything clearly and completed the job right on schedule.' ),
		array( 'name' => 'Marlin E.', 'date' => '7 months ago', 'source' => 'Google', 'rating' => 5, 'verified' => true, 'body' => 'We are very happy with our new locks. The team was respectful, efficient, and did a great job cleaning up.' ),
		array( 'name' => 'Jeffrey B.', 'date' => '1 year ago', 'source' => 'Google', 'rating' => 5, 'verified' => true, 'body' => 'Really impressed with the service. Everyone was professional and the work was completed faster than expected.' ),
	);
}

function medicare_leads_hub_sanitize_testimonial_items( $value ) {
	if ( ! is_array( $value ) ) {
		return array();
	}

	$items = array();
	foreach ( $value as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		$name = isset( $item['name'] ) ? sanitize_text_field( $item['name'] ) : '';
		if ( '' === trim( $name ) ) {
			continue;
		}

		$items[] = array(
			'name'     => $name,
			'date'     => isset( $item['date'] ) ? sanitize_text_field( $item['date'] ) : '',
			'source'   => isset( $item['source'] ) ? sanitize_text_field( $item['source'] ) : '',
			'rating'   => max( 1, min( 5, absint( isset( $item['rating'] ) ? $item['rating'] : 5 ) ) ),
			'verified' => ! empty( $item['verified'] ),
			'body'     => isset( $item['body'] ) ? sanitize_textarea_field( $item['body'] ) : '',
		);
	}

	return $items;
}

function medicare_leads_hub_get_testimonial_items() {
	$saved = get_theme_mod( 'medicare_locksmith_testimonial_items', null );

	if ( is_array( $saved ) ) {
		return medicare_leads_hub_sanitize_testimonial_items( $saved );
	}

	return medicare_leads_hub_sanitize_testimonial_items( medicare_leads_hub_default_testimonial_items() );
}

function medicare_leads_hub_specialty_service_icon_svg( $icon_key ) {
	$icon_key = sanitize_key( $icon_key );
	$icons    = array(
		'emergency'   => '<path d="M12 3 4.5 6.5v5.4c0 4.6 3.1 7.8 7.5 9.6 4.4-1.8 7.5-5 7.5-9.6V6.5L12 3Z"/><rect x="9" y="10" width="6" height="5" rx="1"/><path d="M10.5 10V8.8a1.5 1.5 0 0 1 3 0V10"/>',
		'keys'        => '<circle cx="7.5" cy="9.5" r="3.2"/><path d="m10 12 8.5 8.5M14.5 16.5l2-2M16.8 18.8l2-2"/>',
		'shield-lock' => '<path d="M12 3 4.5 6.5v5.4c0 4.6 3.1 7.8 7.5 9.6 4.4-1.8 7.5-5 7.5-9.6V6.5L12 3Z"/><rect x="8.5" y="10" width="7" height="5.5" rx="1"/><path d="M10 10V8.8a2 2 0 0 1 4 0V10"/>',
		'keypad'      => '<rect x="5" y="3.5" width="14" height="17" rx="2"/><path d="M9 8h.01M12 8h.01M15 8h.01M9 11.5h.01M12 11.5h.01M15 11.5h.01M9 15h.01M12 15h.01M15 15h.01" stroke-width="2.5" stroke-linecap="round"/>',
		'master-key'  => '<circle cx="8" cy="8" r="3.5"/><path d="m10.5 10.5 8 8M14.5 14.5l2-2M16.8 16.8l2-2"/>',
		'safe'        => '<rect x="4" y="4" width="16" height="16" rx="2"/><circle cx="12" cy="12" r="3.5"/><path d="M12 8.5v2M12 13.5v2M8.5 12h2M13.5 12h2"/>',
	);

	$paths = isset( $icons[ $icon_key ] ) ? $icons[ $icon_key ] : $icons['emergency'];

	return '<svg class="specialty-service-card__icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" focusable="false" aria-hidden="true">' . $paths . '</svg>';
}

function medicare_leads_hub_sanitize_about_value_icon( $value ) {
	$allowed = array( 'shield-check', 'medal-star', 'people-check', 'lock' );
	return in_array( $value, $allowed, true ) ? $value : 'shield-check';
}

function medicare_leads_hub_about_value_icon_svg( $icon_key ) {
	$icons = array(
		'shield-check' => '<path d="M12 3 5 6.2v5.1c0 4.5 2.8 7.8 7 9.7 4.2-1.9 7-5.2 7-9.7V6.2L12 3Z"/><path d="m8.7 12.2 2.1 2.1 4.6-4.7"/>',
		'medal-star'   => '<circle cx="12" cy="9" r="4.6"/><path d="m9.3 13-1 7 3.7-2.1 3.7 2.1-1-7"/><path d="m12 6.6.7 1.5 1.6.2-1.2 1.1.3 1.6-1.4-.8-1.4.8.3-1.6-1.2-1.1 1.6-.2.7-1.5Z"/>',
		'people-check' => '<circle cx="9" cy="8.5" r="3"/><path d="M3.8 19.5c.5-3 2.2-4.6 5.2-4.6 2.9 0 4.5 1.6 5 4.6"/><path d="m15 14.5 2 2 3.3-3.4"/>',
		'lock'         => '<rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7.8a4 4 0 0 1 8 0V10"/><circle cx="12" cy="15" r="1"/>',
	);
	$key   = medicare_leads_hub_sanitize_about_value_icon( $icon_key );
	$paths = isset( $icons[ $key ] ) ? $icons[ $key ] : $icons['shield-check'];

	return '<svg class="about-page__value-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" focusable="false" aria-hidden="true">' . $paths . '</svg>';
}

/**
 * Build the editable HTML content used by the homepage locksmith support process.
 *
 * The legacy heading and introduction settings are used as a migration fallback
 * until the new single content setting is saved.
 *
 * @return string
 */
function medicare_leads_hub_locksmith_process_content_default() {
	$heading = get_theme_mod( 'medicare_locksmith_process_info_heading', 'What Does Our Locksmith Service Process Look Like?' );
	$intro   = get_theme_mod( 'medicare_locksmith_process_info_intro', 'Our process starts with a quick conversation about your property, access issue, or security goal. We assess the situation, explain the best options, and provide a clear estimate before work begins.' );
	$blocks  = array(
		array(
			'heading' => 'What Does Our Locksmith Service Process Look Like?',
			'body'    => 'Our process starts with a quick conversation about your property, access issue, or security goal. We assess the situation, explain the best options, and provide a clear estimate before work begins.',
			'list'    => array(
				'Confirm the lock, door, vehicle, or access system',
				'Recommend the right repair, replacement, or upgrade',
				'Complete the work carefully and test everything',
			),
		),
		array(
			'heading' => 'Which Locksmith Solutions Do We Recommend For Centennial, CO?',
			'body'    => 'Every property is different. We recommend dependable hardware and access solutions that fit your doors, daily routines, and security priorities—from rekeying and high-security locks to smart locks and access control.',
			'list'    => array(),
		),
		array(
			'heading' => 'How Quickly Can A Locksmith Help In Centennial, CO?',
			'body'    => 'Emergency lockouts and urgent access problems may be handled the same day when availability allows. Planned installations, rekeying, and commercial work are scheduled around your needs after we confirm the scope.',
			'list'    => array(),
		),
		array(
			'heading' => 'Locksmith Service And Security Timeline',
			'body'    => 'We keep the process simple and transparent from the first call to the final check.',
			'list'    => array(
				'Assessment and estimate — confirm the issue and options',
				'Service scheduling — choose a convenient time',
				'Installation or repair — complete and test the work',
				'Final walkthrough — review the result and next steps',
			),
		),
	);

	if ( medicare_leads_hub_is_portable_starter() ) {
		$blocks = array(
			array(
				'heading' => 'How does our service process work?',
				'body'    => 'Explain what happens from the first conversation through the final check.',
				'list'    => array( 'Share the project or issue', 'Review the best options', 'Complete the work and confirm the result' ),
			),
			array(
				'heading' => 'Which solutions do we recommend?',
				'body'    => 'Describe how your team matches the right solution to each customer and property.',
				'list'    => array(),
			),
			array(
				'heading' => 'How quickly can we help?',
				'body'    => 'Add your usual availability, response times, and scheduling information.',
				'list'    => array(),
			),
			array(
				'heading' => 'Service timeline',
				'body'    => 'Keep the process simple and transparent from the first call to the final check.',
				'list'    => array( 'Assessment and estimate', 'Service scheduling', 'Installation or repair', 'Final walkthrough' ),
			),
		);
	}

	$html  = '<h2>' . esc_html( $heading ) . '</h2>';
	$html .= '<p>' . esc_html( $intro ) . '</p>';
	if ( ! empty( $blocks[0]['list'] ) ) {
		$html .= '<ul>';
		foreach ( $blocks[0]['list'] as $list_item ) {
			$html .= '<li>' . esc_html( $list_item ) . '</li>';
		}
		$html .= '</ul>';
	}

	foreach ( array_slice( $blocks, 1 ) as $block ) {
		$html .= '<h3>' . esc_html( $block['heading'] ) . '</h3>';
		$html .= '<p>' . esc_html( $block['body'] ) . '</p>';
		if ( ! empty( $block['list'] ) ) {
			$html .= '<ul>';
			foreach ( $block['list'] as $list_item ) {
				$html .= '<li>' . esc_html( $list_item ) . '</li>';
			}
			$html .= '</ul>';
		}
	}

	return $html;
}

/**
 * Build the legacy pricing copy as HTML for the single editable content field.
 *
 * @return string
 */
function medicare_leads_hub_locksmith_pricing_content_default() {
	$paragraphs = array(
		get_theme_mod( 'medicare_locksmith_pricing_left_copy_1', 'We believe in honest, straightforward pricing so you know exactly what to expect. Our rates are competitive, with no hidden fees or surprises.' ),
		get_theme_mod( 'medicare_locksmith_pricing_left_copy_2', 'Whether you need help at home, on the road, or for your business, our experienced locksmiths provide dependable service at a fair price.' ),
	);
	$html = '';

	foreach ( $paragraphs as $paragraph ) {
		if ( $paragraph ) {
			$html .= '<p>' . esc_html( $paragraph ) . '</p>';
		}
	}

	return $html;
}

function medicare_leads_hub_customizer( $wp_customize ) {
	if ( ! class_exists( 'Medicare_Leads_Hub_FAQ_Repeater_Control' ) ) {
		class Medicare_Leads_Hub_FAQ_Repeater_Control extends WP_Customize_Control {
			public $type = 'medicare_faq_repeater';

			public function render_content() {
				$items = medicare_leads_hub_sanitize_booking_faq_items( $this->value() );
				?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php if ( $this->description ) : ?>
					<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
				<?php endif; ?>
				<div class="medicare-faq-repeater" data-control-id="<?php echo esc_attr( $this->id ); ?>" data-items="<?php echo esc_attr( wp_json_encode( $items ) ); ?>">
					<div class="medicare-faq-repeater__items"></div>
					<button type="button" class="button button-secondary medicare-faq-repeater__add">+ <?php esc_html_e( 'Add FAQ', 'medicare-leads-hub' ); ?></button>
				</div>
				<?php
			}
		}
	}

	if ( ! class_exists( 'Medicare_Leads_Hub_Pricing_Repeater_Control' ) ) {
		class Medicare_Leads_Hub_Pricing_Repeater_Control extends WP_Customize_Control {
			public $type = 'medicare_pricing_repeater';

			public function render_content() {
				$rows = medicare_leads_hub_sanitize_locksmith_pricing_rows( $this->value() );
				?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php if ( $this->description ) : ?>
					<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
				<?php endif; ?>
				<div class="medicare-pricing-repeater" data-control-id="<?php echo esc_attr( $this->id ); ?>" data-items="<?php echo esc_attr( wp_json_encode( $rows ) ); ?>">
					<div class="medicare-pricing-repeater__items"></div>
					<button type="button" class="button button-secondary medicare-pricing-repeater__add">+ <?php esc_html_e( 'Add row', 'medicare-leads-hub' ); ?></button>
				</div>
				<?php
			}
		}
	}

	if ( ! class_exists( 'Medicare_Leads_Hub_Testimonials_Repeater_Control' ) ) {
		class Medicare_Leads_Hub_Testimonials_Repeater_Control extends WP_Customize_Control {
			public $type = 'medicare_testimonials_repeater';

			public function render_content() {
				$items = medicare_leads_hub_sanitize_testimonial_items( $this->value() );
				?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php if ( $this->description ) : ?>
					<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
				<?php endif; ?>
				<div class="medicare-testimonials-repeater" data-control-id="<?php echo esc_attr( $this->id ); ?>" data-items="<?php echo esc_attr( wp_json_encode( $items ) ); ?>">
					<div class="medicare-testimonials-repeater__items"></div>
					<button type="button" class="button button-secondary medicare-testimonials-repeater__add">+ <?php esc_html_e( 'Add review', 'medicare-leads-hub' ); ?></button>
				</div>
				<?php
			}
		}
	}

	if ( ! class_exists( 'Medicare_Leads_Hub_Theme_Navigation_Control' ) ) {
		class Medicare_Leads_Hub_Theme_Navigation_Control extends WP_Customize_Control {
			public $type = 'medicare_theme_navigation';

			public function render_content() {
				$links = array(
					'medicare_theme_options'       => __( 'Site-wide Settings', 'medicare-leads-hub' ),
					'medicare_homepage_options'    => __( 'Homepage', 'medicare-leads-hub' ),
					'medicare_about_page_options' => __( 'About Us Page', 'medicare-leads-hub' ),
					'medicare_contact_page_options' => __( 'Contact Us Page', 'medicare-leads-hub' ),
				);
				?>
				<span class="customize-control-title"><?php esc_html_e( 'Theme sections', 'medicare-leads-hub' ); ?></span>
				<div class="medicare-theme-navigation">
					<?php foreach ( $links as $panel_id => $label ) : ?>
						<button type="button" class="medicare-theme-navigation__link" data-medicare-customizer-panel="<?php echo esc_attr( $panel_id ); ?>">
							<span><?php echo esc_html( $label ); ?></span><span aria-hidden="true">›</span>
						</button>
					<?php endforeach; ?>
				</div>
				<?php
			}
		}
	}

	$theme_profile_name = trim( sanitize_text_field( get_theme_mod( 'medicare_business_name', '' ) ) );
	if ( '' === $theme_profile_name ) {
		$theme_profile_name = wp_get_theme()->get( 'Name' );
	}

	$wp_customize->add_panel(
		'medicare_theme_identity',
		array(
			'title'       => $theme_profile_name,
			'description' => __( 'Manage this business theme and all of its site sections from one place.', 'medicare-leads-hub' ),
			'auto_expand_sole_section' => true,
			'priority'    => 20,
		)
	);

	$wp_customize->add_section(
		'medicare_theme_navigation',
		array(
			'title'    => __( 'Theme sections', 'medicare-leads-hub' ),
			'panel'    => 'medicare_theme_identity',
			'priority' => 5,
		)
	);
	$wp_customize->add_control(
		new Medicare_Leads_Hub_Theme_Navigation_Control(
			$wp_customize,
			'medicare_theme_navigation_control',
			array(
				'section'  => 'medicare_theme_navigation',
				'settings' => array(),
			)
		)
	);

	$wp_customize->add_panel(
		'medicare_theme_options',
		array(
			'title'       => __( 'Site-wide Settings', 'medicare-leads-hub' ),
			'description' => __( 'Controls shared across the site: colors, typography, header, footer, and mobile actions. Logo and menu content remain managed by WordPress.', 'medicare-leads-hub' ),
			'priority'    => 25,
		)
	);

	$wp_customize->add_panel(
		'medicare_homepage_options',
		array(
			'title'       => __( 'Homepage', 'medicare-leads-hub' ),
			'description' => __( 'Edit the sections that appear on the homepage. Open the homepage in the preview to see changes live.', 'medicare-leads-hub' ),
			'priority'    => 35,
		)
	);

	$wp_customize->add_panel(
		'medicare_about_page_options',
		array(
			'title'       => __( 'About Us Page', 'medicare-leads-hub' ),
			'description' => __( 'Edit the standalone About Us page content, hero, mission, vision, and values.', 'medicare-leads-hub' ),
			'priority'    => 45,
		)
	);

	$wp_customize->add_panel(
		'medicare_contact_page_options',
		array(
			'title'       => __( 'Contact Us Page', 'medicare-leads-hub' ),
			'description' => __( 'Edit the Contact Us page copy, map, and form delivery settings.', 'medicare-leads-hub' ),
			'priority'    => 55,
		)
	);

	$wp_customize->add_section(
		'medicare_global_settings',
		array(
			'title'    => __( 'Global Design System', 'medicare-leads-hub' ),
			'panel'    => 'medicare_theme_options',
			'priority' => 10,
		)
	);

	$global_colors = array(
		'medicare_primary_color'      => array( __( 'Primary navy', 'medicare-leads-hub' ), '#0B3B66' ),
		'medicare_primary_dark_color' => array( __( 'Deep navy / hover color', 'medicare-leads-hub' ), '#062A4A' ),
		'medicare_accent_color'       => array( __( 'Soft blue background', 'medicare-leads-hub' ), '#F3F7FA' ),
		'medicare_text_color'         => array( __( 'Body text color', 'medicare-leads-hub' ), '#344B61' ),
		'medicare_heading_color'      => array( __( 'Heading color', 'medicare-leads-hub' ), '#062A4A' ),
	);

	foreach ( $global_colors as $id => $data ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $data[1],
				'sanitize_callback' => 'medicare_leads_hub_sanitize_hex',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, array( 'label' => $data[0], 'section' => 'medicare_global_settings' ) ) );
	}

	$wp_customize->add_section(
		'medicare_business_profile',
		array(
			'title'       => __( 'Business Profile', 'medicare-leads-hub' ),
			'description' => __( 'Set the business identity once. Shared header and footer details can use these values when a legacy section-specific value has not been saved.', 'medicare-leads-hub' ),
			'panel'       => 'medicare_theme_options',
			'priority'    => 5,
		)
	);

	$business_profile_defaults = medicare_leads_hub_business_profile_defaults();
	$business_profile_fields   = array(
		'medicare_business_name'         => array( __( 'Business name', 'medicare-leads-hub' ), $business_profile_defaults['name'], 'sanitize_text_field', 'text' ),
		'medicare_business_service_type' => array( __( 'Service type', 'medicare-leads-hub' ), $business_profile_defaults['service_type'], 'sanitize_text_field', 'text' ),
		'medicare_business_tagline'      => array( __( 'Short tagline', 'medicare-leads-hub' ), $business_profile_defaults['tagline'], 'sanitize_textarea_field', 'textarea' ),
		'medicare_business_service_area' => array( __( 'Service area', 'medicare-leads-hub' ), $business_profile_defaults['service_area'], 'sanitize_text_field', 'text' ),
		'medicare_business_phone'        => array( __( 'Business phone', 'medicare-leads-hub' ), $business_profile_defaults['phone'], 'sanitize_text_field', 'text' ),
		'medicare_business_email'        => array( __( 'Business email', 'medicare-leads-hub' ), $business_profile_defaults['email'], 'sanitize_email', 'email' ),
		'medicare_business_primary_cta'  => array( __( 'Primary CTA label', 'medicare-leads-hub' ), $business_profile_defaults['cta_text'], 'sanitize_text_field', 'text' ),
	);

	foreach ( $business_profile_fields as $id => $field ) {
		$wp_customize->add_setting( $id, array( 'default' => $field[1], 'sanitize_callback' => $field[2], 'transport' => 'refresh' ) );
		$wp_customize->add_control( $id, array( 'label' => $field[0], 'section' => 'medicare_business_profile', 'type' => $field[3] ) );
	}

	$wp_customize->add_setting( 'medicare_business_primary_url', array( 'default' => $business_profile_defaults['cta_url'], 'sanitize_callback' => 'esc_url_raw', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_business_primary_url', array( 'label' => __( 'Primary CTA URL', 'medicare-leads-hub' ), 'section' => 'medicare_business_profile', 'type' => 'url' ) );

	$wp_customize->add_setting( 'medicare_theme_preview_image_id', array( 'default' => 0, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ) );
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'medicare_theme_preview_image_id',
			array(
				'label'       => __( 'Theme preview image', 'medicare-leads-hub' ),
				'description' => __( 'Choose a Media Library image. When you save the Customizer, it will replace the active theme screenshot.png. Theme folder write permission is required.', 'medicare-leads-hub' ),
				'section'     => 'medicare_business_profile',
				'mime_type'   => 'image',
			)
		)
	);

	$brand_accent_colors = array(
		'medicare_gold_color'      => array( __( 'Logo gold / CTA color', 'medicare-leads-hub' ), '#F5B400' ),
		'medicare_gold_dark_color' => array( __( 'Logo gold hover color', 'medicare-leads-hub' ), '#C98900' ),
	);

	foreach ( $brand_accent_colors as $id => $data ) {
		$wp_customize->add_setting( $id, array( 'default' => $data[1], 'sanitize_callback' => 'medicare_leads_hub_sanitize_hex', 'transport' => 'refresh' ) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, array( 'label' => $data[0], 'section' => 'medicare_global_settings' ) ) );
	}

	$font_choices = array(
		'plus-jakarta' => 'Plus Jakarta Sans',
		'inter'       => 'Inter',
		'roboto'      => 'Roboto',
		'system'      => 'System UI',
		'georgia'     => 'Georgia',
	);

	foreach ( array( 'medicare_body_font' => __( 'Body font', 'medicare-leads-hub' ), 'medicare_heading_font' => __( 'Heading font', 'medicare-leads-hub' ) ) as $id => $label ) {
		$wp_customize->add_setting( $id, array( 'default' => 'plus-jakarta', 'sanitize_callback' => 'medicare_leads_hub_sanitize_font', 'transport' => 'refresh' ) );
		$wp_customize->add_control( $id, array( 'label' => $label, 'section' => 'medicare_global_settings', 'type' => 'select', 'choices' => $font_choices ) );
	}

	$wp_customize->add_setting( 'medicare_body_font_size', array( 'default' => 16, 'sanitize_callback' => function ( $value ) { return medicare_leads_hub_sanitize_range( $value, 14, 22, 16 ); }, 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_body_font_size', array( 'label' => __( 'Body font size (px)', 'medicare-leads-hub' ), 'section' => 'medicare_global_settings', 'type' => 'range', 'input_attrs' => array( 'min' => 14, 'max' => 22, 'step' => 1 ) ) );

	$wp_customize->add_setting( 'medicare_heading_font_size', array( 'default' => 42, 'sanitize_callback' => function ( $value ) { return medicare_leads_hub_sanitize_range( $value, 30, 64, 42 ); }, 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_heading_font_size', array( 'label' => __( 'Base heading size (px)', 'medicare-leads-hub' ), 'section' => 'medicare_global_settings', 'type' => 'range', 'input_attrs' => array( 'min' => 30, 'max' => 64, 'step' => 1 ) ) );

	$wp_customize->add_setting( 'medicare_radius', array( 'default' => 12, 'sanitize_callback' => function ( $value ) { return medicare_leads_hub_sanitize_range( $value, 0, 28, 12 ); }, 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_radius', array( 'label' => __( 'Corner radius (px)', 'medicare-leads-hub' ), 'section' => 'medicare_global_settings', 'type' => 'range', 'input_attrs' => array( 'min' => 0, 'max' => 28, 'step' => 1 ) ) );

	$wp_customize->add_setting( 'medicare_content_width', array( 'default' => 1280, 'sanitize_callback' => function ( $value ) { return medicare_leads_hub_sanitize_range( $value, 960, 1440, 1280 ); }, 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_content_width', array( 'label' => __( 'Content width (px)', 'medicare-leads-hub' ), 'section' => 'medicare_global_settings', 'type' => 'range', 'input_attrs' => array( 'min' => 960, 'max' => 1440, 'step' => 10 ) ) );

	$wp_customize->add_section( 'medicare_header_settings', array( 'title' => __( 'Header', 'medicare-leads-hub' ), 'panel' => 'medicare_theme_options', 'priority' => 20 ) );

	$header_text_settings = array(
		'medicare_announcement_text' => array( __( 'Announcement text', 'medicare-leads-hub' ), 'Serving your local area', 'sanitize_text_field' ),
		'medicare_header_phone'      => array( __( 'Phone number', 'medicare-leads-hub' ), '', 'sanitize_text_field' ),
		'medicare_header_cta_text'   => array( __( 'CTA button text', 'medicare-leads-hub' ), 'Get A Free Quote', 'sanitize_text_field' ),
	);

	foreach ( $header_text_settings as $id => $data ) {
		$wp_customize->add_setting( $id, array( 'default' => $data[1], 'sanitize_callback' => $data[2], 'transport' => 'refresh' ) );
		$wp_customize->add_control( $id, array( 'label' => $data[0], 'section' => 'medicare_header_settings', 'type' => 'text' ) );
	}

	$wp_customize->add_setting( 'medicare_header_cta_url', array( 'default' => '#contact', 'sanitize_callback' => 'esc_url_raw', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_header_cta_url', array( 'label' => __( 'CTA button URL', 'medicare-leads-hub' ), 'section' => 'medicare_header_settings', 'type' => 'url' ) );

	$wp_customize->add_setting( 'medicare_show_announcement', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_show_announcement', array( 'label' => __( 'Show announcement bar', 'medicare-leads-hub' ), 'section' => 'medicare_header_settings', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'medicare_sticky_header', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_sticky_header', array( 'label' => __( 'Make header sticky', 'medicare-leads-hub' ), 'section' => 'medicare_header_settings', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'medicare_header_bg', array( 'default' => '#ffffff', 'sanitize_callback' => 'medicare_leads_hub_sanitize_hex', 'transport' => 'refresh' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_header_bg', array( 'label' => __( 'Header background', 'medicare-leads-hub' ), 'section' => 'medicare_header_settings' ) ) );

	$wp_customize->add_setting( 'medicare_header_text_color', array( 'default' => '#062A4A', 'sanitize_callback' => 'medicare_leads_hub_sanitize_hex', 'transport' => 'refresh' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_header_text_color', array( 'label' => __( 'Header text color', 'medicare-leads-hub' ), 'section' => 'medicare_header_settings' ) ) );

	$wp_customize->add_setting( 'medicare_logo_width', array( 'default' => 190, 'sanitize_callback' => function ( $value ) { return medicare_leads_hub_sanitize_range( $value, 90, 300, 190 ); }, 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_logo_width', array( 'label' => __( 'Logo width (px)', 'medicare-leads-hub' ), 'section' => 'medicare_header_settings', 'type' => 'range', 'input_attrs' => array( 'min' => 90, 'max' => 300, 'step' => 5 ) ) );

	$wp_customize->add_section(
		'medicare_footer_settings',
		array(
			'title'    => __( 'Footer', 'medicare-leads-hub' ),
			'panel'    => 'medicare_theme_options',
			'priority' => 25,
		)
	);

	$wp_customize->add_setting( 'medicare_footer_show_cta', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_footer_show_cta', array( 'label' => __( 'Show footer CTA band', 'medicare-leads-hub' ), 'section' => 'medicare_footer_settings', 'type' => 'checkbox' ) );

	$footer_text_settings = array(
		'medicare_footer_cta_heading'  => array( __( 'CTA heading', 'medicare-leads-hub' ), 'Need help securing your property?', 'sanitize_text_field' ),
		'medicare_footer_cta_text'     => array( __( 'CTA supporting text', 'medicare-leads-hub' ), 'Our local locksmith team is ready to help — fast, reliable, and professional.', 'sanitize_textarea_field' ),
		'medicare_footer_contact_label' => array( __( 'Contact block label', 'medicare-leads-hub' ), 'Service Area & Contact', 'sanitize_text_field' ),
		'medicare_footer_service_area' => array( __( 'Service area', 'medicare-leads-hub' ), '', 'sanitize_text_field' ),
		'medicare_footer_email'        => array( __( 'Email address', 'medicare-leads-hub' ), '', 'sanitize_email' ),
		'medicare_footer_copyright'    => array( __( 'Copyright text', 'medicare-leads-hub' ), 'Your Business Name. All rights reserved.', 'sanitize_text_field' ),
	);

	foreach ( $footer_text_settings as $id => $data ) {
		$wp_customize->add_setting( $id, array( 'default' => $data[1], 'sanitize_callback' => $data[2], 'transport' => 'refresh' ) );
		$wp_customize->add_control( $id, array( 'label' => $data[0], 'section' => 'medicare_footer_settings', 'type' => 'sanitize_textarea_field' === $data[2] ? 'textarea' : 'text' ) );
	}

	$wp_customize->add_setting( 'medicare_footer_cta_button_url', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_footer_cta_button_url', array( 'label' => __( 'CTA button URL (optional)', 'medicare-leads-hub' ), 'description' => __( 'Leave empty to call the header phone number.', 'medicare-leads-hub' ), 'section' => 'medicare_footer_settings', 'type' => 'url' ) );

	$footer_social_settings = array(
		'medicare_footer_facebook_url'  => __( 'Facebook URL', 'medicare-leads-hub' ),
		'medicare_footer_instagram_url' => __( 'Instagram URL', 'medicare-leads-hub' ),
		'medicare_footer_linkedin_url'  => __( 'LinkedIn URL', 'medicare-leads-hub' ),
	);

	foreach ( $footer_social_settings as $id => $label ) {
		$wp_customize->add_setting( $id, array( 'default' => '', 'sanitize_callback' => 'esc_url_raw', 'transport' => 'refresh' ) );
		$wp_customize->add_control( $id, array( 'label' => $label, 'section' => 'medicare_footer_settings', 'type' => 'url' ) );
	}

	$wp_customize->add_setting( 'medicare_footer_cta_background', array( 'default' => '#062A4A', 'sanitize_callback' => 'medicare_leads_hub_sanitize_hex', 'transport' => 'refresh' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_footer_cta_background', array( 'label' => __( 'CTA band background', 'medicare-leads-hub' ), 'section' => 'medicare_footer_settings' ) ) );

	$wp_customize->add_setting( 'medicare_footer_surface', array( 'default' => '#ffffff', 'sanitize_callback' => 'medicare_leads_hub_sanitize_hex', 'transport' => 'refresh' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_footer_surface', array( 'label' => __( 'Footer background', 'medicare-leads-hub' ), 'section' => 'medicare_footer_settings' ) ) );

	$wp_customize->add_section(
		'medicare_mobile_action_bar_settings',
		array(
			'title'    => __( 'Mobile Action Bar', 'medicare-leads-hub' ),
			'panel'    => 'medicare_theme_options',
			'priority' => 28,
		)
	);

	$wp_customize->add_setting( 'medicare_mobile_action_bar_show', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_mobile_action_bar_show', array( 'label' => __( 'Show mobile action bar', 'medicare-leads-hub' ), 'description' => __( 'This bar appears only on mobile devices.', 'medicare-leads-hub' ), 'section' => 'medicare_mobile_action_bar_settings', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'medicare_mobile_action_bar_call_text', array( 'default' => 'Call Now', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_mobile_action_bar_call_text', array( 'label' => __( 'Call button text', 'medicare-leads-hub' ), 'section' => 'medicare_mobile_action_bar_settings', 'type' => 'text' ) );

	$mobile_action_call_number_default = medicare_leads_hub_profile_value( 'medicare_header_phone', 'phone', '' );
	$wp_customize->add_setting( 'medicare_mobile_action_bar_call_number', array( 'default' => $mobile_action_call_number_default, 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
	$wp_customize->add_control(
		'medicare_mobile_action_bar_call_number',
		array(
			'label'       => __( 'Call button phone number', 'medicare-leads-hub' ),
			'description' => __( 'Number used when visitors tap the Call Now button. Leave empty to use the Business Profile phone.', 'medicare-leads-hub' ),
			'section'     => 'medicare_mobile_action_bar_settings',
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting( 'medicare_mobile_action_bar_quote_text', array( 'default' => 'Get a Quote', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_mobile_action_bar_quote_text', array( 'label' => __( 'Quote button text', 'medicare-leads-hub' ), 'section' => 'medicare_mobile_action_bar_settings', 'type' => 'text' ) );

	$contact_page = get_page_by_path( 'contact-us' );
	$mobile_action_quote_url_default = $contact_page ? get_permalink( $contact_page ) : home_url( '/contact-us/' );
	$wp_customize->add_setting( 'medicare_mobile_action_bar_quote_url', array( 'default' => $mobile_action_quote_url_default, 'sanitize_callback' => 'esc_url_raw', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_mobile_action_bar_quote_url', array( 'label' => __( 'Quote button URL', 'medicare-leads-hub' ), 'section' => 'medicare_mobile_action_bar_settings', 'type' => 'url' ) );

	$wp_customize->add_setting( 'medicare_mobile_action_bar_background', array( 'default' => '#062A4A', 'sanitize_callback' => 'medicare_leads_hub_sanitize_hex', 'transport' => 'refresh' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_mobile_action_bar_background', array( 'label' => __( 'Bar background', 'medicare-leads-hub' ), 'section' => 'medicare_mobile_action_bar_settings' ) ) );

	$wp_customize->add_setting( 'medicare_mobile_action_bar_text_color', array( 'default' => '#ffffff', 'sanitize_callback' => 'medicare_leads_hub_sanitize_hex', 'transport' => 'refresh' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_mobile_action_bar_text_color', array( 'label' => __( 'Button text and icon color', 'medicare-leads-hub' ), 'section' => 'medicare_mobile_action_bar_settings' ) ) );

	$wp_customize->add_section(
		'medicare_hero_settings',
		array(
			'title'    => __( 'Homepage Hero', 'medicare-leads-hub' ),
			'panel'    => 'medicare_theme_options',
			'priority' => 30,
		)
	);

	$wp_customize->add_setting(
		'medicare_hero_image_id',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'medicare_hero_image_id',
			array(
				'label'     => __( 'Hero background image', 'medicare-leads-hub' ),
				'description' => __( 'Choose an image from the WordPress Media Library.', 'medicare-leads-hub' ),
				'section'   => 'medicare_hero_settings',
				'mime_type' => 'image',
			)
		)
	);

	$hero_text_settings = array(
		'medicare_hero_title'       => array( __( 'Hero heading', 'medicare-leads-hub' ), 'Reliable service for your home or business', 'sanitize_textarea_field' ),
		'medicare_hero_intro'       => array( __( 'Hero introduction', 'medicare-leads-hub' ), 'Tell visitors what your business does and how you help.', 'sanitize_textarea_field' ),
		'medicare_hero_supporting'  => array( __( 'Hero supporting text', 'medicare-leads-hub' ), 'From emergency lockouts to high-security lock upgrades, our experienced technicians deliver prompt, professional help when you need it.', 'sanitize_textarea_field' ),
		'medicare_hero_primary_text' => array( __( 'Primary CTA text', 'medicare-leads-hub' ), 'Get A Free Estimate', 'sanitize_text_field' ),
	);

	foreach ( $hero_text_settings as $id => $data ) {
		$wp_customize->add_setting( $id, array( 'default' => $data[1], 'sanitize_callback' => $data[2], 'transport' => 'refresh' ) );
		$wp_customize->add_control( $id, array( 'label' => $data[0], 'section' => 'medicare_hero_settings', 'type' => 'sanitize_textarea_field' === $data[2] ? 'textarea' : 'text' ) );
	}

	$wp_customize->add_setting( 'medicare_hero_primary_url', array( 'default' => '#contact', 'sanitize_callback' => 'esc_url_raw', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_hero_primary_url', array( 'label' => __( 'Primary CTA URL', 'medicare-leads-hub' ), 'section' => 'medicare_hero_settings', 'type' => 'url' ) );

	$wp_customize->add_setting( 'medicare_hero_show_phone', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_hero_show_phone', array( 'label' => __( 'Show phone CTA', 'medicare-leads-hub' ), 'section' => 'medicare_hero_settings', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'medicare_hero_overlay', array( 'default' => 48, 'sanitize_callback' => function ( $value ) { return medicare_leads_hub_sanitize_range( $value, 20, 75, 48 ); }, 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_hero_overlay', array( 'label' => __( 'Image overlay strength (%)', 'medicare-leads-hub' ), 'section' => 'medicare_hero_settings', 'type' => 'range', 'input_attrs' => array( 'min' => 20, 'max' => 75, 'step' => 1 ) ) );

	$wp_customize->add_setting( 'medicare_hero_card_width', array( 'default' => 660, 'sanitize_callback' => function ( $value ) { return medicare_leads_hub_sanitize_range( $value, 420, 760, 660 ); }, 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_hero_card_width', array( 'label' => __( 'Content card width (px)', 'medicare-leads-hub' ), 'section' => 'medicare_hero_settings', 'type' => 'range', 'input_attrs' => array( 'min' => 420, 'max' => 760, 'step' => 10 ) ) );

	$wp_customize->add_section(
		'medicare_why_settings',
		array(
			'title'    => __( 'Why Choose Us', 'medicare-leads-hub' ),
			'panel'    => 'medicare_theme_options',
			'priority' => 40,
		)
	);

	$why_text_settings = array(
		'medicare_why_heading' => array( __( 'Why Choose Us label', 'medicare-leads-hub' ), 'Why Choose Us', 'sanitize_text_field' ),
	);

	foreach ( $why_text_settings as $id => $data ) {
		$wp_customize->add_setting( $id, array( 'default' => $data[1], 'sanitize_callback' => $data[2], 'transport' => 'refresh' ) );
		$wp_customize->add_control( $id, array( 'label' => $data[0], 'section' => 'medicare_why_settings', 'type' => 'sanitize_textarea_field' === $data[2] ? 'textarea' : 'text' ) );
	}

	$wp_customize->add_setting( 'medicare_why_show', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_why_show', array( 'label' => __( 'Show Why Choose Us section', 'medicare-leads-hub' ), 'section' => 'medicare_why_settings', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'medicare_why_background', array( 'default' => '#ffffff', 'sanitize_callback' => 'medicare_leads_hub_sanitize_hex', 'transport' => 'refresh' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_why_background', array( 'label' => __( 'Section background', 'medicare-leads-hub' ), 'section' => 'medicare_why_settings' ) ) );

	$why_cards = array(
		1 => array( 'shield-check', 'Licensed & Insured', 'Our locksmiths are trained, insured, and committed to professional service.' ),
		2 => array( 'emergency-clock', '24/7 Emergency Response', 'Lockouts and urgent access problems can happen anytime. We’re ready to help.' ),
		3 => array( 'location-pin', 'Local & Trusted', 'Add the service area and trust signal that matter most to your customers.' ),
	);

	$icon_choices = array(
		'shield-check'    => __( 'Shield / check', 'medicare-leads-hub' ),
		'emergency-clock' => __( '24/7 emergency clock', 'medicare-leads-hub' ),
		'location-pin'    => __( 'Location pin', 'medicare-leads-hub' ),
	);

	foreach ( $why_cards as $index => $card ) {
		$icon_id = 'medicare_why_card_' . $index . '_icon';
		$wp_customize->add_setting( $icon_id, array( 'default' => $card[0], 'sanitize_callback' => 'medicare_leads_hub_sanitize_icon', 'transport' => 'refresh' ) );
		$wp_customize->add_control( $icon_id, array( 'label' => sprintf( __( 'Card %d icon', 'medicare-leads-hub' ), $index ), 'section' => 'medicare_why_settings', 'type' => 'select', 'choices' => $icon_choices ) );

		$title_id = 'medicare_why_card_' . $index . '_title';
		$wp_customize->add_setting( $title_id, array( 'default' => $card[1], 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
		$wp_customize->add_control( $title_id, array( 'label' => sprintf( __( 'Card %d heading', 'medicare-leads-hub' ), $index ), 'section' => 'medicare_why_settings', 'type' => 'text' ) );

		$body_id = 'medicare_why_card_' . $index . '_body';
		$wp_customize->add_setting( $body_id, array( 'default' => $card[2], 'sanitize_callback' => 'sanitize_textarea_field', 'transport' => 'refresh' ) );
		$wp_customize->add_control( $body_id, array( 'label' => sprintf( __( 'Card %d description', 'medicare-leads-hub' ), $index ), 'section' => 'medicare_why_settings', 'type' => 'textarea' ) );
	}

	$wp_customize->add_section(
		'medicare_process_settings',
		array(
			'title'    => __( '3-Step Process', 'medicare-leads-hub' ),
			'panel'    => 'medicare_theme_options',
			'priority' => 60,
		)
	);

	$wp_customize->add_setting( 'medicare_process_show', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_process_show', array( 'label' => __( 'Show 3-step process section', 'medicare-leads-hub' ), 'section' => 'medicare_process_settings', 'type' => 'checkbox' ) );

	$process_text_settings = array(
		'medicare_process_heading' => array( __( 'Section heading', 'medicare-leads-hub' ), 'Get Your Locksmith Service Done in 3 Easy Steps', 'sanitize_text_field' ),
	);

	foreach ( $process_text_settings as $id => $data ) {
		$wp_customize->add_setting( $id, array( 'default' => $data[1], 'sanitize_callback' => $data[2], 'transport' => 'refresh' ) );
		$wp_customize->add_control( $id, array( 'label' => $data[0], 'section' => 'medicare_process_settings', 'type' => 'text' ) );
	}

	$wp_customize->add_setting( 'medicare_process_image_id', array( 'default' => 0, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ) );
	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'medicare_process_image_id', array( 'label' => __( 'Process section image', 'medicare-leads-hub' ), 'description' => __( 'Choose one transparent WebP image from the WordPress Media Library.', 'medicare-leads-hub' ), 'section' => 'medicare_process_settings', 'mime_type' => 'image' ) ) );

	$process_steps = array(
		1 => array( 'Call Us', 'Tell our team what you need—lockout help, rekeying, installation, or security support.' ),
		2 => array( 'Get A Free Quote', 'After we understand the scope and location, we provide a clear, no-obligation estimate.' ),
		3 => array( 'Schedule Service', 'Choose a convenient time and our certified crew will arrive ready to work safely.' ),
	);

	foreach ( $process_steps as $index => $step ) {
		$title_id = 'medicare_process_step_' . $index . '_title';
		$body_id  = 'medicare_process_step_' . $index . '_body';
		$wp_customize->add_setting( $title_id, array( 'default' => $step[0], 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
		$wp_customize->add_control( $title_id, array( 'label' => sprintf( __( 'Step %d title', 'medicare-leads-hub' ), $index ), 'section' => 'medicare_process_settings', 'type' => 'text' ) );
		$wp_customize->add_setting( $body_id, array( 'default' => $step[1], 'sanitize_callback' => 'sanitize_textarea_field', 'transport' => 'refresh' ) );
		$wp_customize->add_control( $body_id, array( 'label' => sprintf( __( 'Step %d description', 'medicare-leads-hub' ), $index ), 'section' => 'medicare_process_settings', 'type' => 'textarea' ) );
	}

	$wp_customize->add_section(
		'medicare_services_settings',
		array(
			'title'    => __( 'Services Grid', 'medicare-leads-hub' ),
			'panel'    => 'medicare_theme_options',
			'priority' => 70,
		)
	);

	$wp_customize->add_setting( 'medicare_services_show', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_services_show', array( 'label' => __( 'Show Services Grid section', 'medicare-leads-hub' ), 'section' => 'medicare_services_settings', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'medicare_services_background', array( 'default' => '#F3F7FA', 'sanitize_callback' => 'medicare_leads_hub_sanitize_hex', 'transport' => 'refresh' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_services_background', array( 'label' => __( 'Section background', 'medicare-leads-hub' ), 'section' => 'medicare_services_settings' ) ) );

	$services_text_settings = array(
		'medicare_services_heading' => array( __( 'Section heading', 'medicare-leads-hub' ), 'Locksmith services that keep your property secure.', 'sanitize_text_field' ),
	);

	foreach ( $services_text_settings as $id => $data ) {
		$wp_customize->add_setting( $id, array( 'default' => $data[1], 'sanitize_callback' => $data[2], 'transport' => 'refresh' ) );
		$wp_customize->add_control( $id, array( 'label' => $data[0], 'section' => 'medicare_services_settings', 'type' => 'sanitize_textarea_field' === $data[2] ? 'textarea' : 'text' ) );
	}

	$service_cards = array(
		1 => array( 'Residential Lock Repair', 'Repair, adjust, and restore reliable locks for doors, gates, bedrooms, and entry points around your home.', 0 ),
		2 => array( 'Rekeying & Key Replacement', 'Restore control after a move, lost key, tenant change, or any time you want a fresh set of working keys.', 0 ),
		3 => array( 'Emergency Lockout Service', 'Fast, professional assistance when you are locked out of your home, office, or vehicle.', 0 ),
		4 => array( 'Smart Lock Installation', 'Upgrade everyday entry with practical smart locks, keypad systems, and connected access options.', 0 ),
		5 => array( 'Car Lockout & Key Service', 'Get dependable help with vehicle lockouts, replacement keys, and common automotive access problems.', 0 ),
		6 => array( 'High-Security Lock Upgrades', 'Strengthen doors and entry points with dependable hardware selected for your property and security needs.', 0 ),
	);

	foreach ( $service_cards as $index => $card ) {
		$fields = array(
			'title' => array( $card[0], 'sanitize_text_field', 'text' ),
			'body'  => array( $card[1], 'sanitize_textarea_field', 'textarea' ),
		);

		foreach ( $fields as $field => $field_data ) {
			$id = 'medicare_service_card_' . $index . '_' . $field;
			$wp_customize->add_setting( $id, array( 'default' => $field_data[0], 'sanitize_callback' => $field_data[1], 'transport' => 'refresh' ) );
			$wp_customize->add_control( $id, array( 'label' => sprintf( __( 'Card %d %s', 'medicare-leads-hub' ), $index, ucwords( str_replace( '_', ' ', $field ) ) ), 'section' => 'medicare_services_settings', 'type' => $field_data[2] ) );
		}

		$image_id = 'medicare_service_card_' . $index . '_image_id';
		$wp_customize->add_setting( $image_id, array( 'default' => $card[2], 'sanitize_callback' => 'absint', 'transport' => 'refresh' ) );
		$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $image_id, array( 'label' => sprintf( __( 'Card %d image', 'medicare-leads-hub' ), $index ), 'description' => __( 'Choose an optimized WebP image from the WordPress Media Library.', 'medicare-leads-hub' ), 'section' => 'medicare_services_settings', 'mime_type' => 'image' ) ) );
	}

	$wp_customize->add_section(
		'medicare_commercial_services_settings',
		array(
			'title'    => __( 'Commercial Services', 'medicare-leads-hub' ),
			'panel'    => 'medicare_theme_options',
			'priority' => 70,
		)
	);

	$wp_customize->add_setting( 'medicare_commercial_services_show', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_commercial_services_show', array( 'label' => __( 'Show Commercial Services section', 'medicare-leads-hub' ), 'section' => 'medicare_commercial_services_settings', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'medicare_commercial_services_background', array( 'default' => '#F3F7FA', 'sanitize_callback' => 'medicare_leads_hub_sanitize_hex', 'transport' => 'refresh' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_commercial_services_background', array( 'label' => __( 'Section background', 'medicare-leads-hub' ), 'section' => 'medicare_commercial_services_settings' ) ) );

	$commercial_services_text_settings = array(
		'medicare_commercial_services_heading' => array( __( 'Section heading', 'medicare-leads-hub' ), 'Commercial locksmith services that keep your business secure.', 'sanitize_text_field' ),
	);

	foreach ( $commercial_services_text_settings as $id => $data ) {
		$wp_customize->add_setting( $id, array( 'default' => $data[1], 'sanitize_callback' => $data[2], 'transport' => 'refresh' ) );
		$wp_customize->add_control( $id, array( 'label' => $data[0], 'section' => 'medicare_commercial_services_settings', 'type' => 'sanitize_textarea_field' === $data[2] ? 'textarea' : 'text' ) );
	}

	$commercial_service_cards = array(
		1 => array( 'Master Key Systems', 'Organize access across offices, suites, and facilities with a dependable master key plan.', 32 ),
		2 => array( 'High-Security Locks', 'Upgrade doors and entry points with commercial-grade locks selected for your property.', 33 ),
		3 => array( 'Commercial Rekeying', 'Restore control after staff changes, tenant turnover, or a lost key without replacing every lock.', 34 ),
		4 => array( 'Business Lockout Service', 'Get fast, professional help when your team is locked out and your business needs to stay moving.', 35 ),
		5 => array( 'Access Control Installation', 'Improve convenience and accountability with practical keypad, card, and electronic entry solutions.', 36 ),
		6 => array( 'Safe & Vault Services', 'Get reliable opening, repair, and security support for safes and vaults used by your business.', 37 ),
	);

	foreach ( $commercial_service_cards as $index => $card ) {
		$fields = array(
			'title' => array( $card[0], 'sanitize_text_field', 'text' ),
			'body'  => array( $card[1], 'sanitize_textarea_field', 'textarea' ),
		);

		foreach ( $fields as $field => $field_data ) {
			$id = 'medicare_commercial_service_card_' . $index . '_' . $field;
			$wp_customize->add_setting( $id, array( 'default' => $field_data[0], 'sanitize_callback' => $field_data[1], 'transport' => 'refresh' ) );
			$wp_customize->add_control( $id, array( 'label' => sprintf( __( 'Card %d %s', 'medicare-leads-hub' ), $index, ucwords( str_replace( '_', ' ', $field ) ) ), 'section' => 'medicare_commercial_services_settings', 'type' => $field_data[2] ) );
		}

		$image_id = 'medicare_commercial_service_card_' . $index . '_image_id';
		$wp_customize->add_setting( $image_id, array( 'default' => $card[2], 'sanitize_callback' => 'absint', 'transport' => 'refresh' ) );
		$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $image_id, array( 'label' => sprintf( __( 'Card %d image', 'medicare-leads-hub' ), $index ), 'description' => __( 'Choose an image from the WordPress Media Library.', 'medicare-leads-hub' ), 'section' => 'medicare_commercial_services_settings', 'mime_type' => 'image' ) ) );
	}

	$wp_customize->add_section(
		'medicare_specialty_services_settings',
		array(
			'title'    => __( 'Specialty Locksmith Services', 'medicare-leads-hub' ),
			'panel'    => 'medicare_theme_options',
			'priority' => 75,
		)
	);

	$wp_customize->add_setting( 'medicare_specialty_services_show', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_specialty_services_show', array( 'label' => __( 'Show Specialty Locksmith Services section', 'medicare-leads-hub' ), 'section' => 'medicare_specialty_services_settings', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'medicare_specialty_services_background', array( 'default' => '#ffffff', 'sanitize_callback' => 'medicare_leads_hub_sanitize_hex', 'transport' => 'refresh' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_specialty_services_background', array( 'label' => __( 'Section background', 'medicare-leads-hub' ), 'section' => 'medicare_specialty_services_settings' ) ) );

	$specialty_services_text_settings = array(
		'medicare_specialty_services_heading' => array( __( 'Section heading', 'medicare-leads-hub' ), 'Specialty Services', 'sanitize_text_field' ),
	);

	foreach ( $specialty_services_text_settings as $id => $data ) {
		$wp_customize->add_setting( $id, array( 'default' => $data[1], 'sanitize_callback' => $data[2], 'transport' => 'refresh' ) );
		$wp_customize->add_control( $id, array( 'label' => $data[0], 'section' => 'medicare_specialty_services_settings', 'type' => 'sanitize_textarea_field' === $data[2] ? 'textarea' : 'text' ) );
	}

	$specialty_service_cards = array(
		1 => array( 'Emergency', 'Emergency Lockout Service', 'Fast, professional assistance when you are locked out of your home, office, or vehicle.', 'emergency' ),
		2 => array( 'Everyday Access', 'Key Duplication & Replacement', 'Accurate replacement keys and practical solutions for everyday access needs.', 'keys' ),
		3 => array( 'Protection', 'High-Security Lock Upgrades', 'Upgrade vulnerable entry points with dependable hardware and stronger protection.', 'shield-lock' ),
		4 => array( 'Modern Entry', 'Access Control Systems', 'Modern keypad, card, and electronic access solutions for easier entry management.', 'keypad' ),
		5 => array( 'Organized Access', 'Master Key Systems', 'Organize access across multiple doors with a clear, convenient key system.', 'master-key' ),
		6 => array( 'Secure Storage', 'Safe & Vault Services', 'Professional support for business and residential safes, including opening and maintenance.', 'safe' ),
	);

	foreach ( $specialty_service_cards as $index => $card ) {
		$fields = array(
			'title'  => array( $card[1], 'sanitize_text_field', 'text' ),
			'body'   => array( $card[2], 'sanitize_textarea_field', 'textarea' ),
		);

		foreach ( $fields as $field => $field_data ) {
			$id = 'medicare_specialty_service_card_' . $index . '_' . $field;
			$wp_customize->add_setting( $id, array( 'default' => $field_data[0], 'sanitize_callback' => $field_data[1], 'transport' => 'refresh' ) );
			$wp_customize->add_control( $id, array( 'label' => sprintf( __( 'Card %d %s', 'medicare-leads-hub' ), $index, ucwords( str_replace( '_', ' ', $field ) ) ), 'section' => 'medicare_specialty_services_settings', 'type' => $field_data[2] ) );
		}
	}

	$wp_customize->add_section(
		'medicare_locksmith_faq_settings',
		array(
			'title'    => __( 'Locksmith Support Process', 'medicare-leads-hub' ),
			'description' => __( 'Edit the complete support process content, eyebrow, background, and image shown on the homepage.', 'medicare-leads-hub' ),
			'panel'    => 'medicare_theme_options',
			'priority' => 80,
		)
	);

	$wp_customize->add_setting( 'medicare_locksmith_faq_show', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_locksmith_faq_show', array( 'label' => __( 'Show Locksmith Support Process section', 'medicare-leads-hub' ), 'section' => 'medicare_locksmith_faq_settings', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'medicare_locksmith_faq_background', array( 'default' => '#F3F7FA', 'sanitize_callback' => 'medicare_leads_hub_sanitize_hex', 'transport' => 'refresh' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_locksmith_faq_background', array( 'label' => __( 'Section background', 'medicare-leads-hub' ), 'section' => 'medicare_locksmith_faq_settings' ) ) );

	$wp_customize->add_setting( 'medicare_locksmith_faq_eyebrow', array( 'default' => 'Locksmith Support', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_locksmith_faq_eyebrow', array( 'label' => __( 'Eyebrow label', 'medicare-leads-hub' ), 'section' => 'medicare_locksmith_faq_settings', 'type' => 'text' ) );

	$wp_customize->add_setting( 'medicare_locksmith_process_content', array( 'default' => medicare_leads_hub_locksmith_process_content_default(), 'sanitize_callback' => 'wp_kses_post', 'transport' => 'refresh' ) );
	$wp_customize->add_control(
		'medicare_locksmith_process_content',
		array(
			'label'       => __( 'Support process content', 'medicare-leads-hub' ),
			'description' => __( 'Use one field for the complete right-side content. Add headings, paragraphs, and lists with basic HTML tags such as h2, h3, p, strong, a, ul, and li. Scripts and unsafe markup are removed.', 'medicare-leads-hub' ),
			'section'     => 'medicare_locksmith_faq_settings',
			'type'        => 'textarea',
		)
	);

	$locksmith_faq_image_id = 'medicare_locksmith_faq_image_id';
	$wp_customize->add_setting( $locksmith_faq_image_id, array( 'default' => 0, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ) );
	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, $locksmith_faq_image_id, array( 'label' => __( 'Section image', 'medicare-leads-hub' ), 'description' => __( 'Choose a replacement image from the WordPress Media Library. The generated locksmith image is used by default.', 'medicare-leads-hub' ), 'section' => 'medicare_locksmith_faq_settings', 'mime_type' => 'image' ) ) );

	$wp_customize->add_section(
		'medicare_locksmith_pricing_settings',
		array(
			'title'    => __( 'Locksmith Pricing', 'medicare-leads-hub' ),
			'panel'    => 'medicare_theme_options',
			'priority' => 90,
		)
	);

	$wp_customize->add_setting( 'medicare_locksmith_pricing_show', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_locksmith_pricing_show', array( 'label' => __( 'Show Locksmith Pricing section', 'medicare-leads-hub' ), 'section' => 'medicare_locksmith_pricing_settings', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'medicare_locksmith_pricing_background', array( 'default' => '#F3F7FA', 'sanitize_callback' => 'medicare_leads_hub_sanitize_hex', 'transport' => 'refresh' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_locksmith_pricing_background', array( 'label' => __( 'Section background', 'medicare-leads-hub' ), 'section' => 'medicare_locksmith_pricing_settings' ) ) );

	$locksmith_pricing_text_settings = array(
		'medicare_locksmith_pricing_left_heading'  => array( __( 'Left column heading', 'medicare-leads-hub' ), 'Reliable locksmith services at fair prices', 'sanitize_text_field' ),
	);

	foreach ( $locksmith_pricing_text_settings as $id => $data ) {
		$wp_customize->add_setting( $id, array( 'default' => $data[1], 'sanitize_callback' => $data[2], 'transport' => 'refresh' ) );
		$wp_customize->add_control( $id, array( 'label' => $data[0], 'section' => 'medicare_locksmith_pricing_settings', 'type' => 'sanitize_textarea_field' === $data[2] ? 'textarea' : 'text' ) );
	}

	$wp_customize->add_setting( 'medicare_locksmith_pricing_left_content', array( 'default' => medicare_leads_hub_locksmith_pricing_content_default(), 'sanitize_callback' => 'wp_kses_post', 'transport' => 'refresh' ) );
	$wp_customize->add_control(
		'medicare_locksmith_pricing_left_content',
		array(
			'label'       => __( 'Left column content', 'medicare-leads-hub' ),
			'description' => __( 'Use one field for all copy below the heading. Add blank lines for separate paragraphs. Basic HTML is supported, including heading tags (h4), strong, em, links, lists, and paragraphs. Scripts and unsafe markup are removed.', 'medicare-leads-hub' ),
			'section'     => 'medicare_locksmith_pricing_settings',
			'type'        => 'textarea',
		)
	);

	$locksmith_pricing_headers = array(
		'medicare_locksmith_pricing_header_service'  => array( __( 'First table column label', 'medicare-leads-hub' ), 'Service Type' ),
		'medicare_locksmith_pricing_header_typical'  => array( __( 'Second table column label', 'medicare-leads-hub' ), 'Typical Service Cost' ),
		'medicare_locksmith_pricing_header_project'  => array( __( 'Third table column label', 'medicare-leads-hub' ), 'Average Project Cost' ),
	);

	foreach ( $locksmith_pricing_headers as $id => $data ) {
		$wp_customize->add_setting( $id, array( 'default' => $data[1], 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
		$wp_customize->add_control( $id, array( 'label' => $data[0], 'section' => 'medicare_locksmith_pricing_settings', 'type' => 'text' ) );
	}

	$wp_customize->add_setting(
		'medicare_locksmith_pricing_rows',
		array(
			'default'           => medicare_leads_hub_get_locksmith_pricing_rows(),
			'sanitize_callback' => 'medicare_leads_hub_sanitize_locksmith_pricing_rows',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		new Medicare_Leads_Hub_Pricing_Repeater_Control(
			$wp_customize,
			'medicare_locksmith_pricing_rows',
			array(
				'label'       => __( 'Pricing table rows', 'medicare-leads-hub' ),
				'description' => __( 'Add, edit, or remove pricing rows. A row with an empty service name is not published.', 'medicare-leads-hub' ),
				'section'     => 'medicare_locksmith_pricing_settings',
				'settings'    => 'medicare_locksmith_pricing_rows',
			)
		)
	);

	$wp_customize->add_section(
		'medicare_locksmith_testimonials_settings',
		array(
			'title'    => __( 'Client Testimonials', 'medicare-leads-hub' ),
			'panel'    => 'medicare_theme_options',
			'priority' => 100,
		)
	);

	$wp_customize->add_setting( 'medicare_locksmith_testimonials_show', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_locksmith_testimonials_show', array( 'label' => __( 'Show Client Testimonials section', 'medicare-leads-hub' ), 'section' => 'medicare_locksmith_testimonials_settings', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'medicare_locksmith_testimonials_background', array( 'default' => '#ffffff', 'sanitize_callback' => 'medicare_leads_hub_sanitize_hex', 'transport' => 'refresh' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_locksmith_testimonials_background', array( 'label' => __( 'Section background', 'medicare-leads-hub' ), 'section' => 'medicare_locksmith_testimonials_settings' ) ) );

	$locksmith_testimonials_text_settings = array(
		'medicare_locksmith_testimonials_eyebrow' => array( __( 'Eyebrow label', 'medicare-leads-hub' ), 'Client Testimonials', 'sanitize_text_field' ),
		'medicare_locksmith_testimonials_heading' => array( __( 'Section heading', 'medicare-leads-hub' ), 'Hear it from our happy clients!', 'sanitize_text_field' ),
	);

	foreach ( $locksmith_testimonials_text_settings as $id => $data ) {
		$wp_customize->add_setting( $id, array( 'default' => $data[1], 'sanitize_callback' => $data[2], 'transport' => 'refresh' ) );
		$wp_customize->add_control( $id, array( 'label' => $data[0], 'section' => 'medicare_locksmith_testimonials_settings', 'type' => 'text' ) );
	}

	$wp_customize->add_setting(
		'medicare_locksmith_testimonial_items',
		array(
			'default'           => medicare_leads_hub_get_testimonial_items(),
			'sanitize_callback' => 'medicare_leads_hub_sanitize_testimonial_items',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		new Medicare_Leads_Hub_Testimonials_Repeater_Control(
			$wp_customize,
			'medicare_locksmith_testimonial_items',
			array(
				'label'       => __( 'Reviews', 'medicare-leads-hub' ),
				'description' => __( 'Edit, reorder, add, or remove customer reviews. Reviews without a name are not published.', 'medicare-leads-hub' ),
				'section'     => 'medicare_locksmith_testimonials_settings',
				'settings'    => 'medicare_locksmith_testimonial_items',
			)
		)
	);

	$wp_customize->add_section(
		'medicare_expectations_settings',
		array(
			'title'    => __( 'What To Expect', 'medicare-leads-hub' ),
			'panel'    => 'medicare_theme_options',
			'priority' => 105,
		)
	);

	$wp_customize->add_setting( 'medicare_expectations_show', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_expectations_show', array( 'label' => __( 'Show What To Expect section', 'medicare-leads-hub' ), 'section' => 'medicare_expectations_settings', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'medicare_expectations_background_image_id', array( 'default' => 0, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ) );
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'medicare_expectations_background_image_id',
			array(
				'label'     => __( 'Background image', 'medicare-leads-hub' ),
				'description' => __( 'Choose a wide image from the WordPress Media Library.', 'medicare-leads-hub' ),
				'section'   => 'medicare_expectations_settings',
				'mime_type' => 'image',
			)
		)
	);

	$wp_customize->add_setting( 'medicare_expectations_background', array( 'default' => '#062A4A', 'sanitize_callback' => 'medicare_leads_hub_sanitize_hex', 'transport' => 'refresh' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_expectations_background', array( 'label' => __( 'Fallback background color', 'medicare-leads-hub' ), 'section' => 'medicare_expectations_settings' ) ) );

	$wp_customize->add_setting( 'medicare_expectations_overlay', array( 'default' => 76, 'sanitize_callback' => function ( $value ) { return medicare_leads_hub_sanitize_range( $value, 35, 90, 76 ); }, 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_expectations_overlay', array( 'label' => __( 'Image overlay strength (%)', 'medicare-leads-hub' ), 'section' => 'medicare_expectations_settings', 'type' => 'range', 'input_attrs' => array( 'min' => 35, 'max' => 90, 'step' => 1 ) ) );

	$expectations_text_settings = array(
		'medicare_expectations_heading' => array( __( 'Section heading', 'medicare-leads-hub' ), 'What to expect when you hire our locksmith team', 'sanitize_textarea_field' ),
	);
	foreach ( $expectations_text_settings as $id => $data ) {
		$wp_customize->add_setting( $id, array( 'default' => $data[1], 'sanitize_callback' => $data[2], 'transport' => 'refresh' ) );
		$wp_customize->add_control( $id, array( 'label' => $data[0], 'section' => 'medicare_expectations_settings', 'type' => 'sanitize_textarea_field' === $data[2] ? 'textarea' : 'text' ) );
	}

	$expectations_cards = array(
		1 => array( 'Share the Problem', 'Tell us what happened, where you are, and what kind of lock or access issue you are facing.', 'navy' ),
		2 => array( 'Get Clear Recommendations', 'We assess the situation, explain your options, and recommend the right repair, rekey, or upgrade.', 'gold' ),
		3 => array( 'Approve the Plan', 'You receive a straightforward estimate before work begins, with no pressure and no hidden surprises.', 'navy' ),
		4 => array( 'Professional Service', 'Our locksmith completes the work carefully, tests the result, and keeps your property protected.', 'gold' ),
		5 => array( 'Secure the Finish', 'We confirm everything works smoothly and show you what to expect from your new hardware.', 'navy' ),
		6 => array( 'Stay Protected', 'Leave with dependable access and practical next steps for your home, vehicle, or business.', 'gold' ),
	);
	foreach ( $expectations_cards as $index => $card ) {
		$title_id = 'medicare_expectations_card_' . $index . '_title';
		$body_id  = 'medicare_expectations_card_' . $index . '_body';
		$tone_id  = 'medicare_expectations_card_' . $index . '_tone';
		$wp_customize->add_setting( $title_id, array( 'default' => $card[0], 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
		$wp_customize->add_control( $title_id, array( 'label' => sprintf( __( 'Card %d title', 'medicare-leads-hub' ), $index ), 'section' => 'medicare_expectations_settings', 'type' => 'text' ) );
		$wp_customize->add_setting( $body_id, array( 'default' => $card[1], 'sanitize_callback' => 'sanitize_textarea_field', 'transport' => 'refresh' ) );
		$wp_customize->add_control( $body_id, array( 'label' => sprintf( __( 'Card %d description', 'medicare-leads-hub' ), $index ), 'section' => 'medicare_expectations_settings', 'type' => 'textarea' ) );
		$wp_customize->add_setting( $tone_id, array( 'default' => $card[2], 'sanitize_callback' => 'medicare_leads_hub_sanitize_expectations_tone', 'transport' => 'refresh' ) );
		$wp_customize->add_control( $tone_id, array( 'label' => sprintf( __( 'Card %d color', 'medicare-leads-hub' ), $index ), 'section' => 'medicare_expectations_settings', 'type' => 'select', 'choices' => array( 'navy' => __( 'Navy', 'medicare-leads-hub' ), 'gold' => __( 'Gold', 'medicare-leads-hub' ) ) ) );
	}

	$wp_customize->add_section(
		'medicare_booking_faq_settings',
		array(
			'title'    => __( 'Booking FAQ', 'medicare-leads-hub' ),
			'panel'    => 'medicare_theme_options',
			'priority' => 110,
		)
	);

	$wp_customize->add_setting( 'medicare_booking_faq_show', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_booking_faq_show', array( 'label' => __( 'Show Booking FAQ section', 'medicare-leads-hub' ), 'section' => 'medicare_booking_faq_settings', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'medicare_booking_faq_background', array( 'default' => '#ffffff', 'sanitize_callback' => 'medicare_leads_hub_sanitize_hex', 'transport' => 'refresh' ) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'medicare_booking_faq_background', array( 'label' => __( 'Section background', 'medicare-leads-hub' ), 'section' => 'medicare_booking_faq_settings' ) ) );

	$booking_faq_text_settings = array(
		'medicare_booking_faq_eyebrow' => array( __( 'Eyebrow label', 'medicare-leads-hub' ), 'FAQ', 'sanitize_text_field' ),
		'medicare_booking_faq_heading' => array( __( 'Section heading', 'medicare-leads-hub' ), 'Questions before you book?', 'sanitize_text_field' ),
		'medicare_booking_faq_intro'   => array( __( 'Section introduction', 'medicare-leads-hub' ), 'Here are answers to common locksmith questions.', 'sanitize_textarea_field' ),
	);

	foreach ( $booking_faq_text_settings as $id => $data ) {
		$wp_customize->add_setting( $id, array( 'default' => $data[1], 'sanitize_callback' => $data[2], 'transport' => 'refresh' ) );
		$wp_customize->add_control( $id, array( 'label' => $data[0], 'section' => 'medicare_booking_faq_settings', 'type' => 'sanitize_textarea_field' === $data[2] ? 'textarea' : 'text' ) );
	}

	$wp_customize->add_setting(
		'medicare_booking_faq_items',
		array(
			'default'           => medicare_leads_hub_get_booking_faq_items(),
			'sanitize_callback' => 'medicare_leads_hub_sanitize_booking_faq_items',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		new Medicare_Leads_Hub_FAQ_Repeater_Control(
			$wp_customize,
			'medicare_booking_faq_items',
			array(
				'label'       => __( 'FAQ items', 'medicare-leads-hub' ),
				'description' => __( 'Add, edit, or remove questions. Empty questions are not published.', 'medicare-leads-hub' ),
				'section'     => 'medicare_booking_faq_settings',
				'settings'    => 'medicare_booking_faq_items',
			)
		)
	);

	$wp_customize->add_section(
		'medicare_about_settings',
		array(
			'title'    => __( 'Homepage — About / Experience', 'medicare-leads-hub' ),
			'description' => __( 'This is the About / Experience block on the homepage. The standalone About Us page is managed separately.', 'medicare-leads-hub' ),
			'panel'    => 'medicare_theme_options',
			'priority' => 50,
		)
	);

	$wp_customize->add_setting( 'medicare_about_show', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_about_show', array( 'label' => __( 'Show About / Experience section', 'medicare-leads-hub' ), 'section' => 'medicare_about_settings', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'medicare_about_heading', array( 'default' => 'Local Locksmith Experience You Can Count On', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_about_heading', array( 'label' => __( 'Section heading', 'medicare-leads-hub' ), 'section' => 'medicare_about_settings', 'type' => 'text' ) );

	$about_content_default = implode(
		"\n\n",
		array_filter(
			array(
				get_theme_mod( 'medicare_about_paragraph_1', 'Tell visitors who you help, what you do, and why your team is a dependable choice.' ),
				get_theme_mod( 'medicare_about_paragraph_2', "Whether you are locked out, moving into a new home, upgrading your office, or planning a better access system, our team is ready. We handle emergency entry, rekeying, lock installation, smart locks, commercial access control, and safe services with practical solutions for every property." ),
				get_theme_mod( 'medicare_about_paragraph_3', 'Add your experience, values, guarantees, and the details that make your business different.' ),
			)
		)
	);

	$wp_customize->add_setting( 'medicare_about_content', array( 'default' => $about_content_default, 'sanitize_callback' => 'wp_kses_post', 'transport' => 'refresh' ) );
	$wp_customize->add_control(
		'medicare_about_content',
		array(
			'label'       => __( 'About / Experience content', 'medicare-leads-hub' ),
			'description' => __( 'Use one field for all content. Add blank lines for separate paragraphs. Basic HTML is supported, including heading tags (h2, h3), strong, em, links, lists, and paragraphs. Scripts and unsafe markup are removed.', 'medicare-leads-hub' ),
			'section'     => 'medicare_about_settings',
			'type'        => 'textarea',
		)
	);

	$wp_customize->add_section(
		'medicare_about_page_settings',
		array(
			'title'    => __( 'Page Content & Values', 'medicare-leads-hub' ),
			'description' => __( 'These settings apply only to the standalone About Us page.', 'medicare-leads-hub' ),
			'panel'    => 'medicare_theme_options',
			'priority' => 55,
		)
	);

	$wp_customize->add_setting( 'medicare_about_page_show', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_about_page_show', array( 'label' => __( 'Show About Us page content', 'medicare-leads-hub' ), 'section' => 'medicare_about_page_settings', 'type' => 'checkbox' ) );

	$wp_customize->add_setting( 'medicare_about_page_hero_image_id', array( 'default' => 0, 'sanitize_callback' => 'absint', 'transport' => 'refresh' ) );
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'medicare_about_page_hero_image_id',
			array(
				'label'       => __( 'Hero background image', 'medicare-leads-hub' ),
				'description' => __( 'Choose a wide WebP image from the Media Library. The generated locksmith image is used by default.', 'medicare-leads-hub' ),
				'section'     => 'medicare_about_page_settings',
				'mime_type'   => 'image',
			)
		)
	);

	$wp_customize->add_setting( 'medicare_about_page_hero_overlay', array( 'default' => 68, 'sanitize_callback' => function ( $value ) { return medicare_leads_hub_sanitize_range( $value, 35, 90, 68 ); }, 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_about_page_hero_overlay', array( 'label' => __( 'Hero overlay strength (%)', 'medicare-leads-hub' ), 'section' => 'medicare_about_page_settings', 'type' => 'range', 'input_attrs' => array( 'min' => 35, 'max' => 90, 'step' => 1 ) ) );

	$about_page_text_settings = array(
		'medicare_about_page_hero_title'   => array( __( 'Hero heading', 'medicare-leads-hub' ), 'About our business', 'sanitize_text_field' ),
		'medicare_about_page_hero_intro'   => array( __( 'Hero introduction', 'medicare-leads-hub' ), 'We provide dependable locksmith service with clear recommendations, careful workmanship, and respect for your property.', 'sanitize_textarea_field' ),
		'medicare_about_page_mission_title' => array( __( 'Mission heading', 'medicare-leads-hub' ), 'Mission', 'sanitize_text_field' ),
		'medicare_about_page_mission_body'  => array( __( 'Mission copy', 'medicare-leads-hub' ), 'Our mission is to provide the Centennial community with reliable, professional locksmith services that keep people, homes, and businesses safe. We are committed to honest communication, expert workmanship, and treating every customer with respect. By focusing on integrity, quality, and service, we aim to be the trusted locksmith partner our neighbors can count on.', 'sanitize_textarea_field' ),
		'medicare_about_page_vision_title' => array( __( 'Vision heading', 'medicare-leads-hub' ), 'Vision', 'sanitize_text_field' ),
		'medicare_about_page_vision_body'  => array( __( 'Vision copy', 'medicare-leads-hub' ), 'Our vision is to be the most trusted and respected locksmith company in Centennial and surrounding areas. We strive to set the standard for professionalism, customer care, and innovation in the locksmith industry, continually improving our skills and services to meet evolving security needs.', 'sanitize_textarea_field' ),
	);

	foreach ( $about_page_text_settings as $id => $data ) {
		$wp_customize->add_setting( $id, array( 'default' => $data[1], 'sanitize_callback' => $data[2], 'transport' => 'refresh' ) );
		$wp_customize->add_control( $id, array( 'label' => $data[0], 'section' => 'medicare_about_page_settings', 'type' => 'sanitize_textarea_field' === $data[2] ? 'textarea' : 'text' ) );
	}

	$wp_customize->add_setting( 'medicare_about_page_values_show', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_about_page_values_show', array( 'label' => __( 'Show values cards', 'medicare-leads-hub' ), 'section' => 'medicare_about_page_settings', 'type' => 'checkbox' ) );

	$about_page_values = array(
		1 => array( 'Integrity', 'We show up, follow through, and deliver exactly what we promise—every time.', 'shield-check' ),
		2 => array( 'Quality', 'We treat every project like it’s our own, ensuring smooth finishes and durable results.', 'medal-star' ),
		3 => array( 'Customer Oriented', 'We prioritize honest communication, transparent pricing, and a seamless experience from start to finish.', 'people-check' ),
		4 => array( 'Reliable', 'We respect your time, property, and trust—always showing up on schedule and cleaning up after ourselves.', 'lock' ),
	);

	foreach ( $about_page_values as $index => $value ) {
		$title_id = 'medicare_about_page_value_' . $index . '_title';
		$body_id  = 'medicare_about_page_value_' . $index . '_body';
		$icon_id  = 'medicare_about_page_value_' . $index . '_icon';
		$wp_customize->add_setting( $title_id, array( 'default' => $value[0], 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
		$wp_customize->add_control( $title_id, array( 'label' => sprintf( __( 'Value %d title', 'medicare-leads-hub' ), $index ), 'section' => 'medicare_about_page_settings', 'type' => 'text' ) );
		$wp_customize->add_setting( $body_id, array( 'default' => $value[1], 'sanitize_callback' => 'sanitize_textarea_field', 'transport' => 'refresh' ) );
		$wp_customize->add_control( $body_id, array( 'label' => sprintf( __( 'Value %d description', 'medicare-leads-hub' ), $index ), 'section' => 'medicare_about_page_settings', 'type' => 'textarea' ) );
		$wp_customize->add_setting( $icon_id, array( 'default' => $value[2], 'sanitize_callback' => 'medicare_leads_hub_sanitize_about_value_icon', 'transport' => 'refresh' ) );
		$wp_customize->add_control( $icon_id, array( 'label' => sprintf( __( 'Value %d icon', 'medicare-leads-hub' ), $index ), 'section' => 'medicare_about_page_settings', 'type' => 'select', 'choices' => array( 'shield-check' => __( 'Shield check', 'medicare-leads-hub' ), 'medal-star' => __( 'Quality medal', 'medicare-leads-hub' ), 'people-check' => __( 'People check', 'medicare-leads-hub' ), 'lock' => __( 'Lock', 'medicare-leads-hub' ) ) ) );
	}

	$wp_customize->add_section(
		'medicare_contact_page_settings',
		array(
			'title'       => __( 'Page Content & Form', 'medicare-leads-hub' ),
			'description' => __( 'All website submissions use Contact Form 7. When Flamingo is active, it stores each submission. Create or edit the form under Contact > Contact Forms, then paste its shortcode below.', 'medicare-leads-hub' ),
			'panel'       => 'medicare_theme_options',
			'priority'    => 60,
		)
	);

	$wp_customize->add_setting( 'medicare_contact_page_show', array( 'default' => true, 'sanitize_callback' => 'wp_validate_boolean', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_contact_page_show', array( 'label' => __( 'Show Contact Us page content', 'medicare-leads-hub' ), 'section' => 'medicare_contact_page_settings', 'type' => 'checkbox' ) );

	$contact_page_text_settings = array(
		'medicare_contact_page_heading'       => array( __( 'Main heading', 'medicare-leads-hub' ), 'Ready to get help from a local locksmith?', 'sanitize_text_field' ),
		'medicare_contact_page_intro'         => array( __( 'Intro copy', 'medicare-leads-hub' ), 'Tell us what happened, where you are, and what kind of lock or access issue you are facing. Our team will respond with clear next steps.', 'sanitize_textarea_field' ),
		'medicare_contact_page_form_heading' => array( __( 'Form heading', 'medicare-leads-hub' ), 'Request a free quote', 'sanitize_text_field' ),
		'medicare_contact_page_address'      => array( __( 'Service address / area', 'medicare-leads-hub' ), '', 'sanitize_text_field' ),
	);

	foreach ( $contact_page_text_settings as $id => $data ) {
		$wp_customize->add_setting( $id, array( 'default' => $data[1], 'sanitize_callback' => $data[2], 'transport' => 'refresh' ) );
		$wp_customize->add_control( $id, array( 'label' => $data[0], 'section' => 'medicare_contact_page_settings', 'type' => 'sanitize_textarea_field' === $data[2] ? 'textarea' : 'text' ) );
	}

	$wp_customize->add_setting( 'medicare_contact_page_map_embed_url', array( 'default' => '', 'sanitize_callback' => 'medicare_leads_hub_sanitize_map_embed_url', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_contact_page_map_embed_url', array( 'label' => __( 'Map embed URL or iframe code', 'medicare-leads-hub' ), 'description' => __( 'Paste either the Google Maps embed URL or the complete iframe code from Share → Embed a map, then click Publish. The theme will extract and save only the iframe URL. A regular or maps.app.goo.gl share link works for the button but cannot render inside the map frame.', 'medicare-leads-hub' ), 'section' => 'medicare_contact_page_settings', 'type' => 'textarea' ) );

	$wp_customize->add_setting( 'medicare_contact_page_recipient_email', array( 'default' => '', 'sanitize_callback' => 'sanitize_email', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_contact_page_recipient_email', array( 'label' => __( 'Contact email display fallback', 'medicare-leads-hub' ), 'description' => __( 'Optional email shown in the Contact Us details when the business profile and footer email are blank. Set the actual CF7 recipient under Contact > Contact Forms > Mail.', 'medicare-leads-hub' ), 'section' => 'medicare_contact_page_settings', 'type' => 'email' ) );

	$wp_customize->add_setting( 'medicare_contact_page_cf7_shortcode', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'medicare_contact_page_cf7_shortcode', array( 'label' => __( 'Contact Form 7 shortcode', 'medicare-leads-hub' ), 'description' => __( 'Example: [contact-form-7 id="123" title="Contact form"]', 'medicare-leads-hub' ), 'section' => 'medicare_contact_page_settings', 'type' => 'text' ) );

	/*
	 * Keep the Customizer navigation task-focused. Existing setting IDs remain
	 * unchanged so saved values are preserved, while the sections are grouped
	 * under the page where their content is actually rendered.
	 */
	$homepage_section_priorities = array(
		'medicare_hero_settings'                    => 10,
		'medicare_why_settings'                     => 20,
		'medicare_about_settings'                   => 30,
		'medicare_process_settings'                 => 40,
		'medicare_services_settings'                => 50,
		'medicare_commercial_services_settings'     => 60,
		'medicare_specialty_services_settings'      => 70,
		'medicare_locksmith_faq_settings'           => 80,
		'medicare_locksmith_pricing_settings'       => 90,
		'medicare_expectations_settings'            => 100,
		'medicare_locksmith_testimonials_settings'  => 110,
		'medicare_booking_faq_settings'             => 120,
	);

	foreach ( $homepage_section_priorities as $section_id => $priority ) {
		$section = $wp_customize->get_section( $section_id );
		if ( $section ) {
			$section->panel    = 'medicare_homepage_options';
			$section->priority = $priority;
		}
	}

	$about_page_section = $wp_customize->get_section( 'medicare_about_page_settings' );
	if ( $about_page_section ) {
		$about_page_section->panel = 'medicare_about_page_options';
	}

	$contact_page_section = $wp_customize->get_section( 'medicare_contact_page_settings' );
	if ( $contact_page_section ) {
		$contact_page_section->panel = 'medicare_contact_page_options';
	}

}
add_action( 'customize_register', 'medicare_leads_hub_customizer' );

/**
 * Turn the selected Business Profile preview image into the static screenshot
 * WordPress displays under Appearance -> Themes.
 *
 * WordPress reads this file from the active theme directory, so this is a
 * package-level preview rather than a front-end image or SEO setting.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 * @return void
 */
function medicare_leads_hub_update_theme_preview_screenshot( $wp_customize ) {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$attachment_id = absint( get_theme_mod( 'medicare_theme_preview_image_id', 0 ) );
	if ( ! $attachment_id ) {
		return;
	}

	$source = get_attached_file( $attachment_id );
	if ( ! $source || ! file_exists( $source ) ) {
		set_transient( 'medicare_theme_preview_notice', __( 'Theme preview could not be updated because the selected image file was not found.', 'medicare-leads-hub' ), MINUTE_IN_SECONDS );
		return;
	}

	$theme_directory = get_stylesheet_directory();
	$target          = trailingslashit( $theme_directory ) . 'screenshot.png';
	if ( ! is_dir( $theme_directory ) || ! is_writable( $theme_directory ) ) {
		set_transient( 'medicare_theme_preview_notice', __( 'Theme preview could not be updated. The active theme folder is not writable by WordPress.', 'medicare-leads-hub' ), MINUTE_IN_SECONDS );
		return;
	}

	$editor = wp_get_image_editor( $source );
	if ( is_wp_error( $editor ) ) {
		set_transient( 'medicare_theme_preview_notice', __( 'Theme preview could not be updated. Please choose a PNG, JPG, GIF, or WebP image.', 'medicare-leads-hub' ), MINUTE_IN_SECONDS );
		return;
	}

	$resize_result = $editor->resize( 1200, 900, false );
	if ( is_wp_error( $resize_result ) ) {
		set_transient( 'medicare_theme_preview_notice', __( 'Theme preview could not be resized.', 'medicare-leads-hub' ), MINUTE_IN_SECONDS );
		return;
	}

	$saved = $editor->save( $target, 'image/png' );
	if ( is_wp_error( $saved ) ) {
		set_transient( 'medicare_theme_preview_notice', __( 'Theme preview could not be saved as screenshot.png. Check theme folder permissions.', 'medicare-leads-hub' ), MINUTE_IN_SECONDS );
		return;
	}

	if ( function_exists( 'wp_clean_themes_cache' ) ) {
		wp_clean_themes_cache();
	}

	set_transient( 'medicare_theme_preview_notice', __( 'Theme preview updated. Open Appearance → Themes and refresh the page to see the new screenshot.', 'medicare-leads-hub' ), MINUTE_IN_SECONDS );
}
add_action( 'customize_save_after', 'medicare_leads_hub_update_theme_preview_screenshot' );

/**
 * Keep the WordPress Appearance -> Themes display name aligned with the
 * reusable Business Profile. The theme folder/stylesheet slug intentionally
 * stays stable so saved Customizer settings continue to work after migration.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 * @return void
 */
function medicare_leads_hub_sync_theme_name_from_business_profile( $wp_customize ) {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$business_name = sanitize_text_field( get_theme_mod( 'medicare_business_name', '' ) );
	$business_name = trim( preg_replace( '/[\r\n]+/', ' ', $business_name ) );

	if ( '' === $business_name ) {
		return;
	}

	$style_path = trailingslashit( get_stylesheet_directory() ) . 'style.css';
	if ( ! is_readable( $style_path ) || ! is_writable( $style_path ) ) {
		set_transient( 'medicare_theme_name_notice', __( 'Theme name could not be updated because the active theme style.css file is not writable.', 'medicare-leads-hub' ), MINUTE_IN_SECONDS );
		return;
	}

	$stylesheet = file_get_contents( $style_path );
	if ( false === $stylesheet || ! preg_match( '/^Theme Name:\s*.*$/mi', $stylesheet ) ) {
		set_transient( 'medicare_theme_name_notice', __( 'Theme name could not be updated because the active theme style.css header was not found.', 'medicare-leads-hub' ), MINUTE_IN_SECONDS );
		return;
	}

	$updated_stylesheet = preg_replace(
		'/^Theme Name:\s*.*$/mi',
		'Theme Name: ' . $business_name,
		$stylesheet,
		1
	);

	if ( ! is_string( $updated_stylesheet ) || $updated_stylesheet === $stylesheet ) {
		return;
	}

	if ( false === file_put_contents( $style_path, $updated_stylesheet, LOCK_EX ) ) {
		set_transient( 'medicare_theme_name_notice', __( 'Theme name could not be saved. Check the active theme style.css permissions.', 'medicare-leads-hub' ), MINUTE_IN_SECONDS );
		return;
	}

	if ( function_exists( 'wp_clean_themes_cache' ) ) {
		wp_clean_themes_cache();
	}

	set_transient( 'medicare_theme_name_notice', sprintf( __( 'Theme name updated to “%s”. Refresh Appearance → Themes to see it.', 'medicare-leads-hub' ), $business_name ), MINUTE_IN_SECONDS );
}
add_action( 'customize_save_after', 'medicare_leads_hub_sync_theme_name_from_business_profile', 20 );

function medicare_leads_hub_theme_name_admin_notice() {
	$message = get_transient( 'medicare_theme_name_notice' );
	if ( ! $message ) {
		return;
	}

	delete_transient( 'medicare_theme_name_notice' );
	$class = false !== stripos( $message, 'could not' ) ? 'notice-error' : 'notice-success';
	printf( '<div class="notice %1$s is-dismissible"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}
add_action( 'admin_notices', 'medicare_leads_hub_theme_name_admin_notice' );

function medicare_leads_hub_theme_preview_admin_notice() {
	$message = get_transient( 'medicare_theme_preview_notice' );
	if ( ! $message ) {
		return;
	}

	delete_transient( 'medicare_theme_preview_notice' );
	$class = false !== stripos( $message, 'could not' ) ? 'notice-error' : 'notice-success';
	printf( '<div class="notice %1$s is-dismissible"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}
add_action( 'admin_notices', 'medicare_leads_hub_theme_preview_admin_notice' );

function medicare_leads_hub_enqueue_customizer_assets() {
	$script_path       = get_template_directory() . '/assets/js/faq-repeater-control.js';
	$navigation_path   = get_template_directory() . '/assets/js/customizer-theme-navigation.js';
	$style_path        = get_template_directory() . '/assets/css/customizer.css';

	wp_enqueue_script(
		'medicare-leads-hub-faq-repeater-control',
		get_template_directory_uri() . '/assets/js/faq-repeater-control.js',
		array( 'customize-controls' ),
		file_exists( $script_path ) ? filemtime( $script_path ) : null,
		true
	);
	wp_enqueue_script(
		'medicare-leads-hub-theme-navigation',
		get_template_directory_uri() . '/assets/js/customizer-theme-navigation.js',
		array( 'customize-controls' ),
		file_exists( $navigation_path ) ? filemtime( $navigation_path ) : null,
		true
	);
	wp_enqueue_style(
		'medicare-leads-hub-customizer',
		get_template_directory_uri() . '/assets/css/customizer.css',
		array( 'customize-controls' ),
		file_exists( $style_path ) ? filemtime( $style_path ) : null
	);
}
add_action( 'customize_controls_enqueue_scripts', 'medicare_leads_hub_enqueue_customizer_assets' );

function medicare_leads_hub_contact_recipient_email() {
	$recipient = sanitize_email( get_theme_mod( 'medicare_contact_page_recipient_email', '' ) );
	if ( $recipient ) {
		return $recipient;
	}

	$profile = medicare_leads_hub_get_business_profile();
	if ( ! empty( $profile['email'] ) && is_email( $profile['email'] ) ) {
		return sanitize_email( $profile['email'] );
	}

	$footer_email = sanitize_email( get_theme_mod( 'medicare_footer_email', '' ) );
	if ( $footer_email ) {
		return $footer_email;
	}

	return sanitize_email( get_option( 'admin_email' ) );
}

/**
 * Seed a neutral starter profile for fresh installs while preserving any
 * existing site's saved Customizer values.
 */
function medicare_leads_hub_migrate_locksmith_content() {
	$migration_key = 'medicare_leads_hub_locksmith_content_migration';
	$migration_version = get_option( $migration_key, '' );
	if ( in_array( $migration_version, array( '1.1', '1.2' ), true ) ) {
		medicare_leads_hub_seed_profile_from_legacy();
		if ( ! get_option( 'medicare_leads_hub_profile_mode', '' ) ) {
			update_option( 'medicare_leads_hub_profile_mode', '1.2' === $migration_version ? 'starter' : 'site', false );
		}
		return;
	}

	// Never overwrite an existing site's saved profile during a theme update.
	$existing_mods = get_theme_mods();
	if ( is_array( $existing_mods ) && ! empty( $existing_mods ) ) {
		medicare_leads_hub_seed_profile_from_legacy();
		update_option( 'medicare_leads_hub_profile_mode', 'site', false );
		update_option( $migration_key, '1.1', false );
		return;
	}

	// Existing sites return above before this block, so their current content is safe.
	$starter_values = array(
		'medicare_announcement_text'             => 'Serving your local area',
		'medicare_footer_service_area'           => '',
		'medicare_footer_contact_label'          => 'Contact',
		'medicare_footer_copyright'              => 'Your Business Name. All rights reserved.',
		'medicare_header_cta_url'                => '#contact',
		'medicare_hero_image_id'                  => 0,
		'medicare_hero_title'                     => 'Reliable service for your home or business',
		'medicare_hero_intro'                     => 'Tell visitors what your business does and how you help.',
		'medicare_hero_supporting'                => 'Clear recommendations, professional workmanship, and dependable support from first contact to final check.',
		'medicare_hero_primary_url'               => '#contact',
		'medicare_process_heading'                => 'Get Your Service Done in 3 Easy Steps',
		'medicare_process_image_id'               => 0,
		'medicare_process_step_1_title'           => 'Tell Us What You Need',
		'medicare_process_step_1_body'            => 'Share the project, issue, or goal so your team can understand the right next step.',
		'medicare_process_step_2_title'           => 'Get A Clear Quote',
		'medicare_process_step_2_body'            => 'Explain the scope, options, and expected cost before work begins.',
		'medicare_process_step_3_title'           => 'Schedule Service',
		'medicare_process_step_3_body'            => 'Choose a convenient time and complete the work with confidence.',
		'medicare_why_card_1_title'               => 'Professional Service',
		'medicare_why_card_1_body'                => 'Show customers the training, care, or standards your team brings to every project.',
		'medicare_why_card_2_title'               => 'Responsive Support',
		'medicare_why_card_2_body'                => 'Explain how customers can reach you and what they can expect after contacting your team.',
		'medicare_why_card_3_title'               => 'Local & Trusted',
		'medicare_why_card_3_body'                => 'Add the service area and trust signal that matter most to your customers.',
		'medicare_services_heading'               => 'Services designed around your needs.',
		'medicare_service_card_1_title'           => 'Primary Service',
		'medicare_service_card_1_body'            => 'Describe your main service and the result customers can expect.',
		'medicare_service_card_1_image_id'        => 0,
		'medicare_service_card_2_title'           => 'Additional Service',
		'medicare_service_card_2_body'            => 'Add a second service with a short, customer-focused description.',
		'medicare_service_card_2_image_id'        => 0,
		'medicare_service_card_3_title'           => 'Specialized Service',
		'medicare_service_card_3_body'            => 'Highlight a specialized solution your team provides.',
		'medicare_service_card_3_image_id'        => 0,
		'medicare_service_card_4_title'           => 'Installation or Repair',
		'medicare_service_card_4_body'            => 'Explain the installation, repair, or improvement you offer.',
		'medicare_service_card_4_image_id'        => 0,
		'medicare_service_card_5_title'           => 'Ongoing Support',
		'medicare_service_card_5_body'            => 'Describe maintenance, follow-up, or recurring support.',
		'medicare_service_card_5_image_id'        => 0,
		'medicare_service_card_6_title'           => 'Premium Service',
		'medicare_service_card_6_body'            => 'Use this card for a premium or high-priority service.',
		'medicare_service_card_6_image_id'        => 0,
		'medicare_commercial_services_show'       => false,
		'medicare_commercial_services_heading'    => 'Commercial Services',
		'medicare_specialty_services_heading'     => 'Specialty Services',
		'medicare_specialty_services_eyebrow'     => '',
		'medicare_specialty_services_intro'       => '',
		'medicare_specialty_service_card_1_title' => 'Specialty Service One',
		'medicare_specialty_service_card_1_body'  => 'Describe a focused solution for a specific customer need.',
		'medicare_specialty_service_card_2_title' => 'Specialty Service Two',
		'medicare_specialty_service_card_2_body'  => 'Add another specialized service and its practical benefit.',
		'medicare_specialty_service_card_3_title' => 'Specialty Service Three',
		'medicare_specialty_service_card_3_body'  => 'Explain how this service helps the people you serve.',
		'medicare_specialty_service_card_4_title' => 'Specialty Service Four',
		'medicare_specialty_service_card_4_body'  => 'Use this space for another focused offering.',
		'medicare_specialty_service_card_5_title' => 'Specialty Service Five',
		'medicare_specialty_service_card_5_body'  => 'Describe the scope and outcome of this service.',
		'medicare_specialty_service_card_6_title' => 'Specialty Service Six',
		'medicare_specialty_service_card_6_body'  => 'Add the final specialty service in this section.',
		'medicare_locksmith_faq_show'              => false,
		'medicare_locksmith_faq_eyebrow'          => 'Service Support',
		'medicare_locksmith_faq_image_id'         => 0,
		'medicare_locksmith_process_info_heading' => 'How our service process works',
		'medicare_locksmith_process_info_intro'   => 'Explain what happens from the first conversation through the final check.',
		'medicare_locksmith_pricing_show'         => false,
		'medicare_locksmith_testimonials_show'    => true,
		'medicare_locksmith_testimonials_widget_id' => '',
		'medicare_expectations_show'              => false,
		'medicare_expectations_heading'           => 'What to expect when you work with us',
		'medicare_booking_faq_items'              => array(
			array( 'question' => 'How does your service work?', 'answer' => 'Share the steps customers can expect from the first conversation to completion.' ),
			array( 'question' => 'What areas do you serve?', 'answer' => 'Add your service area and any travel or scheduling details here.' ),
			array( 'question' => 'How quickly can I schedule?', 'answer' => 'Explain your usual availability and how customers can request a time.' ),
		),
		'medicare_about_heading'                   => 'Experience you can count on',
		'medicare_about_paragraph_1'               => 'Tell visitors who you help, what you do, and why your team is a dependable choice.',
		'medicare_about_paragraph_2'               => 'Use this space to explain your service area, process, and the practical results customers can expect.',
		'medicare_about_paragraph_3'               => 'Add your experience, values, guarantees, and the details that make your business different.',
		'medicare_about_page_hero_image_id'        => 0,
		'medicare_about_page_hero_title'           => 'About our business',
		'medicare_about_page_hero_intro'           => 'Share your story, values, and the reason customers choose your team.',
		'medicare_about_page_mission_body'         => 'Explain the standard of service, care, and communication your business promises to every customer.',
		'medicare_about_page_vision_body'          => 'Describe the future you are building and the experience you want customers to have.',
		'medicare_contact_page_heading'            => 'Ready to get help from our team?',
		'medicare_contact_page_intro'              => 'Tell us what you need, where you are located, and how we can help. We will respond with clear next steps.',
		'medicare_contact_page_address'            => '',
		'medicare_contact_page_map_embed_url'     => '',
		'medicare_contact_page_cf7_shortcode'      => '',
	);

	foreach ( $starter_values as $setting => $value ) {
		set_theme_mod( $setting, $value );
	}

	update_option( 'medicare_leads_hub_profile_mode', 'starter', false );
	update_option( $migration_key, '1.2', false );
}
add_action( 'after_setup_theme', 'medicare_leads_hub_migrate_locksmith_content', 20 );

/**
 * Keep the specialty-services section aligned with the approved reference
 * layout while preserving editable card copy in the Customizer.
 */
function medicare_leads_hub_migrate_specialty_layout() {
	$migration_key = 'medicare_leads_hub_specialty_layout_migration';
	if ( '1.0' === get_option( $migration_key ) ) {
		return;
	}

	if ( medicare_leads_hub_is_portable_starter() ) {
		set_theme_mod( 'medicare_specialty_services_eyebrow', '' );
		set_theme_mod( 'medicare_specialty_services_heading', 'Specialty Services' );
		set_theme_mod( 'medicare_specialty_services_intro', '' );
	}
	update_option( $migration_key, '1.0', false );
}
add_action( 'after_setup_theme', 'medicare_leads_hub_migrate_specialty_layout', 21 );

/**
 * Restore the testimonials section after it was hidden by the earlier
 * starter migration. The section remains hideable from the Customizer.
 */
function medicare_leads_hub_migrate_testimonials_visibility() {
	$migration_key = 'medicare_leads_hub_testimonials_visibility_migration';
	if ( '1.0' === get_option( $migration_key ) ) {
		return;
	}

	set_theme_mod( 'medicare_locksmith_testimonials_show', true );
	update_option( $migration_key, '1.0', false );
}
add_action( 'after_setup_theme', 'medicare_leads_hub_migrate_testimonials_visibility', 22 );

/**
 * Create the editable WordPress content structure for the locksmith homepage.
 *
 * This is intentionally a one-time setup migration: editors can continue to
 * manage the Page, menus, Reading setting, and Media Library from wp-admin.
 */
function medicare_leads_hub_import_theme_image( $filename, $title ) {
	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'meta_query'     => array(
				array(
					'key'     => '_wp_attached_file',
					'value'   => $filename,
					'compare' => 'LIKE',
				),
			),
		)
	);

	if ( $existing ) {
		$existing_file = get_attached_file( $existing[0]->ID );
		if ( $existing_file && file_exists( $existing_file ) ) {
			return absint( $existing[0]->ID );
		}
	}

	$source = get_template_directory() . '/assets/images/' . $filename;
	if ( ! file_exists( $source ) ) {
		return 0;
	}

	$upload = wp_upload_bits( $filename, null, file_get_contents( $source ) );
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => wp_check_filetype( $filename )['type'] ? wp_check_filetype( $filename )['type'] : 'image/png',
			'post_title'     => sanitize_text_field( $title ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	if ( is_wp_error( $attachment_id ) ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	$metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
	wp_update_attachment_metadata( $attachment_id, $metadata );

	return absint( $attachment_id );
}

/**
 * Restore bundled locksmith images when a migration kept the attachment
 * record but lost its physical upload file.
 */
function medicare_leads_hub_repair_bundled_locksmith_images() {
	if ( medicare_leads_hub_is_portable_starter() ) {
		return;
	}

	$bundled_images = array(
		'medicare_process_image_id'       => array( 'locksmith-support-technician-transparent.webp', 'Locksmith Support Technician Transparent' ),
		'medicare_locksmith_faq_image_id' => array( 'locksmith-technician-hardhat-transparent.webp', 'Locksmith Technician Hardhat Transparent' ),
	);

	foreach ( $bundled_images as $setting => $image ) {
		$current_id   = absint( get_theme_mod( $setting, 0 ) );
		$current_file = $current_id ? get_attached_file( $current_id ) : '';

		if ( $current_id && $current_file && file_exists( $current_file ) ) {
			continue;
		}

		$replacement_id = medicare_leads_hub_import_theme_image( $image[0], $image[1] );
		if ( $replacement_id ) {
			set_theme_mod( $setting, $replacement_id );
		}
	}
}
add_action( 'init', 'medicare_leads_hub_repair_bundled_locksmith_images', 18 );

/**
 * Find an attachment by its uploads-relative file path.
 *
 * Some migration tools copy the physical upload but lose the attachment
 * metadata. In that case the attachment GUID is still useful as a fallback.
 *
 * @param string $relative_file Uploads-relative path.
 * @return int Attachment ID or 0 when not found.
 */
function medicare_leads_hub_attachment_id_for_file( $relative_file ) {
	$relative_file = ltrim( wp_normalize_path( (string) $relative_file ), '/' );

	if ( '' === $relative_file ) {
		return 0;
	}

	$attachments = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_query'     => array(
				array(
					'key'     => '_wp_attached_file',
					'value'   => $relative_file,
					'compare' => '=',
				),
			),
		)
	);

	if ( $attachments ) {
		return absint( $attachments[0] );
	}

	$uploads = wp_upload_dir();
	$guid    = trailingslashit( $uploads['baseurl'] ) . $relative_file;
	global $wpdb;
	$attachment_id = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type = 'attachment' AND guid = %s LIMIT 1",
			$guid
		)
	);

	return absint( $attachment_id );
}

/**
 * Make an attachment record usable after a migration copied only its file.
 *
 * @param int    $attachment_id Attachment ID.
 * @param string $relative_file Uploads-relative path.
 * @param string $absolute_file Absolute filesystem path.
 * @return int Attachment ID.
 */
function medicare_leads_hub_repair_attachment( $attachment_id, $relative_file, $absolute_file ) {
	$attachment_id = absint( $attachment_id );
	$relative_file  = ltrim( wp_normalize_path( (string) $relative_file ), '/' );

	if ( ! $attachment_id || ! file_exists( $absolute_file ) || '' === $relative_file ) {
		return 0;
	}

	if ( $relative_file !== get_post_meta( $attachment_id, '_wp_attached_file', true ) ) {
		update_post_meta( $attachment_id, '_wp_attached_file', $relative_file );
	}

	$filetype = wp_check_filetype( $relative_file );
	if ( ! empty( $filetype['type'] ) ) {
		$current_type = get_post_field( 'post_mime_type', $attachment_id );
		if ( $current_type !== $filetype['type'] ) {
			wp_update_post(
				array(
					'ID'             => $attachment_id,
					'post_mime_type' => $filetype['type'],
				)
			);
		}
	}

	$extension = strtolower( (string) pathinfo( $absolute_file, PATHINFO_EXTENSION ) );
	if ( ! in_array( $extension, array( 'svg', 'gif' ), true ) && ! get_post_meta( $attachment_id, '_wp_attachment_metadata', true ) ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$metadata = wp_generate_attachment_metadata( $attachment_id, $absolute_file );
		if ( ! empty( $metadata ) && ! is_wp_error( $metadata ) ) {
			wp_update_attachment_metadata( $attachment_id, $metadata );
		}
	}

	return $attachment_id;
}

/**
 * Register uploads that exist on disk but have no usable Media Library record.
 *
 * This is intentionally a versioned one-time repair. It makes migrations
 * portable without touching editor content or deleting existing media.
 */
function medicare_leads_hub_reconcile_media_library() {
	$repair_key = 'medicare_leads_hub_media_reconciliation_version';
	if ( '1.1' === get_option( $repair_key ) ) {
		return;
	}

	$uploads = wp_upload_dir();
	$base_dir = isset( $uploads['basedir'] ) ? $uploads['basedir'] : '';
	if ( ! $base_dir || ! is_dir( $base_dir ) ) {
		return;
	}

	$allowed_extensions = array( 'avif', 'gif', 'jpeg', 'jpg', 'png', 'svg', 'webp' );
	$iterator            = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $base_dir, FilesystemIterator::SKIP_DOTS )
	);

	foreach ( $iterator as $file_info ) {
		if ( ! $file_info->isFile() ) {
			continue;
		}

		$absolute_file = wp_normalize_path( $file_info->getPathname() );
		$extension     = strtolower( (string) pathinfo( $absolute_file, PATHINFO_EXTENSION ) );
		if ( ! in_array( $extension, $allowed_extensions, true ) ) {
			continue;
		}

		// WordPress-generated sub sizes are represented by their parent image.
		if ( preg_match( '/-\d+x\d+(?=\.[^.]+$)/i', $absolute_file ) ) {
			continue;
		}

		$relative_file = ltrim( str_replace( trailingslashit( $base_dir ), '', $absolute_file ), '/' );
		$attachment_id = medicare_leads_hub_attachment_id_for_file( $relative_file );

		if ( $attachment_id ) {
			medicare_leads_hub_repair_attachment( $attachment_id, $relative_file, $absolute_file );
			continue;
		}

		$filetype = wp_check_filetype( $relative_file );
		$attachment_id = wp_insert_attachment(
			array(
				'post_mime_type' => ! empty( $filetype['type'] ) ? $filetype['type'] : 'application/octet-stream',
				'post_title'     => sanitize_text_field( pathinfo( $relative_file, PATHINFO_FILENAME ) ),
				'post_status'    => 'inherit',
				'guid'           => trailingslashit( $uploads['baseurl'] ) . $relative_file,
			),
			$absolute_file
		);

		if ( ! is_wp_error( $attachment_id ) && $attachment_id ) {
			update_attached_file( $attachment_id, $absolute_file );
			medicare_leads_hub_repair_attachment( $attachment_id, $relative_file, $absolute_file );
		}
	}

	medicare_leads_hub_sync_media_theme_mods();
	update_option( $repair_key, '1.1', false );
}

/**
 * Resolve a migrated image setting to a usable attachment ID.
 *
 * @param int|string $current_id Current Customizer attachment ID.
 * @param string[]  $fallbacks  Uploads-relative candidate paths.
 * @return int Attachment ID or 0.
 */
function medicare_leads_hub_resolve_image_id( $current_id, $fallbacks = array() ) {
	$current_id = absint( $current_id );
	if ( $current_id && wp_get_attachment_image_url( $current_id, 'full' ) ) {
		return medicare_leads_hub_prefer_webp_attachment_id( $current_id );
	}

	foreach ( (array) $fallbacks as $fallback ) {
		$attachment_id = medicare_leads_hub_attachment_id_for_file( $fallback );
		if ( $attachment_id && wp_get_attachment_image_url( $attachment_id, 'full' ) ) {
			return medicare_leads_hub_prefer_webp_attachment_id( $attachment_id );
		}
	}

	return 0;
}

/**
 * Resolve a raster attachment to its WebP sibling when that sibling exists.
 * The original attachment remains untouched for backwards compatibility.
 *
 * @param int $attachment_id Attachment ID.
 * @return int Preferred attachment ID.
 */
function medicare_leads_hub_prefer_webp_attachment_id( $attachment_id ) {
	$attachment_id = absint( $attachment_id );
	if ( ! $attachment_id ) {
		return 0;
	}

	$relative_file = (string) get_post_meta( $attachment_id, '_wp_attached_file', true );
	$webp_file     = preg_replace( '/\.(png|jpe?g)$/i', '.webp', $relative_file );
	if ( ! $webp_file || $webp_file === $relative_file ) {
		return $attachment_id;
	}

	$webp_id = medicare_leads_hub_attachment_id_for_file( $webp_file );
	return $webp_id ? $webp_id : $attachment_id;
}

/**
 * Repair theme image settings after uploads and attachment IDs change during
 * a migration. Existing valid editor-selected images are preserved.
 */
function medicare_leads_hub_sync_media_theme_mods() {
	if ( medicare_leads_hub_is_portable_starter() ) {
		return;
	}

	$current_hero_id   = absint( get_theme_mod( 'medicare_hero_image_id', 0 ) );
	$current_hero_file = $current_hero_id ? (string) get_post_meta( $current_hero_id, '_wp_attached_file', true ) : '';
	$hero_id           = medicare_leads_hub_resolve_image_id(
		$current_hero_id,
		array(
			'2026/09/ChatGPT-Image-Sep-17-2026-02_54_46-PM.webp',
			'2026/09/ChatGPT-Image-Sep-17-2026-02_54_46-PM.png',
			'2026/09/medicare-hero-background.png',
			'2026/09/medicare-hero-background.webp',
		)
	);
	$live_hero_id = medicare_leads_hub_attachment_id_for_file( '2026/09/ChatGPT-Image-Sep-17-2026-02_54_46-PM.webp' );
	if ( $live_hero_id && ( ! $current_hero_id || false !== stripos( $current_hero_file, 'medicare-hero-background' ) ) ) {
		$hero_id = $live_hero_id;
	}
	if ( $hero_id ) {
		set_theme_mod( 'medicare_hero_image_id', $hero_id );
	}

	$current_process_id   = absint( get_theme_mod( 'medicare_process_image_id', 0 ) );
	$current_process_file = $current_process_id ? (string) get_post_meta( $current_process_id, '_wp_attached_file', true ) : '';
	$process_id            = medicare_leads_hub_resolve_image_id(
		$current_process_id,
		array(
			'2026/09/locksmith-support-technician-transparent.webp',
			'2026/09/locksmith-support-technician-transparent.png',
			'2026/09/locksmith-support-technician-transparent-1.webp',
			'2026/09/locksmith-support-technician-transparent-1.png',
			'2026/09/tree-service-process-professional-transparent-1.webp',
			'2026/09/tree-service-process-professional-transparent-1.png',
		)
	);
	$live_process_id = medicare_leads_hub_attachment_id_for_file( '2026/09/locksmith-support-technician-transparent.webp' );
	if ( $live_process_id && ( ! $current_process_id || false !== stripos( $current_process_file, 'tree-service-process' ) ) ) {
		$process_id = $live_process_id;
	}
	if ( $process_id ) {
		set_theme_mod( 'medicare_process_image_id', $process_id );
	}

	$faq_id = medicare_leads_hub_resolve_image_id(
		get_theme_mod( 'medicare_locksmith_faq_image_id', 0 ),
		array(
			'2026/09/locksmith-technician-hardhat-transparent.webp',
			'2026/09/locksmith-technician-hardhat-transparent-1.webp',
			'2026/09/locksmith-technician-hardhat-transparent.png',
			'2026/09/locksmith-technician-hardhat-transparent-1.png',
		)
	);
	if ( $faq_id ) {
		set_theme_mod( 'medicare_locksmith_faq_image_id', $faq_id );
	}

	$service_fallbacks = array(
		1 => array( '2026/09/commercial-high-security-entry.png', '2026/09/commercial-high-security-entry.webp' ),
		2 => array( '2026/09/commercial-rekeying.png', '2026/09/commercial-rekeying.webp' ),
		3 => array( '2026/09/commercial-business-entry.png', '2026/09/commercial-business-entry.webp' ),
		4 => array( '2026/09/commercial-access-control.png', '2026/09/commercial-access-control.webp' ),
		5 => array( '2026/09/commercial-safe-services.png', '2026/09/commercial-safe-services.webp' ),
		6 => array( '2026/09/commercial-high-security-entry.png', '2026/09/commercial-high-security-entry.webp' ),
	);

	foreach ( $service_fallbacks as $index => $fallbacks ) {
		$setting = 'medicare_service_card_' . $index . '_image_id';
		$id      = medicare_leads_hub_resolve_image_id( get_theme_mod( $setting, 0 ), $fallbacks );
		if ( $id ) {
			set_theme_mod( $setting, $id );
		}
	}

	$commercial_fallbacks = array(
		1 => array( '2026/09/commercial-master-key-systems.png', '2026/09/commercial-master-key-systems.webp' ),
		2 => array( '2026/09/commercial-high-security-entry.png', '2026/09/commercial-high-security-entry.webp' ),
		3 => array( '2026/09/commercial-rekeying.png', '2026/09/commercial-rekeying.webp' ),
		4 => array( '2026/09/commercial-business-entry.png', '2026/09/commercial-business-entry.webp' ),
		5 => array( '2026/09/commercial-access-control.png', '2026/09/commercial-access-control.webp' ),
		6 => array( '2026/09/commercial-safe-services.png', '2026/09/commercial-safe-services.webp' ),
	);

	foreach ( $commercial_fallbacks as $index => $fallbacks ) {
		$setting = 'medicare_commercial_service_card_' . $index . '_image_id';
		$id      = medicare_leads_hub_resolve_image_id( get_theme_mod( $setting, 0 ), $fallbacks );
		if ( $id ) {
			set_theme_mod( $setting, $id );
		}
	}

	// If a migrated site has no usable logo, or still points at the old
	// tree-service logo, provide the bundled locksmith logo as a fallback. Do
	// not replace a valid logo selected from the Customizer just because it is a
	// PNG/JPEG or uses a different filename.
	$current_logo = absint( get_theme_mod( 'custom_logo', 0 ) );
	$current_file = $current_logo ? (string) get_post_meta( $current_logo, '_wp_attached_file', true ) : '';
	$logo_id      = medicare_leads_hub_attachment_id_for_file( '2026/09/cropped-Locksmith-logo-.webp' );
	if ( ! $logo_id ) {
		$logo_id = medicare_leads_hub_attachment_id_for_file( '2026/09/cropped-Locksmith-logo-.png' );
	}
	$logo_id = medicare_leads_hub_prefer_webp_attachment_id( $logo_id );
	if ( $logo_id && ( ! $current_logo || '' === $current_file || false !== stripos( $current_file, 'tree-service' ) ) ) {
		set_theme_mod( 'custom_logo', $logo_id );
	}
}

add_action( 'init', 'medicare_leads_hub_reconcile_media_library', 19 );

/**
 * Apply the current live-site image references without rescanning uploads.
 *
 * The lightweight versioned pass lets a migrated database adopt newer image
 * selections even when an older reconciliation flag came across with it.
 */
function medicare_leads_hub_sync_current_image_references() {
	$sync_key = 'medicare_leads_hub_image_reference_sync_version';
	if ( '1.2' === get_option( $sync_key ) ) {
		return;
	}

	medicare_leads_hub_sync_media_theme_mods();
	update_option( $sync_key, '1.2', false );
}
add_action( 'init', 'medicare_leads_hub_sync_current_image_references', 20 );

/**
 * Return the optimized WebP sibling for a local upload when it exists.
 *
 * Attachment IDs and Media Library settings stay unchanged; only the
 * front-end image URL is swapped when an equivalent WebP file is available.
 *
 * @param string $url Original upload URL.
 * @return string Optimized URL or the original URL.
 */
function medicare_leads_hub_preferred_image_url( $url ) {
	if ( ! is_string( $url ) || '' === $url ) {
		return $url;
	}

	$uploads  = wp_upload_dir();
	$base_url = untrailingslashit( $uploads['baseurl'] );
	$base_path = wp_parse_url( $base_url, PHP_URL_PATH );
	$url_parts = wp_parse_url( $url );
	$url_path  = isset( $url_parts['path'] ) ? $url_parts['path'] : '';

	if ( ! $base_path || ! $url_path || 0 !== strpos( $url_path, trailingslashit( $base_path ) ) ) {
		return $url;
	}

	$relative_path = ltrim( substr( $url_path, strlen( trailingslashit( $base_path ) ) ), '/' );
	$webp_relative_path = preg_replace( '/\.(png|jpe?g)$/i', '.webp', $relative_path );

	if ( $webp_relative_path === $relative_path || ! $webp_relative_path ) {
		return $url;
	}

	$webp_path = trailingslashit( $uploads['basedir'] ) . str_replace( '/', DIRECTORY_SEPARATOR, $webp_relative_path );
	if ( ! file_exists( $webp_path ) ) {
		return $url;
	}

	$webp_url = trailingslashit( $uploads['baseurl'] ) . $webp_relative_path;
	if ( isset( $url_parts['query'] ) && '' !== $url_parts['query'] ) {
		$webp_url .= '?' . $url_parts['query'];
	}

	return $webp_url;
}

/**
 * Add a file modification version to local upload URLs so replaced media is
 * not hidden by a browser or page-cache entry from the previous deployment.
 *
 * @param string $url Local or remote image URL.
 * @return string Versioned local URL or the original URL.
 */
function medicare_leads_hub_version_local_image_url( $url ) {
	if ( ! is_string( $url ) || '' === $url || false !== stripos( $url, 'ver=' ) ) {
		return $url;
	}

	$uploads   = wp_upload_dir();
	$base_url  = untrailingslashit( $uploads['baseurl'] );
	$base_path = wp_parse_url( $base_url, PHP_URL_PATH );
	$parts     = wp_parse_url( $url );
	$url_path  = isset( $parts['path'] ) ? $parts['path'] : '';

	if ( ! $base_path || ! $url_path || 0 !== strpos( $url_path, trailingslashit( $base_path ) ) ) {
		return $url;
	}

	$relative_file = ltrim( substr( $url_path, strlen( trailingslashit( $base_path ) ) ), '/' );
	$file_path     = trailingslashit( $uploads['basedir'] ) . str_replace( '/', DIRECTORY_SEPARATOR, $relative_file );
	if ( ! file_exists( $file_path ) ) {
		return $url;
	}

	return $url . ( false === strpos( $url, '?' ) ? '?' : '&' ) . 'ver=' . absint( filemtime( $file_path ) );
}

/**
 * Prefer WebP for front-end attachment image markup, including srcset URLs.
 *
 * @param string       $html          Image markup.
 * @param int          $attachment_id Attachment ID.
 * @param string|int[] $size          Requested image size.
 * @param bool         $icon          Whether the image is being used as an icon.
 * @param array        $attr          Image attributes.
 * @return string Updated image markup.
 */
function medicare_leads_hub_prefer_webp_image_markup( $html, $attachment_id, $size, $icon, $attr ) {
	if ( is_admin() || ! $html ) {
		return $html;
	}

	return preg_replace_callback(
		'/https?:\/\/[^"\'\s]+\.(?:png|jpe?g|webp)(?:\?[^"\'\s]*)?/i',
		function ( $matches ) {
			return medicare_leads_hub_version_local_image_url( medicare_leads_hub_preferred_image_url( $matches[0] ) );
		},
		$html
	);
}
add_filter( 'wp_get_attachment_image', 'medicare_leads_hub_prefer_webp_image_markup', 10, 5 );

function medicare_leads_hub_setup_wordpress_content() {
	$setup_key = 'medicare_leads_hub_wp_setup_version';
	if ( in_array( get_option( $setup_key ), array( '1.0', '1.1' ), true ) ) {
		return;
	}

	$home_page = get_page_by_path( 'home', OBJECT, 'page' );
	$home_id   = $home_page ? absint( $home_page->ID ) : wp_insert_post(
		array(
			'post_title'   => __( 'Home', 'medicare-leads-hub' ),
			'post_name'    => 'home',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => '',
		),
		true
	);

	if ( ! is_wp_error( $home_id ) && $home_id ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', absint( $home_id ) );
	}

	$menu_definitions = array(
		'primary' => array(
			'name'  => 'Primary Navigation',
			'items' => array(
				__( 'Home', 'medicare-leads-hub' )     => home_url( '/' ),
				__( 'Services', 'medicare-leads-hub' ) => home_url( '/#services-grid' ),
				__( 'About', 'medicare-leads-hub' )    => home_url( '/#about-experience' ),
				__( 'Contact', 'medicare-leads-hub' )  => home_url( '/#contact' ),
			),
		),
		'footer' => array(
			'name'  => 'Footer Navigation',
			'items' => array(
				__( 'Home', 'medicare-leads-hub' )     => home_url( '/' ),
				__( 'Services', 'medicare-leads-hub' ) => home_url( '/#services-grid' ),
				__( 'About', 'medicare-leads-hub' )    => home_url( '/#about-experience' ),
				__( 'Contact', 'medicare-leads-hub' )  => home_url( '/#contact' ),
			),
		),
	);

	$locations = get_theme_mod( 'nav_menu_locations', array() );
	foreach ( $menu_definitions as $location => $definition ) {
		$menu = wp_get_nav_menu_object( $definition['name'] );
		$menu_id = $menu ? absint( $menu->term_id ) : wp_create_nav_menu( $definition['name'] );

		if ( is_wp_error( $menu_id ) || ! $menu_id ) {
			continue;
		}

		if ( ! wp_get_nav_menu_items( $menu_id ) ) {
			foreach ( $definition['items'] as $label => $url ) {
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'  => $label,
						'menu-item-url'    => $url,
						'menu-item-status' => 'publish',
					)
				);
			}
		}

		$locations[ $location ] = absint( $menu_id );
	}

	set_theme_mod( 'nav_menu_locations', $locations );
	update_option( $setup_key, '1.1', false );
}
add_action( 'init', 'medicare_leads_hub_setup_wordpress_content', 20 );

/**
 * Create and assign the standalone About Us page template once.
 */
function medicare_leads_hub_claim_setup_lock( $setup_key ) {
	if ( '1.0' === get_option( $setup_key ) ) {
		return false;
	}

	$lock_key = $setup_key . '_lock';
	if ( add_option( $lock_key, time(), '', false ) ) {
		return true;
	}

	$lock_time = absint( get_option( $lock_key ) );
	if ( $lock_time && ( time() - $lock_time ) > 300 ) {
		update_option( $lock_key, time(), false );
		return true;
	}

	return false;
}

function medicare_leads_hub_release_setup_lock( $setup_key ) {
	delete_option( $setup_key . '_lock' );
}

function medicare_leads_hub_setup_about_page() {
	$setup_key = 'medicare_leads_hub_about_page_setup_version';
	if ( ! medicare_leads_hub_claim_setup_lock( $setup_key ) ) {
		return;
	}

	$about_page = get_page_by_path( 'about-us', OBJECT, 'page' );
	$about_id   = $about_page ? absint( $about_page->ID ) : wp_insert_post(
		array(
			'post_title'   => __( 'About Us', 'medicare-leads-hub' ),
			'post_name'    => 'about-us',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => '',
		),
		true
	);

	if ( ! is_wp_error( $about_id ) && $about_id ) {
		update_post_meta( $about_id, '_wp_page_template', 'page-about-us.php' );

		$locations = get_theme_mod( 'nav_menu_locations', array() );
		foreach ( array( 'primary', 'footer' ) as $location ) {
			if ( empty( $locations[ $location ] ) ) {
				continue;
			}

			$items = wp_get_nav_menu_items( absint( $locations[ $location ] ) );
			foreach ( (array) $items as $item ) {
				if ( ! in_array( strtolower( trim( (string) $item->title ) ), array( 'about', 'about us' ), true ) ) {
					continue;
				}

				wp_update_nav_menu_item(
					absint( $locations[ $location ] ),
					absint( $item->ID ),
					array(
						'menu-item-title'  => $item->title,
						'menu-item-url'    => get_permalink( $about_id ),
						'menu-item-status' => 'publish',
					)
				);
			}
		}
	}

	update_option( $setup_key, '1.0', false );
	medicare_leads_hub_release_setup_lock( $setup_key );
}
add_action( 'init', 'medicare_leads_hub_setup_about_page', 23 );

function medicare_leads_hub_setup_about_page_hero_image() {
	$setup_key = 'medicare_leads_hub_about_page_hero_image_setup_version';
	if ( '1.0' === get_option( $setup_key ) || medicare_leads_hub_is_portable_starter() ) {
		if ( medicare_leads_hub_is_portable_starter() ) {
			update_option( $setup_key, '1.1', false );
		}
		return;
	}

	$hero_image_id = medicare_leads_hub_import_theme_image( 'about-locksmith-hero.webp', 'About Locksmith Hero' );
	if ( $hero_image_id && ! get_theme_mod( 'medicare_about_page_hero_image_id', 0 ) ) {
		set_theme_mod( 'medicare_about_page_hero_image_id', $hero_image_id );
	}

	update_option( $setup_key, '1.0', false );
}
add_action( 'init', 'medicare_leads_hub_setup_about_page_hero_image', 24 );

function medicare_leads_hub_setup_contact_page() {
	$setup_key = 'medicare_leads_hub_contact_page_setup_version';
	if ( ! medicare_leads_hub_claim_setup_lock( $setup_key ) ) {
		return;
	}

	$contact_page = get_page_by_path( 'contact-us', OBJECT, 'page' );
	$contact_id   = $contact_page ? absint( $contact_page->ID ) : wp_insert_post(
		array(
			'post_title'   => __( 'Contact Us', 'medicare-leads-hub' ),
			'post_name'    => 'contact-us',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => '',
		),
		true
	);

	if ( ! is_wp_error( $contact_id ) && $contact_id ) {
		update_post_meta( $contact_id, '_wp_page_template', 'page-contact-us.php' );

		$locations = get_theme_mod( 'nav_menu_locations', array() );
		foreach ( array( 'primary', 'footer' ) as $location ) {
			if ( empty( $locations[ $location ] ) ) {
				continue;
			}

			$items = wp_get_nav_menu_items( absint( $locations[ $location ] ) );
			foreach ( (array) $items as $item ) {
				if ( 'contact' !== strtolower( trim( (string) $item->title ) ) ) {
					continue;
				}

				wp_update_nav_menu_item(
					absint( $locations[ $location ] ),
					absint( $item->ID ),
					array(
						'menu-item-title'  => $item->title,
						'menu-item-url'    => get_permalink( $contact_id ),
						'menu-item-status' => 'publish',
					)
				);
			}
		}
	}

	update_option( $setup_key, '1.0', false );
	medicare_leads_hub_release_setup_lock( $setup_key );
}
add_action( 'init', 'medicare_leads_hub_setup_contact_page', 25 );

function medicare_leads_hub_setup_transparent_process_image() {
	$setup_key = 'medicare_leads_hub_transparent_process_image_setup_version';
	if ( '1.0' === get_option( $setup_key ) || medicare_leads_hub_is_portable_starter() ) {
		if ( medicare_leads_hub_is_portable_starter() ) {
			update_option( $setup_key, '1.1', false );
		}
		return;
	}

	$process_media_id = medicare_leads_hub_import_theme_image( 'locksmith-support-technician-transparent.png', 'Locksmith Support Technician Transparent' );
	$current_process_id = absint( get_theme_mod( 'medicare_process_image_id', 0 ) );
	if ( $process_media_id && ( ! $current_process_id || ! wp_get_attachment_image_url( $current_process_id, 'full' ) ) ) {
		set_theme_mod( 'medicare_process_image_id', $process_media_id );
	}

	update_option( $setup_key, '1.0', false );
}
add_action( 'init', 'medicare_leads_hub_setup_transparent_process_image', 21 );

function medicare_leads_hub_setup_locksmith_process_info_image() {
	$setup_key = 'medicare_leads_hub_locksmith_process_info_image_setup_version';
	if ( '1.0' === get_option( $setup_key ) || medicare_leads_hub_is_portable_starter() ) {
		if ( medicare_leads_hub_is_portable_starter() ) {
			update_option( $setup_key, '1.1', false );
		}
		return;
	}

	$process_info_media_id = medicare_leads_hub_import_theme_image( 'locksmith-technician-hardhat-transparent.png', 'Locksmith Technician Hardhat Transparent' );
	$current_process_info_id = absint( get_theme_mod( 'medicare_locksmith_faq_image_id', 0 ) );
	if ( $process_info_media_id && ( ! $current_process_info_id || ! wp_get_attachment_image_url( $current_process_info_id, 'full' ) ) ) {
		set_theme_mod( 'medicare_locksmith_faq_image_id', $process_info_media_id );
	}

	update_option( $setup_key, '1.0', false );
}
add_action( 'init', 'medicare_leads_hub_setup_locksmith_process_info_image', 22 );

/**
 * Register the generated What To Expect background and select it by default.
 *
 * The file lives in uploads so it remains portable through WordPress migration
 * tools, while this small versioned pass repairs installs where the file was
 * copied without a corresponding Media Library record.
 */
function medicare_leads_hub_setup_expectations_background() {
	if ( medicare_leads_hub_is_portable_starter() ) {
		return;
	}

	$relative_file = '2026/09/locksmith-expectations-background.webp';
	$attachment_id = medicare_leads_hub_attachment_id_for_file( $relative_file );
	$uploads       = wp_upload_dir();
	$absolute_file = trailingslashit( $uploads['basedir'] ) . $relative_file;

	if ( ! $attachment_id && file_exists( $absolute_file ) ) {
		$filetype      = wp_check_filetype( $relative_file );
		$attachment_id = wp_insert_attachment(
			array(
				'post_mime_type' => ! empty( $filetype['type'] ) ? $filetype['type'] : 'image/webp',
				'post_title'     => 'Locksmith What To Expect Background',
				'post_status'    => 'inherit',
				'guid'           => trailingslashit( $uploads['baseurl'] ) . $relative_file,
			),
			$absolute_file
		);
		if ( ! is_wp_error( $attachment_id ) && $attachment_id ) {
			update_attached_file( $attachment_id, $absolute_file );
			require_once ABSPATH . 'wp-admin/includes/image.php';
			$metadata = wp_generate_attachment_metadata( $attachment_id, $absolute_file );
			if ( ! empty( $metadata ) && ! is_wp_error( $metadata ) ) {
				wp_update_attachment_metadata( $attachment_id, $metadata );
			}
		}
	}

	$current_id = absint( get_theme_mod( 'medicare_expectations_background_image_id', 0 ) );
	if ( $attachment_id && ( ! $current_id || ! wp_get_attachment_image_url( $current_id, 'full' ) ) ) {
		set_theme_mod( 'medicare_expectations_background_image_id', absint( $attachment_id ) );
	}
}
add_action( 'init', 'medicare_leads_hub_setup_expectations_background', 23 );

// Run once more after the legacy content/image setup hooks above. This keeps
// a migrated site's current live image selections from being overwritten by
// first-run defaults during the same request.
add_action( 'init', 'medicare_leads_hub_sync_media_theme_mods', 30 );

/**
 * Keep WordPress page shortlinks valid when a page ID is not addressable via
 * the post-style `?p=` query in the local SQLite runtime.
 *
 * @param string $shortlink  The generated shortlink.
 * @param int    $id         The post/page ID.
 * @param string $context    The request context.
 * @param bool   $allow_slugs Whether slug-based links are allowed.
 * @return string
 */
function medicare_leads_hub_valid_page_shortlink( $shortlink, $id, $context, $allow_slugs ) {
	if ( ! $id && 'query' === $context && is_singular() ) {
		$id = get_queried_object_id();
	}

	if ( $id && 'page' === get_post_type( $id ) ) {
		$permalink = get_permalink( $id );

		if ( $permalink ) {
			return $permalink;
		}
	}

	return $shortlink;
}
add_filter( 'pre_get_shortlink', 'medicare_leads_hub_valid_page_shortlink', 10, 4 );

/**
 * Redirect legacy post-style page shortlinks to their canonical permalink.
 *
 * @return void
 */
function medicare_leads_hub_redirect_legacy_page_shortlinks() {
	if ( ! is_404() || empty( $_GET['p'] ) ) {
		return;
	}

	$page_id = absint( wp_unslash( $_GET['p'] ) );
	$page    = $page_id ? get_post( $page_id ) : null;

	if ( $page && 'page' === $page->post_type && 'publish' === $page->post_status ) {
		$permalink = get_permalink( $page_id );

		if ( $permalink ) {
			wp_safe_redirect( $permalink, 301 );
			exit;
		}
	}
}
add_action( 'template_redirect', 'medicare_leads_hub_redirect_legacy_page_shortlinks', 1 );

/**
 * Prevent a static front page from being indexable at both / and /home/ (or
 * another imported page slug) after a migration.
 *
 * @return void
 */
function medicare_leads_hub_redirect_front_page_alias() {
	$front_page_id = absint( get_option( 'page_on_front', 0 ) );
	if ( ! $front_page_id || ! is_page() || $front_page_id !== absint( get_queried_object_id() ) ) {
		return;
	}

	$request_path = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ) : '';
	$home_path    = wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	if ( ! $request_path || untrailingslashit( $request_path ) === untrailingslashit( $home_path ) ) {
		return;
	}

	wp_safe_redirect( home_url( '/' ), 301 );
	exit;
}
add_action( 'template_redirect', 'medicare_leads_hub_redirect_front_page_alias', 2 );

function medicare_leads_hub_custom_css() {
	$primary       = get_theme_mod( 'medicare_primary_color', '#0B3B66' );
	$primary_dark  = get_theme_mod( 'medicare_primary_dark_color', '#062A4A' );
	$gold          = get_theme_mod( 'medicare_gold_color', '#F5B400' );
	$gold_dark     = get_theme_mod( 'medicare_gold_dark_color', '#C98900' );
	$accent        = get_theme_mod( 'medicare_accent_color', '#F3F7FA' );
	$text          = get_theme_mod( 'medicare_text_color', '#344B61' );
	$heading       = get_theme_mod( 'medicare_heading_color', '#062A4A' );
	$header_bg     = get_theme_mod( 'medicare_header_bg', '#ffffff' );
	$header_text   = get_theme_mod( 'medicare_header_text_color', '#062A4A' );
	$body_font     = medicare_leads_hub_font_stack( get_theme_mod( 'medicare_body_font', 'plus-jakarta' ) );
	$heading_font  = medicare_leads_hub_font_stack( get_theme_mod( 'medicare_heading_font', 'plus-jakarta' ) );
	$body_size     = medicare_leads_hub_sanitize_range( get_theme_mod( 'medicare_body_font_size', 16 ), 14, 22, 16 );
	$heading_size  = medicare_leads_hub_sanitize_range( get_theme_mod( 'medicare_heading_font_size', 42 ), 30, 64, 42 );
	$radius        = medicare_leads_hub_sanitize_range( get_theme_mod( 'medicare_radius', 12 ), 0, 28, 12 );
	$content_width = medicare_leads_hub_sanitize_range( get_theme_mod( 'medicare_content_width', 1280 ), 960, 1440, 1280 );
	$logo_width    = medicare_leads_hub_sanitize_range( get_theme_mod( 'medicare_logo_width', 190 ), 90, 300, 190 );
	$hero_overlay  = medicare_leads_hub_sanitize_range( get_theme_mod( 'medicare_hero_overlay', 48 ), 20, 75, 48 );
	$hero_card_width = medicare_leads_hub_sanitize_range( get_theme_mod( 'medicare_hero_card_width', 660 ), 420, 760, 660 );
	$why_background = get_theme_mod( 'medicare_why_background', '#ffffff' );
	$services_background = get_theme_mod( 'medicare_services_background', '#F3F7FA' );
	$footer_cta_background = get_theme_mod( 'medicare_footer_cta_background', '#062A4A' );
	$footer_surface        = get_theme_mod( 'medicare_footer_surface', '#ffffff' );

	return ':root{--mlh-primary:' . esc_attr( $primary ) . ';--mlh-primary-dark:' . esc_attr( $primary_dark ) . ';--mlh-gold:' . esc_attr( $gold ) . ';--mlh-gold-dark:' . esc_attr( $gold_dark ) . ';--mlh-accent:' . esc_attr( $accent ) . ';--mlh-text:' . esc_attr( $text ) . ';--mlh-heading:' . esc_attr( $heading ) . ';--mlh-header-bg:' . esc_attr( $header_bg ) . ';--mlh-header-text:' . esc_attr( $header_text ) . ';--mlh-body-font:' . $body_font . ';--mlh-heading-font:' . $heading_font . ';--mlh-body-size:' . absint( $body_size ) . 'px;--mlh-heading-size:' . absint( $heading_size ) . 'px;--mlh-radius:' . absint( $radius ) . 'px;--mlh-content-width:' . absint( $content_width ) . 'px;--mlh-logo-width:' . absint( $logo_width ) . 'px;--mlh-hero-overlay:' . ( absint( $hero_overlay ) / 100 ) . ';--mlh-hero-card-width:' . absint( $hero_card_width ) . 'px;--mlh-why-background:' . esc_attr( $why_background ) . ';--mlh-services-background:' . esc_attr( $services_background ) . ';--mlh-footer-cta-background:' . esc_attr( $footer_cta_background ) . ';--mlh-footer-surface:' . esc_attr( $footer_surface ) . ';}';
}

function medicare_leads_hub_google_fonts_url() {
	$families = array();
	$selected = array(
		get_theme_mod( 'medicare_body_font', 'plus-jakarta' ),
		get_theme_mod( 'medicare_heading_font', 'plus-jakarta' ),
	);

	foreach ( $selected as $font ) {
		if ( 'plus-jakarta' === $font ) {
			$families[ $font ] = 'Plus+Jakarta+Sans:wght@400;500;600;700;800';
		} elseif ( 'inter' === $font ) {
			$families[ $font ] = 'Inter:wght@400;500;600;700;800';
		} elseif ( 'roboto' === $font ) {
			$families[ $font ] = 'Roboto:wght@400;500;700';
		}
	}

	return $families ? 'https://fonts.googleapis.com/css2?family=' . implode( '&family=', array_values( $families ) ) . '&display=swap' : '';
}

function medicare_leads_hub_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type && medicare_leads_hub_google_fonts_url() ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'medicare_leads_hub_resource_hints', 10, 2 );

function medicare_leads_hub_enqueue_assets() {
	$version        = wp_get_theme()->get( 'Version' );
	$style_path     = get_stylesheet_directory() . '/style.css';
	$header_js_path = get_template_directory() . '/assets/js/header.js';
	$style_version  = $version . '.' . ( file_exists( $style_path ) ? filemtime( $style_path ) : 0 );
	$header_version = $version . '.' . ( file_exists( $header_js_path ) ? filemtime( $header_js_path ) : 0 );
	$font_url       = medicare_leads_hub_google_fonts_url();
	$style_deps     = array();

	if ( $font_url ) {
		wp_enqueue_style( 'medicare-leads-hub-fonts', $font_url, array(), null );
		$style_deps[] = 'medicare-leads-hub-fonts';
	}

	wp_enqueue_style( 'medicare-leads-hub-style', get_stylesheet_uri(), $style_deps, $style_version );
	wp_enqueue_script( 'medicare-leads-hub-header', get_template_directory_uri() . '/assets/js/header.js', array(), $header_version, true );
	$booking_faq_script_path = get_template_directory() . '/assets/js/booking-faq.js';
	wp_enqueue_script( 'medicare-leads-hub-booking-faq', get_template_directory_uri() . '/assets/js/booking-faq.js', array( 'medicare-leads-hub-header' ), $version . '.' . filemtime( $booking_faq_script_path ), true );

	if ( is_front_page() ) {
		$testimonials_script_path = get_template_directory() . '/assets/js/testimonials-slider.js';
		wp_enqueue_script( 'medicare-leads-hub-testimonials-slider', get_template_directory_uri() . '/assets/js/testimonials-slider.js', array( 'medicare-leads-hub-header' ), $version . '.' . filemtime( $testimonials_script_path ), true );
	}

	wp_add_inline_style( 'medicare-leads-hub-style', medicare_leads_hub_custom_css() );
}
add_action( 'wp_enqueue_scripts', 'medicare_leads_hub_enqueue_assets' );

/**
 * Clear site caches once whenever this theme version changes.
 *
 * The theme cannot purge a third-party CDN, but it can clear WordPress's
 * object cache and the common page-cache plugin APIs when those plugins are
 * present. Asset and media URLs also carry file versions independently.
 */
function medicare_leads_hub_flush_caches_on_theme_update() {
	$version = (string) wp_get_theme()->get( 'Version' );
	$key     = 'medicare_leads_hub_cache_flush_version';

	if ( $version === (string) get_option( $key, '' ) ) {
		return;
	}

	if ( function_exists( 'wp_cache_flush' ) ) {
		wp_cache_flush();
	}

	if ( function_exists( 'w3tc_flush_all' ) ) {
		w3tc_flush_all();
	}
	if ( function_exists( 'wpfc_clear_all_cache' ) ) {
		wpfc_clear_all_cache();
	}
	if ( function_exists( 'rocket_clean_domain' ) ) {
		rocket_clean_domain( home_url( '/' ) );
	}
	if ( function_exists( 'litespeed_purge_all' ) ) {
		litespeed_purge_all();
	}
	if ( class_exists( 'autoptimizeCache' ) && method_exists( 'autoptimizeCache', 'clearall' ) ) {
		autoptimizeCache::clearall();
	}

	update_option( $key, $version, false );
}
add_action( 'init', 'medicare_leads_hub_flush_caches_on_theme_update', 31 );

function medicare_leads_hub_fallback_menu() {
	$items = array(
		__( 'Home', 'medicare-leads-hub' )     => home_url( '/' ),
		__( 'Services', 'medicare-leads-hub' ) => home_url( '/#services-grid' ),
		__( 'About', 'medicare-leads-hub' )    => home_url( '/#about-experience' ),
		__( 'Contact', 'medicare-leads-hub' )  => home_url( '/#contact' ),
	);

	echo '<ul id="primary-menu" class="menu">';
	foreach ( $items as $label => $url ) {
		echo '<li class="menu-item"><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}

function medicare_leads_hub_footer_fallback_menu() {
	$items = array(
		__( 'Home', 'medicare-leads-hub' )     => home_url( '/' ),
		__( 'Services', 'medicare-leads-hub' ) => home_url( '/#services-grid' ),
		__( 'About', 'medicare-leads-hub' )    => home_url( '/#about-experience' ),
		__( 'Contact', 'medicare-leads-hub' )  => home_url( '/#contact' ),
	);
	$menu  = '<ul id="footer-menu" class="site-footer__menu">';

	foreach ( $items as $label => $url ) {
		$menu .= '<li class="menu-item"><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
	}

	return $menu . '</ul>';
}
