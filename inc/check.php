<?php
/**
 * Check functionality.
 *
 * @package offbase-access-list
 */

namespace Offbase\AccessList\Check;

use Offbase\AccessList\Data;
use Offbase\AccessList\Helpers;
use Offbase\AccessList\IPv4;
use Offbase\AccessList\IPv6;

/**
 * Kick things off.
 *
 * @return void
 */
function bootstrap(): void {
	if ( empty( Data\is_access_list_enabled() ) ) {
		return;
	}

	check_location();
}

/**
 * Check if an IP is validated against the access_list.
 *
 * @param string $ip The IP Address to check.
 * @return bool True if the item is validated against the access list.
 */
function is_in_access_list( string $ip = '' ): bool {
	if ( empty( $ip ) ) {
		// Get IP.
		$ip = $_SERVER['REMOTE_ADDR'] ?? null;
	}

	if ( empty( $ip ) ) {
		return false;
	}

	$access_list = Data\get_access_list();

	foreach ( $access_list as $item ) {
		// Skip lines that start with "#" to allow comments.
		if ( str_starts_with( $item, '#' ) ) {
			continue;
		}
		$pass = against_access_list_item( $ip, $item );
		if ( $pass ) {
			return true;
		}
	}

	return false;
}

/**
 * Check if an IP is validated against an item in the access list.
 *
 * If the item equals or in the range/cidr then allow to continue.
 *
 * @param string $ip IP address.
 * @param string $access_list_item Single value from the access list.
 * @return bool True if the IP is validated against an item from the access list.
 */
function against_access_list_item( string $ip, string $access_list_item ): bool {
	$pass = false;

	// Check for typical IP.
	if (
		filter_var( $access_list_item, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ||
		filter_var( $access_list_item, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 )
	) {
		$pass = check_ip( $ip, $access_list_item );
	} elseif ( strpos( $access_list_item, '-' ) ) { // Range.
		list( $start, $end ) = explode( '-', $access_list_item );
		$pass = check_ip( $ip, $start, $end );
	} elseif ( strpos( $access_list_item, '/' ) ) { // CIDR.
		$pass = check_cidr( $ip, $access_list_item );
	}

	return $pass;
}

/**
 * Check if an IP matches or if an IP is within an IP range.
 *
 * @param string $ip IP to check.
 * @param string $start IP or start of IP range to check against.
 * @param string $end Optional. If specified then the IP will be checked between the range.
 * @return bool True is the IP is equal or within the range.
 */
function check_ip( string $ip, string $start, string $end = '' ): bool {
	// A quick check to potentially save some time.
	if (
		$ip === $start ||
		( ! empty( $end ) && $ip === $end )
	) {
		return true;
	}

	// Route IP depending on version.
	if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
		return IPv4\check_ip( $ip, $start, $end );
	} elseif ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
		return IPv6\check_ip( $ip, $start, $end );
	}

	return false;
}

/**
 * Check if an IP in within a CIDR.
 *
 * @param string $ip IP to check.
 * @param string $cidr CIDR to check against.
 * @return bool True if the IP with within the CIDR.
 */
function check_cidr( string $ip, string $cidr ): bool {
	// Route IP depending on version.
	if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
		return IPv4\check_in_cidr( $ip, $cidr );
	} elseif ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
		// TODO: Implement check for IPv6 CIDR.
		return false;
	}

	return false;
}

/**
 * Check if the current location should be access listed.
 *
 * If it should be access listed and the current IP is not in the access list then return an error.
 *
 * @return void
 */
function check_location(): void {
	$access_list = Data\get_access_list();
	$allowed   = true;

	// If the access list is empty then do not applied to access listed locations.
	if ( empty( $access_list ) ) {
		return;
	}

	// Check if the location should be access listed.
	$access_listed_locations = Data\get_access_list_locations();
	foreach ( $access_listed_locations as $access_listed_location ) {
		$matches = [];
		// This check is open for now to be more strict than not. It may need to be adjusted in the future to be more targeted.
		preg_match( sprintf( '(%1$s)', $access_listed_location ), $_SERVER['REQUEST_URI'], $matches );

		// If we do not have match, no need to check access list.
		if ( empty( $matches ) ) {
			continue;
		}

		// If IP is not in access list then block.
		$check = is_in_access_list();
		if ( ! $check ) {
			// Check if there are any bypasses.
			$bypassed = check_bypass_location( $access_listed_location );
			if ( $bypassed ) {
				continue;
			}

			$allowed = false;
		}
	}

	if ( $allowed ) {
		return;
	}

	Helpers\not_allowed();
}

/**
 * Check if the access listed location has any bypasses within it.
 *
 * @param string $access_listed_location A location that is supposed to be behind an access list.
 * @return bool
 */
function check_bypass_location( string $access_listed_location ): bool {
	$bypass_locations = Data\get_bypassed_access_list_locations();
	if ( empty( $bypass_locations[ $access_listed_location ] ) ) {
		return false;
	}

	$bypassed = false;
	foreach ( $bypass_locations[ $access_listed_location ] as $bypass_location ) {
		// If we do not have match, no need to check access list. Since we are already in the access list check this will
		// just check for the existence of the bypass location within the url.
		$position = strpos( $_SERVER['REQUEST_URI'], $bypass_location );
		if ( ! empty( $position ) ) {
			$bypassed = true;
		}
	}

	return $bypassed;
}
