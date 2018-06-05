<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Template;

class Field
{
	const MULTI_FIELD_TYPES = ['radio', 'checkbox'];

	/**
	 * @var array
	 */
	public $field;

	public function __construct( array $field = [] )
	{
		$this->field = wp_parse_args( $field, [
			'is_hidden' => false,
			'is_multi' => false,
			'is_setting' => false,
			'is_valid' => true,
			'path' => '',
		]);
		$this->normalize();
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return (string)$this->build();
	}

	/**
	 * @return string
	 */
	public function build()
	{
		if( !$this->field['is_valid'] )return;
		if( $this->field['type'] == 'hidden' ) {
			return glsr( Builder::class )->hidden( $this->field );
		}
		if( !$this->field['is_setting'] ) {
			return $this->buildField();
		}
		if( !$this->field['is_multi'] ) {
			return $this->buildSettingField();
		}
		return $this->buildSettingMultiField();
	}

	/**
	 * @return void
	 */
	public function render()
	{
		echo $this->build();
	}

	/**
	 * @return string
	 */
	protected function buildField()
	{
		return glsr( Template::class )->build( 'partials/form/field', [
			'context' => [
				'class' => $this->getFieldClass(),
				'field' => glsr( Builder::class )->{$this->field['type']}( $this->field ),
			],
		]);
	}

	/**
	 * @return string
	 */
	protected function buildSettingField()
	{
		$this->field['data-depends'] = $this->getFieldDependsOn();
		return glsr( Template::class )->build( 'partials/form/table-row', [
			'context' => [
				'class' => $this->getFieldClass(),
				'field' => glsr( Builder::class )->{$this->field['type']}( $this->field ),
				'label' => glsr( Builder::class )->label( $this->field['legend'], ['for' => $this->field['id']] ),
			],
		]);
	}

	/**
	 * @return string
	 */
	protected function buildSettingMultiField()
	{
		return glsr( Template::class )->build( 'partials/form/table-row-multiple', [
			'context' => [
				'class' => $this->getFieldClass(),
				'depends_on' => $this->getFieldDependsOn(),
				'field' => glsr( Builder::class )->{$this->field['type']}( $this->field ),
				'label' => glsr( Builder::class )->label( $this->field['legend'], ['for' => $this->field['id']] ),
				'legend' => $this->field['legend'],
			],
		]);
	}

	/**
	 * @return string
	 */
	protected function getFieldClass()
	{
		$classes = [];
		if( $this->field['is_hidden'] ) {
			$classes[] = 'hidden';
		}
		if( !empty( $this->field['required'] )) {
			$classes[] = 'glsr-required';
		}
		return implode( ' ', $classes );
	}

	/**
	 * @return string
	 */
	protected function getFieldDefault()
	{
		return isset( $this->field['default'] )
			? $this->field['default']
			: '';
	}

	/**
	 * @return string
	 */
	protected function getFieldDependsOn()
	{
		return !empty( $this->field['depends_on'] )
			? $this->field['depends_on']
			: '';
	}

	/**
	 * @return string
	 */
	protected function getFieldPrefix()
	{
		return $this->field['is_setting']
			? OptionManager::databaseKey()
			: Application::ID;
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
	 * @return bool
	 */
	protected function isFieldValid()
	{
		$isValid = true;
		$missingValues = [];
		$requiredValues = [
			// 'label', 'name', 'type',
			'name', 'type',
		];
		foreach( $requiredValues as $value ) {
			if( isset( $this->field[$value] ))continue;
			$missingValues[] = $value;
			$isValid = $this->field['is_valid'] = false;
		}
		if( !empty( $missingValues )) {
			glsr_log()
				->warning( 'Field is missing: '.implode( ', ', $missingValues ))
				->info( $this->field );
		}
		return $isValid;
	}

	/**
	 * @return void
	 */
	protected function normalize()
	{
		if( !$this->isFieldValid() )return;
		$field = $this->field;
		foreach( $field as $key => $value ) {
			$methodName = glsr( Helper::class )->buildMethodName( $key, 'normalize' );
			if( !method_exists( $this, $methodName ))continue;
			$this->$methodName();
		}
		$this->normalizeFieldId();
		$this->normalizeFieldType();
		$this->normalizeFieldValue();
	}

	/**
	 * @return void
	 */
	protected function normalizeDependsOn()
	{
		if( empty( $this->field['depends_on'] ) || !is_array( $this->field['depends_on'] ))return;
		$path = key( $this->field['depends_on'] );
		$value = $this->field['depends_on'][$path];
		$this->field['depends_on'] = json_encode([
			'name' => glsr( Helper::class )->convertPathToName( $path, OptionManager::databaseKey() ),
			'value' => $value,
		], JSON_HEX_APOS|JSON_HEX_QUOT );
		$this->field['is_hidden'] = $this->isFieldHidden( $path, $value );
	}

	/**
	 * @return void
	 */
	protected function normalizeFieldId()
	{
		if( isset( $this->field['id'] ) || empty( $this->field['label'] ))return;
		$this->field['id'] = glsr( Helper::class )->convertNameToId( $this->field['name'] );
	}

	/**
	 * @return void
	 */
	protected function normalizeFieldType()
	{
		$className = glsr( Helper::class )->buildClassName( $this->field['type'], __NAMESPACE__.'\Fields' );
		if( class_exists( $className )) {
			$this->field = array_merge(
				wp_parse_args( $this->field, $className::defaults() ),
				$className::required()
			);
		}
		if( in_array( $this->field['type'], static::MULTI_FIELD_TYPES )) {
			$this->field['is_multi'] = true;
		}
	}

	/**
	 * @return void
	 */
	protected function normalizeFieldValue()
	{
		if( isset( $this->field['value'] ))return;
		$this->field['value'] = glsr( OptionManager::class )->get(
			$this->field['path'],
			$this->getFieldDefault()
		);
	}

	/**
	 * @return void
	 */
	protected function normalizeLabel()
	{
		if( !$this->field['is_setting'] )return;
		$this->field['legend'] = $this->field['label'];
		unset( $this->field['label'] );
	}

	/**
	 * @return void
	 */
	protected function normalizeName()
	{
		$this->field['path'] = $this->field['name'];
		$this->field['name'] = glsr( Helper::class )->convertPathToName(
			$this->field['name'],
			$this->getFieldPrefix()
		);
	}
}
