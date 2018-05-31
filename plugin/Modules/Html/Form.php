<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Database\DefaultsManager;
use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Field;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Translator;

class Form
{
	/**
	 * @param string $id
	 * @return void
	 */
	public function renderFields( $id )
	{
		$method = glsr( Helper::class )->buildMethodName( $id, 'getTemplateContextFor' );
		$context = !method_exists( $this, $method )
			? $this->getTemplateContext( $id )
			: $this->$method( $id );
		glsr( Template::class )->render( 'pages/settings/'.$id, [
			'context' => $context,
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
	 * @param string $id
	 * @return array
	 */
	protected function getTemplateContext( $id )
	{
		$fields = $this->getSettingFields( $this->normalizeSettingPath( $id ));
		$rows = '';
		foreach( $fields as $name => $field ) {
			$field = wp_parse_args( $field, ['name' => $name] );
			$rows.= (new Field( $field ))->build();
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
	 * @return string
	 */
	protected function normalizeSettingPath( $path )
	{
		return glsr( Helper::class )->prefixString( rtrim( $path, '.' ), 'settings.' );
	}
}
