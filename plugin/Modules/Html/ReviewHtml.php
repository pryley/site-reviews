<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use ArrayObject;
use GeminiLabs\SiteReviews\Modules\Html\Template;

class ReviewHtml extends ArrayObject
{
	/**
	 * @var array
	 */
	public $values;

	public function __construct( array $values = [] )
	{
		$this->values = $values;
		parent::__construct( $values, ArrayObject::STD_PROP_LIST|ArrayObject::ARRAY_AS_PROPS );
	}

	/**
	 * @return string
	 */
	public function __get( $key )
	{
		return array_key_exists( $key, $this->values )
			? $this->values[$key]
			: '';
	}

	/**
	 * @return string|void
	 */
	public function __toString()
	{
		if( empty( $this->values ))return;
		return glsr( Template::class )->build( 'templates/review', [
			'context' => $this->values,
		]);
	}
}
