<?php
/**
 * IPv6 Specific functionality.
 *
 * @package offbase-access-list
 */

namespace Offbase\AccessList\IPv6;

/**
 * Check if the IP is equal or within the range.
 *
 * @param string $ipv6 IPv6 IP address.
 * @param string $start The IP/start of IP range to check.
 * @param string $end Optional. The end of the IP range to check, if not set then IP only tested against start value.
 * @return bool True if the IP is equal or in range.
 */
function check_ip( string $ipv6, string $start, string $end = '' ): bool {
	if ( empty( $end ) ) {
		if ( $ipv6 === $start ) {
			return true;
		}
		return false;
	}

	$ip             = inet_pton( $ipv6 );
	$start_ip       = inet_pton( $start );
	$end_ip         = inet_pton( $end );
	$ip_parts       = unpack( 'N*', $ip );
	$start_ip_parts = unpack( 'N*', $start_ip );
	$end_ip_parts   = unpack( 'N*', $end_ip );

	foreach ( $ip_parts as $key => $part ) {
		if ( $part < $start_ip_parts[ $key ] || $part > $end_ip_parts[ $key ] ) {
			return false;
		}
	}

	return true;
}
