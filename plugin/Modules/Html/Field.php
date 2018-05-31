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
			'is_valid' => true,
			'path' => '',
		]);
		$this->normalize();
	}

	/**
	 * @return string
	 */
	public function build()
	{
		if( !$this->field['is_valid'] )return;
		if( $this->field['is_multi'] ) {
			return $this->buildMultiField();
		}
		$this->field['data-depends'] = $this->getFieldDepends();
		return glsr( Template::class )->build( 'partials/settings/form-table-row', [
			'context' => [
				'class' => $this->getFieldClass(),
				'field' => glsr( Builder::class )->{$this->field['type']}( $this->field ),
				'label' => glsr( Builder::class )->label( $this->field['legend'], ['for' => $this->field['id']] ),
			],
		]);
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
	protected function buildMultiField()
	{
		return glsr( Template::class )->build( 'partials/settings/form-table-row-multiple', [
			'context' => [
				'class' => $this->getFieldClass(),
				'depends' => $this->getFieldDepends(),
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
		return $this->field['is_hidden']
			? 'hidden'
			: '';
	}

	/**
	 * @return string
	 */
	protected function getFieldDepends()
	{
		return !empty( $this->field['depends'] )
			? $this->field['depends']
			: '';
	}

	/**
	 * @param string $path
	 * @param string $expectedValue
	 * @return bool
	 */
	protected function isFieldHidden( $path, $expectedValue )
	{
		$optionValue = glsr( OptionManager::class )->get( $path );
		if( is_array( $optionValue )) {
			return !in_array( $expectedValue, $optionValue );
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
			'label', 'name', 'type',
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
	protected function normalizeDepends()
	{
		if( empty( $this->field['depends'] ) || !is_array( $this->field['depends'] ))return;
		$path = key( $this->field['depends'] );
		$value = $this->field['depends'][$path];
		$this->field['depends'] = json_encode([
			'name' => glsr( Helper::class )->convertPathToName( $path, Application::ID ),
			'value' => $value,
		], JSON_HEX_APOS|JSON_HEX_QUOT );
		$this->field['is_hidden'] = $this->isFieldHidden( $path, $value );
	}

	/**
	 * @return void
	 */
	protected function normalizeFieldId()
	{
		if( isset( $this->field['id'] ))return;
		$this->field['id'] = glsr( Helper::class )->convertNameToId( $this->field['name'] );
	}

	/**
	 * @return void
	 */
	protected function normalizeFieldType()
	{
		$className = glsr( Helper::class )->buildClassName( $this->field['type'], 'Modules\Html\Fields' );
		if( class_exists( $className )) {
			$this->field = array_merge( $this->field, glsr( $className )->defaults() );
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
		$defaultValue = isset( $this->field['default'] )
			? $this->field['default']
			: '';
		$this->field['value'] = glsr( OptionManager::class )->get( $this->field['path'], $defaultValue );
	}

	/**
	 * @return void
	 */
	protected function normalizeLabel()
	{
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
			Application::ID
		);
	}
}
