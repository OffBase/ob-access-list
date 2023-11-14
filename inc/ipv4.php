<?php
/**
 * IPv4 Specific functionality.
 *
 * @package offbase-access-list
 */

namespace Offbase\AccessList\IPv4;

/**
 * Check if the IP address is equal or in the range.
 *
 * @param string $ipv4 IPv4 IP address.
 * @param string $start The IP/start of IP range to check.
 * @param string $end Optional. The end of the IP range to check, if not set then IP only tested against start value.
 * @return bool True if the IP is equal or in range.
 */
function check_ip( string $ipv4, string $start, string $end = '' ): bool {
	if ( empty( $end ) ) {
		if ( $ipv4 === $start ) {
			return true;
		}
		return false;
	}

	$ip       = ip2long( $ipv4 );
	$start_ip = ip2long( $start );
	$end_ip   = ip2long( $end );

	return ( $ip >= $start_ip && $ip <= $end_ip );
}

/**
 * Check if the IP address is in the CIDR.
 *
 * @param string $ipv4 IPv4 IP address.
 * @param string $cidr The CIDR address to compare against.
 * @return bool True if IP is in the CIDR.
 */
function check_in_cidr( string $ipv4, string $cidr ): bool {
	list( $subnet_v4, $prefix_length ) = explode( '/', $cidr );

	$ip            = ip2long( $ipv4 );
	$subnet        = ip2long( $subnet_v4 );
	$prefix_length = (int) $prefix_length;

	// Calculate the subnet mask by left shifting the number of bits to mask off the host portion.
	$subnet_mask = -1 << ( 32 - $prefix_length );
	$subnet_base = $subnet & $subnet_mask;

	return ( $ip & $subnet_mask ) === $subnet_base;
}
