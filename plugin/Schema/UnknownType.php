<?php

namespace GeminiLabs\SiteReviews\Schema;

use GeminiLabs\SiteReviews\Schema\BaseType;

class UnknownType extends BaseType
{
	/**
	 * @var array
	 * @see http://schema.org/{property_name}
	 */
	public $allowed = [
		'aggregateRating',
	];

	/**
	 * @var array
	 * @see http://schema.org/{property_name}
	 */
	public $parents = [
		'Thing',
	];
}
