<?php
/**
 * Lightweight GitHub release updater for the theme.
 *
 * The repository is public and GitHub Actions publishes a theme-only ZIP for
 * every matching version tag. WordPress can then offer the update from its
 * normal theme update screen without touching site content or the database.
 *
 * @package Medicare_Leads_Hub
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'MEDICARE_LEADS_HUB_GITHUB_REPOSITORY' ) ) {
	define( 'MEDICARE_LEADS_HUB_GITHUB_REPOSITORY', 'leonislam81/medicare-leads-hub-theme' );
}

/**
 * Fetch and cache the latest valid theme release from GitHub.
 *
 * @return array<string,string>
 */
function medicare_leads_hub_github_latest_release() {
	$cache_key = 'medicare_leads_hub_github_latest_release';
	$cached    = get_site_transient( $cache_key );

	if ( is_array( $cached ) ) {
		return $cached;
	}

	$api_url = 'https://api.github.com/repos/' . MEDICARE_LEADS_HUB_GITHUB_REPOSITORY . '/releases/latest';
	$response = wp_safe_remote_get(
		$api_url,
		array(
			'timeout' => 8,
			'headers' => array(
				'Accept'     => 'application/vnd.github+json',
				'User-Agent' => 'Medicare-Leads-Hub-Theme',
			),
		)
	);

	if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
		set_site_transient( $cache_key, array(), 2 * HOUR_IN_SECONDS );
		return array();
	}

	$release = json_decode( wp_remote_retrieve_body( $response ), true );
	$tag     = is_array( $release ) && ! empty( $release['tag_name'] ) ? sanitize_text_field( $release['tag_name'] ) : '';
	$version = preg_replace( '/^v/i', '', $tag );

	if ( ! $version || ! preg_match( '/^\d+(?:\.\d+){1,3}(?:[-+][0-9A-Za-z.-]+)?$/', $version ) ) {
		set_site_transient( $cache_key, array(), 2 * HOUR_IN_SECONDS );
		return array();
	}

	$asset_url = '';
	if ( ! empty( $release['assets'] ) && is_array( $release['assets'] ) ) {
		foreach ( $release['assets'] as $asset ) {
			$name = isset( $asset['name'] ) ? sanitize_file_name( $asset['name'] ) : '';
			$url  = isset( $asset['browser_download_url'] ) ? esc_url_raw( $asset['browser_download_url'] ) : '';

			if ( $name && $url && 0 === strpos( $name, 'medicare-leads-hub-' ) && '.zip' === substr( $name, -4 ) ) {
				$asset_url = $url;
				break;
			}
		}
	}

	$latest = $asset_url ? array( 'version' => $version, 'package' => $asset_url ) : array();
	set_site_transient( $cache_key, $latest, 6 * HOUR_IN_SECONDS );

	return $latest;
}

/**
 * Add a GitHub release to WordPress' native theme update response.
 *
 * @param object $transient Theme update transient.
 * @return object
 */
function medicare_leads_hub_github_theme_update( $transient ) {
	if ( ! is_object( $transient ) || ! isset( $transient->response ) ) {
		return $transient;
	}

	$theme      = wp_get_theme();
	$stylesheet = $theme->get_stylesheet();

	if ( 'medicare-leads-hub' !== $stylesheet ) {
		return $transient;
	}

	$release = medicare_leads_hub_github_latest_release();
	if ( empty( $release['version'] ) || empty( $release['package'] ) || ! version_compare( $release['version'], $theme->get( 'Version' ), '>' ) ) {
		return $transient;
	}

	$transient->response[ $stylesheet ] = array(
		'theme'       => $stylesheet,
		'new_version' => $release['version'],
		'url'         => 'https://github.com/' . MEDICARE_LEADS_HUB_GITHUB_REPOSITORY,
		'package'     => $release['package'],
	);

	return $transient;
}
add_filter( 'pre_set_site_transient_update_themes', 'medicare_leads_hub_github_theme_update' );

/**
 * Let the WordPress Updates screen force a fresh GitHub release check.
 */
function medicare_leads_hub_github_force_update_check() {
	if ( isset( $_GET['force-check'] ) && current_user_can( 'update_themes' ) ) {
		delete_site_transient( 'medicare_leads_hub_github_latest_release' );
	}
}
add_action( 'admin_init', 'medicare_leads_hub_github_force_update_check' );
