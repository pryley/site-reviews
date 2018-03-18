<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Commands;

class TogglePinned
{
	public $id;
	public $pinned;

	public function __construct( $input )
	{
		$pinned = isset( $input['pinned'] )
			? wp_validate_boolean( $input['pinned'] )
			: null;

		$this->id     = $input['id'];
		$this->pinned = $pinned;
	}
}
