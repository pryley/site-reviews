<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Contracts\ProviderContract;
use GeminiLabs\SiteReviews\Controllers\AdminController;
use GeminiLabs\SiteReviews\Controllers\AjaxController;
use GeminiLabs\SiteReviews\Controllers\EditorController;
use GeminiLabs\SiteReviews\Controllers\ListTableController;
use GeminiLabs\SiteReviews\Controllers\MainController;
use GeminiLabs\SiteReviews\Controllers\MenuController;
use GeminiLabs\SiteReviews\Controllers\PublicController;
// use GeminiLabs\SiteReviews\Controllers\SettingsController;
// use GeminiLabs\SiteReviews\Modules\Session;
use GeminiLabs\SiteReviews\Modules\Translator;

class Provider implements ProviderContract
{
	/**
	 * @return void
	 */
	public function register( Application $app )
	{
		$app->bind( Application::class, $app );
		$app->singleton( AdminController::class, AdminController::class );
		$app->singleton( AjaxController::class, AjaxController::class );
		$app->singleton( EditorController::class, EditorController::class );
		$app->singleton( ListTableController::class, ListTableController::class );
		$app->singleton( MainController::class, MainController::class );
		$app->singleton( MenuController::class, MenuController::class );
		$app->singleton( PublicController::class, PublicController::class );
		$app->singleton( Translator::class, Translator::class );
		// $app->singleton( Session::class, Session::class );
		// $app->singleton( SettingsController::class, SettingsController::class );
	}
}
