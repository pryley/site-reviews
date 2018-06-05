<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Database\DefaultsManager;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Field;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Translator;

class Settings
{
	/**
	 * @param string $id
	 * @return string
	 */
	public function buildFields( $id )
	{
		$method = glsr( Helper::class )->buildMethodName( $id, 'getTemplateContextFor' );
		$context = !method_exists( $this, $method )
			? $this->getTemplateContext( $id )
			: $this->$method( $id );
		return glsr( Template::class )->build( 'pages/settings/'.$id, [
			'context' => $context,
		]);
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
		$settings = glsr( DefaultsManager::class )->settings();
		return array_filter( $settings, function( $key ) use( $path ) {
			return glsr( Helper::class )->startsWith( $path, $key );
		}, ARRAY_FILTER_USE_KEY );
	}

	/**
	 * @param string $id
	 * @return array
	 */
	protected function getTemplateContext( $id )
	{
		$fields = $this->getSettingFields( $this->normalizeSettingPath( $id ));
		$rows = '';
		foreach( $fields as $name => $field ) {
			$field = wp_parse_args( $field, [
				'is_setting' => true,
				'name' => $name,
			]);
			$rows.= new Field( $this->normalize( $field ));
		}
		return [
			'rows' => $rows,
		];
	}

	/**
	 * @return array
	 */
	protected function getTemplateContextForTranslations()
	{
		$translations = glsr( Translator::class )->renderAll();
		$class = empty( $translations )
			? 'glsr-hidden'
			: '';
		return [
			'class' => $class,
			'database_key' => OptionManager::databaseKey(),
			'translations' => $translations,
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
			return !in_array( $optionValue, $expectedValue );
		}
		return $optionValue != $expectedValue;
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
			$field['data-depends'] = json_encode([
				'name' => glsr( Helper::class )->convertPathToName( $path, OptionManager::databaseKey() ),
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
		if( isset( $field['label'] )) {
			$field['legend'] = $field['label'];
			unset( $field['label'] );
		}
		else {
			$field['is_valid'] = false;
			glsr_log()->warning( 'Field is missing label' )->info( $field );
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
