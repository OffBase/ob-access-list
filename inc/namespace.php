<?php
/**
 * Off Base AccessList.
 *
 * @package offbase-access-list
 */

namespace Offbase\AccessList;

/**
 * Initialize the plugin.
 *
 * @return void
 */
function init(): void {
	bootstrap();
}

/**
 * Bootstrap components of the plugin.
 *
 * @return void
 */
function bootstrap(): void {
	Admin\bootstrap();
	Check\bootstrap();
}
