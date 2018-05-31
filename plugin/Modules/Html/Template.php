<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

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
		return $this->interpolate( $template, $data['context'] );
	}

	/**
	 * Interpolate context values into template placeholders
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
	public function render( $templatePath, array $data = [] )
	{
		echo $this->build( $templatePath, $data );
	}

	/**
	 * @return array
	 */
	protected function normalize( array $data )
	{
		$data = wp_parse_args( $data, array_fill_keys( ['context', 'globals'], [] ));
		foreach( $data as $key => $value ) {
			if( is_array( $value ))continue;
			$data[$key] = [];
		}
		$data['template'] = $this;
		$data['render'] = glsr( Html::class )->render( $data['globals'] );
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
