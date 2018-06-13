<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Defaults\BuilderDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Attributes;

class Builder
{
	const INPUT_TYPES = [
		'checkbox', 'date', 'datetime-local', 'email', 'file', 'hidden', 'image', 'month',
		'number', 'password', 'radio', 'range', 'reset', 'search', 'submit', 'tel', 'text', 'time',
		'url', 'week',
	];

	const TAGS_FORM = [
		'input', 'select', 'textarea',
	];

	const TAGS_SINGLE = [
		'img',
	];

	const TAGS_STRUCTURE = [
		'div', 'form', 'nav', 'ol', 'section', 'ul',
	];

	const TAGS_TEXT = [
		'a', 'button', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'i', 'label', 'li', 'option', 'p', 'pre', 'small',
		'span',
	];

	/**
	 * @var array
	 */
	public $args = [];

	/**
	 * @var bool
	 */
	public $render = false;

	/**
	 * @var string
	 */
	public $tag;

	/**
	 * @param string $method
	 * @param array $args
	 * @return string|void
	 */
	public function __call( $method, $args )
	{
		$instance = new static;
		$instance->setTagFromMethod( $method );
		call_user_func_array( [$instance, 'normalize'], $args += ['',''] );
		$tags = array_merge( static::TAGS_FORM, static::TAGS_SINGLE, static::TAGS_STRUCTURE, static::TAGS_TEXT );
		$generatedTag = in_array( $instance->tag, $tags )
			? $instance->buildTag()
			: $instance->buildCustomField();
		if( !$this->render ) {
			return $generatedTag;
		}
		echo $generatedTag;
	}

	/**
	 * @param string $property
	 * @param mixed $value
	 * @return void
	 */
	public function __set( $property, $value )
	{
		$properties = [
			'args' => 'is_array',
			'render' => 'is_bool',
			'tag' => 'is_string',
		];
		if( !isset( $properties[$property] )
			|| empty( array_filter( [$value], $properties[$property] ))
		)return;
		$this->$property = $value;
	}

	/**
	 * @return string
	 */
	public function getClosingTag()
	{
		return '</'.$this->tag.'>';
	}

	/**
	 * @return string
	 */
	public function getOpeningTag()
	{
		$attributes = glsr( Attributes::class )->{$this->tag}( $this->args )->toString();
		return '<'.trim( $this->tag.' '.$attributes ).'>';
	}

	/**
	 * @return string|void
	 */
	protected function buildCustomField()
	{
		$className = $this->getCustomFieldClassName();
		if( class_exists( $className )) {
			return (new $className( $this ))->build();
		}
		glsr_log()->error( 'Field missing: '.$className );
	}

	/**
	 * @return string|void
	 */
	protected function buildDefaultTag( $text = '' )
	{
		if( empty( $text )) {
			$text = $this->args['text'];
		}
		return $this->getOpeningTag().$text.$this->getClosingTag();
	}

	/**
	 * @return string|void
	 */
	protected function buildFieldDescription()
	{
		if( empty( $this->args['description'] ))return;
		if( $this->args['is_widget'] ) {
			return $this->small( $this->args['description'] );
		}
		return $this->p( $this->args['description'], ['class' => 'description'] );
	}

	/**
	 * @return string|void
	 */
	protected function buildFormInput()
	{
		if( !in_array( $this->args['type'], ['checkbox', 'radio'] )) {
			return $this->buildFormLabel().$this->getOpeningTag();
		}
		return empty( $this->args['options'] )
			? $this->buildFormInputChoice()
			: $this->buildFormInputMultiChoice();
	}

	/**
	 * @return string|void
	 */
	protected function buildFormInputChoice()
	{
		$labelText = !empty( $this->args['text'] )
			? $this->args['text']
			: $this->args['label'];
		return $this->label( $this->getOpeningTag().' '.$labelText, [
			'class' => 'glsr-'.$this->args['type'].'-label',
			'for' => $this->args['id'],
		]);
	}

