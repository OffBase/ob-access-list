<?php
/**
 * Tests for check functions.
 *
 * @phpcs:disable HM.Files.NamespaceDirectoryName.NameMismatch
 *
 * @package offbase-access-list
 */

namespace Offbase\AccessList\Tests;

use Offbase\AccessList\Check;
use WP_UnitTestCase;

/**
 * Class Test_Cron
 *
 * @group analytics
 */
class Test_Check extends WP_UnitTestCase {

	const WHITELIST = [
		'69.36.132.252/31',
		'69.36.132.250',
		'69.36.132.255-69.36.132.257',
		'hello',
		'0:0:0:0:0:ffff:192.1.56.200',
		'0:0:0:0:0:ffff:192.1.56.10-0:0:0:0:0:ffff:192.1.56.101',
	];

	/**
	 * Set up.
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
	}

	/**
	 * Test ipv4 addresses.
	 *
	 * @return void
	 */
	public function test_ipv4() {
		$exact_ip = '69.36.132.250';
		$this->assertTrue(
			Check\is_in_access_list( $exact_ip )
		);

		$cidr_ip = '69.36.132.253';
		$this->assertTrue(
			Check\is_in_access_list( $cidr_ip )
		);

		$in_range_ip = '69.36.132.256';
		$this->assertTrue(
			Check\is_in_access_list( $in_range_ip )
		);

		$not_access_listed_ip = '69.36.132.249';
		$this->assertFalse(
			Check\is_in_access_list( $not_access_listed_ip )
		);
	}

	/**
	 * Test ipv6 addresses.
	 *
	 * @return void
	 */
	public function test_ipv6() {
		$exact_ip = '0:0:0:0:0:ffff:192.1.56.200';
		$this->assertTrue(
			Check\is_in_access_list( $exact_ip )
		);

		$in_range_ip = '0:0:0:0:0:ffff:192.1.56.100';
		$this->assertTrue(
			Check\is_in_access_list( $in_range_ip )
		);

		$not_access_listed_ip = '0:0:0:0:0:ffff:192.1.56.1';
		$this->assertFalse(
			Check\is_in_access_list( $not_access_listed_ip )
		);
	}
}
