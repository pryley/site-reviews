<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Database\DefaultsManager;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Field;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Translation;

class Settings
{
	/**
	 * @var array
	 */
	public $settings;

	/**
	 * @param string $id
	 * @return string
	 */
	public function buildFields( $id )
	{
		$this->settings = glsr( DefaultsManager::class )->settings();
		$method = glsr( Helper::class )->buildMethodName( $id, 'getTemplateDataFor' );
		$data = !method_exists( $this, $method )
			? $this->getTemplateData( $id )
			: $this->$method( $id );
		return glsr( Template::class )->build( 'pages/settings/'.$id, $data );
	}

	/**
	 * @return string
	 */
	protected function getFieldDefault( array $field )
	{
		return isset( $field['default'] )
			? $field['default']
			: '';
	}

	/**
	 * @return array
	 */
	protected function getSettingFields( $path )
	{
		return array_filter( $this->settings, function( $key ) use( $path ) {
			return glsr( Helper::class )->startsWith( $path, $key );
		}, ARRAY_FILTER_USE_KEY );
	}

	/**
	 * @return string
	 */
	protected function getSettingRows( array $fields )
	{
		$rows = '';
		foreach( $fields as $name => $field ) {
			$field = wp_parse_args( $field, [
				'is_setting' => true,
				'name' => $name,
			]);
			$rows.= new Field( $this->normalize( $field ));
		}
		return $rows;
	}

	/**
	 * @param string $id
	 * @return array
	 */
	protected function getTemplateData( $id )
	{
		$fields = $this->getSettingFields( $this->normalizeSettingPath( $id ));
		return [
			'context' => [
				'rows' => $this->getSettingRows( $fields ),
			],
		];
	}

	/**
	 * @param string $id
	 * @return array
	 */
	protected function getTemplateDataForAddons( $id )
	{
		$fields = $this->getSettingFields( $this->normalizeSettingPath( $id ));
		$settings = glsr( Helper::class )->convertDotNotationArray( $fields );
		$settingKeys = array_keys( $settings['settings']['addons'] );
		$results = [];
		foreach( $settingKeys as $key ) {
			$addonFields = array_filter( $fields, function( $path ) use( $key ) {
				return glsr( Helper::class )->startsWith( 'settings.addons.'.$key, $path );
			}, ARRAY_FILTER_USE_KEY );
			$results[$key] = $this->getSettingRows( $addonFields );
		}
		ksort( $results );
		return [
			'settings' => $results,
		];
	}

	/**
	 * @param string $id
	 * @return array
	 */
	protected function getTemplateDataForLicenses( $id )
	{
		$fields = $this->getSettingFields( $this->normalizeSettingPath( $id ));
		ksort( $fields );
		return [
			'context' => [
				'rows' => $this->getSettingRows( $fields ),
			],
		];
	}

	/**
	 * @return array
	 */
	protected function getTemplateDataForTranslations()
	{
		$translations = glsr( Translation::class )->renderAll();
		$class = empty( $translations )
			? 'glsr-hidden'
			: '';
		return [
			'context' => [
				'class' => $class,
				'database_key' => OptionManager::databaseKey(),
				'translations' => $translations,
			],
		];
	}

	/**
	 * @param string $path
	 * @param string|array $expectedValue
	 * @return bool
	 */
	protected function isFieldHidden( $path, $expectedValue )
	{
		$optionValue = glsr( OptionManager::class )->get(
			$path,
			glsr( Helper::class )->getPathValue( $path, glsr()->defaults )
		);
		if( is_array( $expectedValue )) {
			return is_array( $optionValue )
				? count( array_intersect( $optionValue, $expectedValue )) === 0
				: !in_array( $optionValue, $expectedValue );
		}
		return $optionValue != $expectedValue;
	}

	/**
	 * @return bool
	 */
	protected function isMultiDependency( $path )
	{
		if( isset( $this->settings[$path] )) {
			$field = $this->settings[$path];
			return ( $field['type'] == 'checkbox' && !empty( $field['options'] ))
				|| !empty( $field['multiple'] );
		}
		return false;
	}

	/**
	 * @return array
	 */
	protected function normalize( array $field )
	{
		$field = $this->normalizeDependsOn( $field );
		$field = $this->normalizeLabelAndLegend( $field );
		$field = $this->normalizeValue( $field );
		return $field;
	}

	/**
	 * @return array
	 */
	protected function normalizeDependsOn( array $field )
	{
		if( !empty( $field['depends_on'] ) && is_array( $field['depends_on'] )) {
			$path = key( $field['depends_on'] );
			$expectedValue = $field['depends_on'][$path];
			$fieldName = glsr( Helper::class )->convertPathToName( $path, OptionManager::databaseKey() );
			if( $this->isMultiDependency( $path )) {
				$fieldName.= '[]';
			}
			$field['data-depends'] = json_encode([
				'name' => $fieldName,
				'value' => $expectedValue,
			], JSON_HEX_APOS|JSON_HEX_QUOT );
			$field['is_hidden'] = $this->isFieldHidden( $path, $expectedValue );
		}
		return $field;
	}

	/**
	 * @return array
	 */
	protected function normalizeLabelAndLegend( array $field )
	{
		if( !empty( $field['label'] )) {
			$field['legend'] = $field['label'];
			unset( $field['label'] );
		}
		else {
			$field['is_valid'] = false;
			glsr_log()->warning( 'Setting field is missing a label' )->info( $field );
		}
		return $field;
	}

	/**
	 * @return array
	 */
	protected function normalizeValue( array $field )
	{
		if( !isset( $field['value'] )) {
			$field['value'] = glsr( OptionManager::class )->get(
				$field['name'],
				$this->getFieldDefault( $field )
			);
		}
		return $field;
	}

	/**
	 * @return string
	 */
	protected function normalizeSettingPath( $path )
	{
		return glsr( Helper::class )->prefixString( rtrim( $path, '.' ), 'settings.' );
	}
}
