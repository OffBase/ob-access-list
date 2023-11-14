<?php
/**
 * Admin-side settings and functionality.
 *
 * @package offbase-access-list
 */

namespace Offbase\AccessList\Admin;

use Offbase\AccessList\Data;

/**
 * Nonce action.
 */
const NONCE_ACTION = 'ob-access-list';

/**
 * Nonce name.
 */
const NONCE_NAME = 'ob_access_list_nonce';

/**
 * Bootstrap admin.
 *
 * @return void
 */
function bootstrap(): void {
	add_action( 'wpmu_options', __NAMESPACE__ . '\\add_network_settings' );
	add_action( 'update_wpmu_options', __NAMESPACE__ . '\\save_network_settings' );
}

/**
 * Output the access_list setting on the network settings page.
 *
 * @return void
 */
function add_network_settings(): void {
	$enabled           = Data\is_access_list_enabled();
	$access_list       = Data\get_access_list();
	$access_list_field = implode( "\r\n", $access_list );
	?>
	<h3><?php esc_html_e( 'Access List', 'ob-access-list' ); ?></h3>
	<?php wp_nonce_field( NONCE_ACTION, NONCE_NAME ); ?>
	<table class="form-table">
		<tr>
			<th scope="row">
				<label for="ob_access_list_enabled"><?php esc_html_e( 'Access List Enabled', 'ob-access-list' ); ?></label>
			</th>
			<td>
				<input type="checkbox" id="ob_access_list_enabled" name="ob_access_list_enabled" value="1" <?php checked( $enabled, '1' ); ?> />
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="ob_access_list_wp_admin"><?php esc_html_e( 'WordPress Admin IP Access List', 'ob-access-list' ); ?></label>
			</th>
			<td>
				<textarea id="ob_access_list_wp_admin" name="ob_access_list_wp_admin" rows="10" cols="100"><?php echo esc_textarea( trim( $access_list_field ) ); ?></textarea>
				<div class="notice-info">
					<p>
						<?php
						echo wp_kses_post(
							__( '
								<b>Notice: No access list applied if empty.</b><br>
								<b>Usage:</b> One IP, Range, CIDR (only IPv4) per line.<br>
								<b>Allowed Formatting:</b><br>
								1.1.1.1<br>
								1.1.1.1-1.1.1.8<br>
								1.1.1.1/32<br>
							', 'ob-access-list' )
						);
						?>
					</p>
				</div>
			</td>
		</tr>
	</table>
	<?php
}

/**
 * Handle saving the access list settings on the network settings page.
 *
 * @return void
 */
function save_network_settings(): void {
	if ( ! check_admin_referer( NONCE_ACTION, NONCE_NAME ) ) {
		return;
	}

	if ( empty( $_POST['ob_access_list_enabled'] ) ) {
		Data\set_access_list_enabled( '' );
	} else {
		Data\set_access_list_enabled( sanitize_text_field( wp_unslash( $_POST['ob_access_list_enabled'] ) ) );
	}

	if ( empty( $_POST['ob_access_list_wp_admin'] ) ) {
		Data\store_access_list( [] );
		return;
	}

	// Massage data so we are storing in a usable state. Need line characters to be preserved.
	// phpcs:ignore HM.Security.ValidatedSanitizedInput.MissingUnslash
	$access_list_field = sanitize_textarea_field( $_POST['ob_access_list_wp_admin'] );
	$access_list       = explode( "\r\n", $access_list_field );

	Data\store_access_list( $access_list );
}
