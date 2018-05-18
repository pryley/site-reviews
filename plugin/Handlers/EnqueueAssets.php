<?php

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Commands\EnqueueAssets as Command;
use GeminiLabs\SiteReviews\Database\OptionManager;

class EnqueueAssets
{
	/**
	 * @var array
	 */
	protected $dependencies;

	/**
	 * @return void
	 */
	public function handle( Command $command )
	{
		$this->dependencies = glsr( 'Html' )->getDependencies();
		$ajaxNonce = wp_create_nonce( Application::ID.'-ajax-nonce' );
		$variables = [
			'action'  => glsr()->prefix . '_action',
			'ajaxurl' => add_query_arg( '_nonce', $ajaxNonce, admin_url( 'admin-ajax.php' )),
			'ajaxnonce' => $ajaxNonce,
			'ajaxpagination' => ['#wpadminbar','.site-navigation-fixed'],
		];
		$this->enqueueAssets();

		wp_localize_script( Application::ID, 'site_reviews', apply_filters( 'site-reviews/enqueue/localize', $variables ));
	}

	/**
	 * @return void
	 */
	public function enqueueAssets()
	{
		$currentTheme = sanitize_title( wp_get_theme()->get( 'Name' ));
		$stylesheet = file_exists( glsr()->path.'assets/css/'.$currentTheme.'.css' )
			? glsr()->url.'assets/css/'.$currentTheme.'.css'
			: glsr()->url.'assets/css/'.Application::ID.'.css';
		if( apply_filters( 'site-reviews/assets/css', true )) {
			wp_enqueue_style(
				Application::ID,
				$stylesheet,
				[],
				glsr()->version
			);
		}
		if( apply_filters( 'site-reviews/assets/js', true )) {
			wp_enqueue_script(
				Application::ID,
				glsr()->url.'assets/js/'.Application::ID.'.js',
				['jquery'],
				glsr()->version,
				true
			);
		}
		if( glsr( OptionManager::class )->get( 'settings.reviews-form.recaptcha.integration' ) == 'custom' ) {
			$this->enqueueRecaptchaScript();
		}
	}

	/**
	 * @return void
	 */
	public function enqueueRecaptchaScript()
	{
		wp_enqueue_script( Application::ID.'/google-recaptcha', add_query_arg([
			'hl' => apply_filters( 'site-reviews/recaptcha/language', get_locale() ),
			'onload' => 'glsr_render_recaptcha',
			'render' => 'explicit',
		], 'https://www.google.com/recaptcha/api.js' ));
		$inlineScript = file_get_contents( glsr()->path.'js/recaptcha.js' );
		wp_add_inline_script( Application::ID.'/google-recaptcha', $inlineScript, 'before' );
	}
}
