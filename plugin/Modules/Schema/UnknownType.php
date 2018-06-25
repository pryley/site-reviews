<?php

namespace GeminiLabs\SiteReviews\Modules\Schema;

use GeminiLabs\SiteReviews\Modules\Schema\BaseType;

class UnknownType extends BaseType
{
	/**
	 * @var array
	 * @see http://schema.org/{property_name}
	 */
	protected $parents = [
		'Thing',
	];
}
