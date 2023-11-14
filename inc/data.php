<?php
/**
 * AccessList data.
 *
 * @package offbase-access-list
 */

namespace Offbase\AccessList\Data;

/**
 * Option key denote if access list is enabled.
 */
const ACCESS_LIST_ENABLED_OPTION_KEY = 'ob-access-list-enabled';

/**
 * Option key to store WordPress Admin access list.
 */
const ACCESS_LIST_OPTION_KEY = 'ob-access-list-wp-admin';

/**
 * Locations that should be behind an access list.
 */
const LOCATIONS = [
	'wp-login.php',
	'wp-admin',
	'wp-activate.php',
	'wp-links-opml.php',
	'wp-trackback.php',
];

/**
 * Specific urls that can be bypassed if defined as a location behind an access list.
 */
const BYPASSED_LOCATIONS = [
	'wp-login.php' => [
		'?action=postpass',
	],
];

/**
 * Check if the access list is enabled.
 *
 * @return string
 */
function is_access_list_enabled(): string {
	return get_site_option( ACCESS_LIST_ENABLED_OPTION_KEY, '' );
}

/**
 * Set if the access list is enabled.
 *
 * @param string $enabled If the access list is enabled.
 * @return bool True if successful.
 */
function set_access_list_enabled( string $enabled ): bool {
	return update_site_option( ACCESS_LIST_ENABLED_OPTION_KEY, $enabled );
}

/**
 * Get the access list items from storage.
 *
 * @return array
 */
function get_access_list(): array {
	return (array) get_site_option( ACCESS_LIST_OPTION_KEY, [] );
}

/**
 * Store the access list items.
 *
 * @param array $access_list Array of access list items.
 * @return bool True if successful.
 */
function store_access_list( array $access_list ): bool {
	return update_site_option( ACCESS_LIST_OPTION_KEY, $access_list );
}

/**
 * Get the locations that will have the access list applied.
 *
 * @return string[]
 */
function get_access_list_locations(): array {
	return LOCATIONS;
}

/**
 * Get locations that are bypassed from the general access list
 *
 * @return array
 */
function get_bypassed_access_list_locations(): array {
	return BYPASSED_LOCATIONS;
}
