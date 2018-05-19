<?php

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Html;

class EnqueuePublicAssets
{
	/**
	 * @return void
	 */
	public function handle()
	{
		$this->enqueueAssets();
		$this->enqueueRecaptchaScript();
		$this->localizeAssets();
	}

	/**
	 * @return void
	 */
	public function enqueueAssets()
	{
		if( apply_filters( 'site-reviews/assets/css', true )) {
			wp_enqueue_style(
				Application::ID,
				$this->getStylesheet(),
				[],
				glsr()->version
			);
		}
		if( apply_filters( 'site-reviews/assets/js', true )) {
			wp_enqueue_script(
				Application::ID,
				glsr()->url( 'assets/scripts/'.Application::ID.'.js' ),
				[],//glsr( Html::class )->getDependencies(),
				glsr()->version,
				true
			);
		}
	}

	/**
	 * @return void
	 */
	public function enqueueRecaptchaScript()
	{
		if( glsr( OptionManager::class )->get( 'settings.submissions.recaptcha.integration' ) != 'custom' )return;
		wp_enqueue_script( Application::ID.'/google-recaptcha', add_query_arg([
			'hl' => apply_filters( 'site-reviews/recaptcha/language', get_locale() ),
			'onload' => 'glsr_render_recaptcha',
			'render' => 'explicit',
		], 'https://www.google.com/recaptcha/api.js' ));
		$inlineScript = file_get_contents( glsr()->path( 'assets/scripts/recaptcha.js' ));
		wp_add_inline_script( Application::ID.'/google-recaptcha', $inlineScript, 'before' );
	}

	/**
	 * @return void
	 */
	public function localizeAssets()
	{
		$variables = [
			'action' => Application::PREFIX.'action',
			'ajaxnonce' => wp_create_nonce( Application::ID.'-ajax-nonce' ),
			'ajaxpagination' => ['#wpadminbar','.site-navigation-fixed'],
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		];
		$variables = apply_filters( 'site-reviews/enqueue/localize', $variables );
		wp_localize_script( Application::ID, 'site_reviews', $variables );
	}

	/**
	 * @return string
	 */
	protected function getStylesheet()
	{
		$currentTheme = sanitize_title( (string)wp_get_theme()->get( 'Name' ));
		return file_exists( glsr()->path.'assets/styles/'.$currentTheme.'.css' )
			? glsr()->url( 'assets/styles/'.$currentTheme.'.css' )
			: glsr()->url( 'assets/styles/'.Application::ID.'.css' );
	}
}
