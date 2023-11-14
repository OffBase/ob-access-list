<?php
/**
 * Helper functions.
 *
 * @package offbase-access-list
 */

namespace Offbase\AccessList\Helpers;

/**
 * Helper function to fail the access list.
 */
function not_allowed(): void {
	wp_die( __( 'Sorry, you are not allowed to access this page.' ), 403 );
}
