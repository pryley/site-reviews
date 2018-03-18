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
use GeminiLabs\SiteReviews\Log\Logger;
use GeminiLabs\SiteReviews\Providers\ProviderInterface;

/**
 * Note: We're using the full "namespace\classname" because "::class" isn't supported in PHP 5.4
 */
class MainProvider implements ProviderInterface
{
	public function register( App $app )
	{
		$app->bind( 'GeminiLabs\SiteReviews\App', $app );

		$app->bind( 'GeminiLabs\SiteReviews\Log\Logger', function( $app ) {
			return Logger::file( trailingslashit( $app->path ) . 'debug.log', $app->prefix );
		});

		$app->singleton(
			'GeminiLabs\SiteReviews\Html',
			'GeminiLabs\SiteReviews\Html'
		);

		$app->singleton(
			'GeminiLabs\SiteReviews\Session',
			'GeminiLabs\SiteReviews\Session'
		);

		$app->singleton(
			'GeminiLabs\SiteReviews\Settings',
			'GeminiLabs\SiteReviews\Settings'
		);

		$app->singleton(
			'GeminiLabs\SiteReviews\Translator',
			'GeminiLabs\SiteReviews\Translator'
		);

		// controllers should go last
		$app->singleton(
			'GeminiLabs\SiteReviews\Controllers\AjaxController',
			'GeminiLabs\SiteReviews\Controllers\AjaxController'
		);

		$app->singleton(
			'GeminiLabs\SiteReviews\Controllers\MainController',
			'GeminiLabs\SiteReviews\Controllers\MainController'
		);

		$app->singleton(
			'GeminiLabs\SiteReviews\Controllers\ReviewController',
			'GeminiLabs\SiteReviews\Controllers\ReviewController'
		);
	}
}
