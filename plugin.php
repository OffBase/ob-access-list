<?php
/**
 * Plugin Name: Off Base Access List
 * Version: 1.0.0
 * Plugin URI: https://offbase.com/
 * Description: A access list plugin to allow access to certain areas of a WordPress instance.
 * Author: Off Base
 * Author URI: https://offbase.com
 *
 * @package offbase-access-list
 */

namespace Offbase\AccessList;

const BASE_DIR = __DIR__;

require_once __DIR__ . '/inc/admin.php';
require_once __DIR__ . '/inc/check.php';
require_once __DIR__ . '/inc/data.php';
require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/ipv4.php';
require_once __DIR__ . '/inc/ipv6.php';
require_once __DIR__ . '/inc/namespace.php';

add_action( 'muplugins_loaded', __NAMESPACE__ . '\\init' );
