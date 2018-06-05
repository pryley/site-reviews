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
	 * @param string $partialPath
	 * @return void
	 */
	public function render( $partialPath, array $args = [] )
	{
		echo $this->build( $partialPath, $args );
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
			$rows.= new Field( $field );
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
