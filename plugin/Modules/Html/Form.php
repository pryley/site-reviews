<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database\DefaultsManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class Form
{
	/**
	 * @param string $path
	 * @return string
	 */
	public function build( $path, array $fields = [] )
	{
		if( empty( $fields )) {
			$fields = $this->getSettingsFields( $this->normalizeSettingsPath( $path ));
		}
		foreach( $fields as $name => &$field ) {
			$field = wp_parse_args( $field, ['name' => $name] );
			// new Field( $field );
		}
		// 1. generate fields (incl default form fields)
		// 2. generate form
		glsr_debug( $path, $fields );
	}

	/**
	 * @return array
	 */
	protected function getSettingsFields( $path )
	{
		$settings = glsr( DefaultsManager::class )->settings();
		return array_filter( $settings, function( $key ) use( $path ) {
			return glsr( Helper::class )->startsWith( $path, $key );
		}, ARRAY_FILTER_USE_KEY );
	}

	/**
	 * @return string
	 */
	protected function normalizeSettingsPath( $path )
	{
		return glsr( Helper::class )->prefixString( rtrim( $path, '.' ), 'settings.' );
	}
}
