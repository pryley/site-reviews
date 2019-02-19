<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html;

class Template
{
	/**
	 * @param string $templatePath
	 * @return void|string
	 */
	public function build( $templatePath, array $data = [] )
	{
		$data = $this->normalize( $data );
		ob_start();
		glsr()->render( $templatePath, $data );
		$template = ob_get_clean();
		$path = glsr( Helper::class )->removePrefix( 'templates/', $templatePath );
		$template = apply_filters( 'site-reviews/build/template/'.$path, $template, $data );
		$template = $this->interpolate( $template, $data['context'], $path );
		$template = apply_filters( 'site-reviews/rendered/template', $template, $templatePath, $data );
		$template = apply_filters( 'site-reviews/rendered/template/'.$path, $template, $data );
		return $template;
	}

	/**
	 * Interpolate context values into template placeholders
	 * @param string $template
	 * @param string $templatePath
	 * @return string
	 */
	public function interpolate( $template, array $context = [], $templatePath )
	{
		$context = $this->normalizeContext( $context );
		$context = apply_filters( 'site-reviews/interpolate/'.$templatePath, $context );
		foreach( $context as $key => $value ) {
			$template = strtr(
				$template,
				array_fill_keys( ['{'.$key.'}', '{{ '.$key.' }}'], $value )
			);
		}
		return trim( $template );
	}

	/**
	 * @param string $templatePath
	 * @return void|string
	 */
	public function render( $templatePath, array $data = [] )
	{
		echo $this->build( $templatePath, $data );
	}

	/**
	 * @return array
	 */
	protected function normalize( array $data )
	{
		$arrayKeys = ['context', 'globals'];
		$data = wp_parse_args( $data, array_fill_keys( $arrayKeys, [] ));
		foreach( $arrayKeys as $key ) {
			if( is_array( $data[$key] ))continue;
			$data[$key] = [];
		}
		return $data;
	}

	/**
	 * @return array
	 */
	protected function normalizeContext( array $context )
	{
		$context = array_filter( $context, function( $value ) {
			return !is_array( $value ) && !is_object( $value );
		});
		return array_map( function( $value ) {
			return (string)$value;
		}, $context );
	}
}
