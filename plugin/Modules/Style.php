<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class Style
{
	/**
	 * @var array
	 */
	public $fields;

	/**
	 * @var string
	 */
	public $style;

	/**
	 * @var array
	 */
	public $validation;

	public function __construct()
	{
		$this->style = glsr( OptionManager::class )->get( 'settings.submissions.style', 'default' );
		$this->setConfig();
	}

	/**
	 * @param string $view
	 * @return string
	 */
	public function filterView( $view )
	{
		$styledViews = [
			'templates/form/field',
			'templates/form/response',
			'templates/form/submit-button',
			'templates/reviews-form',
		];
		if( !preg_match( '('.implode( '|', $styledViews ).')', $view )) {
			return $view;
		}
		$views = $this->generatePossibleViews( $view );
		foreach( $views as $possibleView ) {
			if( !file_exists( glsr()->path( 'views/'.$possibleView.'.php' )))continue;
			return $possibleView;
		}
		return $view;
	}

	/**
	 * @return string
	 */
	public function get()
	{
		return $this->style;
	}

	/**
	 * @return array
	 */
	public function setConfig()
	{
		$keys = [
			'fields' => ['input', 'input_checkbox', 'input_radio', 'label', 'label_checkbox', 'label_radio', 'select', 'textarea'],
			'validation' => ['error_tag', 'error_tag_class', 'field_class', 'field_error_class', 'input_error_class', 'input_success_class'],
		];
		$config = shortcode_atts(
			array_fill_keys( array_keys( $keys ), [] ),
			glsr()->config( 'styles/'.$this->style )
		);
		foreach( array_keys( $config ) as $key ) {
			$this->$key = wp_parse_args( $config[$key], array_fill_keys( $keys[$key], '' ));
		}
	}

	/**
	 * @return void
	 */
	public function modifyField( Builder $instance )
	{
		if( !$this->isPublicInstance( $instance ) || empty( array_filter( $this->fields )))return;
		call_user_func_array( [$this, 'customize'], [&$instance] );
	}

	/**
	 * @return void
	 */
	protected function customize( Builder $instance )
	{
		$args = wp_parse_args( $instance->args, array_fill_keys( ['class', 'type'], '' ));
		$key = $instance->tag.'_'.$args['type'];
		$classes = !isset( $this->fields[$key] )
			? $this->fields[$instance->tag]
			: $this->fields[$key];
		$instance->args['class'] = trim( $args['class'].' '.$classes );
		do_action_ref_array( 'site-reviews/customize/'.$this->style, [&$instance] );
	}

	/**
	 * @param string $view
	 * @return array
	 */
	protected function generatePossibleViews( $view )
	{
		$basename = basename( $view );
		$basepath = rtrim( $view, $basename );
		$customPath = 'partials/styles/'.$this->style.'/';
		$parts = explode( '_', $basename );
		$views = [
			$customPath.$basename,
			$customPath.$parts[0],
			$view,
			$basepath.$parts[0],
		];
		return array_filter( $views );
	}

	/**
	 * @return bool
	 */
	protected function isPublicInstance( Builder $instance )
	{
		$args = wp_parse_args( $instance->args, [
			'is_public' => false,
			'is_raw' => false,
		]);
		if( is_admin() || !$args['is_public'] || $args['is_raw'] ) {
			return false;
		}
		return true;
	}
}
