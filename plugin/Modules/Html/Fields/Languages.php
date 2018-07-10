<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Modules\Html\Fields\Field;

class Languages extends Field
{
	/**
	 * @return string|void
	 */
	public function build()
	{
		$this->builder->tag = 'select';
		$this->mergeFieldArgs();
		return $this->builder->buildFormSelect();
	}

	/**
	 * @return array
	 */
	public static function required()
	{
		return [
			'options' => static::options(),
			'type' => 'select',
		];
	}

	/**
	 * @return array
	 */
	protected static function options()
	{
		require_once( ABSPATH.'wp-admin/includes/translation-install.php' );
		$locales = wp_get_available_translations();
		array_walk( $locales, function( &$value ) {
			$value = $value['native_name'];
		});
		$locales['en_US'] = 'English (United States)';
		ksort( $locales );
		return $locales;
	}
}
