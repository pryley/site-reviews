<?php

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\ValidationStringsDefaults;
use GeminiLabs\SiteReviews\Modules\Style;
use GeminiLabs\SiteReviews\Modules\Validator;

class EnqueuePublicAssets
{
	/**
	 * @return void
	 */
	public function handle()
	{
		$this->enqueueAssets();
		$this->enqueuePolyfillService();
		$this->enqueueRecaptchaScript();
		$this->inlineStyles();
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
			$dependencies = apply_filters( 'site-reviews/assets/polyfill', true )
				? [Application::ID.'/polyfill']
				: [];
			$dependencies = apply_filters( 'site-reviews/enqueue/public/dependencies', $dependencies );
			wp_enqueue_script(
				Application::ID,
				glsr()->url( 'assets/scripts/'.Application::ID.'.js' ),
				$dependencies,
				glsr()->version,
				true
			);
		}
	}

	/**
	 * @return void
	 */
	public function enqueuePolyfillService()
	{
		if( !apply_filters( 'site-reviews/assets/polyfill', true ))return;
		wp_enqueue_script( Application::ID.'/polyfill', add_query_arg([
			'features' => 'CustomEvent,Element.prototype.closest,Element.prototype.dataset,Event,MutationObserver',
			'flags' => 'gated',
		], 'https://cdn.polyfill.io/v2/polyfill.min.js' ));
	}

	/**
	 * @return void
	 */
	public function enqueueRecaptchaScript()
	{
		// wpforms-recaptcha
		// google-recaptcha
		// nf-google-recaptcha
		if( !glsr( OptionManager::class )->isRecaptchaEnabled() )return;
		$language = apply_filters( 'site-reviews/recaptcha/language', get_locale() );
		wp_enqueue_script( Application::ID.'/google-recaptcha', add_query_arg([
			'hl' => $language,
			'render' => 'explicit',
		], 'https://www.google.com/recaptcha/api.js' ));
	}

	/**
	 * @return void
	 */
	public function inlineStyles()
	{
		$inlineStylesheetPath = glsr()->path( 'assets/styles/inline-styles.css' );
		if( !apply_filters( 'site-reviews/assets/css', true ))return;
		if( !file_exists( $inlineStylesheetPath )) {
			glsr_log()->error( 'Inline stylesheet is missing: '.$inlineStylesheetPath );
			return;
		}
		$inlineStylesheetValues = glsr()->config( 'inline-styles' );
		$stylesheet = str_replace(
			array_keys( $inlineStylesheetValues ),
			array_values( $inlineStylesheetValues ),
			file_get_contents( $inlineStylesheetPath )
		);
		wp_add_inline_style( Application::ID, $stylesheet );
	}

	/**
	 * @return void
	 */
	public function localizeAssets()
	{
		$variables = [
			'action' => Application::PREFIX.'action',
			'ajaxpagination' => $this->getFixedSelectorsForPagination(),
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nameprefix' => Application::ID,
			'validationconfig' => glsr( Style::class )->validation,
			'validationstrings' => glsr( ValidationStringsDefaults::class )->defaults(),
		];
		$variables = apply_filters( 'site-reviews/enqueue/public/localize', $variables );
		wp_localize_script( Application::ID, 'GLSR', $variables );
	}

	/**
	 * @return array
	 */
	protected function getFixedSelectorsForPagination()
	{
		$selectors = ['#wpadminbar','.site-navigation-fixed'];
		return apply_filters( 'site-reviews/enqueue/public/localize/ajax-pagination', $selectors );
	}

	/**
	 * @return string
	 */
	protected function getStylesheet()
	{
		$currentStyle = glsr( Style::class )->style;
		return file_exists( glsr()->path( 'assets/styles/custom/'.$currentStyle.'.css' ))
			? glsr()->url( 'assets/styles/custom/'.$currentStyle.'.css' )
			: glsr()->url( 'assets/styles/'.Application::ID.'.css' );
	}
}
