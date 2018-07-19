<?php

namespace GeminiLabs\SiteReviews\Modules\Html;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\Field;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Session;

class Form
{
	/**
	 * @param string $id
	 * @return string
	 */
	public function buildFields( $id )
	{
		return array_reduce( $this->getFields( $id ), function( $carry, $field ) {
			return $carry.$field;
		});
	}

	/**
	 * @param string $id
	 * @return array
	 */
	public function getFields( $id )
	{
		$fields = [];
		$configPath = glsr()->path( 'config/'.$id.'.php' );
		$values = file_exists( $configPath )
			? include $configPath
			: [];
		$values = apply_filters( 'site-reviews/form/fields', $values );
		foreach( $values as $name => $field ) {
			$fields[] = new Field( wp_parse_args( $field, ['name' => $name] ));
		}
		return $fields;
	}

	/**
	 * @param string $id
	 * @return void
	 */
	public function renderFields( $id )
	{
		echo $this->buildFields( $id );
	}
}
