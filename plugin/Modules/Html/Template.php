<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Modules\Html;

class Template
{
	/**
	 * @param string $templatePath
	 * @return void|string
	 */
	public function build( $templatePath, array $args = [] )
	{
		$args = $this->normalize( $args );
		$file = glsr()->path( 'views/'.$templatePath.'.php' );
		if( !file_exists( $file )) {
			glsr_log()->error( 'Template missing: '.$file );
			return;
		}
		ob_start();
		$render = glsr( Html::class )->render( $args['globals'] );
		include $file;
		$template = ob_get_clean();
		return $this->interpolate( $template, $args['context'] );
	}

	/**
	 * Interpolates context values into template placeholders
	 * @param string $template
	 * @return string
	 */
	public function interpolate( $template, array $context = [] )
	{
		$context = $this->normalizeContext( $context );
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
	public function render( $templatePath, array $args = [] )
	{
		echo $this->build( $templatePath, $args );
	}

	/**
	 * @return array
	 */
	protected function normalize( array $args )
	{
		$args = shortcode_atts( array_fill_keys( ['context', 'globals'], [] ), $args );
		foreach( $args as $key => $value ) {
			if( is_array( $value ))continue;
			$args[$key] = [];
		}
		return $args;
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
			return esc_attr( (string)$value );
		}, $context );
	}
}
