<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Tests\Setup;
use WP_UnitTestCase;

/**
 * Test case for the Plugin
 * @group plugin
 */
class TestDatabase extends WP_UnitTestCase
{
	use Setup;

	public function test_get_option()
	{
		glsr( OptionManager::class )->set( 'settings.general.require.approval', 'yes' );
		$this->assertEquals(
			glsr( OptionManager::class )->get( 'settings.general.require.approval' ),
			'yes'
		);
		$this->assertEquals(
			glsr( OptionManager::class )->get( 'settings.general.require.approval', 'no' ),
			'yes'
		);
	}

	public function test_get_options()
	{
		$options = glsr( OptionManager::class )->all();
		$this->assertArrayHasKey( 'settings', $options );
		$this->assertArrayHasKey( 'version', $options );
		$this->assertArrayHasKey( 'version_upgraded_from', $options );
		$this->assertArrayHasKey( 'general', $options['settings'] );
		$this->assertArrayHasKey( 'reviews', $options['settings'] );
		$this->assertArrayHasKey( 'schema', $options['settings'] );
		$this->assertArrayHasKey( 'submissions', $options['settings'] );
	}

	public function test_get_path_value()
	{
		$values = ['parent' => ['child' => 'toys']];
		$this->assertEquals(
			glsr( Helper::class )->getPathValue( 'parent.child', '', $values ),
			'toys'
		);
		$this->assertEquals(
			glsr( Helper::class )->getPathValue( 'parent.child.toys', 'fallback', $values ),
			'fallback'
		);
	}

	public function test_remove_empty_array_values()
	{
		$array = [
			'emptyString' => '',
			'emptyArray'  => [],
			'array' => [
				'string' => 'string',
				'emptyString' => [],
			],
		];
		$this->assertEquals(
			glsr( Helper::class )->removeEmptyArrayValues( $array ),
			['array' => ['string' => 'string']]
		);
	}

	public function test_set_option()
	{
		glsr( OptionManager::class )->set( 'settings.general.require.approval', 'no' );
		$this->assertEquals(
			glsr( OptionManager::class )->get( 'settings.general.require.approval' ),
			'no'
		);
		glsr( OptionManager::class )->set( 'settings.general.require.approval', 'yes' );
		$this->assertEquals(
			glsr( OptionManager::class )->get( 'settings.general.require.approval', 'no' ),
			'yes'
		);
	}

	public function test_set_path_value()
	{
		$this->assertEquals(
			glsr( Helper::class )->setPathValue( 'number.thirteen', '13', [] ),
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
