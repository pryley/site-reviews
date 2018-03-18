<?php

/**
 * @package   GeminiLabs\SiteReviews\Tests
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Tests\Setup;
use WP_UnitTestCase;

/**
 * Test case for the Plugin
 *
 * @group plugin
 */
class TestDatabase extends WP_UnitTestCase
{
	use Setup;

	public function test_get_option()
	{
		$this->db->setOption( 'settings.general.require.approval', 'yes' );

		$this->assertEquals(
			$this->db->getOption( 'settings.general.require.approval' ),
			'yes'
		);

		$this->assertEquals(
			$this->db->getOption( 'general.require.approval', 'no', 'settings' ),
			'yes'
		);
	}

	public function test_get_options()
	{
		$options = $this->db->getOptions();

		$this->assertArrayHasKey( 'logging', $options );
		$this->assertArrayHasKey( 'settings', $options );
		$this->assertArrayHasKey( 'version', $options );
		$this->assertArrayHasKey( 'version_upgraded_from', $options );
		$this->assertArrayHasKey( 'general', $options['settings'] );
		$this->assertArrayHasKey( 'reviews', $options['settings'] );
		$this->assertArrayHasKey( 'reviews-form', $options['settings'] );
	}

	public function test_get_value_from_path()
	{
		$values = ['parent' => ['child' => 'toys']];

		$this->assertEquals(
			$this->db->getValueFromPath( 'parent.child', '', $values ),
			'toys'
		);

		$this->assertEquals(
			$this->db->getValueFromPath( 'parent.child.toys', 'fallback', $values ),
			'fallback'
		);
	}

	public function test_remove_empty_values_from()
	{
		$array = [
			'emptyString' => '',
			'emptyArray'  => [],
			'array'       => [
				'string'      => 'string',
				'emptyString' => [],
			],
		];

		$this->assertEquals(
			$this->db->removeEmptyValuesFrom( $array ),
			['array' => ['string' => 'string']]
		);
	}

	public function test_reset_option()
	{
		$this->db->setOption( 'general.require.approval', 'yes', true );

		$option = $this->db->resetOption( 'general.require.approval', 'no', 'settings' );

		$this->assertEquals( $option, 'yes' );

		$this->assertEquals(
			$this->db->getOption( 'settings.general.require.approval' ),
			'no'
		);
	}

	public function test_set_option()
	{
		$this->db->setOption( 'settings.general.require.approval', 'no' );

		$this->assertEquals(
			$this->db->getOption( 'settings.general.require.approval' ),
			'no'
		);

		$this->db->setOption( 'general.require.approval', 'yes', 'settings' );

		$this->assertEquals(
			$this->db->getOption( 'general.require.approval', 'no', true ),
			'yes'
		);
	}

	public function test_set_value_to_path()
	{
		$this->assertEquals(
			$this->db->setValueToPath( '13', 'number.thirteen', [] ),
			['number' => ['thirteen' => '13']]
		);
	}

	public function test_create_review()
	{}

	public function test_delete_review()
	{}

	public function test_get_review()
	{}

	public function test_get_review_count()
	{}

	public function test_get_review_id()
	{}

	public function test_get_review_ids()
	{}

	public function test_get_review_meta()
	{}

	public function test_get_reviews()
	{}

	public function test_get_reviews_meta()
	{}

	public function test_get_review_types()
	{}

	public function test_get_terms()
	{}

	public function test_normalize_meta()
	{}

	public function test_normalize_meta_key()
	{}

	public function test_normalize_terms()
	{}

	public function test_revert_review()
	{}

	public function test_set_defaults()
	{}

	public function test_set_terms()
	{}
}
