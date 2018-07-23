<?php

namespace GeminiLabs\SiteReviews\Handlers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Modules\Html;
use GeminiLabs\SiteReviews\Modules\Style;

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
			$dependencies = apply_filters( 'site-reviews/enqueue/public/dependencies', [] );
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
	public function enqueueRecaptchaScript()
	{
		if( glsr( OptionManager::class )->get( 'settings.submissions.recaptcha.integration' ) != 'custom' )return;
		$language = apply_filters( 'site-reviews/recaptcha/language', get_locale() );
		wp_enqueue_script( Application::ID.'/google-recaptcha', add_query_arg([
			'hl' => $language,
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
			'ajaxpagination' => $this->getFixedSelectorsForPagination(),
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'validationconfig' => $this->getValidationConfig(),
			'validationstrings' => $this->getValidationStrings(),
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
		return apply_filters( 'site-reviews/localize/pagination/selectors', $selectors );

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

	/**
	 * @return array
	 */
	protected function getValidationConfig()
	{
		$config = [
			'errorClass' => 'has-danger',
			'errorParentClass' => 'form-group',
			'errorTextClass' => 'text-help',
			'errorTextTag' => 'div',
			'fieldGroupClass' => 'form-group',
			'successClass' => 'has-success',
		];
		return apply_filters( 'site-reviews/localize/validation/config', $config );
	}

	/**
	 * @return array
	 */
	protected function getValidationStrings()
	{
		$strings = [
			'email' => __( 'This field requires a valid e-mail address', 'site-reviews' ),
			'max' => __( 'Maximum value for this field is %s', 'site-reviews' ),
			'maxlength' => __( 'This fields length must be < %s', 'site-reviews' ),
			'min' => __( 'Minimum value for this field is %s', 'site-reviews' ),
			'minlength' => __( 'This fields length must be > %s', 'site-reviews' ),
			'number' => __( 'This field requires a number', 'site-reviews' ),
			'pattern' => __( 'Input must match the pattern %s', 'site-reviews' ),
			'required' => __( 'This field is required', 'site-reviews' ),
			'tel' => __( 'This field requires a valid telephone number', 'site-reviews' ),
			'url' => __( 'This field requires a valid website URL', 'site-reviews' ),
		];
		return apply_filters( 'site-reviews/localize/validation/strings', $strings );
	}
}
