<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\Helper;
use WP_UnitTestCase;

/**
 * Test case for the Plugin
 * @group plugin
 */
class TestHelper extends WP_UnitTestCase
{
	public function test_build_class_name()
	{
		$this->assertEquals( glsr( Helper::class )->buildClassName( 'hello-doll' ), 'HelloDoll' );
		$this->assertEquals(
			glsr( Helper::class )->buildClassName( 'Doll', 'Hello' ),
			'GeminiLabs\SiteReviews\Hello\Doll'
		);
	}

	public function test_build_method_name()
	{
		$this->assertEquals( glsr( Helper::class )->buildMethodName( 'Hello-Doll', 'get' ), 'getHelloDoll' );
	}

	public function test_build_property_name()
	{
		$this->assertEquals( glsr( Helper::class )->buildPropertyName( 'Hello-Doll' ), 'helloDoll' );
	}

	public function test_compare_arrays()
	{
		$this->assertTrue( glsr( Helper::class )->compareArrays( ['one' => ['two']], ['one' => ['two']] ));
		$this->assertFalse( glsr( Helper::class )->compareArrays( ['one' => ['two']], ['one' => 'two'] ));
	}

	public function test_convert_dot_notation_array()
	{
		$original = ['parent.child' => 'toys'];
		$converted = ['parent' => ['child' => 'toys']];
		$this->assertEquals( glsr( Helper::class )->convertDotNotationArray( $original ), $converted );
	}

	public function test_convert_path_to_id()
	{
		$this->assertEquals( glsr( Helper::class )->convertPathToId( 'abc.d.e' ), '-abc-d-e' );
		$this->assertEquals( glsr( Helper::class )->convertPathToId( 'd.e', 'abc' ), 'abc-d-e' );
		$this->assertEquals( glsr( Helper::class )->convertPathToId( 'd.e.', 'abc' ), 'abc-d-e-' );
		$this->assertEquals( glsr( Helper::class )->convertPathToId( '.d.e', 'abc' ), 'abc--d-e' );
	}

	public function test_convert_path_to_name()
	{
		$this->assertEquals( glsr( Helper::class )->convertPathToName( 'abc.d.e' ), '[abc][d][e]' );
		$this->assertEquals( glsr( Helper::class )->convertPathToName( 'd.e', 'abc' ), 'abc[d][e]' );
		$this->assertEquals( glsr( Helper::class )->convertPathToName( 'd.e.', 'abc' ), 'abc[d][e][]' );
		$this->assertEquals( glsr( Helper::class )->convertPathToName( '.d.e', 'abc' ), 'abc[][d][e]' );
	}

	public function test_dash_case()
	{
		$this->assertEquals( glsr( Helper::class )->dashCase( 'a-b_cDE' ), 'a-b-c-d-e' );
	}

	public function test_ends_with()
	{
		$this->assertTrue( glsr( Helper::class )->endsWith( 'efg', 'abcdefg' ));
		$this->assertFalse( glsr( Helper::class )->endsWith( 'efg', 'ABCDEFG' ));
	}

	public function test_filter_input()
	{
		$_POST['xxx'] = 'xxx';
		$this->assertEquals( glsr( Helper::class )->filterInput( 'xxx' ), 'xxx' );
		$this->assertEquals( glsr( Helper::class )->filterInput( 'zzz' ), null );
	}

	public function test_filter_input_array()
	{
		$test = ['a' => ['b', 'c']];
		$_POST['xxx'] = $test;
		$this->assertEquals( glsr( Helper::class )->filterInputArray( 'xxx' ), $test );
		$this->assertEquals( glsr( Helper::class )->filterInputArray( 'zzz' ), [] );
	}

	public function test_flatten_array()
	{
		$test = ['one' => ['two' => ['three' => ['x','y','z']]]];
		$this->assertEquals(
			glsr( Helper::class )->flattenArray( $test ),
			['one.two.three' => ['x','y','z']]
		);
		$this->assertEquals(
			glsr( Helper::class )->flattenArray( $test, true ),
			['one.two.three' => '[x, y, z]']
		);
		$this->assertEquals(
			glsr( Helper::class )->flattenArray( $test, true, 'test' ),
			['test.one.two.three' => '[x, y, z]']
		);
	}

	public function test_get_path_value()
	{
		$values = ['parent' => ['child' => 'toys']];
		$this->assertEquals(
			glsr( Helper::class )->getPathValue( 'parent.child', $values ),
			'toys'
		);
		$this->assertEquals(
			glsr( Helper::class )->getPathValue( 'parent.child.toys', $values, 'fallback' ),
			'fallback'
		);
	}

	public function test_is_indexed_array()
	{
		$this->assertFalse( glsr( Helper::class )->isIndexedArray( 'not an array' ));
		$this->assertFalse( glsr( Helper::class )->isIndexedArray( ['key' => 'value'] ));
		$this->assertTrue( glsr( Helper::class )->isIndexedArray( [[]] ));
		$this->assertTrue( glsr( Helper::class )->isIndexedArray( [1,2,3] ));
	}

	public function test_is_indexed_flat_array()
	{
		$this->assertFalse( glsr( Helper::class )->isIndexedFlatArray( 'not an array' ));
		$this->assertFalse( glsr( Helper::class )->isIndexedFlatArray( [[]] ));
		$this->assertTrue( glsr( Helper::class )->isIndexedFlatArray( [] ));
		$this->assertTrue( glsr( Helper::class )->isIndexedFlatArray( [1,2,3] ));
	}

	public function test_prefix_string()
	{
		$this->assertEquals( glsr( Helper::class )->prefixString( ' bob ', 'hello_' ), 'hello_bob' );
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

	public function test_set_path_value()
	{
		$this->assertEquals(
			glsr( Helper::class )->setPathValue( 'number.thirteen', '13', [] ),
			['number' => ['thirteen' => '13']]
		);
	}

	public function test_snake_case()
	{
		$this->assertEquals( glsr( Helper::class )->snakeCase( 'a-b_cDE' ), 'a_b_c_d_e' );
	}

	public function test_starts_with()
	{
		$this->assertTrue( glsr( Helper::class )->startsWith( 'abc', 'abcdefg' ));
		$this->assertFalse( glsr( Helper::class )->startsWith( 'abc', 'ABCDEFG' ));
	}
}