	/**
	 * @return string|void
	 */
	protected function buildFormInputMultiChoice()
	{
		if( $this->args['type'] == 'checkbox' ) {
			$this->args['name'].= '[]';
		}
		$options = array_reduce( array_keys( $this->args['options'] ), function( $carry, $key ) {
			return $carry.$this->li( $this->{$this->args['type']}([
				'checked' => in_array( $key, (array)$this->args['value'] ),
				'name' => $this->args['name'],
				'text' => $this->args['options'][$key],
				'value' => $key,
			]));
		});
		return $this->ul( $options, [
			'class' => $this->args['class'],
			'id' => $this->args['id'],
		]);
	}

	/**
	 * @return void|string
	 */
	protected function buildFormLabel()
	{
		if( empty( $this->args['label'] ) || $this->args['type'] == 'hidden' )return;
		return $this->label([
			'for' => $this->args['id'],
			'text' => $this->args['label'],
		]);
	}

	/**
	 * @return string|void
	 */
	protected function buildFormSelect()
	{
		return $this->buildFormLabel().$this->buildDefaultTag( $this->buildFormSelectOptions() );
	}

	/**
	 * @return string|void
	 */
	protected function buildFormSelectOptions()
	{
		return array_reduce( array_keys( $this->args['options'] ), function( $carry, $key ) {
			return $carry.$this->option([
				'selected' => $this->args['value'] == $key,
				'text' => $this->args['options'][$key],
				'value' => $key,
			]);
		});
	}

	/**
	 * @return string|void
	 */
	protected function buildFormTextarea()
	{
		return $this->buildFormLabel().$this->buildDefaultTag( $this->args['value'] );
	}

	/**
	 * @return string|void
	 */
	protected function buildTag()
	{
		$this->mergeArgsWithRequiredDefaults();
		if( in_array( $this->tag, static::TAGS_SINGLE )) {
			return $this->getOpeningTag();
		}
		if( !in_array( $this->tag, static::TAGS_FORM )) {
			return $this->buildDefaultTag();
		}
		return call_user_func( [$this, 'buildForm'.ucfirst( $this->tag )] ).$this->buildFieldDescription();
	}

	/**
	 * @return string
	 */
	protected function getCustomFieldClassName()
	{
		return glsr( Helper::class )->buildClassName( $this->tag, __NAMESPACE__.'\Fields' );
	}

	/**
	 * @return void
	 */
	protected function mergeArgsWithRequiredDefaults()
	{
		$args = glsr( BuilderDefaults::class )->merge( $this->args );
		$className = $this->getCustomFieldClassName();
		if( class_exists( $className )) {
			$args = array_merge(
				wp_parse_args( $args, $className::defaults() ),
				$className::required()
			);
		}
		$this->args = $args;
	}

	/**
	 * @param string|array ...$params
	 * @return void
	 */
	protected function normalize( ...$params )
	{
		if( is_string( $params[0] ) || is_numeric( $params[0] )) {
			$this->setNameOrTextAttributeForTag( $params[0] );
		}
		if( is_array( $params[0] )) {
			$this->args += $params[0];
		}
		else if( is_array( $params[1] )) {
			$this->args += $params[1];
		}
	}

	/**
	 * @param string $value
	 * @return void
	 */
	protected function setNameOrTextAttributeForTag( $value )
	{
		$attribute = in_array( $this->tag, static::TAGS_FORM )
			? 'name'
			: 'text';
		$this->args[$attribute] = $value;
	}

	/**
	 * @param string $method
	 * @return void
	 */
	protected function setTagFromMethod( $method )
	{
		$this->tag = strtolower( $method );
		if( in_array( $this->tag, static::INPUT_TYPES )) {
			$this->args['type'] = $this->tag;
			$this->tag = 'input';
		}
	}
}
