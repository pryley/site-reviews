<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Database\DefaultsManager;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html;
use GeminiLabs\SiteReviews\Modules\Html\Field;

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
	 * @param string $id
	 * @return void
	 */
	public function renderSettingFields( $id )
	{
		$fields = $this->getSettingFields( $this->normalizeSettingPath( $id ));
		$rows = '';
		foreach( $fields as $name => $field ) {
			$field = wp_parse_args( $field, [
				'name' => $name,
				'table' => true,
			]);
			$rows.= (new Field( $field ))->build();
		}
		$this->render( 'pages/settings/'.$id, [
			'context' => [
				'rows' => $rows,
			],
		]);
	}

	/**
	 * @return array
	 */
	protected function getSettingFields( $path )
	{
		$settings = glsr( DefaultsManager::class )->settings();
		return array_filter( $settings, function( $key ) use( $path ) {
			return glsr( Helper::class )->startsWith( $path, $key );
		}, ARRAY_FILTER_USE_KEY );
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
			return esc_attr( (string)$value );
		}, $context );
	}

	/**
	 * @return string
	 */
	protected function normalizeSettingPath( $path )
	{
		return glsr( Helper::class )->prefixString( rtrim( $path, '.' ), 'settings.' );
	}
}
