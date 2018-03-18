<?php

/**
 * @package   GeminiLabs\SiteReviews
 * @copyright Copyright (c) 2016, Paul Ryley
 * @license   GPLv3
 * @since     1.0.0
 * -------------------------------------------------------------------------------------------------
 */

namespace GeminiLabs\SiteReviews\Providers;

use GeminiLabs\SiteReviews\App;

interface ProviderInterface
{
	/**
	 * @return void
	 */
	public function register( App $app );
}
